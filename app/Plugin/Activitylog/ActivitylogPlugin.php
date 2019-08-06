<?php 
App::uses('MooPlugin','Lib');
class ActivitylogPlugin implements MooPlugin{
    public function install(){
        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $role_ids[] = $role['Role']['id'];
        }
        //Add Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/activity_log');
        if (!$menu)
        {
            $menuModel->clear();
            $menuModel->save(array(
                'role_access'=>json_encode($role_ids),
                'name' => 'Activity log',
                'original_name' => 'Activity log',
                'url' => '/activity_log',
                'type' => 'plugin',
                'is_active' => 1,
                'menu_order'=> 999,
                'menu_id' => 1
            ));
            /*$menuModel->id = $menu['CoreMenuItem']['id'];
            $menuModel->save(array('role_access'=>json_encode($role_ids)));*/

            $menu = $menuModel->read();
            $i18nModel      = MooCore::getInstance()->getModel('I18nModel');
            $languageModel  = MooCore::getInstance()->getModel('Language');
            $languages      = $languageModel->find('all');

            $tmp = array();
            foreach ($languages as $language)
            {
                if ($language['Language']['key'] == Configure::read('Config.language'))
                    continue;

                $tmp[$language['Language']['key']] = $language;
            }
            $languages = $tmp;

            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreMenuItem',
                    'foreign_key' => $menu['CoreMenuItem']['id'],
                    'field' => 'name',
                    'content' => 'Activity log'
                ));
            }
        }

        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('activitylog_enabled');
        if ($setting)
        {
        	$settingModel->id = $setting['Setting']['id'];
        	$settingModel->save(array('is_boot'=>1));
        }
    }
    public function uninstall(){
        //Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/activity_log');
        if ($menu)
        {
            $menuModel->delete($menu['CoreMenuItem']['id']);
        }
    }
    
	public function settingGuide(){
		
	}
    public function menu()
    {
        return array(
            __d('activitylog','Settings') => array('plugin' => 'activitylog', 'controller' => 'activitylog_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}