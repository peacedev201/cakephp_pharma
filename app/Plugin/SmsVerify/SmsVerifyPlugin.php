<?php 
App::uses('MooPlugin','Lib');
class SmsVerifyPlugin implements MooPlugin{
    public function install(){
    	//Setting
    	$settingModel = MooCore::getInstance()->getModel('Setting');
    	$setting = $settingModel->findByName('sms_verify_enable');
    	if ($setting)
    	{
    		$settingModel->id = $setting['Setting']['id'];
    		$settingModel->save(array('is_boot'=>1));
    	}
    	
    	$settingModel->query("UPDATE ".$settingModel->tablePrefix."users SET sms_verify = 1");
    	
    	$userModel = MooCore::getInstance()->getModel("User");
    	$userModel->getDataSource()->flushMethodCache();
    	
    }
    public function uninstall(){
    	$userModel = MooCore::getInstance()->getModel("User");
    	$userModel->getDataSource()->flushMethodCache();
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('sms_verify','Users Manager') => array('plugin' => 'sms_verify', 'controller' => 'sms_verifys', 'action' => 'admin_index'),
        	__d('sms_verify','Gateways')=> array('plugin' => 'sms_verify', 'controller' => 'sms_verify_gateways', 'action' => 'admin_index'),
            __d('sms_verify','Settings') => array('plugin' => 'sms_verify', 'controller' => 'sms_verify_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
    
    public function callback_1_1(){
    	$userModel = MooCore::getInstance()->getModel("User");
    	$userModel->getDataSource()->flushMethodCache();
    	
    	$userModel->query("UPDATE ".$userModel->tablePrefix."users SET sms_verify_checked = 1 WHERE sms_verify=1");
    }
}