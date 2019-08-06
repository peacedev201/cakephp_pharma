<?php 
class PopupSettingsController extends PopupAppController{

    public $components = array('QuickSettings');
    public function admin_index($id = null)
    {
        $this->QuickSettings->run($this,array("Popup"),$id);
        $this->set('title_for_layout', __d('popup','Popup Setting'));
    }
}