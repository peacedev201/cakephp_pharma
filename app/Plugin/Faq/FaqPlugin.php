<?php

App::uses('MooPlugin', 'Lib');

class FaqPlugin implements MooPlugin {

    public function install() {
        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role) {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',', $role['Role']['params']);
            //admin have permission create faq
            if ($role['Role']['id'] != "1") {
                $params = array_unique(array_merge($params, array('faq_view')));
            } else {
                $params = array_unique(array_merge($params, array('faq_create', 'faq_view')));
            }
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params' => implode(',', $params)));
        }
        //Add Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/faqs');
        if (!$menu) {
            $menuModel->clear();
            $menuModel->save(array(
                'role_access' => json_encode($role_ids),
                'name' => 'Faqs',
                'original_name' => 'Faqs',
                'url' => '/faqs',
                'type' => 'plugin',
                'is_active' => 1,
                'menu_order' => 999,
                'menu_id' => 1
            ));
        }
        ////Add page
        $pageModel = MooCore::getInstance()->getModel('Page.Page');
        $blockModel = MooCore::getInstance()->getModel('CoreBlock');
        $contentModel = MooCore::getInstance()->getModel('CoreContent');
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $languages = $languageModel->find('all');

        //add translate page
        $pageModel->Behaviors->unload('Translate');
        $pages = $pageModel->find('all', array(
            'conditions' => array(
                'uri' => array('faqs.view', 'faqs.index')
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

        foreach (array_keys($languages) as $key)
        {
            $i18nModel->clear();
            $i18nModel->save(array(
                'locale' => $key,
                'model' => 'CoreMenuItem',
                'foreign_key' => $menu['CoreMenuItem']['id'],
                'field' => 'name',
                'content' => 'FAQ'
            ));
        }

        //add block to browse page
        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'faq.menu')
        ));
        $block_menu_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'faq.browse')
        ));
        $block_browse_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'faq.header')
        ));
        $block_header_id = $block['CoreBlock']['id'];
        //add block to index page
        $browse_page = $pageModel->findByUri('faqs.index');
        if ($browse_page) {
            $page_id = $browse_page['Page']['id'];

            //insert north
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'core_content_count' => '1',
                'name' => 'north'
            ));
            $north_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $north_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }

            //insert header to north
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'faq.header',
                'parent_id' => $north_id,
                'params' => '{"title":"Faq Header","plugin":"Faq","role_access":"all"}',
                'plugin' => 'Faq',
                'order' => 1,
                'core_block_id' => $block_header_id,
                'core_block_title' => 'Faq Header',
                'role_access' => 'all'
            ));
            //update count north
            $contentModel->save(array(
                'id' => $north_id,
                'core_content_count' => '1'
            ));
            $header_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $header_id,
                    'field' => 'core_block_title',
                    'content' => 'Faq Header'
                ));
            }
            //insert west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'core_content_count' => '1',
                'name' => 'west'
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
                'name' => 'faq.menu',
                'parent_id' => $west_id,
                'params' => '{"title":"Faq menu","plugin":"Faq","role_access":"all"}',
                'plugin' => 'Faq',
                'order' => 1,
                'core_block_id' => $block_menu_id,
                'core_block_title' => 'Faq Menu',
                'role_access' => 'all'
            ));
            //update count west
            $contentModel->save(array(
                'id' => $west_id,
                'core_content_count' => '1'
            ));

            $menu_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $menu_id,
                    'field' => 'core_block_title',
                    'content' => 'Faq Menu'
                ));
            }

            //insert center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'core_content_count' => '1',
                'name' => 'center'
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

            //insert browse to center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'faq.browse',
                'parent_id' => $center_id,
                'params' => '{"title":"Browse Faq","plugin":"Faq","role_access":"all"}',
                'plugin' => 'Faq',
                'order' => 1,
                'core_block_id' => $block_browse_id,
                'core_block_title' => 'Faq Browse',
                'role_access' => 'all'
            ));
            //update count center
            $contentModel->save(array(
                'id' => $center_id,
                'core_content_count' => '1'
            ));
            $browse_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $browse_id,
                    'field' => 'core_block_title',
                    'content' => 'Faq Browse'
                ));
            }
        }
        //done add widget to index page
        //Add block to detail page
        $detail_page = $pageModel->findByUri('faqs.view');
        if ($detail_page) {
            $page_id = $detail_page['Page']['id'];

            //insert north
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'core_content_count' => '1',
                'name' => 'north'
            ));
            $north_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $north_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }

            //insert header to north
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'faq.header',
                'parent_id' => $north_id,
                'params' => '{"title":"Faq Header","plugin":"Faq","role_access":"all"}',
                'plugin' => 'Faq',
                'order' => 1,
                'core_block_id' => $block_header_id,
                'core_block_title' => 'Faq Header',
                'role_access' => 'all'
            ));
            //update count north
            $contentModel->save(array(
                'id' => $north_id,
                'core_content_count' => '1'
            ));
            $header_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $header_id,
                    'field' => 'core_block_title',
                    'content' => 'Faq Header'
                ));
            }
            //insert west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'core_content_count' => '1',
                'name' => 'west'
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
                'name' => 'faq.menu',
                'parent_id' => $west_id,
                'params' => '{"title":"Menu Faq","plugin":"Faq","role_access":"all"}',
                'plugin' => 'Faq',
                'order' => 1,
                'core_block_id' => $block_menu_id,
                'core_block_title' => 'Faq Menu',
                'role_access' => 'all'
            ));
            //update count west
            $contentModel->save(array(
                'id' => $west_id,
                'core_content_count' => '1'
            ));

            $menu_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $menu_id,
                    'field' => 'core_block_title',
                    'content' => 'Faq Menu'
                ));
            }

            //insert center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'core_content_count' => '1',
                'name' => 'center'
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
            //insert invisiblecontent to center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'invisiblecontent',
                'parent_id' => $center_id,
                'order' => 1,
                'params' => '{"title":"Faq\'s Content","maincontent":"1"}',
                'plugin' => 'Faq',
                'core_block_title' => 'Faq\'s Content'
            ));

            $invisiblecontent_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $invisiblecontent_id,
                    'field' => 'core_block_title',
                    'content' => 'Faq\'s Content'
                ));
            }

            //insert similar to center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'faq.similar',
                'parent_id' => $center_id,
                'params' => '{"title":"Similar Faq","plugin":"Faq","role_access":"all"}',
                'plugin' => 'Faq',
                'order' => 1,
                'core_block_id' => $block_browse_id,
                'core_block_title' => 'Faq Similar',
                'role_access' => 'all'
            ));
            //update count center
            $contentModel->save(array(
                'id' => $center_id,
                'core_content_count' => '1'
            ));
            $browse_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $browse_id,
                    'field' => 'core_block_title',
                    'content' => 'Faq Similar'
                ));
            }
        }
        //done insert block to detail page
        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('faq_enabled');
        if ($setting) {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot' => 1));
        }
        
        $setting = $settingModel->findByName('faq_consider_force');
        if ($setting)
        {
        	$settingModel->id = $setting['Setting']['id'];
        	$settingModel->save(array('is_boot'=>1));
        }
    }

    public function uninstall() {
        //Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/faqs');
        if ($menu) {
            $menuModel->delete($menu['CoreMenuItem']['id']);
        }
        //Menu core content
        $menuCoreModel = MooCore::getInstance()->getModel('Menu.CoreContent');
        $menuCoreModel = $menuModel->findByUrl('/faqs');
        if ($menuCoreModel) {
            $menuCoreModel->delete($menu['CoreContent']['id']);
        }
        
        //Delete S3
        $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
        $types = array('fa_category_icon');
        foreach ($types as $type)
        	$objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            __d('faq', 'FAQ Manager') => array('plugin' => 'faq', 'controller' => 'faqs', 'action' => 'admin_index'),
            __d('faq', 'FAQ Settings') => array('plugin' => 'faq', 'controller' => 'faq_settings', 'action' => 'admin_index'),
            __d('faq', 'FAQ Categories') => array('plugin' => 'faq', 'controller' => 'faq_categories', 'action' => 'admin_index'),
            __d('faq', 'Reports') => array('plugin' => 'faq', 'controller' => 'faq_helpfulreports', 'action' => 'admin_index'),
        );
    }

    /*
      Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
      public function callback_1_0(){}
     */
}