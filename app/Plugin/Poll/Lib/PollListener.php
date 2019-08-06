<?php
App::uses('CakeEventListener', 'Event');

class PollListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
        	'Model.beforeDelete' => 'doAfterDelete',
        	'MooView.beforeRender' => 'beforeRender',
        	'View.Adm.Layout.adminGetContentInfo' => 'widgetTag',
        	'welcomeBox.afterRenderMenu' => 'welcomeBoxAfterRenderMenu',
        	'profile.afterRenderMenu'=> 'profileAfterRenderMenu',
        	'Controller.Search.search' => 'search',
        	'Controller.Search.suggestion' => 'suggestion',
        	'Controller.Search.hashtags_filter' => 'hashtags_filter',
        	'Controller.Search.hashtags' => 'hashtags',
        	'Controller.Share.afterShare' => 'afterShare',
			'Controller.Comment.afterComment' => 'afterComment',
        	'Plugin.View.Api.Search' => 'apiSearch',
        	'Controller.Home.adminIndex.Statistic' => 'statistic',
        		
        	'StorageHelper.polls.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.polls.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.polls.getFilePath' => 'storage_amazon_get_file_path',
        		
        	'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
        		
        	'ApiHelper.renderAFeed.poll_create' => 'exportPollCreate',
        	'ApiHelper.renderAFeed.poll_item_detail_share' => 'exportPollItemDetailShare',
        		
        	'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu'
        );
    }
    
    public function apiAfterRenderMenu($e)
    {
    	$subject = MooCore::getInstance()->getSubject();
    	$e->data['result']['poll'] = array(
    			'text' => __d('poll','Polls'),
    			'url' => FULL_BASE_URL . $e->subject()->request->base . '/polls/browse/profile/'. $subject['User']['id'],
    			'cnt' => 0
    	);
    }
    
    public function exportPollCreate($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$pollModel = MooCore::getInstance()->getModel("Poll_Poll");
    	$poll = $pollModel->findById($data['Activity']['item_id']);
    	$helper = MooCore::getInstance()->getHelper('Poll_Poll');
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
    	if(!empty($title_tmp)){
    		$title =  $title_tmp['title'];
    		$titleHtml = $title_tmp['titleHtml'];
    	}else{
    		$title = __d('poll','created a new poll');
    		$titleHtml = $actorHtml . ' ' . __d('poll','created a new poll');
    	}
    	$e->result['result'] = array(
    			'type' => 'create',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Poll_Poll',
    					'id' => $poll['Poll']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($poll['Poll']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => '',
    					'title' => $poll['Poll']['moo_title'],
    					'images' => array('850'=>$helper->getImage($poll,array('prefix'=>''))),
    			),
    			'target' => $target,
    	);
    }
    
    public function exportPollItemDetailShare($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$pollModel = MooCore::getInstance()->getModel("Poll_Poll");
    	$poll = $pollModel->findById($data['Activity']['parent_id']);
    	$helper = MooCore::getInstance()->getHelper('Poll_Poll');
    	
    	$target = array();
    	
    	if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
    	{
    		$title = $data['User']['name'] . ' ' . __d('poll',"shared %s's poll", $poll['User']['name']);
    		$titleHtml = $actorHtml . ' ' . __d('poll',"shared %s's poll", $e->subject()->Html->link($poll['User']['name'], FULL_BASE_URL . $poll['User']['moo_href']));
    		$target = array(
    				'url' => FULL_BASE_URL . $poll['User']['moo_href'],
    				'id' => $poll['User']['id'],
    				'name' => $poll['User']['name'],
    				'type' => 'User',
    		);
    	}
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml,true);
    	if(!empty($title_tmp)){
    		$title .=  $title_tmp['title'];
    		$titleHtml .= $title_tmp['titleHtml'];
    	}
    	
    	$e->result['result'] = array(
    			'type' => 'share',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Poll_Poll',
    					'id' => $poll['Poll']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($poll['Poll']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => '',
    					'title' => $poll['Poll']['moo_title'],
    					'images' => array('850'=>$helper->getImage($poll,array('prefix'=>''))),
    			),
    			'target' => $target,
    	);
    }
    
    public function storage_geturl_local($e)
    {
    	$v = $e->subject();
    	$request = Router::getRequest();
    	$oid = $e->data['oid'];
    	$type = $e->data['type'];
    	$thumb = $e->data['thumb'];
    	$prefix = $e->data['prefix'];

    	if ($e->data['thumb']) {
    		$url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/polls/thumbnail/' . $oid . '/' . $prefix . $thumb;
    	} else {
    		//$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
    		$url = $v->getImage("poll/img/noimage/poll.png");
    	}

    	$e->result['url'] = $url;
    }
    
    public function storage_geturl_amazon($e)
    {
    	$v = $e->subject();
    	$type = $e->data['type'];

    	$e->result['url'] = $v->getAwsURL($e->data['oid'], "polls", $e->data['prefix'], $e->data['thumb']);

    }
    
    public function storage_amazon_get_file_path($e)
    {
    	$objectId = $e->data['oid'];
    	$name = $e->data['name'];
    	$thumb = $e->data['thumb'];
    	$type = $e->data['type'];;
    	$path = false;

    	if (!empty($thumb)) {
    		$path = WWW_ROOT . "uploads" . DS . "polls" . DS . "thumbnail" . DS . $objectId . DS . $name . $thumb;
    	}
    	
    	$e->result['path'] = $path;
    }
    
    public function storage_task_transfer($e)
    {
    	$v = $e->subject();
    	$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
    	$polls = $pollModel->find('all', array(
    			'conditions' => array("Poll.id > " => $v->getMaxTransferredItemId("polls")),
    			'limit' => 10,
    			'fields'=>array('Poll.id','Poll.thumbnail'),
    			'order' => array('Poll.id'),
    		)
    	);
    	
    	if($polls){
    		$photoSizes = $v->photoSizes();
    		foreach($polls as $poll){
    			if (!empty($poll["Poll"]["thumbnail"])) {
    				if (!empty($poll["Poll"]["thumbnail"])) {
    					foreach ($photoSizes as $size){
    						$v->transferObject($poll["Poll"]['id'],"polls",$size.'_',$poll["Poll"]["thumbnail"]);
    					}
    					$v->transferObject($poll["Poll"]['id'],"polls",'',$poll["Poll"]["thumbnail"]);
    				}
    			}
    		}
    	}
    }
    
    public function statistic($event)
    {
    	$request = Router::getRequest();
    	$pollModel = MooCore::getInstance()->getModel("Poll.Poll");
    	$event->result['statistics'][] = array(
    			'item_count' => $pollModel->find('count'),
    			'ordering' => 9999,
    			'name' => __d('poll','Polls'),
    			'href' => $request->base.'/admin/poll/polls',
    			'icon' => '<i class="fa fa-bar-chart"></i>'
    	);
    }
    
    public function apiSearch($event)
    {
    	$view = $event->subject();
    	$items = &$event->data['items'];
    	$type = $event->data['type'];
    	$viewer = MooCore::getInstance()->getViewer();
    	$utz = $viewer['User']['timezone'];
    	if ($type == 'Poll' && isset($view->viewVars['polls']) && count($view->viewVars['polls']))
    	{
    		$helper = MooCore::getInstance()->getHelper('Poll_Poll');
    		foreach ($view->viewVars['polls'] as $item){
    			$items[] = array(
    					'id' => $item["Poll"]['id'],
    					'url' => FULL_BASE_URL.$item['Poll']['moo_href'],
    					'avatar' =>  $helper->getImage($item),
    					'owner_id' => $item["Poll"]['user_id'],
    					'title_1' => $item["Poll"]['moo_title'],
    					'title_2' => __( 'Posted by') . ' ' . $view->Moo->getNameWithoutUrl($item['User'], false) . ' ' .$view->Moo->getTime( $item["Poll"]['created'], Configure::read('core.date_format'), $utz ),
    					'created' => $item["Poll"]['created'],
    					'type' => "Poll"
    			);
    		}
    	}
    }
    
    public function afterShare($event){
    	$data = $event->data['data'];
    	if (isset($data['item_type']) && $data['item_type'] == 'Poll_Poll'){
    		$poll_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
    		$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
    		$pollModel->updateAll(array('Poll.share_count' => 'Poll.share_count + 1'), array('Poll.id' => $poll_id));
    	}
    }
    
    public function afterComment($event){
    	$data = $event->data['data'];
    	$target_id = isset($data['target_id']) ? $data['target_id'] : null;
    	$type = isset($data['type']) ? $data['type'] : '';
    	if ($type == 'Poll_Poll' && !empty($target_id)){
    		$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
    		$pollModel->updateCounter($target_id);
    	}
    }
    
    public function hashtags($event)
    {
    	if(Configure::read('Poll.poll_enabled')){    		
    		$polls = array();
    		$e = $event->subject();
    		$tagModel = MooCore::getInstance()->getModel('Tag');
    		$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
    		$page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
    
    		$uid = MooCore::getInstance()->getViewer(true);
    		// get tagged item
    		$tag = h(urldecode($event->data['search_keyword']));
    		$tags = $tagModel->find('all', array('conditions' => array(
    				'Tag.type' => 'Poll_Poll',
    				'Tag.tag' => $tag
    		)));
    		$poll_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');
    		$items = $pollModel->getPolls(array('user_id'=>$uid,'page'=>$page,'ids'=>$poll_ids));
    		 
    		$polls = array_merge($polls, $items);
    
    		//only display 5 items on All Search Result page
    		if(isset($event->data['type']) && $event->data['type'] == 'all')
    		{
    			$polls = array_slice($polls,0,5);
    		}
    		$polls = array_map("unserialize", array_unique(array_map("serialize", $polls)));
    		if(!empty($polls))
    		{
    			$event->result['polls']['header'] = __d('poll','Polls');
    			$event->result['polls']['icon_class'] = 'insert_chart';
    			$event->result['polls']['view'] = "Poll.lists/polls";
    			if(isset($event->data['type']) && $event->data['type'] == 'polls')
    			{
    				$e->set('result',1);
    				$e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/polls/page:' . ( $page + 1 ));
    				$e->set('element_list_path',"Poll.lists/polls");
    			}
    			$e->set('polls', $polls);
    		}
    	}
    }
    
    public function hashtags_filter($event)
    {
    	if(Configure::read('Poll.poll_enabled')){
    		$e = $event->subject();
    		$pollModel = MooCore::getInstance()->getModel('Poll_Poll');
    		$uid = MooCore::getInstance()->getViewer(true);
    
    		if(isset($event->data['type']) && $event->data['type'] == 'polls')
    		{
    			$page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
    			$polls = $pollModel->getPolls(array('user_id'=>$uid,'page'=>$page,'ids'=>$event->data['item_ids']));
    			$e->set('polls', $polls);
    			$e->set('result',1);
    			$e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/polls/page:' . ( $page + 1 ));
    			$e->set('element_list_path',"Poll.lists/polls");
    		}
    		$table_name = $pollModel->table;
    		if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
    		{
    			$event->result['polls'] = null;
    
    			$polls = $pollModel->getPolls(array('user_id'=>$uid,'limit'=>5,'ids'=>$event->data['item_groups'][$table_name]));
    
    			if(!empty($polls))
    			{
    				$event->result['polls']['header'] = __d('poll','Polls');
    				$event->result['polls']['icon_class'] = 'insert_chart';
    				$event->result['polls']['view'] = "Poll.lists/polls";
    				$e->set('polls', $polls);
    			}
    		}
    	}
    }
    
    public function widgetTag($event)
    {
    	$event->result['tag']['type']['Poll_Poll'] = 'Poll';
    }
    
    public function profileAfterRenderMenu($event)
    {
    	$view = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if(Configure::read('Poll.poll_enabled')){
    		$pollModel = MooCore::getInstance()->getModel('Poll_Poll');
    		$subject = MooCore::getInstance()->getSubject();
    		$total = $pollModel->getTotalPolls(array('owner_id'=>$subject['User']['id'],'user_id'=>$uid));
    		echo $view->element('menu_profile',array('count'=>$total),array('plugin'=>'Poll'));
    	}
    }
    
    public function search($event)
    {
    	if(Configure::read('Poll.poll_enabled')){
    		$e = $event->subject();
    		$uid = MooCore::getInstance()->getViewer(true);
    		$pollModel = MooCore::getInstance()->getModel('Poll_Poll');
    		$results = $pollModel->getPolls(array('search'=>$e->keyword,'user_id'=>$uid,'type' => 'all','limit'=>4));
    		 
    		if(isset($e->plugin) && $e->plugin == 'Poll')
    		{
    			$e->set('polls', $results);
    			$e->render("Poll.Elements/lists/polls");
    			$e->set('no_list_id',true);
    		}
    		else
    		{
    			$event->result['Poll']['header'] = __d('poll',"Poll");
    			$event->result['Poll']['icon_class'] = "insert_chart";
    			$event->result['Poll']['view'] = "lists/polls";
    			$e->set('no_list_id',true);
    			if(!empty($results))
    				$event->result['Poll']['notEmpty'] = 1;
    				$e->set('polls', $results);
    		}
    	}
    }
    
    public function suggestion($event)
    {
    	if(Configure::read('Poll.poll_enabled')){
    		$e = $event->subject();
    		$pollModel = MooCore::getInstance()->getModel('Poll_Poll');
    		$uid = MooCore::getInstance()->getViewer(true);
    
    		$event->result['poll']['header'] = __d('poll',"Poll");
    		$event->result['poll']['icon_class'] = 'insert_chart';
    
    		if(isset($event->data['type']) && $event->data['type'] == 'poll')
    		{
    			$page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
    			$polls = $pollModel->getPolls(array('page'=>$page,'user_id'=>$uid,'search'=>$event->data['searchVal']));
    			$polls_next = $pollModel->getPolls(array('page'=>$page + 1 ,'user_id'=>$uid,'search'=>$event->data['searchVal']));
    
    			$e->set('polls', $polls);
    			$e->set('result',1);
    			$e->set('no_list_id',true);
    			if ($polls_next && count($polls_next))
    				$e->set('is_view_more',true);
    			
    			$e->set('url_more','/search/suggestion/poll/'.$e->params['pass'][1]. '/page:' . ( $page + 1 ));
    			$e->set('element_list_path',"Poll.lists/polls");
    		}
    		if(isset($event->data['type']) && $event->data['type'] == 'all')
    		{
    			$event->result['poll'] = null;
    			$polls = $pollModel->getPolls(array('page'=>1,'limit'=>2,'user_id'=>$uid,'search'=>$event->data['searchVal']));
    			$helper = MooCore::getInstance()->getHelper('Poll_Poll');
    			$mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
    			 
    			if(!empty($polls)){
    				$event->result['poll'] = array(__d('poll','Poll'));
    				foreach($polls as $index=>$detail){
						$index++;
    					$event->result['poll'][$index]['id'] = $detail['Poll']['id'];
    					$event->result['poll'][$index]['img'] = $helper->getImage($detail);

    					$event->result['poll'][$index]['title'] = $detail['Poll']['title'];
    					$event->result['poll'][$index]['find_name'] = __d('poll','Find Poll');
    					$event->result['poll'][$index]['icon_class'] = 'insert_chart';
    					$event->result['poll'][$index]['view_link'] = 'polls/view/';
    					 
    					$event->result['poll'][$index]['more_info'] = __d('poll','Posted by') . ' ' . $mooHelper->getNameWithoutUrl($detail['User'], false) . ' ' . $mooHelper->getTime( $detail['Poll']['created'], Configure::read('core.date_format'), $e->viewVars['utz'] );
    				}
    			}
    		}
    	}
    }
    
    public function welcomeBoxAfterRenderMenu($event)
    {
    	$view = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if(Configure::read('Poll.poll_enabled') && $uid){
    		$pollModel = MooCore::getInstance()->getModel('Poll_Poll');
    		$total = $pollModel->getTotalPolls(array('type'=>'my','user_id'=>$uid));
    		echo $view->element('menu_welcome',array('count'=>$total),array('plugin'=>'Poll'));
    	}
    }
    
	public function beforeRender($event)
    {    	
    	if(Configure::read('Poll.poll_enabled')){
    		$e = $event->subject();
    		$e->Helpers->Html->css( array(
					'Poll.main'
				),
				array('block' => 'css')
			);
    		
    		if (Configure::read('debug') == 0){
    			$min="min.";
    		}else{
    			$min="";
    		}
    		$e->Helpers->MooRequirejs->addPath(array(
    			"mooPoll"=>$e->Helpers->MooRequirejs->assetUrlJS("Poll.js/main.{$min}js"),
    			"mooJqueryUi" => $e->Helpers->MooRequirejs->assetUrlJS("js/global/jquery-ui/jquery-ui-1.10.3.custom.min.js")
    		));
    		
    		$e->Helpers->MooRequirejs->addShim(array(
    			'mooJqueryUi'=>array("deps" =>array('jquery')),
    		));
    		
    		$e->addPhraseJs(array(
    			'delete_poll_confirm' => __d('poll','Are you sure you want to delete this poll?')
    		));
    		
    		$e->Helpers->MooPopup->register('pollModal');
    	}
    }
    
    public function doAfterDelete($event)
    {
    	$model = $event->subject();
    	$type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);
    	if ($type == 'User')
    	{    		    	
    		$pollModel = MooCore::getInstance()->getModel('Poll_Poll');
    		$pollModel->deleteAll(array('Poll.user_id' => $model->id)); 
    	}
    } 
}