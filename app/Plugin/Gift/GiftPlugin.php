<?php
App::uses('MooPlugin','Lib');
class GiftPlugin implements MooPlugin{
    public function install()
    {
        $i18nModel      = MooCore::getInstance()->getModel('I18nModel');
        $languageModel  = MooCore::getInstance()->getModel('Language');
        $languages      = $languageModel->find('all');

        //menu
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mMenu->findByUrl('/gifts');
        if ($menu)
        {
            $mMenu->id = $menu['CoreMenuItem']['id'];
            $mMenu->save(array(
                'name' => 'Gifts',
                'url' => '/gifts'
            ));

            $menu = $mMenu->read();

            $tmp = array();
            foreach ($languages as $language)
            {
                if ($language['Language']['key'] == Configure::read('Config.language'))
                    continue;

                $tmp[$language['Language']['key']] = $language;
            }
            $langs = $tmp;

            foreach (array_keys($langs) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreMenuItem',
                    'foreign_key' => $menu['CoreMenuItem']['id'],
                    'field' => 'name',
                    'content' => 'Gifts'
                ));
            }
        }

        //Setting
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $setting = $mSetting->findByName('gift_enabled');
        if ($setting)
        {
            $mSetting->id = $setting['Setting']['id'];
            $mSetting->save(array('is_boot'=>1));
        }

        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $params = explode(',',$role['Role']['params']);
            if($role['Role']['id'] == 1)
            {
                $permission = array(
                    'gift_can_send_gift', 'gift_allow_photo_gift', 'gift_allow_audio_gift', 'gift_allow_video_gift'
                );
            }
            else
            {
                $permission = array(
                    'gift_can_send_gift', 'gift_allow_photo_gift', 'gift_allow_audio_gift', 'gift_allow_video_gift'
                );
            }
            $params = array_unique(array_merge($params, $permission));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }

        //widget
        $this->installBlock();

        //add default category
        $db = ConnectionManager::getDataSource("default");
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $table_prefix = $mSetting->tablePrefix;

        $cat_data[] = array("name" => "Default", "id" => "1", "lft" => "5", "rght" => "6", "item_count" => "22", "description" => "");
        $cat_data[] = array("name" => "Wedding", "id" => "2", "lft" => "1", "rght" => "2", "item_count" => "7", "description" => "");
        $cat_data[] = array("name" => "Birthday", "id" => "3", "lft" => "3", "rght" => "4", "item_count" => "6", "description" => "");

        foreach($cat_data as $item)
        {
            $db->query("insert into `".$table_prefix."gift_categories` (`id`, `parent_id`, `lft`, `rght`, `name`, `description`, `item_count`, `created`, `updated`, `enable`) values('".$item["id"]."','0','".$item["lft"]."','".$item["rght"]."','".$item["name"]."','','".$item["item_count"]."','2016-08-26 02:58:39','2016-08-26 02:58:39','1');");
            $cat_id = $db->lastInsertId();
            if($cat_id > 0 && $languages != null)
            {
                foreach ($languages as $language)
                {
                    $i18nModel->create();
                    $i18nModel->save(array(
                        'locale' => $language['Language']['key'],
                        'model' => 'GiftCategory',
                        'foreign_key' => $cat_id,
                        'field' => 'name',
                        'content' => $item["name"]
                    ));
                    $i18nModel->create();
                    $i18nModel->save(array(
                        'locale' => $language['Language']['key'],
                        'model' => 'GiftCategory',
                        'foreign_key' => $cat_id,
                        'field' => 'description',
                        'content' => $item["description"]
                    ));
                }
            }
        }

        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('gift_consider_force');
        if ($setting)
        {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }
    }

    //////////////////////////////////////////block//////////////////////////////////////////
    private function installBlock()
    {
        //Add page
        $pageData[] = array(
            'title' => 'Gift Page',
            'alias' => 'gift_index',
            'url' => '/gifts/',
            'uri' => 'gift.index',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageData[] = array(
            'title' => 'Gift Create',
            'alias' => 'gift_create',
            'url' => '/gifts/',
            'uri' => 'gift.create',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageData[] = array(
            'title' => 'Gift Detail Page',
            'alias' => 'gift_view',
            'url' => '/gifts/view/$id',
            'uri' => 'gift.view',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageOutput = $this->addPage($pageData);

        //add core content east west
        $contentParentData[] = array(
            'page_id' => $pageOutput['gift_index'],
            'type' => 'container',
            'name' => 'west',
            'plugin' => 'Gift'
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['gift_create'],
            'type' => 'container',
            'name' => 'west',
            'plugin' => 'Gift'
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['gift_view'],
            'type' => 'container',
            'name' => 'west',
            'plugin' => 'Gift'
        );
        $contentParentOutput = $this->addCoreContentParent($contentParentData);

        //add core block
        $blockData[] = $this->parseBlockData('Gift Navi', 'gift.nav_gift');
        $blockData[] = $this->parseBlockData('Gift Detail Statistic', 'gift.detail_statistic');
        $blockData[] = $this->parseBlockData('Gift Popular', 'gift.popular', null, 1);
        $blockOutput = $this->addCoreBlock($blockData);

        //add core content
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "gift_index", "west", "Gift Navi", "gift.nav_gift",false, 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "gift_index", "west", "Gift Popular", "gift.popular", true, 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "gift_create", "west", "Gift Navi", "gift.nav_gift",false,1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "gift_create", "west", "Gift Popular", "gift.popular", true,2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "gift_view", "west", "Gift Navi", "gift.nav_gift",false,1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "gift_view", "west", "Gift Detail Statistic", "gift.detail_statistic",false,2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "gift_view", "west", "Gift Popular", "gift.popular", true,3);
        $this->addCoreContent($contentData);
    }

    private function parseBlockData($name, $path_view, $params = null, $is_active = 0)
    {
        $params = $params != null ? $params : '{"0":{"label":"Title","input":"text","value":"'.$name.'","name":"title"},"1":{"label":"plugin","input":"hidden","value":"Gift","name":"plugin"}}';
        return array(
            'name' => $name,
            'path_view' => $path_view,
            'params' => $params,
            'is_active' => $is_active,
            'group' => 'Gift',
            'plugin' => 'Gift',
        );;
    }

    private function parseContentData($contentParentOutput, $pageOutput, $blockOutput, $page_alias, $east_west, $block_name, $name, $visible = false, $order = 1)
    {
        return array(
            'page_id' => $pageOutput[$page_alias],
            'parent_id' => $contentParentOutput[$pageOutput[$page_alias].$east_west],
            'core_block_id' => $blockOutput[$block_name],
            'type' => 'widget',
            'name' => $name,
            'order' => $order,
            'params' => !$visible ? '{"title":"'.$block_name.'","maincontent":"1"}' : '{"title":"'.$block_name.'","plugin":"Gift","role_access":"all"}',
            'core_block_title' => $block_name,
            'plugin' => 'Gift'
        );
    }

    private function addPage($data)
    {
        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $langs = array_keys($languageModel->getLanguages());

        $output = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $mPage->create();
                if($mPage->save($item))
                {
                    $id = $mPage->id;
                    foreach ($langs as $lKey)
                    {
                        $mPage->locale = $lKey;
                        $mPage->id = $id;
                        $mPage->saveField('title', $item['title']);
                        $mPage->saveField('content', "");
                    }
                    $output[$item['alias']] = $id;
                }
            }
        }
        return $output;
    }

    private function addCoreBlock($data)
    {
        $mCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $langs = array_keys($languageModel->getLanguages());

        $output = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $mCoreBlock->create();
                if($mCoreBlock->save($item))
                {
                    $id = $mCoreBlock->id;
                    foreach ($langs as $lKey)
                    {
                        $mCoreBlock->locale = $lKey;
                        $mCoreBlock->id = $id;
                        $mCoreBlock->saveField('name', $item['name']);
                    }
                    $output[$item['name']] = $id;
                }
            }
        }
        return $output;
    }

    private function addCoreContentParent($data)
    {
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $output = array();
        if($data != null)
        {
            $langs = array_keys($languageModel->getLanguages());
            foreach($data as $item)
            {
                $mCoreContent->create();
                if($mCoreContent->save($item))
                {
                    $id = $mCoreContent->id;
                    foreach ($langs as $lKey)
                    {
                        $mCoreContent->locale = $lKey;
                        $mCoreContent->id = $id;
                        $mCoreContent->saveField('core_block_title', $item['name']);
                    }
                    $output[$item['page_id'].$item['name']] = $id;
                }
            }
        }
        return $output;
    }

    private function addCoreContent($data)
    {
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $languageModel = MooCore::getInstance()->getModel('Language');
        if($data != null)
        {
            $langs = array_keys($languageModel->getLanguages());
            foreach($data as $item)
            {
                $mCoreContent->create();
                if($mCoreContent->save($item))
                {
                    $id = $mCoreContent->id;
                    foreach ($langs as $lKey)
                    {
                        $mCoreContent->locale = $lKey;
                        $mCoreContent->id = $id;
                        $mCoreContent->saveField('core_block_title', $item['core_block_title']);
                    }
                }
            }
        }
    }

    public function uninstall()
    {
        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $mCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $roleModel = MooCore::getInstance()->getModel('Role');

        //Menu
        $mMenu->deleteAll(array(
            'CoreMenuItem.name' => 'Gifts'
        ));

        //delete setting
        $settingGroup = $mSettingGroup->findByModuleId('Gift');
        if($settingGroup != null)
        {
            $mSetting->deleteAll(array(
                'Setting.group_id' => $settingGroup['SettingGroup']['id']
            ));
            $mSettingGroup->delete($settingGroup['SettingGroup']['id']);
        }

        //Permission
        $roles = $roleModel->find('all');
        foreach ($roles as $role)
        {
            $params = explode(',',$role['Role']['params']);
            $params = array_diff($params,
                array(
                    'gift_can_send_gift', 'gift_allow_photo_gift', 'gift_allow_audio_gift', 'gift_allow_video_gift'
                ));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }

        //page, widget
        $coreContentIds = $mCoreContent->find("list", array(
            "conditions" => array("CoreContent.plugin" => "Gift"),
            "fields" => array("CoreContent.id")
        ));
        $pageIds = $mPage->find("list", array(
            "conditions" => array("Page.alias" => array('gift_index', 'gift_view', 'gift_create')),
            "fields" => array("Page.id")
        ));
        $mCoreContent->deleteAll(array(
            'CoreContent.plugin' => 'Gift'
        ));
        $mCoreBlock->deleteAll(array(
            'CoreBlock.plugin' => 'Gift'
        ));
        $mPage->deleteAll(array(
            "Page.alias" => array('gift_index', 'gift_view', 'gift_create')
        ));


        //delete language
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18nModel->deleteAll(array(
            'I18nModel.model' => array('Gift', 'GiftCategory')
        ));
        $i18nModel->deleteAll(array(
            'I18nModel.content' => array(
                'Gift Page', 'Gift Detail Page', 'Gift Create',
                'Gift Detail Statistic', 'Gift Navi', 'Gift Popular'
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
        $i18nModel->deleteAll(array(
            'I18nModel.model' => "Core\Item",
            "I18nModel.content" => "Gifts"
        ));

        //delete credit
        if(CakePlugin::loaded("Credit"))
        {
            $mCreditLog = MooCore::getInstance()->getModel('Credit.CreditLog');
            $mCreditActiontype = MooCore::getInstance()->getModel('Credit.CreditActiontype');
            $mCreditLog->deleteAll(array(
                'CreditLog.object_type' => array('gift', 'gift_gift_sents')
            ));
            $mCreditActiontype->deleteAll(array(
                'CreditActiontype.plugin' => "gift"
            ));
        }

        //Delete S3
        $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
        $types = array('gifts','gift_files');
        foreach ($types as $type)
            $objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
    }

    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('gift', 'Categories') => array('plugin' => 'gift', 'controller' => 'gift_categories', 'action' => 'admin_index'),
            __d('gift', 'Manage Gifts') => array('plugin' => 'gift', 'controller' => 'gift', 'action' => 'admin_index'),
            __d('gift', 'Settings') => array('plugin' => 'gift', 'controller' => 'gift_settings', 'action' => 'admin_index'),
            __d('gift', 'Credits Integration') => array('plugin' => 'gift', 'controller' => 'gift_settings', 'action' => 'admin_credit_integration'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}