<?php 
App::uses('MooPlugin','Lib');
class QuestionPlugin implements MooPlugin{
    public function install(){
    	//Permission
    	$roleModel = MooCore::getInstance()->getModel('Role');
    	$roles = $roleModel->find('all');
    	$role_ids = array();
    	foreach ($roles as $role)
    	{
    		$role_ids[] = $role['Role']['id'];
    		$params = explode(',',$role['Role']['params']);
    		$params = array_unique(array_merge($params,array('question_create','question_view')));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}
    	
    	$languageModel = MooCore::getInstance()->getModel('Language');
    	$i18nModel = MooCore::getInstance()->getModel('I18nModel');
    	$languages = $languageModel->find('all');
    	
    	//Add Menu
    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
    	$menu = $menuModel->findByUrl('/questions');
    	if (!$menu)
    	{
    		$menuModel->clear();
    		$menuModel->save(array(
    				'role_access'=>json_encode($role_ids),
    				'name' => 'Questions',
    				'original_name' => 'Questions',
    				'url' => '/questions',
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
    	
    	//add translate page
    	$pageModel = MooCore::getInstance()->getModel('Page.Page');
    	$pageModel->Behaviors->unload('Translate');
    	$pages = $pageModel->find('all',array(
    			'conditions' => array(
    					'uri' => array('questions.index','questions.badges','questions.view','questions.ratings','question_tags.index')
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
    	
    	//Category
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$categoryModel->save(array(
    			'type' => 'Question',
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
    	
    	//Mail template
    	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
    	$langs = $languageModel->find('all');
    	$data['Mailtemplate'] = array(
    			'type' => 'question_approve',
    			'plugin' => 'Question',
    			'vars' => '[question_link],[question_title]'
    	);
    	$mailModel->save($data);
    	$id = $mailModel->id;
    	foreach ($langs as $lang)
    	{
    		$language = $lang['Language']['key'];
    		$mailModel->locale = $language;
    		$data_translate['subject'] = 'Question Approve';
    		$content = <<<EOF
		    <p>[header]</p>
			<p>Your <a href="[question_link]">[question_title]</a> has been approved.</p>
			<p>[footer]</p>
EOF;
    		$data_translate['content'] = $content;
    		$mailModel->save($data_translate);
    	}
    	
    	//Setting
    	$settingModel = MooCore::getInstance()->getModel('Setting');
    	$setting = $settingModel->findByName('question_enabled');
    	if ($setting)
    	{
    		$settingModel->id = $setting['Setting']['id'];
    		$settingModel->save(array('is_boot'=>1));
    	}
    	
    	$setting = $settingModel->findByName('question_consider_force');
    	if ($setting)
    	{
    		$settingModel->id = $setting['Setting']['id'];
    		$settingModel->save(array('is_boot'=>1));
    	}
    	
    	//Add page
    	$blockModel = MooCore::getInstance()->getModel('CoreBlock');
    	$contentModel = MooCore::getInstance()->getModel('CoreContent');
    	    	
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.menu')
    	));
    	$block_menu_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.browse')
    	));
    	$block_browse_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.block')
    	));
    	$block_block_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.top_users')
    	));
    	$block_top_users_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.tags')
    	));
    	$block_tags_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.browse_tags')
    	));
    	$block_browse_tags_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.top_point_users')
    	));
    	$block_top_point_users_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.top_answer_users')
    	));
    	$block_top_answer_users_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.browse_badges')
    	));
    	$block_browse_badges_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.how_do_collect_points')
    	));
    	$block_how_do_collect_points_id = $block['CoreBlock']['id'];    
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.browse_ratings')
    	));
    	$block_browse_ratings_id = $block['CoreBlock']['id'];
    	$block = $blockModel->find('first',array(
    			'conditions' => array('CoreBlock.path_view' => 'question.related')
    	));
    	$block_related_id = $block['CoreBlock']['id'];
    	
    	//add block to browse page
    	$browse_page = $pageModel->findByUri('questions.index');
    	if ($browse_page)
    	{
    		$page_id = $browse_page['Page']['id'];
    		
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
    				'name' => 'question.browse',
    				'parent_id' => $center_id,
    				'params' => '{"title":"Browse Question","plugin":"Question"}',
    				'plugin' => 'Question',
    				'order' => 1,
    				'core_block_id' => $block_browse_id,
    				'core_block_title' => 'Browse Question'
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
    					'content' => 'Browse Question'
    			));
    		}
    		
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
    				'name' => 'question.menu',
    				'parent_id' => $west_id,
    				'order' => 1,
    				'params' => '{"title":"Menu question","plugin":"Question"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_menu_id,
    				'core_block_title' => 'Menu question'
    		));
    		$menu_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $menu_id,
    					'field' => 'core_block_title',
    					'content' => 'Menu question'
    			));
    		}
    		
    		//insert feature to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Featured Questions","order_type":"feature","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 2,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Featured Questions'
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
    					'content' => 'Featured Questions'
    			));
    		}
    		
    		//insert top users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_users',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Top Question Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 3,
    				'core_block_id' => $block_top_users_id,
    				'core_block_title' => 'Top Question Contributors'
    		));
    		$top_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Question Contributors'
    			));
    		}
    		
    		//insert tag to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.tags',
    				'parent_id' => $west_id,
    				'order' => 4,
    				'params' => '{"title":"Popular Question Tag","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_tags_id,
    				'core_block_title' => 'Popular Question Tag'
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
    					'content' => 'Popular Question Tag'
    			));
    		}
    		
    		//insert top point users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_point_users',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Top Q&A Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 5,
    				'core_block_id' => $block_top_point_users_id,
    				'core_block_title' => 'Top Q&A Contributors'
    		));
    		$top_point_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_point_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Q&A Contributors'
    			));
    		}
    		
    		//insert top answer users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_answer_users',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Top Answer Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 6,
    				'core_block_id' => $block_top_answer_users_id,
    				'core_block_title' => 'Top Answer Contributors'
    		));
    		$top_top_answer_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_top_answer_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Answer Contributors'
    			));
    		}
    		
    		//insert collect point to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.how_do_collect_points',
    				'parent_id' => $west_id,
    				'params' => '{"title":"How do I get points?","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 7,
    				'core_block_id' => $block_how_do_collect_points_id,
    				'core_block_title' => 'How do I get points?'
    		));
    		$top_how_do_collect_points_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_how_do_collect_points_id,
    					'field' => 'core_block_title',
    					'content' => 'How do I get points?'
    			));
    		}
    		
    		//insert top vote to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Highest Voted Questions","order_type":"Question.vote_count desc","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 8,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Highest Voted Questions'
    		));
    		$top_vote_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_vote_id,
    					'field' => 'core_block_title',
    					'content' => 'Highest Voted Questions'
    			));
    		}    		
    	}
    	
    	//add block to badges page
    	$badges_page = $pageModel->findByUri('questions.badges');
    	if ($badges_page)
    	{
    		$page_id = $badges_page['Page']['id'];
    		
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
    				'name' => 'question.browse_badges',
    				'parent_id' => $center_id,
    				'params' => '{"title":"Browse Badges Question","plugin":"Question"}',
    				'plugin' => 'Question',
    				'order' => 1,
    				'core_block_id' => $block_browse_badges_id,
    				'core_block_title' => 'Browse Badges Question'
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
    					'content' => 'Browse Badges Question'
    			));
    		}
    		
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
    				'name' => 'question.menu',
    				'parent_id' => $west_id,
    				'order' => 2,
    				'params' => '{"title":"Menu question","plugin":"Question"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_menu_id,
    				'core_block_title' => 'Menu question'
    		));
    		$menu_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $menu_id,
    					'field' => 'core_block_title',
    					'content' => 'Menu question'
    			));
    		}
    		
    		//insert top point users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_point_users',
    				'parent_id' => $west_id,
    				'order' => 3,
    				'params' => '{"title":"Top Q&A Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_top_point_users_id,
    				'core_block_title' => 'Top Q&A Contributors'
    		));
    		$top_point_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_point_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Q&A Contributors'
    			));
    		}
    		
    		//insert recent to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'order' => 4,
    				'params' => '{"title":"Recent Questions","order_type":"Question.id desc","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Recent Questions'
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
    					'content' => 'Recent Questions'
    			));
    		}
    		
    		//insert top share to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'order' => 5,
    				'params' => '{"title":"Most Shared Questions","order_type":"Question.share_count desc","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Most Shared Questions'
    		));
    		$top_share_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_share_id,
    					'field' => 'core_block_title',
    					'content' => 'Most Shared Questions'
    			));
    		}
    		
    		//insert top answer users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_answer_users',
    				'parent_id' => $west_id,
    				'order' => 6,
    				'params' => '{"title":"Top Answer Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_top_answer_users_id,
    				'core_block_title' => 'Top Answer Contributors'
    		));
    		$top_top_answer_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_top_answer_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Question Top Answer Users'
    			));
    		}
    	}
    	
    	//add block to tags page
    	$tags_page = $pageModel->findByUri('question_tags.index');
    	if ($tags_page)
    	{
    		$page_id = $tags_page['Page']['id'];
    	
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
    				'name' => 'question.browse_tags',
    				'parent_id' => $center_id,
    				'order' => 1,
    				'params' => '{"title":"Most Popular Q&A Tags","plugin":"Question"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_browse_tags_id,
    				'core_block_title' => 'Most Popular Q&A Tags'
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
    					'content' => 'Most Popular Q&A Tags'
    			));
    		}
    		
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
    		
    		//insert top point users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_point_users',
    				'parent_id' => $west_id,
    				'order' => 2,
    				'params' => '{"title":"Top Q&A Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_top_point_users_id,
    				'core_block_title' => 'Top Q&A Contributors'
    		));
    		$top_point_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_point_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Q&A Contributors'
    			));
    		}
    		
    		//insert popular to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'order' => 3,
    				'parent_id' => $west_id,
    				'params' => '{"title":"Most Popular Questions","order_type":"popular","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Most Popular Questions'
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
    					'content' => 'Most Popular Questions'
    			));
    		}
    		
    		//insert top favorite to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'order' => 4,
    				'params' => '{"title":"Most Favorited Questions","order_type":"Question.favorite_count desc","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Most Favorited Questions'
    		));
    		$top_favorite_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_favorite_id,
    					'field' => 'core_block_title',
    					'content' => 'Most Favorited Questions'
    			));
    		}
    		
    		//insert collect point to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.how_do_collect_points',
    				'parent_id' => $west_id,
    				'order' => 5,
    				'params' => '{"title":"How do I get points?","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_how_do_collect_points_id,
    				'core_block_title' => 'How do I get points?'
    		));
    		$top_how_do_collect_points_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_how_do_collect_points_id,
    					'field' => 'core_block_title',
    					'content' => 'How do I get points?'
    			));
    		}
    	}
    	
    	//add block to ratings page
    	$tags_page = $pageModel->findByUri('questions.ratings');
    	if ($tags_page)
    	{
    		$page_id = $tags_page['Page']['id'];
    		 
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
    				'name' => 'question.browse_ratings',
    				'parent_id' => $center_id,
    				'order' => 1,
    				'params' => '{"title":"Browse Ratings Question","plugin":"Question"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_browse_tags_id,
    				'core_block_title' => 'Browse Ratings Question'
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
    					'content' => 'Browse Ratings Question'
    			));
    		}
    		
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
    				'name' => 'question.menu',
    				'order' => 1,
    				'parent_id' => $west_id,
    				'params' => '{"title":"Menu question","plugin":"Question"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_menu_id,
    				'core_block_title' => 'Menu question'
    		));
    		$menu_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $menu_id,
    					'field' => 'core_block_title',
    					'content' => 'Menu question'
    			));
    		}
    		
    		//insert top view to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'order' => 2,
    				'params' => '{"title":"Highest Viewed Questions","order_type":"Question.view_count desc","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Highest Viewed Questions'
    		));
    		$top_view_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_view_id,
    					'field' => 'core_block_title',
    					'content' => 'Highest Viewed Questions'
    			));
    		}
    		
    		//insert popular to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Most Popular Questions","order_type":"popular","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 3,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Most Popular Questions'
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
    					'content' => 'Most Popular Questions'
    			));
    		}
    		
    		//insert top answer users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_answer_users',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Top Answer Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 4,
    				'core_block_id' => $block_top_answer_users_id,
    				'core_block_title' => 'Top Answer Contributors'
    		));
    		$top_top_answer_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_top_answer_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Answer Contributors'
    			));
    		}
    		
    		//insert collect point to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.how_do_collect_points',
    				'parent_id' => $west_id,
    				'params' => '{"title":"How do I get points?","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 5,
    				'core_block_id' => $block_how_do_collect_points_id,
    				'core_block_title' => 'How do I get points?'
    		));
    		$top_how_do_collect_points_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_how_do_collect_points_id,
    					'field' => 'core_block_title',
    					'content' => 'How do I get points?'
    			));
    		}
    		
    		//insert top answer to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'params' => '{"title":"Most Answered Questions","order_type":"Question.answer_count desc","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'order' => 6,
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Most Answered Questions'
    		));
    		$top_answer_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_answer_id,
    					'field' => 'core_block_title',
    					'content' => 'Most Answered Questions'
    			));
    		}
    	}
    	
    	//add block to detail page
    	$view_page = $pageModel->findByUri('questions.view');
    	if ($view_page)
    	{
    		$page_id = $view_page['Page']['id'];
    		 
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
    		
    		//insert invisiblecontent to center
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'invisiblecontent',
    				'parent_id' => $center_id,
    				'params' => '{"title":"Question Content","maincontent":"1"}',
    				'plugin' => 'Question',
    				'order' => 1,
    				'core_block_title' => 'Question Content'
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
    					'content' => 'Question Content'
    			));
    		}
    		
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
    		
    		//insert top point users to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.top_point_users',
    				'parent_id' => $west_id,
    				'order' => 1,
    				'params' => '{"title":"Top Q&A Contributors","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_top_point_users_id,
    				'core_block_title' => 'Top Q&A Contributors'
    		));
    		$top_point_users_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_point_users_id,
    					'field' => 'core_block_title',
    					'content' => 'Top Q&A Contributors'
    			));
    		}
    		
    		//insert top view to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.block',
    				'parent_id' => $west_id,
    				'order' => 2,
    				'params' => '{"title":"Highest Viewed Questions","order_type":"Question.view_count desc","num_item_show":"10","plugin":"Question","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_block_id,
    				'core_block_title' => 'Highest Viewed Questions'
    		));
    		$top_view_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $top_view_id,
    					'field' => 'core_block_title',
    					'content' => 'Highest Viewed Questions'
    			));
    		}
    		
    		//insert top related to west
    		$contentModel->clear();
    		$contentModel->save(array(
    				'page_id' => $page_id,
    				'type' => 'widget',
    				'name' => 'question.related',
    				'parent_id' => $west_id,
    				'order' => 3,
    				'params' => '{"title":"Related Tags","plugin":"Question","num_item_show":"10","title_enable":"1"}',
    				'plugin' => 'Question',
    				'core_block_id' => $block_related_id,
    				'core_block_title' => 'Related Tags'
    		));
    		$related_id = $contentModel->id;
    		foreach (array_keys($languages) as $key)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    					'locale' => $key,
    					'model' => 'CoreContent',
    					'foreign_key' => $related_id,
    					'field' => 'core_block_title',
    					'content' => 'Related Tags'
    			));
    		}
    	}
    }
    public function uninstall(){
    	//Permission
    	$roleModel = MooCore::getInstance()->getModel('Role');
    	$roles = $roleModel->find('all');
    	foreach ($roles as $role)
    	{
    		$params = explode(',',$role['Role']['params']);
    		$params = array_diff($params,array('question_create','question_view'));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}
    	
    	//Mail
    	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
    	$mail = $mailModel->findByType('question_approve');
    	if ($mail)
    	{
    		$mailModel->delete($mail['Mailtemplate']['id']);
    	}
    	
    	//Category
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$categories = $categoryModel->findAllByType('Question');
    	foreach ($categories as $category)
    	{
    		$categoryModel->delete($category['Category']['id']);
    	}
    	 
    	//Menu
    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
    	$menu = $menuModel->findByUrl('/questions');
    	if ($menu)
    	{
    		$menuModel->delete($menu['CoreMenuItem']['id']);
    	}
    	
    	//Delete S3
    	$objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
    	$types = array('questions');
    	foreach ($types as $type)
    		$objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('question','Question Manager') => array('plugin' => 'question', 'controller' => 'questions', 'action' => 'admin_index'),
            __d('question','Settings') => array('plugin' => 'question', 'controller' => 'question_settings', 'action' => 'admin_index'),
            __d('question','Categories') => array('plugin' => 'question', 'controller' => 'question_categories', 'action' => 'admin_index'),
        	__d('question','Badges') => array('plugin' => 'question', 'controller' => 'question_badges', 'action' => 'admin_index'),
        	__d('question','Tags') => array('plugin' => 'question', 'controller' => 'question_tags', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}