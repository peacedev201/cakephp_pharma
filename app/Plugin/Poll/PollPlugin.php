<?php 
App::uses('MooPlugin','Lib');
class PollPlugin implements MooPlugin{
    public function install(){
    	//Permission
    	$roleModel = MooCore::getInstance()->getModel('Role');
    	$roles = $roleModel->find('all');
    	$role_ids = array();
    	foreach ($roles as $role)
    	{
    		$role_ids[] = $role['Role']['id'];
    		$params = explode(',',$role['Role']['params']);
    		$params = array_unique(array_merge($params,array('poll_create','poll_view')));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}
    	
    	//Add Menu
		$languageModel = MooCore::getInstance()->getModel('Language');
    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
    	$menu = $menuModel->findByUrl('/polls');
    	if (!$menu)
    	{
    		$menuModel->clear();
    		$menuModel->save(array(
    			'role_access'=>json_encode($role_ids),
    			'name' => 'Polls',
    			'original_name' => 'Polls',
    			'url' => '/polls',
    			'type' => 'plugin',
    			'is_active' => 1,
    			'menu_order'=> 999,
    			'menu_id' => 1
    		));
    		/*$menuModel->id = $menu['CoreMenuItem']['id'];
    		$menuModel->save(array('role_access'=>json_encode($role_ids)));*/
			$menu = $menuModel->read();
    		$langs = array_keys($languageModel->getLanguages());
    		foreach ($langs as $lKey) {
    			$menuModel->locale = $lKey;
    			$menuModel->id = $menu['CoreMenuItem']['id'];
    			$menuModel->saveField('name', $menu['CoreMenuItem']['name']);
    		}
    	}
    	
    	//Add page
    	$pageModel = MooCore::getInstance()->getModel('Page.Page');
    	$blockModel = MooCore::getInstance()->getModel('CoreBlock');
    	$contentModel = MooCore::getInstance()->getModel('CoreContent');
    	$i18nModel = MooCore::getInstance()->getModel('I18nModel');
    	$languages = $languageModel->find('all');
    	
    	//add translate page
    	$pageModel->Behaviors->unload('Translate');
    	$pages = $pageModel->find('all',array(
    			'conditions' => array(
    					'uri' => array('polls.view','polls.index')
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
    	$languages = $tmp;
    	
    	//add block to browse page
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'poll.menu')
    	));
    	$block_menu_id = $block['CoreBlock']['id'];
    	 
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'poll.browse')
    	));
    	$block_browse_id = $block['CoreBlock']['id'];
    	 
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'poll.block')
    	));
    	$block_block_id = $block['CoreBlock']['id'];
    	 
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'poll.my')
    	));
    	$block_my_id = $block['CoreBlock']['id'];
    	 
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'poll.profile')
    	));
    	$block_profile_id = $block['CoreBlock']['id'];
    	 
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'core.tags')
    	));
    	$block_tag_id = $block['CoreBlock']['id'];
    	 
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'poll.tag')
    	));
    	$block_poll_tag_id = $block['CoreBlock']['id'];
    	
    	$browse_page = $pageModel->findByUri('polls.index');
    	if ($browse_page)
    	{
    		$page_id = $browse_page['Page']['id'];
    	
    		//insert west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'container',
    				'name' => 'west'
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
    				'name' => 'poll.menu',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Menu poll & Search","plugin":"Poll"}',
    				'plugin' => 'Poll',
    				'order' => 1,
    				'core_block_id' => $block_menu_id,
    				'core_block_title' => 'Menu poll & Search'
    		));
    		$menu = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $menu,
    					'field' => 'core_block_title',
    					'content' => 'Menu poll & Search'
    			));
    		}
    		//insert popular to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Block Poll","order_type":"popular","num_item_show":"4","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'order' => 2,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Popular Poll'
    		));
    		$popular_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $popular_id,
    					'field' => 'core_block_title',
    					'content' => 'Popular Poll'
    			));
    		}
    	
    		//insert top comment to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Block Poll","order_type":"Poll.comment_count desc","num_item_show":"10","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'order' => 3,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Top Comment Poll'
    		));
    		$comment_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $comment_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Poll Poll'
    			));
    		}
    	
    		//insert like to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Block Poll","order_type":"Poll.like_count desc","num_item_show":"10","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'order' => 4,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Top Like Poll'
    		));
    		$like_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $like_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Like Poll'
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
    	
    		//insert browse to center
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.browse',
    				'parent_id' => $center_id,
    				'params' => '{"title":"Browse Poll","plugin":"Poll"}',
    				'plugin' => 'Poll',
    				'order' => 5,
    				'core_block_id' => $block_browse_id,
    				'core_block_title' => 'Browse Poll'
    		));
    	
    		$browse_id = $contentModel->id;
    	
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $browse_id,
    					'field' => 'core_block_title',
    					'content' => 'Browse Poll'
    			));
    		}
    	
    		//insert east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'container',
    				'name' => 'east'
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
    	
    		//insert tag to east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'core.tags',
    				'parent_id' => $east_id,
    				'order' => 1,
    				'params' => '{"title":"Popular Tags","num_item_show":"10","type":"Poll_Poll","order_by":"newest","title_enable":"1"}',
    				'plugin' => '',
    				'core_block_id' => $block_tag_id,
    				'core_block_title' => 'Popular Tags'
    		));
    		$tag_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $tag_id,
    					'field' => 'core_block_title',
    					'content' => 'Popular Tags'
    			));
    		}
    		
    		//insert feature to east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $east_id,
    				'order' => 2,
    				'params' => '{"title":"Block Poll","order_type":"feature","num_item_show":"4","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Poll Feature'
    		));
    		$feature_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $feature_id,
    					'field' => 'core_block_title',
    					'content' => 'Poll Feature'
    			));
    		}
    	
    		//insert recent to east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $east_id,
    				'order' => 3,
    				'params' => '{"title":"Block Poll","order_type":"Poll.id desc","num_item_show":"4","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Recent Poll'
    		));
    		$recent_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $recent_id,
    					'field' => 'core_block_title',
    					'content' => 'Recent Poll'
    			));
    		}
    	
    		//insert view to east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $east_id,
    				'params' => '{"title":"Block Poll","order_type":"Poll.answer_count desc","num_item_show":"10","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'order' => 4,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Top Answer Poll'
    		));
    		$view_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $view_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Answer Poll'
    			));
    		}
    	}
    	
    	//Add block to detail page
    	$detail_page = $pageModel->findByUri('polls.view');
    	if ($detail_page)
    	{
    		$page_id = $detail_page['Page']['id'];
    	
    		//insert center
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'container',
    				'name' => 'center'
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
    	
    		//insert invisiblecontent to center
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'invisiblecontent',
    				'parent_id' => $center_id,
    				'order' => 1,
    				'params' => '{"title":"Poll\'s Content","maincontent":"1"}',
    				'plugin' => 'Poll',
    				'core_block_title' => 'Poll\'s Content'
    		));
    	
    		$invisiblecontent_id = $contentModel->id;
    	
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $invisiblecontent_id,
    					'field' => 'core_block_title',
    					'content' => 'Poll\'s Content'
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
    	
    		//insert detail tag to east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.tag',
    				'parent_id' => $east_id,
    				'order' => 1,
    				'params' => '{"title":"Poll tags","title_enable":"1","title_enable":"1"}',    				
    				'core_block_id' => $block_poll_tag_id,
    				'plugin' => 'Poll',
    				'core_block_title' => 'Poll tags'
    		));
    		$tag_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $tag_id,
    					'field' => 'core_block_title',
    					'content' => 'Poll tags'
    			));
    		}
    	
    		//insert feature east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $east_id,
    				'order' => 2,
    				'params' => '{"title":"Block Poll","order_type":"Poll.answer_count desc","num_item_show":"4","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Top Answer Poll'
    		));
    	
    		$feature_id = $contentModel->id;
    	
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $feature_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Answer Poll'
    			));
    		}
    	
    		//insert popular east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $east_id,
    				'order' => 3,
    				'params' => '{"title":"Block Poll","order_type":"popular","num_item_show":"4","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Popular Poll'
    		));
    	
    		$popular_id = $contentModel->id;
    	
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $popular_id,
    					'field' => 'core_block_title',
    					'content' => 'Popular Poll'
    			));
    		}
    	
    		//insert like east
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'poll.block',
    				'parent_id' => $east_id,
    				'order' => 4,
    				'params' => '{"title":"Block Poll","order_type":"Poll.like_count desc","num_item_show":"10","plugin":"Poll","title_enable":"1"}',
    				'plugin' => 'Poll',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Top Like Poll'
    		));
    	
    		$like_id = $contentModel->id;
    	
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $like_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Like Poll'
    			));
    		}
    	}
    	
    	//Category
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$categoryModel->save(array(
    			'type' => 'Poll',
    			'name' => 'Default Category',
    			'active' => 1,
    	));
    	$category_id = $categoryModel->id;
    	foreach (array_keys($languages) as $key)
    	{
    		$i18nModel->clear();
    		$i18nModel->save(array(
    				'locale' => $key,
    				'model' => 'Category',
    				'foreign_key' => $category_id,
    				'field' => 'name',
    				'content' => 'Default Category'
    		));
    	}
    	//Setting
    	$settingModel = MooCore::getInstance()->getModel('Setting');
    	$setting = $settingModel->findByName('poll_enabled');
    	if ($setting)
    	{
    		$settingModel->id = $setting['Setting']['id'];
    		$settingModel->save(array('is_boot'=>1));
    	}
    	
    	$setting = $settingModel->findByName('poll_consider_force');
    	if ($setting)
    	{
    		$settingModel->id = $setting['Setting']['id'];
    		$settingModel->save(array('is_boot'=>1));
    	}
    }
    
    public function callback_1_3()
    {
    	$pageModel = MooCore::getInstance()->getModel('Page.Page');
    	$i18nModel = MooCore::getInstance()->getModel('I18nModel');
    	$languageModel = MooCore::getInstance()->getModel('Language');
    	$languages = $languageModel->find('all');
    	 
    	//add translate page
    	$pageModel->Behaviors->unload('Translate');
    	$pages = $pageModel->find('all',array(
    			'conditions' => array(
    					'uri' => array('polls.view','polls.index')
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
    }
    
    public function uninstall(){
    	//Category
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$categories = $categoryModel->findAllByType('Poll');
    	foreach ($categories as $category)
    	{
    		$categoryModel->delete($category['Category']['id']);
    	}
    	 
    	//Menu
    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
    	$menu = $menuModel->findByUrl('/polls');
    	if ($menu)
    	{
    		$menuModel->delete($menu['CoreMenuItem']['id']);
    	}
    	
    	//Delete S3
    	$objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
    	$types = array('polls');
    	foreach ($types as $type)
    		$objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('poll','Poll Manager') => array('plugin' => 'poll', 'controller' => 'polls', 'action' => 'admin_index'),
        	__d('poll','Categories') => array('plugin' => 'poll', 'controller' => 'poll_categories', 'action' => 'admin_index'),
            __d('poll','Settings') => array('plugin' => 'poll', 'controller' => 'poll_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}