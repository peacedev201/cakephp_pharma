<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('VerifyProfileAppModel', 'VerifyProfile.Model');

class VerifyProfile extends VerifyProfileAppModel {

    public $belongsTo = array('User' => array('counterCache' => true));

    public function beforeDelete($cascade = true) {
        $aVerifyProfile = $this->findById($this->id);
        if (!empty($aVerifyProfile)) {
            $oActivityModel = MooCore::getInstance()->getModel('Activity');
            $aActivityId = $oActivityModel->find('list', array('fields' => array('Activity.id'), 'conditions' => array('Activity.action' => 'verify_profile_verified', 'Activity.item_type' => 'VerifyProfile.VerifyProfile', 'Activity.user_id' => $aVerifyProfile['VerifyProfile']['user_id'])));
            if (!empty($aActivityId)) {
                $oActivityModel->deleteAll(array('Activity.id' => $aActivityId), true, true);
            }
        }

        parent::beforeDelete($cascade);
    }

    public function getStatus($iUserId) {
        $aVerifyProfile = $this->find('first', array('conditions' => array('VerifyProfile.user_id' => $iUserId)));
        if (!empty($aVerifyProfile)) {
            return $aVerifyProfile['VerifyProfile']['status'];
        }
        return false;
    }

    public function updateStatus($aVerifyProfile, $sType, $aReasons = array(), $iUserId) {
        $sRoleGroup = Configure::read('VerifyProfile.verify_profile_group');
        $aRoleGroup = explode('_', $sRoleGroup);

        $aUnverifyGroup = array();
        if (!empty($aRoleGroup[0])) {
            $aUnverifyGroup = json_decode($aRoleGroup[0]);
        }

        $aVerifyGroup = array();
        if (!empty($aRoleGroup[1])) {
            $aVerifyGroup = json_decode($aRoleGroup[1]);
        }

        $iRoleId = false;
        $sPrefix = Configure::read('core.prefix');
        $this->id = $aVerifyProfile['VerifyProfile']['id'];
        $oDataSource = ConnectionManager::getDataSource('default');
        if ($sType == "verify") {
            foreach ($aUnverifyGroup as $key => $value) {
                if ($value == $aVerifyProfile['User']['role_id']) {
                    $iRoleId = $aVerifyGroup[$key];
                    break;
                }
            }

            if ($iRoleId) {
                $oDataSource->rawQuery("UPDATE `{$sPrefix}users` SET `role_id` = {$iRoleId} WHERE `id` = {$aVerifyProfile['User']['id']}");
            }

            $this->save(array('status' => 'verified'));
        } else if ($sType == "unverify") {
            foreach ($aVerifyGroup as $key => $value) {
                if ($value == $aVerifyProfile['User']['role_id']) {
                    $iRoleId = $aUnverifyGroup[$key];
                    break;
                }
            }

            if ($iRoleId) {
                $oDataSource->rawQuery("UPDATE `{$sPrefix}users` SET `role_id` = {$iRoleId} WHERE `id` = {$aVerifyProfile['User']['id']}");
            }

            $this->delete($aVerifyProfile['VerifyProfile']['id']);
        }

        $this->sendMailAction($aVerifyProfile, $sType, $aReasons, $iUserId);
    }

    public function sendMailAction($aVerifyProfile, $sType, $aReasons = array(), $iUserId) {
        $aViewer = MooCore::getInstance()->getViewer();
        if ((!empty($aViewer) && !$aViewer['Role']['is_admin']) || $aVerifyProfile['User']['id'] == $iUserId) {
            return;
        }

        $oActivityModel = MooCore::getInstance()->getModel('Activity');
        $oMailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
        $sHttp = Configure::read('core.ssl_mode') ? 'https' : 'http';
        $oRequest = Router::getRequest();

        if ($sType == 'verify') {

            // Add to feed
            $oActivityModel->create();
            $oActivityModel->save(array(
                'type' => 'user',
                'action' => 'verify_profile_verified',
                'user_id' => $aVerifyProfile['User']['id'],
                'item_type' => 'VerifyProfile.VerifyProfile',
                'plugin' => 'VerifyProfile'
            ));

            // Send mail verified
            $aParamVerified = array(
                'recipient_link' => $sHttp . '://' . $_SERVER['SERVER_NAME'] . $oRequest->base . $aVerifyProfile['User']['moo_url'],
                'recipient_title' => $aVerifyProfile['User']['name'],
            );
            $oMailComponent->send($aVerifyProfile['User']['email'], 'verify_profile_verified', $aParamVerified);
        } elseif ($sType == 'unverify') {

            $sStatusSubject = 'denied';
            if ($aVerifyProfile['VerifyProfile']['status'] == 'verified') {
                $sStatusSubject = 'unverified';
            }

            $sReason = '';
            if (empty($aReasons)) {
                $sReason.= '
                            - ' . __d('verify_profile', 'Your profile has changed!');
            } else {
                foreach ($aReasons as $sReasonTmp) {
                    $sReason.= '
                                - ' . $sReasonTmp;
                }
            }

            // Send mail verified
            $aParamUnverified = array(
                'recipient_link' => $sHttp . '://' . $_SERVER['SERVER_NAME'] . $oRequest->base . $aVerifyProfile['User']['moo_url'],
                'recipient_title' => $aVerifyProfile['User']['name'],
                'recipient_status' => $sStatusSubject,
                'recipient_reason' => $sReason,
            );
            $oMailComponent->send($aVerifyProfile['User']['email'], 'verify_profile_unverified', $aParamUnverified);
        }
    }

}
