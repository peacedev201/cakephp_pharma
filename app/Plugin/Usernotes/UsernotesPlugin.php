
<?php

App::uses('MooPlugin', 'Lib');

class UsernotesPlugin implements MooPlugin {

    public function install() {
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role) {
            $role_ids[] = $role['Role']['id'];
        }


        // add page
        $pageModel = MooCore::getInstance()->getModel('Page.Page');
        $contentModel = MooCore::getInstance()->getModel('CoreContent');
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $languages = $languageModel->find('all');
        
         //Add Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/usernotess');
        if (!$menu) {
            $role_except_guest = array_diff($role_ids, array(ROLE_GUEST));
            $menuModel->clear();
            $menuModel->save(array(
                'role_access' => json_encode($role_except_guest),
                'name' => 'My notes',
                'original_name' => 'My notes',
                'url' => '/usernotess',
                'type' => 'plugin',
                'is_active' => 1,
                'menu_order' => 999,
                'menu_id' => 1,
                'plugin' => 'Usernotes'
            ));
            
                $menu = $menuModel->read();
                foreach ($languages as $language)
    		{
    			if ($language['Language']['key'] == Configure::read('Config.language'))
    				continue;
    			
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $language['Language']['key'],
    					'model' => 'CoreMenuItem',
    					'foreign_key' => $menu['CoreMenuItem']['id'],
    					'field' => 'name',
    					'content' => 'My notes'
    			));
    			
    		}
        }
        
        //add translate page
        $pageModel->Behaviors->unload('Translate');
        $pages = $pageModel->find('all', array(
            'conditions' => array(
                'uri' => array('usernotess.index')
            )
        ));
        foreach ($pages as $page) {
            foreach ($languages as $language) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $language['Language']['key'],
                    'model' => 'Page',
                    'foreign_key' => $page['Page']['id'],
                    'field' => 'title',
                    'content' => $page['Page']['title']
                ));

                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $language['Language']['key'],
                    'model' => 'Page',
                    'foreign_key' => $page['Page']['id'],
                    'field' => 'content',
                    'content' => $page['Page']['content']
                ));
            }
        }
        $tmp = array();
        foreach ($languages as $language) {
            if ($language['Language']['key'] == Configure::read('Config.language'))
                continue;

            $tmp[$language['Language']['key']] = $language;
        }
        $languages = $tmp;
        $note_page = $pageModel->findByUri('usernotess.index');

        if ($note_page) {
            $page_id = $note_page['Page']['id'];
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'west',
            ));
            $west_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $west_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            //insert menu to west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'invisiblecontent',
                'parent_id' => $west_id,
                'params' => '{"title":"Menu friend & Search","maincontent":"1"}',
                'plugin' => 'Usernotes',
                'order' => 1,
                'core_block_id' => 1,
                'core_block_title' => 'Menu friend & Search'
            ));
            //insert center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'center',
            ));
            $center_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $center_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            //insert Page Content to center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'invisiblecontent',
                'parent_id' => $center_id,
                'order' => 1,
                'params' => '{"title":"Page Content","maincontent":"1"}',
                'core_block_title' => 'Page Content'
            ));
            $content_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $content_id,
                    'field' => 'core_block_title',
                    'content' => 'Page Content'
                ));
            }

            // insert east
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'east',
            ));
            $east_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $east_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
        }
        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('usernotes_enabled');
        if ($setting) {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot' => 1));
        }
        
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',',$role['Role']['params']);
            $params = array_unique(array_merge($params,array('usernotes_can_write_note')));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }
        
    }

    public function uninstall() {
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/usernotess');
        if ($menu) {
            $menuModel->delete($menu['CoreMenuItem']['id']);
        }
          //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        foreach ($roles as $role)
        {
            $params = explode(',',$role['Role']['params']);
            $params = array_diff($params,array('usernotes_can_write_note'));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            /*__d('usernotes', 'Notes manager') => array('plugin' => 'usernotes', 'controller' => 'usernotess', 'action' => 'admin_index'),*/
            __d('usernotes', 'Settings') => array('plugin' => 'usernotes', 'controller' => 'usernotes_settings', 'action' => 'admin_index'),
        );
    }

    /*
      Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
      public function callback_1_0(){}
     */
}
