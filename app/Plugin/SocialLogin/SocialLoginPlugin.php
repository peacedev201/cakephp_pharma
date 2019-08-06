<?php 
App::uses('MooPlugin','Lib');
class SocialLoginPlugin implements MooPlugin{
    public function install(){
        $settingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
        $settingModel = MooCore::getInstance()->getModel('Setting');
        
        $request = Router::getRequest();     
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        $twitterIntergration = $settingGroupModel->findByName('Twitter Integration');
        if(!$twitterIntergration){
            $twitter_group = array(
                'group_type' =>  'TwitterIntegration',
                'module_id' =>  'TwitterIntegration',
                'name' => 'Twitter Integration'
            );
            $settingGroupModel->save($twitter_group);
            $twitterIntergration = $settingGroupModel->read();          
            $save_values = array(
                0 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Twitter App Consumer Key', 'name' => 'twitter_app_id', 'field' => 'twitter_app_id', 'type_id' => 'text', 'ordering' => 1),
                1 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Twitter App Consumer Secret', 'name' => 'twitter_app_secret', 'field' => 'twitter_app_secret', 'type_id' => 'text', 'ordering' => 2),
                2 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Return Url', 'name' => 'twitter_app_return_url', 'field' => 'twitter_app_return_url', 'type_id' => 'text', 'value_actual' => $http . '://' . $_SERVER['HTTP_HOST'] . $request->base . '/sociallogin/endpoint/twitter?hauth.done=Twitter','ordering' => 3),
                3 => array('group_id' => $twitterIntergration['SettingGroup']['id'], 'label' => 'Enable', 'name' => 'twitter_integration','field' => 'twitter_integration', 'type_id' => 'checkbox', 'value_actual' => '[{"name":"","value":"1","select":"1"}]', 'value_default' => '[{"name":"","value":"0","select":"1"}]', 'ordering' => 4),
            );
            
            $settingModel->saveAll($save_values);          
        }
                
        $settingGroupModel->clear();
        $linkedinIntergration = $settingGroupModel->findByName('Linkedin Integration');
        if(!$linkedinIntergration){
            $linked_group = array(
                'group_type' =>  'LinkedinIntegration',
                'module_id' =>  'LinkedinIntegration',
                'name' => 'Linkedin Integration'
            );
            $settingGroupModel->save($linked_group);
            $linkedinIntergration = $settingGroupModel->read();
            
            $save_values = array(
                0 => array('group_id' => $linkedinIntergration['SettingGroup']['id'], 'label' => 'Linkedin Client ID', 'name' => 'linkedin_app_id', 'field' => 'linkedin_app_id', 'type_id' => 'text', 'ordering' => 1),
                1 => array('group_id' => $linkedinIntergration['SettingGroup']['id'], 'label' => 'Linkedin Client Secret', 'name' => 'linkedin_app_secret', 'field' => 'linkedin_app_secret', 'type_id' => 'text', 'ordering' => 2),
                2 => array('group_id' => $linkedinIntergration['SettingGroup']['id'], 'label' => 'Return Url', 'name' => 'linkedin_app_return_url', 'field' => 'twitter_app_return_url', 'type_id' => 'text', 'value_actual' => $http . '://' . $_SERVER['HTTP_HOST'] . $request->base . '/sociallogin/endpoint/linkedin?hauth.done=Linkedin','ordering' => 3),
                4 => array('group_id' => $linkedinIntergration['SettingGroup']['id'], 'label' => 'Enable', 'name' => 'linkedin_integration', 'field' => 'linkedin_integration', 'type_id' => 'checkbox', 'value_actual' => '[{"name":"","value":"1","select":"1"}]', 'value_default' => '[{"name":"","value":"0","select":"1"}]','ordering' => 4),
            );
            
            $settingModel->saveAll($save_values);          
        }
        
        $setting = $settingModel->findByName('social_login_enable');
        if ($setting)
        {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }
    }
    public function uninstall(){
        $removed_settings = array('linkedin_app_id','linkedin_app_secret','linkedin_app_return_url','linkedin_integration');
        $removed_group = array('LinkedinIntegration');
        $pluginModel = MooCore::getInstance()->getModel('Plugin');
        if(!$pluginModel->isKeyExist('SocialPublisher')){
            $removed_settings = array_merge($removed_settings, array('twitter_app_id','twitter_app_secret','twitter_app_return_url','twitter_integration'));
            $removed_group = array_merge($removed_group, array('TwitterIntegration'));
        }
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $settingModel->deleteAll(array('Setting.name' => $removed_settings));
             
        $settingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
        $settingGroupModel->deleteAll(array('group_type' => $removed_group));
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('social_login','Settings') => array('plugin' => 'social_login', 'controller' => 'social_login_settings', 'action' => 'admin_index'),
            __d('social_login','Linkedin') => array('plugin' => 'social_login', 'controller' => 'social_login_settings', 'action' => 'admin_linkedin'),
            __d('social_login','Twitter') => array('plugin' => 'social_login', 'controller' => 'social_login_settings', 'action' => 'admin_twitter')
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}