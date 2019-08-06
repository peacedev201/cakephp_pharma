<?php 
class SocialLoginSettingsController extends SocialLoginAppController{
    public $components = array('QuickSettings');
    
    public function admin_index()
    {
        $this->QuickSettings->run($this, array("SocialLogin"));
        $this->set('title_for_layout',  __d('social_login','Social Login Setting'));
    }
    
    public function admin_linkedin($id = null) {

        $this->QuickSettings->run($this, array("LinkedinIntegration"), $id);
        $this->set('url', '/admin/social/linkedin/');
        $this->set('title_for_layout',  __d('social_login','Social Login Setting'));
    }
    
    public function admin_twitter($id = null) {

        $this->QuickSettings->run($this, array("TwitterIntegration"), $id);
        $this->set('url', '/admin/social/twitter/');
        $this->set('title_for_layout',  __d('social_login','Social Login Setting'));
    }
}