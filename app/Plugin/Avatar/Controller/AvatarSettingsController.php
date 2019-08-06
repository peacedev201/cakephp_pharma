<?php 
class AvatarSettingsController extends AvatarAppController{
    public $components = array('QuickSettings');
    public function admin_index($id = null)
    {
        $this->QuickSettings->run($this,array("Avatar"),$id);
        $this->set('title_for_layout', __d('avatar','Avatar Setting'));
    }
}