<?php 
App::uses('MooPlugin','Lib');
class ForumPlugin implements MooPlugin{
    public function install(){
    	//Setting
    	$settingModel = MooCore::getInstance()->getModel('Setting');
    	$setting = $settingModel->findByName('forum_enabled');
    	if ($setting)
    	{
    		$settingModel->id = $setting['Setting']['id'];
    		$settingModel->save(array('is_boot'=>1));
    	}
    	
    	$setting = $settingModel->findByName('forum_by_pass_force_login');
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
    		$params = array_unique(array_merge($params,array('forum_create','forum_view')));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}

        //Add Menu
        $languageModel = MooCore::getInstance()->getModel('Language');
        $pageModel = MooCore::getInstance()->getModel('Page.Page');
        $blockModel = MooCore::getInstance()->getModel('CoreBlock');
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');

        $languages = $languageModel->find('all');

        $menu = $menuModel->findByUrl('/forums');
        if (!$menu)
        {
            $menuModel->clear();
            $menuModel->save(array(
                'role_access'=>json_encode($role_ids),
                'name' => 'Forums',
                'original_name' => 'Forums',
                'url' => '/forums',
                'type' => 'plugin',
                'is_active' => 1,
                'menu_order'=> 999,
                'menu_id' => 1
            ));

            $menu = $menuModel->read();

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
                    'content' => 'Forums'
                ));
            }
        }

        //add translate page
        $pageModel->Behaviors->unload('Translate');
        $pages = $pageModel->find('all',array(
            'conditions' => array(
                'uri' => array('forum_topics.index', 'forum_topics.view', 'forum_topics.search', 'forums.view','forums.index')
            )
        ));

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
        $langs = $tmp;
        //add block
        $this->_languages = $langs;
        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'forum.my_contribution')
        ));
        $block_contribution_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'forum.tag')
        ));
        $block_tag_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first',array(
            'conditions' => array('CoreBlock.path_view' => 'forum.search_topic')
        ));
        $block_search_id = $block['CoreBlock']['id'];

        //add block forum topic
        $topic_page = $pageModel->findByUri('forum_topics.index');
        if ($topic_page)
        {
            $page_id = $topic_page['Page']['id'];
            //insert west
            $this->insertPostion($page_id, array(
                'west' => array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'forum.search_topic',
                        'params' => '{"title":"Forum Global Search","plugin":"Forum","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 2,
                        'core_block_id' => $block_search_id,
                        'core_block_title' => 'Forum Global Search'
                    ),
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'forum.my_contribution',
                        'params' => '{"title":"My Contribution","plugin":"Forum","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 3,
                        'core_block_id' => $block_contribution_id,
                        'core_block_title' => 'My Contribution'
                    ),
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'forum.tag',
                        'params' => '{"title":"Topic Tags","num_item_show":"10", "plugin":"Forum", "title_enable":"1", "role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 4,
                        'core_block_id' => $block_tag_id,
                        'core_block_title' => 'Topic Tags'
                    ),
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Forum menu","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Forum menu'
                    ),
                ),
                'center'=>array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Topic Browse","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Topic Browse'
                    )
                )
            ));

            // update core content count
            $contentModel = MooCore::getInstance()->getModel('CoreContent');
            $contentModel->updateAll(
                array('CoreContent.core_content_count' => '1'),
                array('CoreContent.page_id' => $page_id,'CoreContent.type'=>'container','CoreContent.name'=>'west')
            );
        }

        $topic_detail_page = $pageModel->findByUri('forum_topics.view');
        if ($topic_detail_page)
        {
            $page_id = $topic_detail_page['Page']['id'];

            $this->insertPostion($page_id, array(
                'center'=>array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Topic Detail","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Topic Detail'
                    )
                )
            ));
        }

        //add block forum search topic
        $topic_search_page = $pageModel->findByUri('forum_topics.search');
        if ($topic_search_page)
        {
            $page_id = $topic_search_page['Page']['id'];
            //insert west
            $this->insertPostion($page_id, array(
                'west' => array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'forum.my_contribution',
                        'params' => '{"title":"My Contribution","plugin":"Forum","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 2,
                        'core_block_id' => $block_contribution_id,
                        'core_block_title' => 'My Contribution'
                    ),
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Forum menu","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Forum menu'
                    ),
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'forum.tag',
                        'params' => '{"title":"Topic Tags","num_item_show":"10", "plugin":"Forum", "title_enable":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 3,
                        'core_block_id' => $block_tag_id,
                        'core_block_title' => 'Topic Tags'
                    ),
                ),
                'center'=>array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Topic Search Results","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Topic Search Results'
                    )
                )
            ));
        }

        $forum_detail_page = $pageModel->findByUri('forums.view');
        if ($forum_detail_page)
        {
            $page_id = $forum_detail_page['Page']['id'];

            $this->insertPostion($page_id, array(
                'center'=>array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Forum Detail","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Forum Detail'
                    )
                )
            ));
        }

        $forum_browse_page = $pageModel->findByUri('forums.index');
        if ($forum_browse_page)
        {
            $page_id = $forum_browse_page['Page']['id'];

            $this->insertPostion($page_id, array(
                'west' => array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Forum menu","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Forum menu'
                    ),
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'forum.search_topic',
                        'params' => '{"title":"Forum Global Search","plugin":"Forum","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 2,
                        'core_block_id' => $block_search_id,
                        'core_block_title' => 'Forum Global Search'
                    ),
                ),
                'center'=>array(
                    array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'invisiblecontent',
                        'params' => '{"title":"Forum Browse","maincontent":"1","role_access":"all"}',
                        'plugin' => 'Forum',
                        'order' => 1,
                        'core_block_id' => 0,
                        'core_block_title' => 'Forum Browse'
                    )
                )
            ));
        }

        //Category
        $tmp = array();
        foreach ($languages as $language)
        {
            $tmp[$language['Language']['key']] = $language;
        }
        $langs = $tmp;
        foreach (array_keys($langs) as $key)
        {
            $i18nModel->clear();
            $i18nModel->save(array(
                'locale' => $key,
                'model' => 'ForumCategory',
                'foreign_key' => 1,
                'field' => 'name',
                'content' => 'Forums'
            ));
        }

        //Mail template
        $mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $langs = $languageModel->find('all');
        $data['Mailtemplate'] = array(
            'type' => 'forum_topic_invite_none_member',
            'plugin' => 'Forum',
            'vars' => '[email],[sender_title],[sender_link],[link],[topic_title]'
        );
        $mailModel->save($data);
        $id = $mailModel->id;
        foreach ($langs as $lang)
        {
            $language = $lang['Language']['key'];
            $mailModel->locale = $language;
            $data_translate['subject'] = 'You have been invited to join the topic [topic_title]';
            $content = <<<EOF
		    <p>[header]</p>
			<p>You have been invited to join the topic "[topic_title]". Please click the following link to view it:</p>
            <p><a href="[link]">[topic_title]</a></p>
			<p>[footer]</p>
EOF;
            $data_translate['content'] = $content;
            $mailModel->save($data_translate);
        }
        
        $mailModel->clear();
        $data['Mailtemplate'] = array(
        		'type' => 'forum_ping_topic',
        		'plugin' => 'Forum',
        		'vars' => '[topic_title],[topic_link]'
        );
        $mailModel->save($data);
        $id = $mailModel->id;
        foreach ($langs as $lang)
        {
        	$language = $lang['Language']['key'];
        	$mailModel->locale = $language;
        	$data_translate['subject'] = 'Your topic is pinned';
        	$content = <<<EOF
		    <p>[header]</p>
			<p>Your topic now is pinned on top. Please check more details here</p>
            <p><a href="[topic_link]">[topic_title]</a></p>
			<p>[footer]</p>
EOF;
        	$data_translate['content'] = $content;
        	$mailModel->save($data_translate);
        }
        
    }
    public function uninstall(){
        //Mail
        $mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $mail = $mailModel->findByType('forum_topic_invite_none_member');
        if ($mail)
        {
            $mailModel->delete($mail['Mailtemplate']['id']);
        }
        
        $mail = $mailModel->findByType('forum_ping_topic');
        if ($mail)
        {
        	$mailModel->delete($mail['Mailtemplate']['id']);
        }

        //Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/forums');
        if ($menu)
        {
            $menuModel->delete($menu['CoreMenuItem']['id']);
        }

        //Delete S3
        $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
        $types = array('forum_topic_thumb','forum_files','forum_thumb','forum_category_thumb');
        foreach ($types as $type)
            $objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);

        $userModel = MooCore::getInstance()->getModel("User");
        $userModel->getDataSource()->flushMethodCache();
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18nModel->deleteAll(array('I18nModel.model' => 'ForumCategory'),false);
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('forum','Forums Manager') => array('plugin' => 'forum', 'controller' => 'forums', 'action' => 'admin_index'),
            __d('forum', 'Reports') => array('plugin' => 'forum', 'controller' => 'forum_reports', 'action' => 'admin_index'),
            __d('forum','Settings') => array('plugin' => 'forum', 'controller' => 'forum_settings', 'action' => 'admin_index'),
        );
    }

    protected $_languages = null;
    public function insertPostion($page_id,$items)
    {
        $contentModel = MooCore::getInstance()->getModel('CoreContent');
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $languageModel = MooCore::getInstance()->getModel('Language');

        if (!$this->_languages)
        {
            $languages = $languageModel->find('all');
            $tmp = array();
            foreach ($languages as $language)
            {
                if ($language['Language']['key'] == Configure::read('Config.language'))
                    continue;

                $tmp[$language['Language']['key']] = $language;
            }
            $languages = $tmp;
            $this->_languages = $languages;
        }
        else
            $languages = $this->_languages;

        foreach ($items as $type=>$datas)
        {
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => $type,
            ));
            $type_id = $contentModel->id;
            foreach (array_keys($languages) as $key)
            {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $type_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }

            foreach ($datas as $data)
            {
                //insert menu to west
                $data['parent_id'] = $type_id;
                $contentModel->clear();
                $contentModel->save($data);
                $content_id = $contentModel->id;
                foreach (array_keys($languages) as $key)
                {
                    $i18nModel->clear();
                    $i18nModel->save(array(
                        'locale' => $key,
                        'model' => 'CoreContent',
                        'foreign_key' => $content_id,
                        'field' => 'core_block_title',
                        'content' => $data['core_block_title']
                    ));
                }
            }
        }
    }

    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}