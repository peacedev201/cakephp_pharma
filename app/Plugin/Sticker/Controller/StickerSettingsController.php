<?php 
class StickerSettingsController extends StickerAppController{
    public $components = array('QuickSettings');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('SettingGroup');
    }
    
    public function admin_index()
    {
        $this->QuickSettings->run($this, array("Sticker"));
        
        $this->set(array(
            'title_for_layout' => __d('sticker', 'Settings')
        ));
    }
}