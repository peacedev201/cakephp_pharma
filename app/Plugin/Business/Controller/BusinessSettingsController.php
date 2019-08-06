<?php 
class BusinessSettingsController extends BusinessAppController{
    public $components = array('QuickSettings');
    
    public function admin_index($id = null)
    {
        $this->loadModel('Business.Business');
        if(Configure::read('Business.business_enabled'))
        {
            $this->Business->activeMenu(1);
        }
        else 
        {
            $this->Business->activeMenu(0);
        }
        $this->QuickSettings->run($this, array("Business"), $id);
        $this->set('title_for_layout', __d('business', 'Business Settings'));
    }
}