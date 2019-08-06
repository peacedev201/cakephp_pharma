<?php 
class SpotlightSettingsController extends SpotlightAppController{
	public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
        $this->loadModel('Plugin');
    }

    public function admin_index($id = null)
    {

        Cache::clearGroup('spotlight', 'spotlight');
        $this->set('title_for_layout', __d('spotlight', 'Spotlight Setting'));
        
        $this->QuickSettings->run($this, array("Spotlight"), $id);
    }

}