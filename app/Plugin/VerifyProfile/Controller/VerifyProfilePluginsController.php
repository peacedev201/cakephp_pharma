<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class VerifyProfilePluginsController extends VerifyProfileAppController {

    public $paginate = array(
        'limit' => RESULTS_LIMIT
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('VerifyProfile.VerifyProfile');
    }

    public function admin_index() {
        $aCond = array("VerifyProfile.status != 'unverified' ");
        $sStatus = 'All';
        if (!empty($this->request->named['filter'])) {
            $sStatus = $this->request->named['filter'];
            $aCond['VerifyProfile.status'] = $this->request->named['filter'];
        }
        $aVerifyProfiles = $this->paginate('VerifyProfile', $aCond);

        $this->set('sStatus', ucfirst($sStatus));
        $this->set('aVerifyProfiles', $aVerifyProfiles);
        $this->set('title_for_layout', __d('verify_profile', 'Profile Verify Manager'));
    }

    public function admin_unverify($id) {

        $this->loadModel('User');
        $aUser = $this->User->findById($id);
        if (empty($id) || empty($aUser)) {
            $this->autoRender = false;
        }

        $this->loadModel('VerifyProfile.VerifyReason');
        $aReasons = $this->VerifyReason->find('all');

        $sStatus = $this->VerifyProfile->getStatus($id);
        $sButton = __d('verify_profile', 'Deny');
        if ($sStatus == "verified") {
            $sButton = __d('verify_profile', 'Unverify');
        }

        $this->set('aReasons', $aReasons);
        $this->set('sButton', $sButton);
        $this->set('user_id', $id);
    }

    public function admin_verify($id) {

        $aUser = $this->User->findById($id);
        if (empty($id) || empty($aUser)) {
            $this->autoRender = false;
        }

        $this->set('user_id', $id);
    }

    public function admin_view_document($id) {

        $aVerifyProfile = $this->VerifyProfile->findById($id);
        if (empty($id) || empty($aVerifyProfile)) {
            $this->autoRender = false;
        }

        $this->loadModel('Role');
        $aRole = $this->Role->findById($aVerifyProfile['User']['role_id']);

        $this->set('sRole', $aRole['Role']['name']);
        $this->set('aVerifyProfile', $aVerifyProfile);
    }

    public function admin_photo_document($id) {
        $this->loadModel('Photo.Photo');
        $aPhoto = $this->Photo->findById($id);
        if (empty($id) || empty($aPhoto)) {
            $this->autoRender = false;
        }

        $this->set('aPhoto', $aPhoto);
    }

}
