<?php 
class AdsSettingsController extends AdsAppController{
    public $components = array('QuickSettings');
    public function admin_index($id = null)
    {
        $this->QuickSettings->run($this, array("Ads"), $id);
        $adsEnable = Configure::read('Ads.ads_enabled');
        $this->loadModel('Menu.CoreMenuItem');
        $item = $this->CoreMenuItem->find('first',array('conditions'=>array('plugin'=>'Ads','original_name'=>ADS_MENU_ITEM)));
    
        if($item){

            $this->CoreMenuItem->id = $item['CoreMenuItem']['id'];
            $this->CoreMenuItem->saveField('is_active',$adsEnable);

        }
        $this->set(array(
            'title_for_layout' => __d('ads', 'Ad Settings')
        ));
    }
}