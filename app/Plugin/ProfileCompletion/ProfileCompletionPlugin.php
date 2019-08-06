<?php 
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('MooPlugin','Lib');
class ProfileCompletionPlugin implements MooPlugin{
    public function install(){
        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('profile_completion_enabled');
        if ($setting) {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot' => 1));
        }
    }
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('profile_completion', 'General') => array('plugin' => 'profile_completion', 'controller' => 'profile_completions', 'action' => 'admin_index'),
            __d('profile_completion', 'Settings') => array('plugin' => 'profile_completion', 'controller' => 'profile_completion_settings', 'action' => 'admin_index')
        );
    }
}