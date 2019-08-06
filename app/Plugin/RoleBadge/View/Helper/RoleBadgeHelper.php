<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('AppHelper', 'View/Helper');

class RoleBadgeHelper extends AppHelper {

    public function __construct(\View $View, $settings = array()) {
        if ($this->checkStorageEnabled()) {
            $this->helpers = array('Storage.Storage');
        }

        parent::__construct($View, $settings);
    }

    public function getImage($sThumbnail) {

        if ($this->checkStorageEnabled()) {
            $url = $this->Storage->getImage('role_badge/img/setting/' . $sThumbnail);
        } else {
            $request = Router::getRequest();
            $url = FULL_BASE_URL . $request->webroot . 'role_badge/img/setting/' . $sThumbnail;
        }

        return $url;
    }

    public function getEnable() {
        return Configure::check('RoleBadge.role_badge_enabled') ? Configure::read('RoleBadge.role_badge_enabled') : 0;
    }

    public function checkStorageEnabled() {
        $oModelPlugin = MooCore::getInstance()->getModel('Plugin');
        $aPlugin = $oModelPlugin->findByKey('Storage', array('enabled'));
        if (!empty($aPlugin)) {
            return true;
        }

        return false;
    }

}
