<?php 
App::uses('MooPlugin','Lib');
class AvatarPlugin implements MooPlugin{
    public function install(){
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('avatars_enabled');
        if ($setting)
        {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }
    }
    public function uninstall(){
        //delete setting
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $settingGroup = $mSettingGroup->findByModuleId('Avatar');
        if($settingGroup != null)
        {
            $mSetting->deleteAll(array(
                'Setting.group_id' => $settingGroup['SettingGroup']['id']
            ));
            $mSettingGroup->delete($settingGroup['SettingGroup']['id']);
        }

    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('avatar','Settings') => array('plugin' => 'avatar', 'controller' => 'avatar_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}