<?php 
App::uses('MooPlugin','Lib');
class DocumentPlugin implements MooPlugin{
    public function install(){   	    	
    	//Permission
    	$roleModel = MooCore::getInstance()->getModel('Role');
    	$roles = $roleModel->find('all');
    	$role_ids = array();
    	foreach ($roles as $role)
    	{
    		$role_ids[] = $role['Role']['id'];
    		$params = explode(',',$role['Role']['params']);
    		$params = array_unique(array_merge($params,array('document_create','document_view')));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}   	
    	
	    //Add Menu
    	$languageModel = MooCore::getInstance()->getModel('Language');
    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
    	$menu = $menuModel->findByUrl('/documents');
    	if (!$menu)
    	{
    		$menuModel->clear();
    		$menuModel->save(array(
    			'role_access'=>json_encode($role_ids),
    			'name' => 'Documents',
    			'original_name' => 'Documents',
    			'url' => '/documents',
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
    			'uri' => array('documents.view','documents.index')
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
    		'conditions' => array('CoreBlock.path_view' => 'document.menu')
    	)); 
    	$block_menu_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    		'conditions' => array('CoreBlock.path_view' => 'document.browse')
    	)); 
    	$block_browse_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    		'conditions' => array('CoreBlock.path_view' => 'document.feature')
    	)); 
    	$block_feature_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    		'conditions' => array('CoreBlock.path_view' => 'document.block')
    	)); 
    	$block_block_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    		'conditions' => array('CoreBlock.path_view' => 'document.my')
    	)); 
    	$block_my_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    		'conditions' => array('CoreBlock.path_view' => 'document.profile')
    	)); 
    	$block_profile_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    		'conditions' => array('CoreBlock.path_view' => 'core.tags')
    	)); 
    	$block_tag_id = $block['CoreBlock']['id'];
    	
    	$block = $blockModel->find('first',array(
    		'conditions' => array('CoreBlock.path_view' => 'document.tag')
    	)); 
    	$block_document_tag_id = $block['CoreBlock']['id'];
    	
    	
    	$browse_page = $pageModel->findByUri('documents.index');
    	if ($browse_page)
    	{
    		$page_id = $browse_page['Page']['id'];
    		
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
    			'name' => 'document.menu',
    			'parent_id' => $west_id,
    			'params' => '{"title":"Menu document & Search","plugin":"Document"}',
    			'plugin' => 'Document',
    			'order' => 1,
    			'core_block_id' => $block_menu_id,
    			'core_block_title' => 'Menu document & Search'
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
    				'content' => 'Menu document & Search'
    			));
    		}
    		//insert popular to west
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $west_id,
    			'params' => '{"title":"Block Document","order_type":"popular","num_item_show":"4","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'order' => 2,
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Popular Document'
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
    				'content' => 'Popular Document'    				
    			));
    		}
    		
    		//insert top comment to west
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $west_id,
    			'params' => '{"title":"Block Document","order_type":"Document.comment_count desc","num_item_show":"10","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'order' => 3,
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Top Comment Document'
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
    				'content' => 'Top Comment Document'
    			));
    		}
    		
    		//insert like to west
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $west_id,
    			'order' => 4,
    			'params' => '{"title":"Block Document","order_type":"Document.like_count desc","num_item_show":"10","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Top Like Document'
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
    				'content' => 'Top Like Document'
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
    		
    		//insert feature to center
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.feature',
    			'parent_id' => $center_id,
    			'params' => '{"title":"Feature Document","num_item_show":"10","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'order' => 1,
    			'core_block_id' => $block_feature_id,
    			'core_block_title' => 'Feature Document'
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
    				'content' => 'Feature Document'
    			));
    		}
    		
    		//insert browse to center
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.browse',
    			'parent_id' => $center_id,
    			'order' => 2,
    			'params' => '{"title":"Browse Document","plugin":"Document"}',
    			'plugin' => 'Document',
    			'core_block_id' => $block_browse_id,
    			'core_block_title' => 'Browse Document'
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
    				'content' => 'Browse Document'
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
    		
    		//insert tag to east
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'core.tags',
    			'parent_id' => $east_id,
    			'params' => '{"title":"Popular Tags","num_item_show":"10","type":"Document_Document","order_by":"newest","title_enable":"1"}',
    			'plugin' => '',
    			'order' => 1,
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
    		
    		//insert recent to east
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $east_id,
    			'order' => 2,
    			'params' => '{"title":"Block Document","order_type":"Document.id desc","num_item_show":"4","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Recent Document'
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
    				'content' => 'Recent Document'
    			));
    		}
    		
    		//insert view to east
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $east_id,
    			'params' => '{"title":"Block Document","order_type":"Document.view_count desc","num_item_show":"10","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'order' => 3,
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Top View Document'
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
    				'content' => 'Top View Document'
    			));
    		}
    		
    		//insert view to east
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $east_id,
    			'params' => '{"title":"Block Document","order_type":"Document.download_count desc","num_item_show":"10","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'order' => 4,
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Top Download Document'
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
    				'content' => 'Top Download Document'
    			));
    		}
    	}
    	
    	//Add block to detail page
    	$detail_page = $pageModel->findByUri('documents.view');
    	if ($detail_page)
    	{
    		$page_id = $detail_page['Page']['id'];
    		
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
    			'order' => 1,
    			'params' => '{"title":"Document\'s Content","maincontent":"1"}',
    			'plugin' => 'Document',
    			'core_block_title' => 'Document\'s Content'
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
    				'content' => 'Document\'s Content'
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
    			'name' => 'document.tag',
    			'order' => 2,
    			'parent_id' => $east_id,
    			'params' => '{"title":"Document tags","title_enable":"1","title_enable":"1"}',
    			'core_block_id' => $block_document_tag_id,
    			'plugin' => 'Document',
    			'core_block_title' => 'Document tags'
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
    				'content' => 'Document tags'
    			));
    		}
    		
    		//insert popular tag to east
    		/*$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'core.tags',
    			'parent_id' => $east_id,
    			'params' => '{"title":"Popular Tags","num_item_show":"10","type":"Document_Document","order_by":"newest","style":"classic","title_enable":"1"}',
    			'lft' => 320,
    			'rght' => 321,
    			'core_block_id' => $block_tag_id,
    			'plugin' => '',
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
    		}*/
    		
    		//insert feature east
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $east_id,
    			'order' => 3,
    			'params' => '{"title":"Block Document","order_type":"feature","num_item_show":"4","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Feature Document'
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
    				'content' => 'Feature Document'
    			));
    		}
    		
    		//insert popular east
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $east_id,
    			'params' => '{"title":"Block Document","order_type":"popular","num_item_show":"4","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'order' => 4,
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Popular Document'
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
    				'content' => 'Popular Document'
    			));
    		}
    		
    		//insert like east
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'widget',
    			'name' => 'document.block',
    			'parent_id' => $east_id,
    			'params' => '{"title":"Block Document","order_type":"Document.like_count desc","num_item_show":"10","plugin":"Document","title_enable":"1"}',
    			'plugin' => 'Document',
    			'order' => 5,
    			'core_block_id' => $block_block_id,
    			'core_block_title' => 'Top Like Document'
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
    				'content' => 'Top Like Document'
    			));
    		}
    	}
    	
    	//Mail template
     	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $langs = $languageModel->find('all');
        $data['Mailtemplate'] = array(
            'type' => 'document_approve',
            'plugin' => 'Document',
            'vars' => '[document_link],[document_title]'
        );
        $mailModel->save($data);
        $id = $mailModel->id;
        foreach ($langs as $lang)
        {
            $language = $lang['Language']['key'];
            $mailModel->locale = $language;
            $data_translate['subject'] = 'Document Approve';
            $content = <<<EOF
		    <p>[header]</p>
			<p>Your <a href="[document_link]">[document_title]</a> has been approved.</p>
			<p>[footer]</p>
EOF;
            $data_translate['content'] = $content;
            $mailModel->save($data_translate);
        }
        
        //Category 
        $categoryModel = MooCore::getInstance()->getModel('Category');
        $categoryModel->save(array(
        	'type' => 'Document',
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
        $setting = $settingModel->findByName('document_enabled');
        if ($setting)
        {
        	$settingModel->id = $setting['Setting']['id'];
        	$settingModel->save(array('is_boot'=>1));
        }
        
    	$setting = $settingModel->findByName('document_consider_force');
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
    		$params = array_diff($params,array('document_create','document_view'));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	} 
    	
    	//Mail
    	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
    	$mail = $mailModel->findByType('document_approve');
    	if ($mail)
    	{
    		$mailModel->delete($mail['Mailtemplate']['id']);
    	}
    	//Category
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$categories = $categoryModel->findAllByType('Document');
    	foreach ($categories as $category)
    	{
    		$categoryModel->delete($category['Category']['id']);
    	}
    	
    	//Menu
    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
    	$menu = $menuModel->findByUrl('/documents');
    	if ($menu)
    	{
    		$menuModel->delete($menu['CoreMenuItem']['id']);
    	}
		
		//Delete S3
		$objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
		$types = array('documents','documents_files');
		foreach ($types as $type)
			$objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
    }
    
    public function callback_1_1()
    {
    	$contentModel = MooCore::getInstance()->getModel('CoreContent');
    	$contents = $contentModel->findAllByPlugin('Document');
    	foreach ($contents as $content)
    	{
    		$params = json_decode($content['CoreContent']['params'],true);
    		$params['title_enable'] = "1";
    		$contentModel->clear();
    		$contentModel->id = $content['CoreContent']['id'];
    		$contentModel->save(array('params'=>json_encode($params)));
    	}
    }
    
    public function callback_1_2()
    {
    	$pageModel = MooCore::getInstance()->getModel('Page.Page');    	
    	$i18nModel = MooCore::getInstance()->getModel('I18nModel');
    	$languageModel = MooCore::getInstance()->getModel('Language');
    	$languages = $languageModel->find('all');
    	
    	//add translate page
    	$pageModel->Behaviors->unload('Translate');
    	$pages = $pageModel->find('all',array(
    		'conditions' => array(
    			'uri' => array('documents.view','documents.index')
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
    
	public function settingGuide(){
		
	}
    public function menu()
    {
        return array(
            __d('document','Document Manager') => array('plugin' => 'document', 'controller' => 'documents', 'action' => 'admin_index'),
            __d('document','Settings') => array('plugin' => 'document', 'controller' => 'document_settings', 'action' => 'admin_index'),
        	__d('document','Categories') => array('plugin' => 'document', 'controller' => 'document_categories', 'action' => 'admin_index'),
        	__d('document','License Manager') => array('plugin' => 'document', 'controller' => 'document_licenses', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}