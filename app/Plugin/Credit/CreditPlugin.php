<?php
App::uses('MooPlugin','Lib');
class CreditPlugin implements MooPlugin{
    public function install(){
        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',',$role['Role']['params']);
            $params = array_unique(array_merge($params,array('credit_use')));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }

        //Add page
        $pageModel = MooCore::getInstance()->getModel('Page.Page');
        $contentModel = MooCore::getInstance()->getModel('CoreContent');
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $languages = $languageModel->find('all');
        //add translate page
        $pageModel->Behaviors->unload('Translate');
        $pages = $pageModel->find('all',array(
            'conditions' => array(
                'uri' => array('credits.index')
            )
        ));
        $tmp = array();
        foreach ($pages as $page)
        {
            foreach ($languages as $language)
            {
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
        foreach ($languages as $language)
        {
            if ($language['Language']['key'] == Configure::read('Config.language'))
                continue;

            $tmp[$language['Language']['key']] = $language;
        }
        $languages = $tmp;
        //add block to browse page
        $blockModel = MooCore::getInstance()->getModel('CoreBlock');
        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'credits.buy')
        ));
        $block_buy_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'credits.send')
        ));
        $block_send_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'credits.options')
        ));
        $block_options_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'credits.rank')
        ));
        $block_rank_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'credits.badge')
        ));
        $block_badge_id = $block['CoreBlock']['id'];

        $credit_page = $pageModel->findByUri('credits.index');
        if ($credit_page) {
            $page_id = $credit_page['Page']['id'];
            //insert west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'west',
            ));
            $west_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
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
                'params' => '{"title":"Menu Credits","maincontent":"1"}',
                'plugin' => 'Credit',
                'order' => 1,
                'core_block_id' => 1,
                'core_block_title' => 'Credit Menu'
            ));

            //insert options to west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'credits.options',
                'parent_id' => $west_id,
                'params' => '{"title":"Earn Credits","title_enable":"1","plugin":"Credit"}',
                'plugin' => 'Credit',
                'order' => 2,
                'core_block_id' => $block_options_id,
                'core_block_title' => 'Earn Credits'
            ));
            $options_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $options_id,
                    'field' => 'core_block_title',
                    'content' => 'Earn Credits'
                ));
            }

            //insert center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'center',
            ));
            $center_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
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
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $content_id,
                    'field' => 'core_block_title',
                    'content' => 'Page Content'
                ));
            }

            //insert east
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'east',
            ));
            $east_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $east_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            //insert badge to est
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'credits.badge',
                'parent_id' => $east_id,
                'params' => '{"title":"Credit Rank","title_enable":"1","plugin":"Credit"}',
                'plugin' => 'Credit',
                'order' => 1,
                'core_block_id' => $block_badge_id,
                'core_block_title' => 'Credit Rank'
            ));
            $badge_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $badge_id,
                    'field' => 'core_block_title',
                    'content' => 'Credit Rank'
                ));
            }
            //insert rank to est
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'credits.rank',
                'parent_id' => $east_id,
                'order' => 2,
                'params' => '{"title":"Credit Statistics","title_enable":"1","plugin":"Credit"}',
                'plugin' => 'Credit',
                'core_block_id' => $block_rank_id,
                'core_block_title' => 'Credit Statistics'
            ));
            $rank_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $rank_id,
                    'field' => 'core_block_title',
                    'content' => 'Credit Statistics'
                ));
            }
            //insert buy to est
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'credits.buy',
                'order' => 3,
                'parent_id' => $east_id,
                'params' => '{"title":"Buy Credits","title_enable":"1","plugin":"Credit"}',
                'plugin' => 'Credit',
                'core_block_id' => $block_buy_id,
                'core_block_title' => 'Buy Credits'
            ));
            $buy_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $buy_id,
                    'field' => 'core_block_title',
                    'content' => 'Buy Credits'
                ));
            }
            //insert send to est
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'credits.send',
                'parent_id' => $east_id,
                'params' => '{"title":"Send Credits","title_enable":"1","plugin":"Credit"}',
                'plugin' => 'Credit',
                'order' => 4,
                'core_block_id' => $block_send_id,
                'core_block_title' => 'Send Credits'
            ));
            $send_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $send_id,
                    'field' => 'core_block_title',
                    'content' => 'Send Credits'
                ));
            }


        }

        //menu
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mMenu->findByUrl('/credits');
        if ($menu)
        {
            $mMenu->id = $menu['CoreMenuItem']['id'];
            $mMenu->save(array(
                'name' => 'Credits',
                'url' => '/credits'
            ));
            foreach ($languages as $language) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $language['Language']['key'],
                    'model' => 'CoreMenuItem',
                    'foreign_key' => $menu['CoreMenuItem']['id'],
                    'field' => 'name',
                    'content' => 'Credits'
                ));
            }
        }

        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('credit_enabled');
        if ($setting)
        {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }
        $setting = $settingModel->findByName('credit_consider_force');
        if ($setting)
        {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }
    }
    public function uninstall(){

        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        foreach ($roles as $role)
        {
            $params = explode(',',$role['Role']['params']);
            $params = array_diff($params,array('credit_use'));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }

        //Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/credits');
        if ($menu)
        {
            $menuModel->delete($menu['CoreMenuItem']['id']);
        }

        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $pageIds = $mPage->find("list", array(
            "conditions" => array("Page.alias" => array('credits')),
            "fields" => array("Page.id")
        ));
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $coreContentIds = $mCoreContent->find("list", array(
            "conditions" => array("CoreContent.plugin" => "Credit"),
            "fields" => array("CoreContent.id")
        ));

        //delete language
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18nModel->deleteAll(array(
            'I18nModel.content' => array(
                'Credits', 'Send Credits', 'Buy Credits',
                'Credit Statistics', 'Credit Rank', 'Earn Credits', 'Credit Menu'
            )
        ));
        $i18nModel->deleteAll(array(
            'I18nModel.model' => "CoreContent",
            "I18nModel.foreign_key" => $coreContentIds
        ));
        $i18nModel->deleteAll(array(
            'I18nModel.model' => "Page",
            "I18nModel.foreign_key" => $pageIds
        ));

        //Delete S3
        $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
        $types = array('credit_ranks');
        foreach ($types as $type) {
            $objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
        }
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('credit', 'List members') => array('plugin' => 'credit', 'controller' => 'credits', 'action' => 'admin_index'),
            __d('credit', 'Settings') => array('plugin' => 'credit', 'controller' => 'credit_settings', 'action' => 'admin_index'),
            __d('credit', 'Credit settings') => array('plugin' => 'credit', 'controller' => 'credit_settings', 'action' => 'admin_settings'),
            __d('credit', 'Credit Packages') => array('plugin' => 'credit', 'controller' => 'sell', 'action' => 'admin_index'),
            __d('credit', 'Manage ranks') => array('plugin' => 'credit', 'controller' => 'ranks', 'action' => 'admin_index'),
            __d('credit', 'Manage Transactions') => array('plugin' => 'credit', 'controller' => 'transactions', 'action' => 'admin_index'),
            __d('credit', 'Manage FAQ') => array('plugin' => 'credit', 'controller' => 'credit_faqs', 'action' => 'admin_index'),
            __d('credit', 'Give mass credits') => array('plugin' => 'credit', 'controller' => 'gives', 'action' => 'admin_index')
        );
    }
    //__d('credit', 'Withdraw request') => array('plugin' => 'credit', 'controller' => 'withdraw', 'action' => 'admin_index')
    
    // Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_8(){
        $credit_action_type = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
        $action_type = $credit_action_type->getActionTypeFormModule('friend_inviter');
        if (!$action_type) {
            $credit_action_type->create();
            $saved_data = array(
                'action_type' => 'friend_inviter',
                'action_type_name' => 'Friend Inviter',
                'action_module' => 'Friend Inviter',
                'credit' => 10,
                'max_credit' => 1000,
                'rollover_period' => 0,
                'type' => 'none',
                'plugin' => 'Credit',
                'show' => 1
            );
            $credit_action_type->save($saved_data);                       
        }
    }
    
}
