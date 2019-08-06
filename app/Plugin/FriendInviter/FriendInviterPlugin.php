<?php 
App::uses('MooPlugin','Lib');
class FriendInviterPlugin implements MooPlugin{
    public function install(){
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $setting = $mSetting->findByName('friendinviter_enabled');
        if ($setting)
        {
        	$mSetting->id = $setting['Setting']['id'];
        	$mSetting->save(array('is_boot'=>1));
        }
        
        
        $roleModel = MooCore::getInstance()->getModel('Role');
    	$roles = $roleModel->find('all');
    	$role_ids = array();
    	foreach ($roles as $role)
    	{
    		$role_ids[] = $role['Role']['id'];
    		$params = explode(',',$role['Role']['params']);
    		$params = array_unique(array_merge($params,array('friendinviter_invite')));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}   
        
        //Add page
    	$pageModel = MooCore::getInstance()->getModel('Page.Page');
    	$blockModel = MooCore::getInstance()->getModel('CoreBlock');
    	$contentModel = MooCore::getInstance()->getModel('CoreContent');
    	$i18nModel = MooCore::getInstance()->getModel('I18nModel');
    	$languageModel = MooCore::getInstance()->getModel('Language');
    	$languages = $languageModel->find('all');
    	
    	//add translate page
    	$pageModel->Behaviors->unload('Translate');
    	$pages = $pageModel->find('all',array(
                    'conditions' => array(
                    'uri' => array('friend_inviters.index','friend_inviters.pending')
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
        
        $browse_page = $pageModel->findByUri('friend_inviters.index');
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
    		foreach ($languages as $language)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    				'locale' => $language['Language']['key'],
    				'model' => 'CoreContent',
    				'foreign_key' => $west_id,
    				'field' => 'core_block_title',
    				'content' => ''
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
    		
    		foreach ($languages as $language)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    				'locale' => $language['Language']['key'],
    				'model' => 'CoreContent',
    				'foreign_key' => $center_id,
    				'field' => 'core_block_title',
    				'content' => ''
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
    		foreach ($languages as $language)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    				'locale' => $language['Language']['key'],
    				'model' => 'CoreContent',
    				'foreign_key' => $east_id,
    				'field' => 'core_block_title',
    				'content' => ''
    			));
    		}
    		  		
    	}
        
        $pending_invite_page = $pageModel->findByUri('friend_inviters.pending');
    	if ($pending_invite_page)
    	{
    		$page_id = $pending_invite_page['Page']['id'];
    		
    		//insert west
    		$contentModel->clear();
    		$contentModel->save(array(
    			'page_id' => $page_id,
    			'type' => 'container',
    			'name' => 'west',
    		));
    		$west_id = $contentModel->id;
    		foreach ($languages as $language)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    				'locale' => $language['Language']['key'],
    				'model' => 'CoreContent',
    				'foreign_key' => $west_id,
    				'field' => 'core_block_title',
    				'content' => ''
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
    		
    		foreach ($languages as $language)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    				'locale' => $language['Language']['key'],
    				'model' => 'CoreContent',
    				'foreign_key' => $center_id,
    				'field' => 'core_block_title',
    				'content' => ''
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
    		foreach ($languages as $language)
    		{
    			$i18nModel->clear();
    			$i18nModel->save(array(
    				'locale' => $language['Language']['key'],
    				'model' => 'CoreContent',
    				'foreign_key' => $east_id,
    				'field' => 'core_block_title',
    				'content' => ''
    			));
    		}
    		  		
    	}  
        
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mMenu->findByUrl('/friend_inviters');
        
        if ($menu){
            foreach ($languages as $language)
    		{
                    if(Configure::read('core.default_language') != $language['Language']['key']){
    			$i18nModel->clear();
    			$i18nModel->save(array(
                                        'locale' => $language['Language']['key'],
                                        'model' => 'CoreMenuItem',
                                        'foreign_key' => $menu['CoreMenuItem']['id'],
                                        'field' => 'name',
                                        'content' => $menu['CoreMenuItem']['name']
                        ));
                    }
    		}
        }
       
    }
    public function uninstall(){
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
         //Menu
        $menu = $mMenu->findByUrl('/friend_inviters');
        if ($menu)
        {
            $mMenu->delete($menu['CoreMenuItem']['id']);
        }
         //delete setting
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $settingGroup = $mSettingGroup->findByModuleId('FriendInviter');
        if($settingGroup != null)
        {
            $mSetting->deleteAll(array(
                'Setting.group_id' => $settingGroup['SettingGroup']['id']
            ));
            $mSettingGroup->delete($settingGroup['SettingGroup']['id']);
        }
        
        //Permission
    	$roleModel = MooCore::getInstance()->getModel('Role');
    	$roles = $roleModel->find('all');
    	foreach ($roles as $role)
    	{
    		$params = explode(',',$role['Role']['params']);
    		$params = array_diff($params,array('friendinviter_invite'));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	} 
        
        if (Configure::read('Credit.credit_enabled')) {
            $credit_action_type = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
            $action_type = $credit_action_type->getActionTypeFormModule('friend_inviter');
            if ($action_type) {
                $credit_action_type->delete($action_type['CreditActiontypes']['id']);                     
            }
        }
        Cache::delete('friendinviter.checkcredit');
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
             __d('friend_inviter','Settings') => array('plugin' => 'friend_inviter', 'controller' => 'friend_inviter_settings', 'action' => 'admin_index')
        );
    }
   
    public function callback_1_0(){}

    public  function callback_1_1() {
        $roleModel = MooCore::getInstance()->getModel('Role');
    	$roles = $roleModel->find('all');
    	$role_ids = array();
    	foreach ($roles as $role)
    	{
    		$role_ids[] = $role['Role']['id'];
    		$params = explode(',',$role['Role']['params']);
    		$params = array_unique(array_merge($params,array('friendinviter_invite')));
    		$roleModel->id = $role['Role']['id'];
    		$roleModel->save(array('params'=>implode(',', $params)));
    	}         
    }
    
    public  function callback_1_2() {
        $mMenu = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mMenu->findByUrl('/friend_inviters');        
        if($menu){
            $languageModel = MooCore::getInstance()->getModel('Language');
            $languages = $languageModel->find('all');

            $i18nModel = MooCore::getInstance()->getModel('I18nModel');
            $exist_menu_language = $i18nModel->find('list', array( 'fields' => array('id', 'locale'), 'conditions' => array(
                'model' => 'CoreMenuItem',
                'foreign_key' => $menu['CoreMenuItem']['id'],
                'field' => 'name'
           )) );
      
            foreach ($languages as $language){
                    if(!in_array($language['Language']['key'], $exist_menu_language)){
    			$i18nModel->save(array(
                                        'locale' => $language['Language']['key'],
                                        'model' => 'CoreMenuItem',
                                        'foreign_key' => $menu['CoreMenuItem']['id'],
                                        'field' => 'name',
                                        'content' => $menu['CoreMenuItem']['name']
                        ));
                    }
    		}
        }
    }
}