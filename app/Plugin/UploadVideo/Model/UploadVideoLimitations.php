<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('UploadVideoAppModel', 'UploadVideo.Model');

class UploadVideoLimitations extends UploadVideoAppModel {

    var $useTable = 'video_limitations';
    //
    public $belongsTo = array('Role');
    public $validate = array(
        'role_id' => array(
            'required' => true,
            'rule' => 'notBlank',
            'message' => 'User role is required',
        ),
        'value' => array(
            'notBlank' => array(
                'required' => true,
                'rule' => 'notBlank',
                'message' => 'Videos can upload is required',
            ),
            'numeric' => array(
                'rule' => 'numeric',
                'message' => 'Videos can upload is numeric',
            )
        ),
        'size' => array(
            'notBlank' => array(
                'required' => true,
                'rule' => 'notBlank',
                'message' => 'File size is required',
            ),
            'numeric' => array(
                'rule' => 'numeric',
                'message' => 'File size is numeric',
            )
        )
    );

    public function checkLimitation($aUser) {
        $aLimitation = $this->findByRoleId($aUser['Role']['id']);
        if (!empty($aLimitation['UploadVideoLimitations']['value'])) {
            $aCond = array('Video.user_id' => $aUser['User']['id'], 'Video.pc_upload' => 1);
            $oVideoModel = MooCore::getInstance()->getModel('Video.Video');

            // Limit by Day
            if ($aLimitation['UploadVideoLimitations']['per_type'] == 'D') {
                array_push($aCond, 'DATE(Video.created) = CURDATE()');
            }
            
            // Limit by Month
            if ($aLimitation['UploadVideoLimitations']['per_type'] == 'M') {
                array_push($aCond, 'YEAR(Video.created) = YEAR(NOW())');
                array_push($aCond, 'MONTH(Video.created) = MONTH(NOW())');
            }
            
            // Limit by Month
            if ($aLimitation['UploadVideoLimitations']['per_type'] == 'Y') {
                array_push($aCond, 'YEAR(Video.created) = YEAR(NOW())');
            }

            $iCound = $oVideoModel->find('count', array('conditions' => $aCond));
            if ($iCound >= $aLimitation['UploadVideoLimitations']['value']) {
                return true;
            }
        }

        return false;
    }

}
