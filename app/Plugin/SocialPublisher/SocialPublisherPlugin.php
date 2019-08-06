<?php 
App::uses('MooPlugin','Lib');
class SocialPublisherPlugin implements MooPlugin{
    public function install(){
         //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        
        $settingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
        $request = Router::getRequest();
        
        $twitterIntergration = $settingGroupModel->findByName('Twitter Integration');
        if(!$twitterIntergration){
            $twitter_group = array(
                'group_type' =>  'TwitterIntegration',
                'module_id' =>  'TwitterIntegration',
                'name' => 'Twitter Integration'
            );
            $settingGroupModel->save($twitter_group);
            $twitterIntergration = $settingGroupModel->read();
            $ssl_mode = Configure::read('core.ssl_mode');
            $http = (!empty($ssl_mode)) ? 'https' : 'http';
            $save_values = array(
                0 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Twitter App Consumer Key', 'name' => 'twitter_app_id', 'field' => 'twitter_app_id', 'type_id' => 'text', 'ordering' => 1),
                1 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Twitter App Consumer Secret', 'name' => 'twitter_app_secret', 'field' => 'twitter_app_secret', 'type_id' => 'text', 'ordering' => 2),
                2 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Return Url', 'name' => 'twitter_app_return_url', 'field' => 'twitter_app_return_url', 'type_id' => 'text', 'value_actual' => $http . '://' . $_SERVER['HTTP_HOST'] . $request->base . '/sociallogin/endpoint/twitter?hauth.done=Twitter','ordering' => 3),
                3 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Enable', 'name' => 'twitter_integration','field' => 'twitter_integration', 'type_id' => 'checkbox', 'value_actual' => '[{"name":"","value":"1","select":"1"}]', 'value_default' => '[{"name":"","value":"0","select":"1"}]', 'ordering' => 4),
            );
            
            $settingModel->saveAll($save_values);          
        }
                
        $setting = $settingModel->findByName('publish_social_active');
        if ($setting)
        {
        	$settingModel->id = $setting['Setting']['id'];
        	$settingModel->save(array('is_boot'=>1));
        }
    }
    public function uninstall(){
        $pluginModel = MooCore::getInstance()->getModel('Plugin');
        if(!$pluginModel->isKeyExist('SocialLogin')){
            $settingModel = MooCore::getInstance()->getModel('Setting');
            $settingModel->deleteAll(array('Setting.name' => array('twitter_app_id','twitter_app_secret','twitter_app_return_url','twitter_integration')));

            $settingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroupModel->deleteAll(array('group_type' => array('TwitterIntegration')));
        }
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('social_publisher','Settings') => array('plugin' => 'social_publisher', 'controller' => 'social_publisher_settings', 'action' => 'admin_index'),
            __d('social_publisher','Twitter') => array('plugin' => 'social_publisher', 'controller' => 'social_publisher_settings', 'action' => 'admin_twitter')
        );
    }
    public function callback_1_5(){
		$settingModel = MooCore::getInstance()->getModel('Setting');
		$setting = $settingModel->findByName('sharing_activity_link');
        if ($setting){
			$settingModel->delete($setting['Setting']['id']);
        }
	}
}