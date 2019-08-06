<?php 
class PollsController extends PollAppController{
	public $components = array('Paginator');
    public function admin_index()
    {
    	$this->set('title_for_layout', __d('poll','Polls'));
    	$this->loadModel('Category');
    	$this->loadModel('Poll.Poll');
    	$this->Paginator->settings = array(
    			'limit' => Configure::read('Poll.poll_item_per_pages'),
    			'order' => array(
    					'Poll.id' => 'DESC'
    			)
    	);
    	
    	$cond = array();
    	$passedArgs = array();
    	$named = $this->request->params['named'];
    	if ($named)
    	{
    		foreach ($named as $key => $value)
    		{
    			$this->request->data[$key] = $value;
    		}
    	}
    	
    	if ( !empty( $this->request->data['category_id'] ) )
    	{
    		$cond['Poll.category_id'] = $this->request->data['category_id'];
    		$this->set('category_id',$this->request->data['category_id']);
    		$passedArgs['category_id'] = $this->request->data['category_id'];
    	}
    	
    	if ( isset( $this->request->data['feature'] ) && $this->request->data['feature'] != '' )
    	{
    		$cond['Poll.feature'] = $this->request->data['feature'];
    		$this->set('feature',$this->request->data['feature']);
    		$passedArgs['feature'] = $this->request->data['feature'];
    	}
    	
    	if ( !empty( $this->request->data['title'] ) )
    	{
    		$cond['Poll.title LIKE'] = '%'.$this->request->data['title'].'%';
    		$this->set('title',$this->request->data['title']);
    		$passedArgs['title'] = $this->request->data['title'];
    	}
    	
    	if ( isset( $this->request->data['visable'] ) && $this->request->data['visable'] !='' )
    	{
    		$cond['Poll.visiable'] = $this->request->data['visable'];
    		$this->set('visable',$this->request->data['visable']);
    		$passedArgs['visable'] = $this->request->data['visable'];
    	}
    	
    	$this->loadModel('Category');
    	$categories = $this->Category->getCategoriesList('Poll');
    	$this->set('categories',$categories);
    	 
    	$polls = $this->Paginator->paginate('Poll',$cond);
    	$this->set('polls', $polls);
    	$this->set('passedArgs',$passedArgs);
    }
    
    public function admin_feature()
    {
    	$this->loadModel('Poll.Poll');
    	$id = $this->request->data['id'];
    	$value = $this->request->data['value'];
    	if ($id)
    	{
    		$this->Poll->id = $id;
    		$this->Poll->save(array('feature'=>$value));
    	}
    	die();
    	 
    }
    
    public function admin_delete($id = null)
    {
    	$this->loadModel('Poll.Poll');
    	if ($id)
    	{
    		$this->Poll->delete($id);
    	}
    	 
    	$this->Session->setFlash( __d('poll','Poll has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	$this->redirect( '/admin/poll/polls' );
    }
    
    public function admin_visiable()
    {
    	$this->loadModel('Poll.Poll');
    	$id = $this->request->data['id'];
    	$value = $this->request->data['value'];
    	if ($id)
    	{
    		$this->Poll->id = $id;
    		$this->Poll->save(array('visiable'=>$value));
    	}
    	die();
    }
    
    public function index()
    {
    	$this->set('title_for_layout', '');
    	if ($this->isApp())
    	{
    		App::uses('browsePollWidget', 'Poll.Controller'.DS.'Widgets'.DS.'poll');
    		$widget = new browsePollWidget(new ComponentCollection(),null);
    		$widget->beforeRender($this);
    	}
    }
    
    private function checkPermissionAjaxView($poll)
    {

    	$uid = MooCore::getInstance()->getViewer(true);
    	$viewer = MooCore::getInstance()->getViewer();
    	$this->_checkExistence($poll);
    	 
    	//check visiable
    	if (!$poll['Poll']['visiable']  && !($uid && $viewer['Role']['is_admin']))
    	{
    		$this->_checkExistence(null);
    	}
    	$this->_checkPermission(array('confirm' => true));
    	$this->_checkPermission( array('aco' => 'poll_view'));
    	$this->_checkPermission( array('user_block' => $poll['Poll']['user_id']) );
    	 
    	$areFriends = false;
    	if ($uid)
    	{
    		$this->loadModel('Friend');
    		$areFriends = $this->Friend->areFriends($uid, $poll['User']['id']);
    	}
    	$this->_checkPrivacy($poll['Poll']['privacy'], $poll['User']['id'], $areFriends);
    }
    
    public function view($id = null)
    {
    	$uid = MooCore::getInstance()->getViewer(true);
    	$viewer = MooCore::getInstance()->getViewer();
    	$this->loadModel('Poll.Poll');
    	
    	$this->Poll->recursive = 2;    	
    	$poll = $this->Poll->findById($id);
    	if ($poll['Category']['id'])
    	{
    		foreach ($poll['Category']['nameTranslation'] as $translate)
    		{
    			if ($translate['locale'] == Configure::read('Config.language'))
    			{
    				$poll['Category']['name'] = $translate['content'];
    				break;
    			}
    		}
    	}
    	$this->Poll->recursive = 0;
    	
    	$this->_checkExistence($poll);
    
    	//check visiable
    	if (!$poll['Poll']['visiable']  && !($uid && $viewer['Role']['is_admin']))
    	{
    		$this->_checkExistence(null);
    	}
    	 
    	$this->_checkPermission( array('aco' => 'poll_view'));
    	
    	$this->_checkPermission( array('user_block' => $poll['Poll']['user_id']) );
    	 
    	MooCore::getInstance()->setSubject($poll);
    
    	$areFriends = false;
    	if ($uid)
    	{
    		$this->loadModel('Friend');
    		$areFriends = $this->Friend->areFriends($uid, $poll['User']['id']);
    	}
    	$this->_checkPrivacy($poll['Poll']['privacy'], $poll['User']['id'], $areFriends);
    	 
    	$this->set('title_for_layout', $poll['Poll']['title']);
		
    	$this->loadModel("Tag");
    	$tags = $this->Tag->getContentTags($poll['Poll']['id'],'Poll_Poll');
    	if (count($tags))
    	{
    		$tags = implode(",", $tags).' ';
    		$this->set('mooPageKeyword', $this->getKeywordsForMeta($tags));
    	}  		
    
    	// set og:image
    	if ($poll['Poll']['thumbnail']){
    		$mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
    		$this->set('og_image', $mooHelper->getImageUrl($poll));
    	}
    	 
    	$this->set('poll',$poll);
    }
    
    public function browse($type = null,$param = null)
    {
    	$this->loadModel('Poll.Poll');
    	$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
    	$url = ( !empty( $param ) )	? $type . '/' . $param : $type;
    	$uid = MooCore::getInstance()->getViewer(true);
    	$polls = array();
    	$total = 0;
    
    	switch ($type) {
    		case 'all':
    		case 'my':
    		case 'friend':
    		case 'home':
    			$polls = $this->Poll->getPolls(array('type'=>$type,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Poll->getTotalPolls(array('type'=>$type,'user_id'=>$uid,'page'=>$page));
    			break;
    		case 'profile':
    			$polls = $this->Poll->getPolls(array('owner_id'=>$param,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Poll->getTotalPolls(array('owner_id'=>$param,'user_id'=>$uid,'page'=>$page));
    			break;
    		case 'category':
    			$polls = $this->Poll->getPolls(array('category'=>$param,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Poll->getTotalPolls(array('category'=>$param,'user_id'=>$uid,'page'=>$page));
    			break;
    		case 'search':
    			$polls = $this->Poll->getPolls(array('search'=>$param,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Poll->getTotalPolls(array('search'=>$param,'user_id'=>$uid,'page'=>$page));
    			break;
    	}
    	 
    	$limit = Configure::read('Poll.poll_item_per_pages');
    	$is_view_more = (($page - 1) * $limit  + count($polls)) < $total;
    	 
    	$this->set('is_view_more',$is_view_more);
    	$this->set('polls', $polls);
    	$this->set('type',$type);
    	$this->set('page',$page);
    	$this->set('param',$param);
    	$this->set('url_more', '/polls/browse/' . h($url) . '/page:' . ( $page + 1 ) ) ;
    }
    
    public function ajax_view($id = null)
    {
    	$uid = MooCore::getInstance()->getViewer(true);
    	
    	$poll = $this->Poll->findById($id);
    	$this->checkPermissionAjaxView($poll);
    	
    	$this->loadModel("Poll.PollItem");
    	$result = $this->PollItem->getItems($poll['Poll']['id'],$uid);
    	$max_answer = $result['max_answer'];
    	$items = $result['result'];
    	
    	$this->set('poll',$poll);
    	$this->set('max_answer',$max_answer);
    	$this->set('items',$items);
    }
    
    public function create($id = null)
    {
    	$this->_checkPermission(array('aco' => 'poll_create'));
    	$this->_checkPermission(array('confirm' => true));
    	
    	$uid = MooCore::getInstance()->getViewer(true);
    	$this->loadModel('Tag');
    	$this->loadModel('Poll.PollItem');
    	
    	$this->loadModel('Category');
    	$role_id = $this->_getUserRoleId();
    	$categories = $this->Category->getCategoriesList('Poll',$role_id);
    	$this->set('categories',$categories);
    	
    	$is_edit = false;
    	if ($id)
    	{
    		$poll = $this->Poll->findById($id);
    		if ($poll)
    		{
    			$this->_checkPermission(array('admins' => array( $poll['User']['id'])));
    			 
    			$this->Poll->id = $id;
    			$is_edit = true;
    			$tags = $this->Tag->getContentTags($id, 'Poll_Poll');
    			$poll['Poll']['tags'] = $tags;
    			$poll['Poll']['items'] = $this->PollItem->getItemByPollId($id);
    		}
    	}
    	else
    	{
    		$poll = $this->Poll->initFields();
    		$poll['Poll']['tags'] = '';
    		$poll['Poll']['create_new_answer'] = 1;
    		$poll['Poll']['type'] = 1;
    	}
    	
    	$this->set('poll',$poll);
    	$this->set('is_edit',$is_edit);
    }
    
    public function upload_avatar()
    {
    	$uid = MooCore::getInstance()->getViewer(true);
    
    	if (!$uid)
    		return;
    
    		// save this picture to album
    		$path = 'uploads' . DS . 'tmp';
    		$url = 'uploads/tmp/';
    
    		$this->_prepareDir($path);
    
    		$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    
    		App::import('Vendor', 'qqFileUploader');
    		$uploader = new qqFileUploader($allowedExtensions);
    
    		// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
    		$result = $uploader->handleUpload($path);
    
    		if (!empty($result['success'])) {
    			App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
    
    			$result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
    			$result['file'] = $path . DS . $result['filename'];
    		}
    		// to pass data through iframe you will need to encode all html tags
    		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    		die();
    }
    
    public function save()
    {
    	$this->loadModel('Poll.Poll');
    	$this->loadModel('Tag');
    	$this->loadModel('Poll.PollItem');
    	$this->autoRender = false;
    	$helper = MooCore::getInstance()->getHelper('Poll_Poll');
    	$create_answer = true;
    	
    	$this->_checkPermission(array('aco' => 'poll_create'));
    	$this->_checkPermission(array('confirm' => true));
    	
    	$uid = MooCore::getInstance()->getViewer(true);
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$is_edit = false;
    	
    	if ($id)
    	{
    		$poll = $this->Poll->findById($id);
    		if ($poll)
    		{
    			$this->_checkPermission(array('admins' => array( $poll['User']['id'])));
    			 
    			$this->Poll->id = $id;
    			$is_edit = true;
    			if (!$helper->canEditAnswer($poll))
    			{
    				unset($this->Poll->validate['answers']);
    				$create_answer = false;
    			}
    		}
    	}
    	
    	$this->Poll->set($this->request->data);
    	$this->_validateData($this->Poll);
    	$data = $this->request->data;
    	
    	if (!isset($data['show_feed']) || !$data['show_feed'])
    	{
    		$this->Poll->Behaviors->unload('Activity');
    	}
    	
    	if (!$is_edit)
    	{
    		$data['user_id'] = $uid;
    	}
    	
    	if ($this->Poll->save($data))
    	{
    		$this->Tag->saveTags($this->request->data['tags'], $this->Poll->id, 'Poll_Poll');
    		
    		//share
    		$this->loadModel("Activity");
    		$activity = $this->Activity->find('first', array('conditions' => array(
    				'Activity.item_type' => 'Poll_Poll',
    				'Activity.item_id' => $this->Poll->id,
    		)));
    		
    		if (!empty($activity)){
    			$share = false;
    			// only enable share feature for public event
    			if ($this->request->data['privacy'] == PRIVACY_EVERYONE || $this->request->data['privacy'] == PRIVACY_FRIENDS) {
    				$share = true;
    			}
    			$this->Activity->clear();
    			$this->Activity->updateAll(array('Activity.share' => $share), array('Activity.id' => $activity['Activity']['id']));
    		}
    	
    		if (!$is_edit)
    		{
    			$this->Session->setFlash(__d('poll','Poll has been successfully added'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));    				
    		}
    		else
    		{
    			$this->Session->setFlash(__d('poll','Poll has been successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
    		}
    	
    		$response['result'] = 1;
    		
    		if ($is_edit && $create_answer)
    		{
    			$this->PollItem->deleteByPollId($id);
    		}
    		
    		if ($create_answer)
    		{
    			foreach ($data['answers'] as $answer)
    			{
    				if (!$answer['text'])
    					continue;
    				
    				$this->PollItem->clear();
    				$this->PollItem->save(array(
    					'poll_id' => $this->Poll->id,
    					'name' => $answer['text']
    				));
    			}
    		}
    		
    		$poll = $this->Poll->read();
    		$response['result'] = 1;
    		$response['href'] = $poll['Poll']['moo_href'];
    			
    		echo json_encode($response);
    		exit;
    	}
    }
    
    public function ajax_save_answer()
    {
    	$this->loadModel('Poll.Poll');
    	$this->loadModel('Poll.PollItem');
    	$this->loadModel('Poll.PollAnswer');
    	$uid = MooCore::getInstance()->getViewer(true);
    	
    	$poll_id = isset($this->request->data['poll_id']) ? $this->request->data['poll_id'] : 0;
    	$item_id = isset($this->request->data['item_id']) ? $this->request->data['item_id'] : 0;
    	$is_activity = isset($this->request->data['is_activity']) ? $this->request->data['is_activity'] : 0;
    	
    	$poll = $this->Poll->findById($poll_id);
    	$item = $this->PollItem->findById($item_id);
    	
    	$this->checkPermissionAjaxView($poll);
    	
    	$this->_checkExistence($poll);
    	$this->_checkExistence($item);
    	
    	if ($poll['Poll']['type']) // multi answer
    	{
	    	$answer= $this->PollAnswer->checkAnswer($item['PollItem']['id'],$uid);
	    	
	    	if ($answer)
	    	{
	    		//remove
	    		$this->PollAnswer->delete($answer['PollAnswer']['id']);
	    	}
	    	else
	    	{
	    		//add
	    		$this->PollAnswer->save(
	    			array(
	    				'user_id' => $uid,
	    				'poll_id' => $poll_id,
	    				'item_id' => $item_id
	    			)
	    		);
	    	}
    	}
    	else // single answer
    	{
    		$this->PollAnswer->removeAnswerByPollId($poll_id,$uid);
    		
    		$this->PollAnswer->save(
				array(
					'user_id' => $uid,
					'poll_id' => $poll_id,
					'item_id' => $item_id
				)
    		);
    	}
    	
    	$this->set('poll',$poll);
    	$this->set('is_activity',$is_activity);
    }
    
    public function ajax_add_answer()
    {
    	$this->loadModel('Poll.Poll');
    	$this->loadModel('Poll.PollItem');
    	$this->loadModel('Poll.PollAnswer');
    	$uid = MooCore::getInstance()->getViewer(true);
    	 
    	$poll_id = isset($this->request->data['poll_id']) ? $this->request->data['poll_id'] : 0;    	
    	$text = isset($this->request->data['text']) ? $this->request->data['text'] : '';
    	 
    	$poll = $this->Poll->findById($poll_id);
    	
    	$this->checkPermissionAjaxView($poll);
    	 
    	$this->_checkExistence($poll);
    	if (!$text)
    	{
    		$this->_showError( __d('poll','Text is empty') );
    		return;
    	}
    	$text = trim($text);
    	$item= $this->PollItem->findByName($text);
    	if (!$item)
    	{
    		$this->PollItem->save(
    			array(
					'user_id' => $uid,
					'poll_id' => $poll_id,
					'name' => $text
				)
    		);
    		
    		if (!$poll['Poll']['type']) // single answer
    		{
    			$this->PollAnswer->removeAnswerByPollId($poll_id,$uid);
    		}
    		
    		//add
    		$this->PollAnswer->save(
    			array(
					'user_id' => $uid,
					'poll_id' => $poll_id,
					'item_id' => $this->PollItem->id
				)
    		);
    	}
    	 
    	$this->set('poll',$poll);
    }
    public function ajax_show_user_answer($id = null)
    {
    	$page = isset($this->request->data['page']) ? $this->request->data['page'] : 1;
    	$this->loadModel('Poll.PollItem');
    	$this->loadModel('Poll.PollAnswer');
    	
    	$item = $this->PollItem->findById($id);
    	$this->_checkExistence($item);
    	
    	$limit = 20;
    	
    	$answers = $this->PollAnswer->getAnswers($id,array('page'=>$page,'limit'=>$limit));
    	$this->set('answers',$answers);
    	$this->set('item',$item);
    	$this->set('page',$page);
    	$this->set('limit',$limit);    	
    }
    
    private function _prepareDir($path) {
    	$path = WWW_ROOT . $path;
    
    	if (!file_exists($path)) {
    		mkdir($path, 0755, true);
    		file_put_contents($path . DS . 'index.html', '');
    	}
    }
    
    public function delete($id = null)
    {
    	$this->loadModel('Poll.Poll');
    	$poll = $this->Poll->findById($id);
    	$this->_checkExistence($poll);
    	$this->_checkPermission(array('admins' => array($poll['User']['id'])));
    	 
    	$this->Poll->delete($id);
    	$this->Session->setFlash( __d('poll','Poll has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	if (!$this->isApp())
    	{
    		$this->redirect( '/polls' );
    	}
    }
    
    protected function _checkExistence( $item = null )
    {
    	if ( empty( $item ) ){
    		$this->_showError( __d('poll','Item does not exist') );
    		return;
    	}
    }
}