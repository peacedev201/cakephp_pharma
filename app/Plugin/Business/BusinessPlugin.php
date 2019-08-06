<?php 
App::uses('MooPlugin','Lib');
class BusinessPlugin implements MooPlugin{
    public function install(){
        
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        $aRoleIds = array();
        foreach ($aRoles as $aRole) {
            $aRoleIds[] = $aRole['Role']['id'];
            $aParams = array_unique(array_merge(explode(',', $aRole['Role']['params']), array('business_create', 'business_view')));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }
        
        //role acos
        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $oAcoModel->create();
        $oAcoModel->save(array(
            'key' => 'claim',
            'group' => 'business',
            'description' => 'Claimable Page Creators',
        ));

        $oAcoModel->create();
        $oAcoModel->save(array(
            'key' => 'create',
            'group' => 'business',
            'description' => 'Can create business',
        ));

        $oAcoModel->create();
        $oAcoModel->save(array(
            'key' => 'view',
            'group' => 'business',
            'description' => 'Can view business',
        ));
        
        //menu
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mMenu->findByUrl('/businesss');
        if ($menu)
        {
            $mMenu->id = $menu['CoreMenuItem']['id'];
            $mMenu->save(array(
                'name' => 'Business',
                'url' => '/businesses'
            ));
            
            //add language for menu
            $languageModel = MooCore::getInstance()->getModel('Language');
            $langs = array_keys($languageModel->getLanguages());
            foreach ($langs as $lKey) {
                $mMenu->locale = $lKey;
                $mMenu->id = $menu['CoreMenuItem']['id'];
                $mMenu->saveField('name', $menu['CoreMenuItem']['name']);
            }
        }
        
        //Setting
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $setting = $mSetting->findByName('business_enabled');
        if ($setting)
        {
        	$mSetting->id = $setting['Setting']['id'];
        	$mSetting->save(array('is_boot'=>1));
        }
        
        //mail template
        $this->installMailTemplate();
        
        //widget
        $this->installBlock();
        
        $setting = $mSetting->findByName('business_by_pass_force_login');
        if ($setting)
        {
        	$mSetting->id = $setting['Setting']['id'];
        	$mSetting->save(array('is_boot'=>1));
        }
		
		$this->callback_1_3(true);
		$this->callback_1_7(true);
    }
    
    //////////////////////////////////////////mail template//////////////////////////////////////////
    private function installMailTemplate()
    {
        //Mail template approve business
        $content = 
'
    <p>[header]</p>
    <p>Your business has been approved by admin. Click <a href="[link]">here</a> to go to business page.</p>
    <p>[footer]</p>
';
        $this->createMailTemplate('business_approve', '[reason]', 'Your business has been approved by Admin', $content);
        
        //Mail template approve sub page
        $content = 
'
    <p>[header]</p>
    <p>Your sub page has been approved by admin. Click <a href="[link]">here</a> to go to sub page.</p>
    <p>[footer]</p>
';
        $this->createMailTemplate('business_subpage_approve', '[reason]', 'Your sub page has been approved by Admin', $content);
        
        //Mail template reject business
        $content = 
'
    <p>[header]</p>
    <p>Your business has been rejected by admin because of the below reason: [reason]</p>
    <p>Click <a href="[link]">here</a> to go to business page.</p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('business_reject', '[reason]', 'Your business has been rejected by Admin', $content);
        
        //Mail template reject subpage
        $content = 
'
    <p>[header]</p>
    <p>Your sub page has been rejected by admin because of the below reason: [reason]</p>
    <p>Click <a href="[link]">here</a> to go to sub page.</p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('business_subpage_reject', '[reason]', 'Your sub page has been rejected by Admin', $content);
        
        //Mail template business contact
        $content = 
'
    <p>[header]</p>
    <p>[name] sent you below message on <a href="[business_link]">[business_name]</a></p>
    <p>[message]</p>
    <p>Email: [sender_email]</p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('business_contact', '[name],[business_name],[message],[sender_email]', 'New message alert - [business_name]', $content);

        //Mail template feature expired business
        $business_expire_feature = 
'
    <p>[header]</p>
    <p>Your featured business has been expired. You can buy more feature day by link: [link].</p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('business_expire_feature', '[link][package_title][expire_time][business_name]', 'Your featured business has been expired', $business_expire_feature);
        
        //Mail template expired business
        $business_expire = 
'
    <p>[header]</p>
    <p>Your package business has been expired. You can upgrade business on link [link].</p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('business_expire', '[link][package_title][expire_time][business_name]', 'Your package business has been expired', $business_expire);
        
        //Mail template feature reminder business
        $business_reminder_feature = 
'
    <p>[header]</p>
    <p>Your featured business will be expired on [expire_time]. You can upgrade business on link [link].</p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('business_reminder_featured_package', '[link][package_title][expire_time][business_name]', 'Reminder Featured Expiration', $business_reminder_feature);
        
        //Mail template feature expired business
        $business_reminder = 
'
    <p>[header]</p>
    <p>Your package business will be expired on [expire_time]. You can upgrade business on link [link].</p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('business_reminder_business_package', '[link][package_title][expire_time][business_name]', 'Reminder Package Business Expiration', $business_reminder);
        
        // Verify request by document
        $sContentVerifyDocument = <<<EOF
            <p>[header]</p>
            <p>Request a verified badge has been sent from [recipient_title] for the below business page.</p>
            <p>[business_link]</p>
            <p>Please check attached documents to review and verify.</p>
            <p>[attachment_link]</p>
            <p>[footer]</p>
EOF;
        $this->createMailTemplate('business_verify_documents', '[recipient_title],[business_link],[attachment_link]', '[recipient_title] sent a request to get verified badget for this business page', $sContentVerifyDocument);
        
        // Verify request by phone
        $sContentVerifyPhone = <<<EOF
            <p>[header]</p>
            <p>Request a verified badge has been sent from [recipient_title] for the below business page.</p>
            <p>[business_link]</p>
            <p>Please call this phone number: [phone_number] to verify.</p>
            <p>[footer]</p>
EOF;
        $this->createMailTemplate('business_verify_phone', '[recipient_title],[business_link],[phone_number]', '[recipient_title] sent a request to get verified badget for this business page', $sContentVerifyPhone);
    }
    
    private function createMailTemplate($type, $vars, $subject, $content)
    {
        $languageModel = MooCore::getInstance()->getModel('Language');
     	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $langs = $languageModel->find('all');
        $data['Mailtemplate'] = array(
            'type' => $type,
            'plugin' => 'Business',
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

    //////////////////////////////////////////block//////////////////////////////////////////
    private function installBlock()
    {
        //Add page
        $pageData[] = array(
            'title' => 'Business Home',
            'alias' => 'business_index',
            'url' => '/businesses/',
            'uri' => 'business.index',
            'type' => 'plugin',
            'layout' => 5
        );
        $pageData[] = array(
            'title' => 'Business Categories',
            'alias' => 'business_categories',
            'url' => '/categories/',
            'uri' => 'business_categories.categories',
            'type' => 'plugin',
            'layout' => 5
        );
        $pageData[] = array(
            'title' => 'Business Locations',
            'alias' => 'business_locations',
            'url' => '/locations/',
            'uri' => 'business_locations.locations',
            'type' => 'plugin',
            'layout' => 5
        );
        $pageData[] = array(
            'title' => 'Business Search',
            'alias' => 'business_search',
            'url' => '/business_search/',
            'uri' => 'business.search',
            'type' => 'plugin',
            'layout' => 5
        );
        $pageData[] = array(
            'title' => 'Business Detail',
            'alias' => 'business_view',
            'url' => '/businesses/view/$id',
            'uri' => 'business.view',
            'type' => 'plugin',
            'layout' => 5
        );
        $pageData[] = array(
            'title' => 'Business Create',
            'alias' => 'business_create',
            'url' => '/businesses/create',
            'uri' => 'business.create',
            'type' => 'plugin',
            'layout' => 8
        );
        $pageData[] = array(
            'title' => 'Business Dashboard',
            'alias' => 'business_dashboard',
            'url' => '/businesses/dashboard/$/$id',
            'uri' => 'business.dashboard',
            'type' => 'plugin',
            'layout' => 6
        );
        $pageOutput = $this->addPage($pageData);
        
        //add core content east west
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_index'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_index'],
            'type' => 'container',
            'name' => 'east',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_index'],
            'type' => 'container',
            'name' => 'north',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_index'],
            'type' => 'container',
            'name' => 'center',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_categories'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_categories'],
            'type' => 'container',
            'name' => 'east',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_categories'],
            'type' => 'container',
            'name' => 'center',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_categories'],
            'type' => 'container',
            'name' => 'north',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_locations'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_locations'],
            'type' => 'container',
            'name' => 'east',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_locations'],
            'type' => 'container',
            'name' => 'center',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_locations'],
            'type' => 'container',
            'name' => 'north',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_search'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_search'],
            'type' => 'container',
            'name' => 'east',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_search'],
            'type' => 'container',
            'name' => 'center',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_search'],
            'type' => 'container',
            'name' => 'north',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_view'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_view'],
            'type' => 'container',
            'name' => 'east',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_view'],
            'type' => 'container',
            'name' => 'north',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_view'],
            'type' => 'container',
            'name' => 'center',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_create'],
            'type' => 'container',
            'name' => 'north',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_dashboard'],
            'type' => 'container',
            'name' => 'north',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['business_dashboard'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentOutput = $this->addCoreContentParent($contentParentData);
        
        //add core block business index
        $blockData[] = $this->parseBlockData('Business Breadcrumb', 'business.breadcrumb');
        $blockData[] = $this->parseBlockData('Business Map Preview', 'business.map_preview');
        $blockData[] = $this->parseBlockData('Business Menu', 'business.menu');
        $blockData[] = $this->parseBlockData('Business Categories', 'business.top_categories');
        $blockData[] = $this->parseBlockData('Business Search', 'business.search');
        $blockData[] = $this->parseBlockData('Business Locations', 'business.popular_locations');
        $blockData[] = $this->parseBlockData('Featured Businesses', 'business.featured', null, 1);
        $blockData[] = $this->parseBlockData('All Businesses', 'business.all');
        $blockData[] = $this->parseBlockData('Business Recent Reviews', 'business.recent_reviews', null, 1);
        $blockData[] = $this->parseBlockData('Business Reviews of Day', 'business.reviews_of_day', null, 1);
        
        //add core block business detail
        $blockData[] = $this->parseBlockData('Business Reviews & Ratings', 'business.rating');
        $blockData[] = $this->parseBlockData('Business Checkins', 'business.people_checkin');
        $blockData[] = $this->parseBlockData('Business Opening Hours', 'business.open_hours');
        $blockData[] = $this->parseBlockData('Business Social Media', 'business.social_link');
        $blockData[] = $this->parseBlockData('Business Claim Business', 'business.claim');
        $blockData[] = $this->parseBlockData('Business Menu Detail', 'business.menu_detail');
        $blockData[] = $this->parseBlockData('Business Detail Info', 'business.detail_info');
        $blockData[] = $this->parseBlockData('Business Detail Section', 'business.detail_section');
        $blockData[] = $this->parseBlockData('Business Payment Gateways', 'business.payment_gateways');
        $blockData[] = $this->parseBlockData('Business Same Categories', 'business.business_same_cat');
        
        //add core block dashboard
        $blockData[] = $this->parseBlockData('Business Dashboard Menu', 'business.dashboard_menu');
        $blockOutput = $this->addCoreBlock($blockData);
        
        //add core content business index
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "north", "Business Map Preview", "business.map_preview", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "west", "Business Menu", "business.menu", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "west", "Business Categories", "business.top_categories", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "west", "Business Recent Reviews", "business.recent_reviews", 3, true);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "east", "Business Search", "business.search", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "east", "Business Locations", "business.popular_locations", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "east", "Business Reviews of Day", "business.reviews_of_day", 3, true);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "center", "Featured Businesses", "business.featured", 1, true);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_index", "center", "Main Content", "invisiblecontent", 2, false, null, null, 0);

        //add core content business categories
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_categories", "west", "Business Menu", "business.menu", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_categories", "west", "Business Categories", "business.top_categories", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_categories", "east", "Business Search", "business.search", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_categories", "east", "Business Locations", "business.popular_locations", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_categories", "center", "Main Content", "invisiblecontent", 1, false, null, null, 0);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_categories", "north", "Business Breadcrumb", "business.breadcrumb", 1);

        //add core content business locations
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_locations", "west", "Business Menu", "business.menu", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_locations", "west", "Business Categories", "business.top_categories", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_locations", "east", "Business Search", "business.search", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_locations", "east", "Business Locations", "business.popular_locations", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_locations", "center", "Main Content", "invisiblecontent", 1, false, null, null, 0);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_locations", "north", "Business Breadcrumb", "business.breadcrumb", 1);

        //add core content business search
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_search", "west", "Business Menu", "business.menu", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_search", "west", "Business Categories", "business.top_categories", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_search", "east", "Business Search", "business.search", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_search", "east", "Business Locations", "business.popular_locations", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_search", "north", "Business Breadcrumb", "business.breadcrumb", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_search", "north", "Business Map Preview", "business.map_preview", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_search", "center", "Main Content", "invisiblecontent", 1, false, null, null, 0);

        //add core content business view
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "east", "Business Reviews & Ratings", "business.rating", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "east", "Business Checkins", "business.people_checkin", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "east", "Business Opening Hours", "business.open_hours", 3);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "east", "Business Social Media", "business.social_link", 4);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "west", "Business Menu Detail", "business.menu_detail", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "west", "Business Payment Gateways", "business.payment_gateways", 2);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "west", "Business Same Categories", "business.business_same_cat", 3);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "west", "Business Claim Business", "business.claim", 4);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "north", "Business Breadcrumb", "business.breadcrumb", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "center", "Business Detail Info", "business.detail_info", 1);
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_view", "center", "Business Detail Section", "business.detail_section", 2);
        
        //add core content business create
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_create", "north", "Business Breadcrumb", "business.breadcrumb", 1);
        
        //add core content business dashboard
        $contentData[] = $this->parseContentData($contentParentOutput, $pageOutput, $blockOutput, "business_dashboard", "west", "Business Dashboard Menu", "business.dashboard_menu", 1);
        
        $this->addCoreContent($contentData);
    }

    private function parseBlockData($name, $path_view, $params = null, $is_active = 0) {
        $params = $params != null ? $params : '{"0":{"label":"Title","input":"text","value":"' . $name . '","name":"title"},"1":{"label":"plugin","input":"hidden","value":"Business","name":"plugin"}}';
        return array(
            'name' => $name,
            'path_view' => $path_view,
            'params' => $params,
            'is_active' => $is_active,
            'group' => 'Business',
            'plugin' => 'Business',
        );
        ;
    }

    private function parseContentData($contentParentOutput, $pageOutput, $blockOutput, $page_alias, $east_west, $block_name, $name, $ordering = 1, $visible = false, $page_id = null, $parent_id = null, $core_block_id = null) {
        return array(
            'page_id' => is_numeric($page_id) ? $page_id : $pageOutput[$page_alias],
            'parent_id' => is_numeric($parent_id) ? $parent_id : $contentParentOutput[$pageOutput[$page_alias] . $east_west],
            'core_block_id' => is_numeric($core_block_id) ? $core_block_id : $blockOutput[$block_name],
            'type' => 'widget',
            'name' => $name,
            'order' => $ordering,
            'params' => !$visible ? '{"title":"' . $block_name . '","maincontent":"1"}' : '{"title":"' . $block_name . '","plugin":"Business","role_access":"all"}',
            'core_block_title' => $block_name,
            'plugin' => 'Business'
        );
    }

    private function addPage($data) {
        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $langs = array_keys($languageModel->getLanguages());

        $output = array();
        if ($data != null) {
            foreach ($data as $item) {
                $mPage->create();
                if ($mPage->save($item)) {
                    $id = $mPage->id;
                    foreach ($langs as $lKey) {
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

    private function addCoreBlock($data) {
        $mCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $langs = array_keys($languageModel->getLanguages());

        $output = array();
        if ($data != null) {
            foreach ($data as $item) {
                $mCoreBlock->create();
                if ($mCoreBlock->save($item)) {
                    $id = $mCoreBlock->id;
                    foreach ($langs as $lKey) {
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

    private function addCoreContentParent($data) {
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $output = array();
        if ($data != null) {
            $langs = array_keys($languageModel->getLanguages());
            foreach ($data as $item) {
                $mCoreContent->create();
                if ($mCoreContent->save($item)) {
                    $id = $mCoreContent->id;
                    foreach ($langs as $lKey) {
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

    private function addCoreContent($data) {
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $languageModel = MooCore::getInstance()->getModel('Language');
        if ($data != null) {
            $langs = array_keys($languageModel->getLanguages());
            foreach ($data as $item) {
                $mCoreContent->create();
                if ($mCoreContent->save($item)) {
                    $id = $mCoreContent->id;
                    foreach ($langs as $lKey) {
                        $mCoreContent->locale = $lKey;
                        $mCoreContent->id = $id;
                        $mCoreContent->saveField('core_block_title', $item['core_block_title']);
                    }
                }
            }
        }
    }
    
    public function uninstall(){
        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $mCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        
        //delete Mail template
    	$mails = $mailModel->find('all', array(
            'conditions' => array(
                'Mailtemplate.plugin' => 'Business',
            ),
            'fields' => array('Mailtemplate.id')
        ));
    	if ($mails != null)
    	{
            foreach($mails as $mail)
            {
                $mailModel->delete($mail['Mailtemplate']['id']);
            }   
        }
        
        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        foreach ($roles as $role) {
            $params = explode(',', $role['Role']['params']);
            $params = array_diff($params, array(
                'business_claim', 'business_create', 'business_view'
            ));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params' => implode(',', $params)));
        }
        
        $mAco = MooCore::getInstance()->getModel('Aco');
        $mAco->deleteAll(array(
            'Aco.group' => 'business'
        ));
        
        //Menu
        $menu = $menuModel->findByUrl('/businesses');
        $menuModel->deleteAll(array(
            'CoreMenuItem.url' => '/businesses'
        ));
        
        //delete setting
        $settingGroup = $mSettingGroup->findByModuleId('Business');
        if($settingGroup != null)
        {
            $mSetting->deleteAll(array(
                'Setting.group_id' => $settingGroup['SettingGroup']['id']
            ));
            $mSettingGroup->delete($settingGroup['SettingGroup']['id']);
        }
        
        //page, widget
        $coreContentIds = $mCoreContent->find("list", array(
            "conditions" => array("CoreContent.plugin" => "Business"),
            "fields" => array("CoreContent.id")
        ));
        $pageIds = $mPage->find("list", array(
            "conditions" => array("Page.alias" => array('business_index','business_categories','business_locations','business_search','business_view','business_create','business_dashboard')),
            "fields" => array("Page.id")
        ));
        $mCoreContent->deleteAll(array(
            'CoreContent.plugin' => 'Business'
        ));
        $mCoreBlock->deleteAll(array(
            'CoreBlock.plugin' => 'Business'
        ));
        $mPage->deleteAll(array(
            "Page.alias IN('business_index','business_categories','business_locations','business_search','business_view','business_create','business_dashboard')"
        ));
        
        //delete language
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18nModel->deleteAll(array(
            'I18nModel.model' => array('Business', 'BusinessCategory', 'BusinessType')
        ));
        $i18nModel->deleteAll(array(
            'I18nModel.content' => array(
                'Business Home', 'Business Create', 'Business Detail', 'Business Dashboard',
                'Business Breadcrumb', 'Business Menu Detail', 'Business Claim Business',
                'Business Social Media', 'Business Opening Hours', 'Business Checkins',
                'Business Reviews & Ratings', 'Business Menu', 'Business Search',
                'Business Locations', 'Business Categories', 'Featured Businesses',
                'All Businesses', 'Business Search', 'Business Map Preview',
                'Business Detail', 'Business Detail Section', 'Business Payment Gateways',
                'Business Detail Info', 'Business Dashboard Info', 'Business Dashboard Menu'
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
        if($menu != null)
        {
            $i18nModel->deleteAll(array(
                'I18nModel.model' => 'CoreMenuItem',
                'I18nModel.foreign_key' => $menu['CoreMenuItem']['id']
            ));
        }
        
        //delete activities
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $mActivity->deleteAll(array(
            "Activity.plugin" => 'Business'
        ));
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $mActivity->deleteAll(array(
            "Activity.type" => BUSINESS_ACTIVITY_TYPE
        ));
        
        //delete notification
        $mNotification = MooCore::getInstance()->getModel('Notification');
        $mNotification->deleteAll(array(
            "Notification.plugin" => "Business"
        ));
        
        //delete photos
        $mPhoto = MooCore::getInstance()->getModel('Photo');
        $mPhoto->deleteAll(array(
            "Photo.type" => "Business_Review"
        ));
        
        //delete albums
        $mAlbum = MooCore::getInstance()->getModel('Album');
        $mAlbum->deleteAll(array(
            "Album.type" => "business"
        ));
        
        //delete user tagging
        $mUserTagging = MooCore::getInstance()->getModel('UserTagging');
        $mUserTagging->deleteAll(array(
            "UserTagging.item_table" => "business_checkin"
        ));
		
		//delete s3
        $mStorageAwsTask = MooCore::getInstance()->getModel('Storage.StorageAwsTask');
        $mStorageAwsObjectMap = MooCore::getInstance()->getModel('Storage.StorageAwsObjectMap');
        
        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.type" => array("businesses", "business_verifies")
        ));
        
        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.name LIKE '%business/css%' OR StorageAwsTask.name LIKE '%business/images%' OR StorageAwsTask.name LIKE '%business/js%'"
        ));
        
        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.type" => array("businesses", "business_verifies")
        ));
        
        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.key LIKE '%business/css%' OR StorageAwsObjectMap.key LIKE '%business/images%' OR StorageAwsObjectMap.key LIKE '%business/js%'"
        ));
        
        //delete credit
        if(CakePlugin::loaded("Credit"))
        {
            $mCreditLog = MooCore::getInstance()->getModel('Credit.CreditLog');
            $mCreditActiontype = MooCore::getInstance()->getModel('Credit.CreditActiontype');
            $mCreditLog->deleteAll(array(
                'CreditLog.object_type' => array('Business_Business_Paid')
            ));
            $mCreditActiontype->deleteAll(array(
                'CreditActiontype.plugin' => "Business"
            ));
        }
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('business', 'Manage Businesses')  => array('plugin' => 'business', 'controller' => 'business', 'action' => 'admin_index'),
            __d('business', 'Business Settings')  => array('plugin' => 'business', 'controller' => 'business_settings', 'action' => 'admin_index'),
            __d('business', 'Locations') => array('plugin' => 'business', 'controller' => 'business_locations', 'action' => 'admin_index'),
            __d('business', 'Categories') => array('plugin' => 'business', 'controller' => 'business_categories', 'action' => 'admin_index'),
            __d('business', 'Business Types') => array('plugin' => 'business', 'controller' => 'business_types', 'action' => 'admin_index'),
            __d('business', 'Packages') => array('plugin' => 'business', 'controller' => 'business_packages', 'action' => 'admin_index'),
            __d('business', 'Transactions') => array('plugin' => 'business', 'controller' => 'business_transactions', 'action' => 'admin_index'),
            __d('business', 'Claim Requests') => array('plugin' => 'business', 'controller' => 'business_claims', 'action' => 'admin_index'),
            __d('business', 'Verification Requests') => array('plugin' => 'business', 'controller' => 'business_verifies', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
	public function callback_1_3($install = false){
		$mMailtemplate = MooCore::getInstance()->getModel('Mail.Mailtemplate');
		$mI18n = MooCore::getInstance()->getModel('I18nModel');
		$mail = $mMailtemplate->findByTypeAndPlugin('business_contact', 'Business');
		if($mail != null)
		{
			$content = 
'
    <p>[header]</p>
    <p>[name] sent you below message on <a href="[business_link]">[business_name]</a></p>
    <p>[message]</p>
    <p>Email: [sender_email]</p>
    <p>[footer]</p>
';
			$mMailtemplate->updateAll(array(
				'Mailtemplate.vars' => "'[name],[business_name],[message],[sender_email]'",
				'Mailtemplate.content' => "'$content'",
			), array(
				'Mailtemplate.id' => $mail['Mailtemplate']['id']
			));
			
			$mI18n->updateAll(array(
				'I18nModel.content' => "'$content'",
			), array(
				'I18nModel.model' => 'Mailtemplate',
				'I18nModel.foreign_key' => $mail['Mailtemplate']['id'],
				'I18nModel.field' => 'content'
			));
		}
	}
	
	public function callback_1_7($install = false){
		$mActivty = MooCore::getInstance()->getModel('Activity');
        $activities = $mActivty->find('all', array(
            'conditions' => array(
                'Activity.type' => 'user',
                'Activity.action' => 'business_create',
                'Activity.target_id > 0'
            )
        ));
        if($activities != null)
        {
            foreach($activities as $activity)
            {
                $activity = $activity['Activity'];
                $mActivty->updateAll(array(
                    'Activity.item_id' => $activity['target_id'],
                    'Activity.target_id' => 0
                ), array(
                    'Activity.id' => $activity['id']
                ));
            }
        }
	}
}