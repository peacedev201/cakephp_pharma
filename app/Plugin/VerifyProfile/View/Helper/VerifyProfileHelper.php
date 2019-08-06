<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('AppHelper', 'View/Helper');

class VerifyProfileHelper extends AppHelper {
    
    public function __construct(\View $View, $settings = array()) {
        if ($this->checkStorageEnabled()) {
            $this->helpers = array('Storage.Storage');
        }
        
        parent::__construct($View, $settings);
    }

    public function getImageSetting($sImage) {
        if ($this->checkStorageEnabled()) {
            $url = $this->Storage->getImage('verify_profile/img/setting/' . $sImage);
        } else {
            $request = Router::getRequest();
            $url = FULL_BASE_URL . $request->webroot . 'verify_profile/img/setting/' . $sImage;
        }

        return $url;
    }

    public function getEnable() {
        return Configure::check('VerifyProfile.verify_profile_enable') ? Configure::read('VerifyProfile.verify_profile_enable') : 0;
    }
    
    public function checkStorageEnabled(){
        $oModelPlugin = MooCore::getInstance()->getModel('Plugin');
        $aPlugin = $oModelPlugin->findByKey('Storage', array('enabled'));
        if (!empty($aPlugin)) {
            return true;
        }
        
        return false;
    }

}
