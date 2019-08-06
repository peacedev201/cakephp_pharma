<?php 

/**
* Copyright (c) SocialLOFT LLC
* mooSocial - The Web 2.0 Social Network Software
* @website: http://www.moosocial.com
* @author: mooSocial - Linh.LHD
* @license: https://moosocial.com/license/
 */

class SocialPublisherSettingsController extends SocialPublisherAppController{
    public $components = array('QuickSettings');
    
    public function admin_index()
    {
        $this->QuickSettings->run($this, array("SocialPublisher"));
        $this->set('title_for_layout',  __d('social_publisher','Social Publisher Setting'));
    }
    
    public function admin_twitter($id = null) {
        $this->set('social_publisher', __('Social Publisher Setting'));
        $this->set('title_for_layout',  __d('social_publisher','Social Publisher Setting'));
        $this->QuickSettings->run($this, array("TwitterIntegration"), $id);
        $this->set('url', '/admin/social/twitter/');
    }
}