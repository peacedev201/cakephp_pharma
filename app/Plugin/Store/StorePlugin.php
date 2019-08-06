<?php

App::uses('MooPlugin', 'Lib');

class StorePlugin implements MooPlugin
{

    public function install()
    {
        $path_products = 'uploads' . DS . 'products';

        if (!is_dir($path_products))
        {
            $mask = umask(0);
            mkdir($path_products, 0777);
            umask($mask);
        }

        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',', $role['Role']['params']);
            $params = array_unique(array_merge($params, array('store_create_store', 'store_view_product_detail', 'store_buy_product')
            ));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params' => implode(',', $params)));
        }

        //Setting
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $setting = $mSetting->findByName('store_enabled');
        if ($setting)
        {
            $mSetting->id = $setting['Setting']['id'];
            $mSetting->save(array('is_boot' => 1));
        }
        $setting = $mSetting->findByName('store_by_pass_force_login');
        if ($setting != null)
        {
            $mSetting->id = $setting['Setting']['id'];
            $mSetting->save(array('is_boot' => 1));
        }

        //widget
        $this->installBlock();

        $this->callback_1_2();
        $this->callback_1_3(true);
        $this->callback_1_4();
        $this->callback_1_5(true);
        $this->callback_1_6(true);
        $this->callback_1_9(true);
        $this->callback_2_0(true);
        $this->callback_2_1(true);
        $this->callback_2_4(true);
        $this->callback_2_5(true);
        $this->callback_2_6(true);
    }

    //////////////////////////////////////////block//////////////////////////////////////////
    private function installBlock()
    {
        //Add page
        $pageData[] = array(
            'title' => 'Store Page',
            'alias' => 'stores_index',
            'url' => '/stores/',
            'uri' => 'stores.index',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageData[] = array(
            'title' => 'Store Product Detail Page',
            'alias' => 'stores_product',
            'url' => '/stores/product/$id',
            'uri' => 'stores.product',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageOutput = $this->addPage($pageData);

        //add core content east west
        $contentParentData[] = array(
            'page_id' => $pageOutput['stores_index'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['stores_product'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentOutput = $this->addCoreContentParent($contentParentData);

        //add core block
        $blockData[] = $this->parseBlockData('Store Products Most Viewed', 'products.most_viewed_products', null, 1);
        $blockData[] = $this->parseBlockData('Store Products Latest', 'products.latest_products', null, 1);
        $blockData[] = $this->parseBlockData('Store Products Sale', 'products.sale_products', null, 1);
        $blockOutput = $this->addCoreBlock($blockData);

        //add core content
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "stores_index", "west", "Store Products Most Viewed", "products.most_viewed_products", true);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "stores_index", "west", "Store Products Sale", "products.sale_products", true);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "stores_product", "west", "Store Products Latest", "products.latest_products", true);
        $this->addCoreContent($contentData);
    }

    private function parseBlockData($name, $path_view, $params = null, $is_active = 0)
    {
        $params = $params != null ? $params : '{"0":{"label":"Title","input":"text","value":"' . $name . '","name":"title"},"1":{"label":"plugin","input":"hidden","value":"Store","name":"plugin"}}';
        return array(
            'name' => $name,
            'path_view' => $path_view,
            'params' => $params,
            'is_active' => $is_active,
            'group' => 'Store',
            'plugin' => 'Store',
        );
        ;
    }

    private function parseContentData($contentParentOutput, $pageOutput, $blockOutput, $page_alias, $east_west, $block_name, $name, $visible = false, $page_id = null, $parent_id = null, $core_block_id = null, $ordering = 1)
    {
        return array(
            'page_id' => is_numeric($page_id) ? $page_id : $pageOutput[$page_alias],
            'parent_id' => is_numeric($parent_id) ? $parent_id : $contentParentOutput[$pageOutput[$page_alias] . $east_west],
            'core_block_id' => is_numeric($core_block_id) ? $core_block_id : $blockOutput[$block_name],
            'type' => 'widget',
            'name' => $name,
            'order' => $ordering,
            'params' => !$visible ? '{"title":"' . $block_name . '","maincontent":"1"}' : '{"title":"' . $block_name . '","plugin":"Store","role_access":"all"}',
            'core_block_title' => $block_name,
            'plugin' => 'Store'
        );
    }

    private function addPage($data)
    {
        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $langs = array_keys($languageModel->getLanguages());

        $output = array();
        if ($data != null)
        {
            foreach ($data as $item)
            {
                $mPage->create();
                if ($mPage->save($item))
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
        if ($data != null)
        {
            foreach ($data as $item)
            {
                $mCoreBlock->create();
                if ($mCoreBlock->save($item))
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
        if ($data != null)
        {
            $langs = array_keys($languageModel->getLanguages());
            foreach ($data as $item)
            {
                $mCoreContent->create();
                if ($mCoreContent->save($item))
                {
                    $id = $mCoreContent->id;
                    foreach ($langs as $lKey)
                    {
                        $mCoreContent->locale = $lKey;
                        $mCoreContent->id = $id;
                        $mCoreContent->saveField('core_block_title', $item['name']);
                    }
                    $output[$item['page_id'] . $item['name']] = $id;
                }
            }
        }
        return $output;
    }

    private function addCoreContent($data)
    {
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $languageModel = MooCore::getInstance()->getModel('Language');
        if ($data != null)
        {
            $langs = array_keys($languageModel->getLanguages());
            foreach ($data as $item)
            {
                $mCoreContent->create();
                if ($mCoreContent->save($item))
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

    //////////////////////////////////////////mail template//////////////////////////////////////////
    private function createMailTemplate($type, $vars, $subject, $content)
    {
        $languageModel = MooCore::getInstance()->getModel('Language');
        $mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $langs = $languageModel->find('all');
        $data['Mailtemplate'] = array(
            'type' => $type,
            'plugin' => 'Store',
            'vars' => $vars
        );
        $mailModel->create();
        $mailModel->save($data);
        $id = $mailModel->id;
        foreach ($langs as $lang)
        {
            $language = $lang['Language']['key'];
            $mailModel->locale = $language;
            $data_translate['subject'] = $subject;
            $data_translate['content'] = $content;
            $mailModel->save($data_translate);
        }
    }

    //////////////////////////////////////////uninstall//////////////////////////////////////////
    public function uninstall()
    {
        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $mCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $mLike = MooCore::getInstance()->getModel('Like');
        $mComment = MooCore::getInstance()->getModel('Comment');
        $mNotification = MooCore::getInstance()->getModel('Notification');

        //page, widget
        $coreContentIds = $mCoreContent->find("list", array(
            "conditions" => array("CoreContent.plugin" => "Store"),
            "fields" => array("CoreContent.id")
        ));
        $pageIds = $mPage->find("list", array(
            "conditions" => array("Page.alias" => array('stores_index', 'stores_product', 'stores_sellers', 'stores_seller_products')),
            "fields" => array("Page.id")
        ));
        $mCoreContent->deleteAll(array(
            'CoreContent.plugin' => 'Store'
        ));
        $mCoreBlock->deleteAll(array(
            'CoreBlock.plugin' => 'Store'
        ));
        $mPage->deleteAll(array(
            "Page.alias IN('stores_index', 'stores_product', 'stores_sellers', 'stores_seller_products')"
        ));

        //delete language
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18nModel->deleteAll(array(
            'I18nModel.content' => array(
                'Store Page', 'Store Categories', 'Store Categories & Search',
                'Store Products Latest', 'Store Products Sale', 'Store Products Most Viewed',
                'Store Products Featured', 'Store Featured', 'Store Same Product Images',
                'Store Same Product Videos', 'Store Related Products', 'Store Seller Info',
                'Store Menu', 'Store Categories', 'Store Search'
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
            'I18nModel.model' => "CoreMenuItem",
            "I18nModel.content" => "Store"
        ));
        $i18nModel->deleteAll(array(
            'I18nModel.model' => array('Store', 'StoreCategory', 'StorePackage', 'StorePayment', 'StoreShippingMethod'),
        ));

        //Menu
        $menuModel->deleteAll(array(
            'CoreMenuItem.url' => '/stores'
        ));

        //delete setting
        $settingGroup = $mSettingGroup->findByModuleId('Store');
        if ($settingGroup != null)
        {
            $mSetting->deleteAll(array(
                'Setting.group_id' => $settingGroup['SettingGroup']['id']
            ));
            $mSettingGroup->delete($settingGroup['SettingGroup']['id']);
        }

        //delete activity
        $mActivity->deleteAll(array(
            "Activity.item_type IN('Store_Store', 'Store_Store_Product')"
        ));

        //delete notification
        $mNotification->deleteAll(array(
            "Notification.plugin" => "Store"
        ));

        //delete like
        $mLike->deleteAll(array(
            "Like.type IN('Store_Store_Product')"
        ));

        //delete comment
        $mComment->deleteAll(array(
            "Comment.type IN('Store_Store_Product')"
        ));

        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        foreach ($roles as $role)
        {
            $params = explode(',', $role['Role']['params']);
            $params = array_diff($params, array(
                'store_create_store', 'store_view_product_detail', 'store_buy_product'
            ));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params' => implode(',', $params)));
        }

        //delete Mail template
        $mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $mails = $mailModel->find('all', array(
            'conditions' => array(
                'Mailtemplate.plugin' => 'Store',
            ),
            'fields' => array('Mailtemplate.id')
        ));
        if ($mails != null)
        {
            foreach ($mails as $mail)
            {
                $mailModel->delete($mail['Mailtemplate']['id']);
            }
        }

        //delete s3
        $mStorageAwsTask = MooCore::getInstance()->getModel('Storage.StorageAwsTask');
        $mStorageAwsObjectMap = MooCore::getInstance()->getModel('Storage.StorageAwsObjectMap');

        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.type" => array("products", "stores")
        ));

        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.name LIKE '%store/css%' OR StorageAwsTask.name LIKE '%store/images%' OR StorageAwsTask.name LIKE '%store/js%'"
        ));

        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.type" => array("products", "stores")
        ));

        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.key LIKE '%store/css%' OR StorageAwsObjectMap.key LIKE '%store/images%' OR StorageAwsObjectMap.key LIKE '%store/js%'"
        ));

        //delete credit
        if (CakePlugin::loaded("Credit"))
        {
            $mCreditLog = MooCore::getInstance()->getModel('Credit.CreditLog');
            $mCreditActiontype = MooCore::getInstance()->getModel('Credit.CreditActiontype');
            $mCreditLog->deleteAll(array(
                'CreditLog.object_type' => array('store_store_order')
            ));
            $mCreditActiontype->deleteAll(array(
                'CreditActiontype.plugin' => "Store"
            ));
        }

        //delete photos
        $mPhoto = MooCore::getInstance()->getModel('Photo');
        $mPhoto->deleteAll(array(
            "Photo.type" => "Store_Review"
        ));
    }

    public function settingGuide()
    {
        
    }

    public function menu()
    {
        return array(
            __d('store', 'Sellers') => array('plugin' => 'store', 'controller' => 'stores', 'action' => 'admin_index'),
            __d('store', 'Store Categories') => array('plugin' => 'store', 'controller' => 'store_categories', 'action' => 'admin_index'),
            __d('store', 'Products') => array('plugin' => 'store', 'controller' => 'store_products', 'action' => 'admin_index'),
            __d('store', 'Orders') => array('plugin' => 'store', 'controller' => 'store_orders', 'action' => 'admin_index'),
            __d('store', 'Reports') => array('plugin' => 'store', 'controller' => 'store_product_reports', 'action' => 'admin_index'),
            __d('store', 'Packages') => array('plugin' => 'store', 'controller' => 'store_packages', 'action' => 'admin_index'),
            __d('store', 'Payments') => array('plugin' => 'store', 'controller' => 'store_payments', 'action' => 'admin_index'),
            __d('store', 'Shipping Methods') => array('plugin' => 'store', 'controller' => 'store_shipping_methods', 'action' => 'admin_index'),
            __d('store', 'Transactions') => array('plugin' => 'store', 'controller' => 'store_transactions', 'action' => 'admin_index'),
            __d('store', 'Settings') => array('plugin' => 'store', 'controller' => 'store_settings', 'action' => 'admin_index'),
        );
    }

    public function callback_1_2()
    {
        //Mail template order
        $content = '
[content]
';
        $this->createMailTemplate('store_order', '[content]', 'Thanks for your order! Please check your information', $content);

        //remove dir
        $dirPath = APP . 'Plugin' . DS . 'Store' . DS . 'View' . DS . 'Layouts';
        if (is_dir($dirPath))
        {
            $objects = scandir($dirPath);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (file_exists($dirPath . DS . $object))
                    {
                        unlink($dirPath . DS . $object);
                    }
                }
            }
            rmdir($dirPath);
        }
    }

    public function callback_1_3($install = false)
    {
        //find store page
        $mPage = MooCore::getInstance()->getModel('Page');
        $pages = $mPage->find('list', array(
            'conditions' => array(
                'Page.alias' => array('stores_index', 'stores_product')
            ),
            'fields' => array('Page.id')
        ));

        if ($pages != null)
        {
            $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
            $core_contents = $mCoreContent->find('list', array(
                'conditions' => array(
                    'CoreContent.page_id' => $pages,
                    'CoreContent.type' => 'container',
                    'CoreContent.name' => 'west'
                ),
                'fields' => array('CoreContent.page_id', 'CoreContent.id')
            ));
            if ($core_contents != null)
            {
                //add core block
                $blockData[] = $this->parseBlockData('Store Products Featured', 'products.featured_products', null, 1);
                $blockOutput = $this->addCoreBlock($blockData);

                //add core content
                $contentData = array();
                foreach ($core_contents as $page_id => $core_content_id)
                {
                    $contentData[] = $this->parseContentData(null, null, $blockOutput, "stores_index", "west", "Store Products Featured", "products.featured_products", true, $page_id, $core_content_id);
                }
                $this->addCoreContent($contentData);
            }
        }

        //store categories multi language
        $mLanguage = MooCore::getInstance()->getModel('Language');
        $mStorePackage = MooCore::getInstance()->getModel('StorePackage');
        $mI18n = MooCore::getInstance()->getModel('I18nModel');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $languages = $mLanguage->find('all');
        if (!$install)
        {
            $mStoreCategory = MooCore::getInstance()->getModel('StoreCategory');
            $categories = $mStoreCategory->find('list', array(
                'fields' => array('StoreCategory.name')
            ));
        }
        if ($languages != null)
        {
            $db = ConnectionManager::getDataSource("default");
            $table_prefix = $mSetting->tablePrefix;
            foreach ($languages as $language)
            {
                if (!$install && $categories != null)
                {
                    foreach ($categories as $category_id => $category_name)
                    {
                        $mI18n->create();
                        $mI18n->save(array(
                            'locale' => $language['Language']['key'],
                            'model' => 'StoreCategory',
                            'foreign_key' => $category_id,
                            'field' => 'name',
                            'content' => $category_name
                        ));
                    }
                }

                //multi language for package
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePackage', 1,'name','Featured Product')");

                //multi language for payment
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 1,'name','Cash on delivery')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 1,'description','Customer pays when goods are delivered.')");

                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 2,'name','Pay in Store')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 2,'description','Customer pay in store when collecting goods.')");

                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 3,'name','PayPal')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 3,'description','Customer pays online and goods get delivered.')");

                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 4,'name','PayPal Collect')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 4,'description','Customer pays online and collects goods from store.')");
            }
        }

        //new setting
        if (!$install)
        {
            $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $mSettingGroup->findByModuleId('Store');
            if ($settingGroup != null)
            {
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Site Profit',
                    'name' => 'store_site_profit',
                    'type_id' => 'text',
                    'value_actual' => 0,
                    'value_default' => 0,
                    'description' => 'Site profit percentage per order (for online transaction)',
                    'ordering' => 13
                ));
            }
        }
    }

    public function callback_1_4()
    {
        //Mail template email to friends
        $content = '
[content]
';
        $this->createMailTemplate('email_to_friend', '[content]', 'Checkout this product: [product_name]', $content);
    }

    public function callback_1_5($install = false)
    {
        //new setting
        if (!$install)
        {
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $mSettingGroup->findByModuleId('Store');
            if ($settingGroup != null)
            {
                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Number of Featured products',
                    'name' => 'featured_products',
                    'type_id' => 'text',
                    'value_actual' => 5,
                    'value_default' => 5,
                    'description' => '',
                    'ordering' => 14
                ));
            }
        }
    }

    public function callback_1_6($install = false)
    {
        //find store page
        $mPage = MooCore::getInstance()->getModel('Page');
        $pages = $mPage->find('list', array(
            'conditions' => array(
                'Page.alias' => array('stores_index', 'stores_product')
            ),
            'fields' => array('Page.id')
        ));

        if ($pages != null)
        {
            $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
            $core_contents = $mCoreContent->find('list', array(
                'conditions' => array(
                    'CoreContent.page_id' => $pages,
                    'CoreContent.type' => 'container',
                    'CoreContent.name' => 'west'
                ),
                'fields' => array('CoreContent.page_id', 'CoreContent.id')
            ));
            if ($core_contents != null)
            {
                //add core block
                $blockData[] = $this->parseBlockData('Store Featured', 'products.featured_stores', null, 1);
                $blockOutput = $this->addCoreBlock($blockData);

                //add core content
                $contentData = array();
                foreach ($core_contents as $page_id => $core_content_id)
                {
                    $contentData[] = $this->parseContentData(null, null, $blockOutput, "stores_index", "west", "Store Featured", "products.featured_stores", true, $page_id, $core_content_id);
                }
                $this->addCoreContent($contentData);
            }
        }

        //shipping method language
        $mLanguage = MooCore::getInstance()->getModel('Language');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $languages = $mLanguage->find('all');
        if ($languages != null)
        {
            $db = ConnectionManager::getDataSource("default");
            $table_prefix = $mSetting->tablePrefix;
            foreach ($languages as $language)
            {
                //multi language for package
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePackage', 2,'name','Featured Store')");

                //multi language for payment
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StoreShippingMethod', 1,'name','Free Shipping')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StoreShippingMethod', 2,'name','Per item Shipping Rate')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StoreShippingMethod', 3,'name','Pickup From Store')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StoreShippingMethod', 4,'name','Flat Shipping Rate')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StoreShippingMethod', 5,'name','Weight Based Shipping')");
            }
        }

        //new setting
        if (!$install)
        {
            $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $mSettingGroup->findByModuleId('Store');
            if ($settingGroup != null)
            {
                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Number of Featured stores',
                    'name' => 'featured_stores',
                    'type_id' => 'text',
                    'value_actual' => 5,
                    'value_default' => 5,
                    'description' => '',
                    'ordering' => 15
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Show store list',
                    'name' => 'show_store_list',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'description' => '',
                    'ordering' => 16
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Hide cart store option',
                    'name' => 'store_hide_cart_store_option',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'description' => '',
                    'ordering' => 17
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Store Per Page',
                    'name' => 'store_store_per_page',
                    'type_id' => 'text',
                    'value_actual' => 5,
                    'value_default' => 5,
                    'description' => '',
                    'ordering' => 18
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Buy Featured Product',
                    'name' => 'store_buy_featured_product',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'description' => '',
                    'ordering' => 19
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Buy Featured Store',
                    'name' => 'store_buy_featured_store',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'description' => '',
                    'ordering' => 20
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'By pass force login',
                    'name' => 'store_by_pass_force_login',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'description' => '',
                    'ordering' => 21,
                    'is_boot' => 1
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Enable shipping',
                    'name' => 'store_enable_shipping',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'description' => '',
                    'ordering' => 22
                ));
            }

            //upgrade transaction
            $mStoreTransaction = MooCore::getInstance()->getModel('Store.StoreTransaction');
            $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $mStoreProduct->unbindModel(array(
                'belongsTo' => array('StoreProducer', 'User', 'Store'),
                'hasMany' => array('StoreProductImage')
                    ), true);
            $transactions = $mStoreTransaction->find('all', array(
                'conditions' => array(
                    'StoreTransaction.store_product_id > 0',
                    'StoreTransaction.status' => TRANSACTION_STATUS_COMPLETED
                )
            ));
            if ($transactions != null)
            {
                foreach ($transactions as $transaction)
                {
                    $mStoreProduct->updateAll(array(
                        'StoreProduct.feature_expiration_date' => "'" . $transaction['StoreTransaction']['expiration_date'] . "'"
                            ), array(
                        'StoreProduct.id' => $transaction['StoreTransaction']['store_product_id']
                    ));
                }
            }
        }

        //Mail remind featured product expired
        $content = '
<p>[header]</p>
<p>Your featured product: "[product_name]" will be expired on [expire_time]. You can upgrade product on this link: <a href="[link]">[link]</a>.</p>
<p>[footer]</p>
';
        $this->createMailTemplate('store_featured_product_expiration_reminder', '[product_name][expire_time][link]', 'Featured product expiration reminder', $content);

        //Mail featured product expired
        $content = '
<p>[header]</p>
<p>Your featured product: "[product_name]" has been expired. You can buy more feature day on this link: <a href="[link]">[link]</a>.</p>
<p>[footer]</p>
';
        $this->createMailTemplate('store_featured_product_expiration', '[product_name][link]', 'Your featured product has been expired', $content);

        //Mail remind featured store expired
        $content = '
<p>[header]</p>
<p>Your featured store: "[store_name]" will be expired on [expire_time]. You can upgrade store on this link: <a href="[link]">[link]</a>.</p>
<p>[footer]</p>
';
        $this->createMailTemplate('store_featured_store_expiration_reminder', '[store_name][expire_time][link]', 'Featured store expiration reminder', $content);

        //Mail featured store expired
        $content = '
<p>[header]</p>
<p>Your featured store: "[store_name]" has been expired. You can buy more feature day on this link: <a href="[link]">[link]</a>.</p>
<p>[footer]</p>
';
        $this->createMailTemplate('store_featured_store_expiration', '[store_name][link]', 'Your featured store has been expired', $content);
    }

    public function callback_1_9($install = false)
    {
        //new setting
        if (!$install)
        {
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $mSettingGroup->findByModuleId('Store');
            if ($settingGroup != null)
            {
                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'My files item per page',
                    'name' => 'my_files_item_per_page',
                    'type_id' => 'text',
                    'value_actual' => 10,
                    'value_default' => 10,
                    'description' => '',
                    'ordering' => 23
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Allow digital file extensions',
                    'name' => 'store_allow_digital_file_extensions',
                    'type_id' => 'text',
                    'value_actual' => 'mp3,mp4,docx,txt',
                    'value_default' => 'mp3,mp4,docx,txt',
                    'description' => '',
                    'ordering' => 24
                ));

                /* $mSetting->create();
                  $mSetting->save(array(
                  'group_id' => $settingGroup['SettingGroup']['id'],
                  'label' => 'Allow video extensions',
                  'name' => 'store_allow_video_extensions',
                  'type_id' => 'text',
                  'value_actual' => 'mp4',
                  'value_default' => 'mp4',
                  'description' => '',
                  'ordering' => 25
                  )); */

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Integrate credit for check-out process',
                    'name' => 'store_integrate_credit',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'description' => '',
                    'ordering' => 26
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Show money type for product',
                    'name' => 'store_show_money_type',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Normal value","value":"1","select":1},{"name":"Credit value","value":"2","select":0},{"name":"All values","value":"3","select":0}]',
                    'value_default' => '[{"name":"Normal value","value":"1","select":1},{"name":"Credit value","value":"2","select":0},{"name":"All values","value":"3","select":0}]',
                    'description' => '',
                    'ordering' => 27
                ));
            }
        }

        //language
        $mLanguage = MooCore::getInstance()->getModel('Language');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $languages = $mLanguage->find('all');
        if ($languages != null)
        {
            $db = ConnectionManager::getDataSource("default");
            $table_prefix = $mSetting->tablePrefix;
            foreach ($languages as $language)
            {
                //multi language for credit payment
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 5,'name','Credit')");
                $db->query("insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values ('" . $language['Language']['key'] . "', 'StorePayment', 5,'description','Customer pays online by using credits.')");
            }
        }
    }

    public function callback_2_0($install = false)
    {
        //new setting
        if (!$install)
        {
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $mSettingGroup->findByModuleId('Store');
            if ($settingGroup != null)
            {
                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Video item per page',
                    'name' => 'video_item_per_page',
                    'type_id' => 'text',
                    'value_actual' => '9',
                    'value_default' => '9',
                    'description' => '',
                    'ordering' => 28
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Product review item per page',
                    'name' => 'product_review_per_page',
                    'type_id' => 'text',
                    'value_actual' => '10',
                    'value_default' => '10',
                    'description' => '',
                    'ordering' => 29
                ));

                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Paypal type',
                    'name' => 'store_paypal_type',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"PayPal Adaptive","value":"1","select":1},{"name":"PayPal Express Checkout","value":"2","select":0}]',
                    'value_default' => '[{"name":"PayPal Adaptive","value":"1","select":1},{"name":"PayPal Express Checkout","value":"2","select":0}]',
                    'description' => '',
                    'ordering' => 30
                ));
            }
        }

        //add new widget
        $mPage = MooCore::getInstance()->getModel('Page');
        $page = $mPage->findByAlias('stores_product');

        if ($page != null)
        {
            $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
            $page_id = $page['Page']['id'];

            //add core content center
            $contentParentData[] = array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'center',
            );
            $contentParentOutput = $this->addCoreContentParent($contentParentData);

            //add core block
            $blockData[] = $this->parseBlockData('Store Related Products', 'products.related_products', null, 1);
            $blockOutput = $this->addCoreBlock($blockData);

            //add block
            $contentData[] = $this->parseContentData(null, null, null, "stores_product", "center", "Main Content", "invisiblecontent", false, $page_id, $contentParentOutput[$page_id . 'center'], 0);
            $contentData[] = $this->parseContentData(null, null, null, "stores_product", "center", "Store Related Products", "products.related_products", true, $page_id, $contentParentOutput[$page_id . 'center'], $blockOutput['Store Related Products'], 2);
            $this->addCoreContent($contentData);
        }
    }

    public function callback_2_1($install = false)
    {
        //new setting
        if (!$install)
        {
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $mSettingGroup->findByModuleId('Store');
            if ($settingGroup != null)
            {
                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Only users who buy product can review',
                    'name' => 'store_only_buyer_can_review',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":1},{"name":"No","value":"0","select":0}]',
                    'description' => '',
                    'ordering' => 31
                ));
            }
        }
    }

    public function callback_2_4($install = false)
    {
        //add new widget
        $mPage = MooCore::getInstance()->getModel('Page');
        $page = $mPage->findByAlias('stores_product');

        if ($page != null)
        {
            $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
            $page_id = $page['Page']['id'];

            //find core content center
            $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
            $core_content = $mCoreContent->find('first', array(
                'conditions' => array(
                    'CoreContent.page_id' => $page_id,
                    'CoreContent.type' => 'container',
                    'CoreContent.name' => 'center'
                ),
                'fields' => array('CoreContent.page_id', 'CoreContent.id')
            ));

            //add core block
            $blockData[] = $this->parseBlockData('Store Product Detail Description', 'products.product_detail_description', null, 1);
            $blockOutput = $this->addCoreBlock($blockData);

            //add block
            if ($core_content != null)
            {
                $contentData[] = $this->parseContentData(null, null, null, "stores_product", "center", "Store Product Detail Description", "products.product_detail_description", true, $page_id, $core_content['CoreContent']['id'], $blockOutput['Store Product Detail Description'], 2);
                $this->addCoreContent($contentData);
            }
        }

        //update core content order
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $table_prefix = $mSetting->tablePrefix;
        $db = ConnectionManager::getDataSource("default");
        $db->query("UPDATE `" . $table_prefix . "core_contents` SET `order` = 3 WHERE name = 'products.related_products'");
    }

    public function callback_2_5($install = false)
    {
        //add new widget seller on product detail
        $mPage = MooCore::getInstance()->getModel('Page');
        $page = $mPage->findByAlias('stores_product');

        if ($page != null)
        {
            $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
            $page_id = $page['Page']['id'];

            //find core content center
            $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
            $core_content = $mCoreContent->find('first', array(
                'conditions' => array(
                    'CoreContent.page_id' => $page_id,
                    'CoreContent.type' => 'container',
                    'CoreContent.name' => 'west'
                ),
                'fields' => array('CoreContent.page_id', 'CoreContent.id')
            ));

            //add core block
            $blockData[] = $this->parseBlockData('Store Seller Info', 'products.seller_info', null, 1);
            $blockOutput = $this->addCoreBlock($blockData);

            //add block
            if ($core_content != null)
            {
                $contentData[] = $this->parseContentData(null, null, null, "stores_product", "west", "Store Seller Info", "products.seller_info", true, $page_id, $core_content['CoreContent']['id'], $blockOutput['Store Seller Info'], 1);
                $this->addCoreContent($contentData);
            }
            
            //update ordering
            $core_contents = $mCoreContent->find('list', array(
                'conditions' => array(
                    'CoreContent.page_id' => $page_id,
                    'CoreContent.type' => 'widget',
                    'CoreContent.name != "products.seller_info"'
                ),
                'order' => array('CoreContent.order ASC', 'CoreContent.id ASC'),
                'fields' => array('CoreContent.id', 'CoreContent.id')
            ));
            if ($core_contents != null)
            {
                $ordering = 1;
                foreach($core_contents as $core_content_id)
                {
                    $ordering++;
                    $mCoreContent->updateAll(array(
                        'CoreContent.order' => $ordering
                    ), array(
                        'CoreContent.id' => $core_content_id
                    ));
                }
            }
        }

        //update core content order
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $table_prefix = $mSetting->tablePrefix;
        $db = ConnectionManager::getDataSource("default");
        $db->query("UPDATE `" . $table_prefix . "settings` SET `label` = 'Allow to select seller to checkout separately at check out step' WHERE label = 'Hide cart store option'");
    }

    public function callback_2_6($install = false)
    {
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $mCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        
        //new setting
        if (!$install) {
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $mSettingGroup->findByModuleId('Store');
            if ($settingGroup != null) {
                $mSetting->create();
                $mSetting->save(array(
                    'group_id' => $settingGroup['SettingGroup']['id'],
                    'label' => 'Integrate with business directory plugin',
                    'name' => 'store_integrate_business',
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'value_default' => '[{"name":"Yes","value":"1","select":0},{"name":"No","value":"0","select":1}]',
                    'description' => '',
                    'ordering' => 32
                ));
            }
            
            //upgrade for unlimited feature stores
            $mStore = MooCore::getInstance()->getModel('Store');
            $stores = $mStore->find('all', array(
                'conditions' => array(
                    'Store.featured' => 1,
                    'Store.feature_expiration_date' => '0000-00-00 00:00:00'
                )
            ));
            if($stores != null)
            {
                foreach($stores as $store)
                {
                    $mStore->create();
                    $mStore->updateAll(array(
                        'Store.unlimited_feature' => 1
                    ), array(
                        'Store.id' => $store['Store']['id']
                    ));
                }
            }
            
            //upgrade for unlimited feature store_products
            $mStoreProduct = MooCore::getInstance()->getModel('StoreProduct');
            $store_products = $mStoreProduct->find('all', array(
                'conditions' => array(
                    'StoreProduct.featured' => 1,
                    'StoreProduct.feature_expiration_date' => '0000-00-00 00:00:00'
                )
            ));
            if($store_products != null)
            {
                foreach($store_products as $store_product)
                {
                    $mStoreProduct->create();
                    $mStoreProduct->updateAll(array(
                        'StoreProduct.unlimited_feature' => 1
                    ), array(
                        'StoreProduct.id' => $store_product['StoreProduct']['id']
                    ));
                }
            }
            
            //delete 
            $mCoreContent->deleteAll(array(
                'CoreContent.plugin' => 'Store',
                'CoreContent.core_block_id' => 0,
                'CoreContent.core_block_title IN("Store Categories", "Store Categories &amp; Search", "Store Categories & Search")'
            ));
            
            //change product share activity
            $mActivity = MooCore::getInstance()->getModel('Activity');
            $mActivity->updateAll(array(
                "Activity.action" => "'product_item_detail_share'"
            ), array(
                "Activity.action" => "product_share",
                "Activity.item_type" => "Store_Store_Product"
            ));
			
			//remove files
			$dirPath = APP . 'Plugin' . DS . 'Store' . DS . 'Controller';
			$files = array("AttributesController.php", "CartsController.php", "OrderDetailsController.php", "ProductUploadController.php", "WishlistsController.php",
				"OrdersController.php", "PopupsController.php", "ProducersController.php", "ProductReportsController.php", "ProductsController.php");
			if (is_dir($dirPath))
			{
				foreach ($files as $file)
				{
					if (file_exists($dirPath . DS . $file))
					{
						unlink($dirPath . DS . $file);
					}
				}
			}
			
			//remove view
			$dirPath = APP . 'Plugin' . DS . 'Store' . DS . 'View';
			$folders = array("Attributes", "Carts", "Orders", "Producers", "ProductReports", "Products");
			foreach($folders as $folder)
			{
				$dir = $dirPath . DS.$folder;
				$objects = scandir($dir);
				foreach ($objects as $object)
				{
					if ($object != "." && $object != "..")
					{
						if (file_exists($dir . DS . $object))
						{
							unlink($dir . DS . $object);
						}
					}
				}
				rmdir($dir);
			}
        }
        
        //add new widget menu, categories, search
        $mPage = MooCore::getInstance()->getModel('Page');
        $pages = $mPage->find('all', array(
            'conditions' => array(
                'Page.alias' => array('stores_index', 'stores_product')
            )
        ));

        if ($pages != null)
        {
            //add core block
            $blockData[] = $this->parseBlockData('Store Menu', 'products.menu', null, 1);
            $blockData[] = $this->parseBlockData('Store Categories', 'products.categories', null, 1);
            $blockData[] = $this->parseBlockData('Store Search', 'products.search', null, 1);
            $blockOutput = $this->addCoreBlock($blockData);

            foreach($pages as $page)
            {
                $page_id = $page['Page']['id'];
                $alias = $page['Page']['alias'];

                //find core content center
                $core_content = $mCoreContent->find('first', array(
                    'conditions' => array(
                        'CoreContent.page_id' => $page_id,
                        'CoreContent.type' => 'container',
                        'CoreContent.name' => 'west'
                    ),
                    'fields' => array('CoreContent.page_id', 'CoreContent.id')
                ));

                //add block
                $start_ordering = 1;
                if($alias == "stores_product")
                {
                    $start_ordering = 2;
                }
                if ($core_content != null)
                {
                    $contentData = array();
                    $contentData[] = $this->parseContentData(null, null, null, "stores_product", "west", "Store Menu", "products.menu", true, $page_id, $core_content['CoreContent']['id'], $blockOutput['Store Menu'], $start_ordering);$start_ordering+=1;
                    $contentData[] = $this->parseContentData(null, null, null, "stores_product", "west", "Store Categories", "products.categories", true, $page_id, $core_content['CoreContent']['id'], $blockOutput['Store Categories'], $start_ordering);$start_ordering+=1;
                    $contentData[] = $this->parseContentData(null, null, null, "stores_product", "west", "Store Search", "products.search", true, $page_id, $core_content['CoreContent']['id'], $blockOutput['Store Search'], $start_ordering);
                    $this->addCoreContent($contentData);
                }

                //update ordering
                $core_contents = $mCoreContent->find('list', array(
                    'conditions' => array(
                        'CoreContent.page_id' => $page_id,
                        'CoreContent.type' => 'widget',
                        'CoreContent.name NOT IN ("products.menu", "products.categories", "products.search", "products.seller_info")'
                    ),
                    'order' => array('CoreContent.order ASC', 'CoreContent.id ASC'),
                    'fields' => array('CoreContent.id', 'CoreContent.id')
                ));
                if ($core_contents != null)
                {
                    $ordering = $start_ordering;
                    foreach($core_contents as $core_content_id)
                    {
                        $ordering++;
                        $mCoreContent->updateAll(array(
                            'CoreContent.order' => $ordering
                        ), array(
                            'CoreContent.id' => $core_content_id
                        ));
                    }
                }
            }
        }
        
        //add new pages - seller page and seller's products page
        $pageData = array();
        $pageData[] = array(
            'title' => 'Store Seller Page',
            'alias' => 'stores_sellers',
            'url' => '/stores/sellers',
            'uri' => 'stores.sellers',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageData[] = array(
            'title' => 'Store Seller Product List',
            'alias' => 'stores_seller_products',
            'url' => '/stores/seller_products/$id',
            'uri' => 'stores.seller_products',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageOutput = $this->addPage($pageData);

        //add core content east west
        $contentParentData = array();
        $contentParentData[] = array(
            'page_id' => $pageOutput['stores_sellers'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['stores_seller_products'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentOutput = $this->addCoreContentParent($contentParentData);
        
        //add block for sellers
        $contentData = array();
        $core_block = $mCoreBlock->find('all', array(
            'conditions' => array(
                'CoreBlock.path_view' => array('products.menu', 'products.categories', 'products.search', 'products.featured_stores'),
                'CoreBlock.plugin' => 'Store'
            )
        ));
        foreach ($core_block as $item)
        {
            $item = $item['CoreBlock'];
            switch($item['path_view'])
            {
                case "products.menu":
                    $ordering = 1;
                    break;
                case "products.categories":
                    $ordering = 2;
                    break;
                case "products.search":
                    $ordering = 3;
                    break;
                case "products.featured_stores":
                    $ordering = 4;
                    break;
            }
            $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, null, "stores_sellers", "west", $item['name'], $item['path_view'], true, null, null, $item['id'], $ordering);
        }
        
        //add block for seller's products
        $core_block = $mCoreBlock->find('all', array(
            'conditions' => array(
                'CoreBlock.path_view' => array('products.seller_info', 'products.menu', 'products.categories', 'products.search', 'products.most_viewed_products', 'products.latest_products', 'products.sale_products'),
                'CoreBlock.plugin' => 'Store'
            ),
        ));
        foreach ($core_block as $item)
        {
            $item = $item['CoreBlock'];
            switch($item['path_view'])
            {
                case "products.seller_info":
                    $ordering = 1;
                    break;
                case "products.menu":
                    $ordering = 2;
                    break;
                case "products.categories":
                    $ordering = 3;
                    break;
                case "products.search":
                    $ordering = 4;
                    break;
                case "products.most_viewed_products":
                    $ordering = 5;
                    break;
                case "products.latest_products":
                    $ordering = 6;
                    break;
                case "products.sale_products":
                    $ordering = 7;
                    break;
            }
            $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, null, "stores_seller_products", "west", $item['name'], $item['path_view'], true, null, null, $item['id'], $ordering);
        }
        $this->addCoreContent($contentData);
    }
}