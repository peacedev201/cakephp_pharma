<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class ReviewSettingsController extends ReviewAppController {

    public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function admin_index($iId = null) {
        $this->QuickSettings->run($this, array("Review"), $iId);
        $this->set('title_for_layout', __d('review', 'Reviews Setting'));
    }

}
