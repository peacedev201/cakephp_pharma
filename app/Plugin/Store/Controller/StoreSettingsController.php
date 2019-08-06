<?php 
class StoreSettingsController extends StoreAppController
{
    public $components = array('QuickSettings');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('SettingGroup');
        $this->loadModel('Store.StoreSetting');
        $this->loadModel('Store.Store');
    }
    
    public function admin_index()
    {
        if(Configure::read('Store.store_enabled'))
        {
            $this->Store->activeMenu(1);
        }
        else 
        {
            $this->Store->activeMenu(0);
        }
        
        //credit
        $this->StoreSetting->integrateCredit();
        
        $this->QuickSettings->run($this, array("Store"));
        
        $this->set(array(
            'title_for_layout' => __d('store', 'Store Settings')
        ));
    }
    
    function price_filter_range()
    {
        return $this->StoreSetting->loadPriceFilterRange();
    }
}