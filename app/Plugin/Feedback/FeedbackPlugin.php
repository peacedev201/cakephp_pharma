<?php 
App::uses('MooPlugin','Lib');
class FeedbackPlugin implements MooPlugin{
    public function install()
    {   
        //menu
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mMenu->findByUrl('/feedbacks');
        if ($menu)
        {   
            $mMenu->id = $menu['CoreMenuItem']['id'];
            $mMenu->save(array(
                'name' => 'Feedback',
                'url' => '/feedbacks'
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
        $setting = $mSetting->findByName('feedback_enabled');
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
                    'feedback_create_feedback', 'feedback_view_feedback_listing', 'feedback_can_upload_photo',
                    'feedback_approve_feedback', 'feedback_delete_own_feedback',
                    'feedback_delete_all_feedbacks', 'feedback_edit_all_feedbacks', 'feedback_edit_own_feedback',
                    'feedback_set_status'
                );
            }
            else
            {
                $permission = array(
                    'feedback_create_feedback', 'feedback_view_feedback_listing', 'feedback_can_upload_photo',
                    'feedback_delete_own_feedback', 'feedback_edit_own_feedback'
                );
            }
    		$params = array_unique(array_merge($params, $permission));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}
        
        //Mail template create feedback
        $content = 
'
    <p>[header]</p>
    <p>[username] has submit a feedback to your site. Please follow this link to see detail:</p>
    <p><a href="[link]">[link]</a><p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('feedback_create', '[username],[link]', '[username] submit a new feedback to your site', $content);
        
        //Mail template approve feedback
        $content = 
'
    <p>[header]</p>
    <p>[username] approved your feedback. Please follow this link to see detail:</p>
    <p><a href="[link]">[link]</a><p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('feedback_approve', '[username],[link]', '[username] approved your feedback', $content);
        
        //Mail template change feedback status
        $content = 
'
    <p>[header]</p>
    <p>[username] changed your feedback to [status]</p>
    <p>Message: [message]</p>
    <p>Click <a href="[link]">here</a> to see detail.<p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('feedback_change_status', '[username],[status],[message],[link]', 'Your feedback\'s status has been changed', $content);
        
        //Mail template comment feedback
        $content = 
'
    <p>[header]</p>
    <p>[username] commented on your feedback: [message]</p>
    <p>Click <a href="[link]">here</a> to see detail<p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('feedback_comment', '[username],[message],[link]', '[username] commented on your feedback', $content);
        
        //Add page
        $pageData[] = array(
            'title' => 'Feedback Page',
            'alias' => 'feedbacks_index',
            'url' => '/feedback/',
            'uri' => 'feedbacks.index',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageData[] = array(
            'title' => 'Feedback Detail Page',
            'alias' => 'feedbacks_view',
            'url' => '/feedback/feedbacks/view/$id',
            'uri' => 'feedbacks.view',
            'type' => 'plugin',
            'layout' => 2
        );
        $pageOutput = $this->addPage($pageData);
        
        //add core block
        $blockData[] = array(
            'name' => 'Feedback Most Voted',
            'path_view' => 'feedback.most_voted_feedback',
            'params' => '{"0":{"label":"Title","input":"text","value":"Feedback Most Voted","name":"title"},"1":{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},"2":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"3":{"label":"plugin","input":"hidden","value":"Feedback","name":"plugin"}}',
            'is_active' => 1,
            'group' => 'Feedbacks',
            'plugin' => 'Feedback',
        );
        $blockOutput = $this->addCoreBlock($blockData);
        
        //add core content east west
        $contentParentData[] = array(
            'page_id' => $pageOutput['feedbacks_index'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentData[] = array(
            'page_id' => $pageOutput['feedbacks_view'],
            'type' => 'container',
            'name' => 'west',
        );
        $contentParentOutput = $this->addCoreContentParent($contentParentData);
        
        //add core content
        $contentData[] = array(
            'page_id' => $pageOutput['feedbacks_index'],
            'parent_id' => $contentParentOutput[$pageOutput['feedbacks_index'].'west'],
            'core_block_id' => 1,
            'type' => 'widget',
            'name' => 'invisiblecontent',
            'order' => 1,
            'params' => '{"title":"Addons Search Block","maincontent":"1"}',
            'core_block_title' => 'Feedback menu & Search',
            'plugin' => ''
        );
        $contentData[] = array(
            'page_id' => $pageOutput['feedbacks_index'],
            'parent_id' => $contentParentOutput[$pageOutput['feedbacks_index'].'west'],
            'core_block_id' => $blockOutput['Feedback Most Voted'],
            'type' => 'widget',
            'name' => 'feedback.most_voted_feedback',
            'order' => 2,
            'params' => '{"title":"Feedback Most Voted","num_item_show":"5","title_enable":"1","plugin":"Feedback"}',
            'core_block_title' => 'Feedback Most Voted',
            'plugin' => 'Feedback'
        );
        $contentData[] = array(
            'page_id' => $pageOutput['feedbacks_view'],
            'parent_id' => $contentParentOutput[$pageOutput['feedbacks_view'].'west'],
            'core_block_id' => 1,
            'type' => 'widget',
            'name' => 'invisiblecontent',
            'order' => 1,
            'params' => '{"title":"Addons Search Block","maincontent":"1"}',
            'core_block_title' => 'Feedback menu detail',
            'plugin' => ''
        );
        $this->addCoreContent($contentData);
        
        // update core content count
        $data[] = array(
            'core_content_count' => 2,
            'CoreContent.page_id' => $pageOutput['feedbacks_index'], 
            'CoreContent.type' => 'container', 
            'CoreContent.name' => 'west'
        );
        $data[] = array(
            'core_content_count' => 1,
            'CoreContent.page_id' => $pageOutput['feedbacks_view'], 
            'CoreContent.type' => 'container', 
            'CoreContent.name' => 'west'
        );
        $this->updateCoreContentCounter($data);
        
        $setting = $mSetting->findByName('feedback_consider_force');
        if ($setting)
        {
        	$mSetting->id = $setting['Setting']['id'];
        	$mSetting->save(array('is_boot'=>1));
        }
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
    
    private function updateCoreContentCounter($data)
    {
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        if($data != null)
        {
            foreach($data as $item)
            {
                $count = $item['core_content_count'];
                unset($item['core_content_count']);
                $mCoreContent->clear();
                $mCoreContent->updateAll(array(
                    'CoreContent.core_content_count' => $count
                ), $item);
            }
        }
    }
    
    private function createMailTemplate($type, $vars, $subject, $content)
    {
        $languageModel = MooCore::getInstance()->getModel('Language');
     	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $langs = $languageModel->find('all');
        $data['Mailtemplate'] = array(
            'type' => $type,
            'plugin' => 'Feedback',
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
        $menu = $mMenu->findByUrl('/feedback');
        if ($menu)
        {
            $mMenu->delete($menu['CoreMenuItem']['id']);
        }
        
        //delete setting
        $settingGroup = $mSettingGroup->findByModuleId('Feedback');
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
                    'feedback_create_feedback', 'feedback_view_feedback_listing', 'feedback_can_upload_photo',
                    'feedback_approve_feedback_before_public', 'feedback_approve_feedback', 'feedback_delete_own_feedback',
                    'feedback_delete_all_feedbacks', 'feedback_edit_all_feedbacks', 'feedback_edit_own_feedback',
                    'feedback_set_status'
                ));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	} 
        
        //delete Mail template
    	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
    	$mails = $mailModel->find('all', array(
            'conditions' => array(
                'Mailtemplate.plugin' => 'Feedback',
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
        
        //page, widget
        $coreContentIds = $mCoreContent->find("list", array(
            "conditions" => array("CoreContent.plugin" => "Feedback"),
            "fields" => array("CoreContent.id")
        ));
        $pageIds = $mPage->find("list", array(
            "conditions" => array("Page.alias" => array('feedbacks_index','feedbacks_view')),
            "fields" => array("Page.id")
        ));
        $mCoreContent->deleteAll(array(
            'CoreContent.plugin' => 'Feedback'
        ));
        $mCoreBlock->deleteAll(array(
            'CoreBlock.plugin' => 'Feedback'
        ));
        $mPage->deleteAll(array(
            "Page.alias IN('feedbacks_index', 'feedbacks_view')"
        ));

        //delete language
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18nModel->deleteAll(array(
            'I18nModel.model' => array('Feedback', 'FeedbackCategory', 'FeedbackSeverity', 'FeedbackStatus')
        ));
        $i18nModel->deleteAll(array(
            'I18nModel.content' => array(
                'Feedback Page', 'Feedback Detail Page', 'Feedback Most Voted', 'Feedback menu & Search', 'Feedback menu detail'
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
        
        //delete activity
        $activityModel = MooCore::getInstance()->getModel('Activity');
        $activityModel->deleteAll(array('Activity.item_type' => 'Feedback_Feedback'));
        
        //Delete S3
        $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
	$type = 'feedbacks';
        $objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
        
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('feedback', 'Feedback') => array('plugin' => 'feedback', 'controller' => 'feedbacks', 'action' => 'admin_index'),
            __d('feedback', 'Categories') => array('plugin' => 'feedback', 'controller' => 'feedback_categories', 'action' => 'admin_index'),
            __d('feedback', 'Severities') => array('plugin' => 'feedback', 'controller' => 'feedback_severities', 'action' => 'admin_index'),
            __d('feedback', 'Statuses') => array('plugin' => 'feedback', 'controller' => 'feedback_statuses', 'action' => 'admin_index'),
            __d('feedback', 'Block Users') => array('plugin' => 'feedback', 'controller' => 'feedback_blockusers', 'action' => 'admin_index'),
            __d('feedback', 'Block IP Addresses') => array('plugin' => 'feedback', 'controller' => 'feedback_blockips', 'action' => 'admin_index'),
            __d('feedback', 'Statistics') => array('plugin' => 'feedback', 'controller' => 'feedback_statistics', 'action' => 'admin_index'),
            __d('feedback', 'Settings') => array('plugin' => 'feedback', 'controller' => 'feedback_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
    
    public function callback_1_7()
    {
        $mPage = MooCore::getInstance()->getModel('Page.Page');
        $mCoreContent = MooCore::getInstance()->getModel('CoreContent');
        $mCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $langs = array_keys($languageModel->getLanguages());

        // translate menu
        $menu = $mMenu->findByUrl('/feedbacks');
        if ($menu)
        {   
            //add language for menu
            foreach ($langs as $lKey) {
                $mMenu->locale = $lKey;
                $mMenu->id = $menu['CoreMenuItem']['id'];
                $mMenu->saveField('name', $menu['CoreMenuItem']['name']);
            }
        }

        //translate page
        $pages = $mPage->find("list", array(
            "conditions" => array("Page.alias" => array('feedbacks_index','feedbacks_view')),
            "fields" => array("Page.title")
        ));

        if($pages != null)
        {
            foreach($pages as $key => $item)
            {
                foreach ($langs as $lKey) {
                    $mPage->locale = $lKey;
                    $mPage->id = $key;
                    $mPage->saveField('title', $item);
                    $mPage->saveField('content', "");
                }
            }
        }

        //translate core block
        $coreBlocks = $mCoreBlock->find("list", array(
            "conditions" => array("CoreBlock.plugin" => "Feedback"),
            "fields" => array("CoreBlock.name")
        ));
        if($coreBlocks != null)
        {
            foreach($coreBlocks as $key => $item)
            {
                foreach ($langs as $lKey) {
                    $mCoreBlock->locale = $lKey;
                    $mCoreBlock->id = $key;
                    $mCoreBlock->saveField('name', $item);
                }
            }
        }

        //translate core content
        $coreContents = $mCoreContent->find("list", array(
            "conditions" => array("CoreContent.plugin" => "Feedback"),
            "fields" => array("CoreContent.core_block_title")
        ));
        if($coreContents != null)
        {
            foreach($coreContents as $key => $item)
            {
                foreach ($langs as $lKey) {
                    $mCoreContent->locale = $lKey;
                    $mCoreContent->id = $key;
                    $mCoreContent->saveField('core_block_title', $item);
                }
            }
        }

        //translate category
        $mFeedbackCategory = MooCore::getInstance()->getModel('Feedback.FeedbackCategory');
        $mFeedbackCategory->Behaviors->disable('Translate');
        $categories = $mFeedbackCategory->find("list", array(
            "fields" => array("FeedbackCategory.name")
        ));
        if($categories != null)
        {
            $mFeedbackCategory->Behaviors->enable('Translate');
            foreach($categories as $key => $category)
            {
                foreach ($langs as $lKey) {
                    $mFeedbackCategory->locale = $lKey;
                    $mFeedbackCategory->id = $key;
                    $mFeedbackCategory->saveField('name', $category);
                }
            }
        }

        //translate severity
        $mFeedbackSeverity = MooCore::getInstance()->getModel('Feedback.FeedbackSeverity');
        $mFeedbackSeverity->Behaviors->disable('Translate');
        $severities = $mFeedbackSeverity->find("list", array(
            "fields" => array("FeedbackSeverity.name")
        ));
        if($severities != null)
        {
            $mFeedbackSeverity->Behaviors->enable('Translate');
            foreach($severities as $key => $severity)
            {
                foreach ($langs as $lKey) {
                    $mFeedbackSeverity->locale = $lKey;
                    $mFeedbackSeverity->id = $key;
                    $mFeedbackSeverity->saveField('name', $severity);
                }
            }
        }

        //translate status
        $mFeedbackStatus = MooCore::getInstance()->getModel('Feedback.FeedbackStatus');
        $mFeedbackStatus->Behaviors->disable('Translate');
        $statuss = $mFeedbackStatus->find("list", array(
            "fields" => array("FeedbackStatus.name")
        ));
        if($statuss != null)
        {
            $mFeedbackStatus->Behaviors->enable('Translate');
            foreach($statuss as $key => $status)
            {
                foreach ($langs as $lKey) {
                    $mFeedbackStatus->locale = $lKey;
                    $mFeedbackStatus->id = $key;
                    $mFeedbackStatus->saveField('name', $status);
                }
            }
        }
    }
}