<?php 
App::uses('MooPlugin','Lib');
class SliderPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){
        //Delete S3
        $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
        $types = array('sliders');
        foreach ($types as $type) {
            $objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
        }
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('slider', 'Slideshows') => array('plugin' => 'slider', 'controller' => 'sliders', 'action' => 'admin_index'),
            __d('slider', 'Settings') => array('plugin' => 'slider', 'controller' => 'slider_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}