<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class RoleBadgeSettingsController extends RoleBadgeAppController {

    public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function admin_index($iId = null) {
        $this->QuickSettings->run($this, array("RoleBadge"), $iId);
        $this->set('title_for_layout', __d('role_badge', 'User Badges'));
    }

}
