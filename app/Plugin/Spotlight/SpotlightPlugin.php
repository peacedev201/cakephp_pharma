<?php 
App::uses('MooPlugin','Lib');
class SpotlightPlugin implements MooPlugin{
    public function install(){
        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('spotlight_enabled');
        if ($setting)
        {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }

        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',',$role['Role']['params']);
            $params = array_unique(array_merge($params,array('spotlight_use')));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }
    }
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('spotlight','Spotlight Users') => array('plugin' => 'spotlight', 'controller' => 'spotlights', 'action' => 'admin_index'),
            __d('spotlight','Spotlight Add Users') => array('plugin' => 'spotlight', 'controller' => 'spotlight_add_users', 'action' => 'admin_index'),
            __d('spotlight','Settings') => array('plugin' => 'spotlight', 'controller' => 'spotlight_settings', 'action' => 'admin_index'),
            __d('spotlight','Manage Transactions') => array('plugin' => 'spotlight', 'controller' => 'spotlight_transactions', 'action' => 'admin_index'),
            //__d('spotlight','Credits Integration') => array('plugin' => 'spotlight', 'controller' => 'spotlight_settings', 'action' => 'admin_credit_integration'),
        );
    }

}