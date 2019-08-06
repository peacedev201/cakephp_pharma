<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class ReviewPluginsController extends ReviewAppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }
    
    public function admin_index() {
        $this->redirect(array('plugin' => 'review', 'controller' => 'review_settings', 'action' => 'admin_index'));
    }

}
