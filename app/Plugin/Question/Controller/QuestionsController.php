<?php 
class QuestionsController extends QuestionAppController{
	public $components = array('Paginator');
	public function admin_index()
    {
    	$this->set('title_for_layout', __d('question','Questions'));
    	$this->loadModel('Question.Question');
    	$this->Paginator->settings = array(
            'limit' => Configure::read('Question.question_item_per_pages'),
            'order' => array(
                'Question.id' => 'DESC'
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
        	$cond['Question.category_id'] = $this->request->data['category_id'];
        	$this->set('category_id',$this->request->data['category_id']);
        	$passedArgs['category_id'] = $this->request->data['category_id'];
        }
        
    	if ( !empty( $this->request->data['title'] ) )
        {
        	$cond['Question.title LIKE'] = '%'.$this->request->data['title'].'%';
        	$this->set('title',$this->request->data['title']);
        	$passedArgs['title'] = $this->request->data['title'];
        }
        
    	if ( isset( $this->request->data['feature'] ) && $this->request->data['feature'] != '' )
        {
        	$cond['Question.feature'] = $this->request->data['feature'];
        	$this->set('feature',$this->request->data['feature']);
        	$passedArgs['feature'] = $this->request->data['feature'];
        }
        
    	if ( isset( $this->request->data['visable'] ) && $this->request->data['visable'] !='' )
        {
        	$cond['Question.visiable'] = $this->request->data['visable'];
        	$this->set('visable',$this->request->data['visable']);
        	$passedArgs['visable'] = $this->request->data['visable'];
        }
        
        $this->loadModel('Category');
    	$categories = $this->Category->getCategoriesList('Question');
    	$this->set('categories',$categories);
    	
    	$questions = $this->Paginator->paginate('Question',$cond);
        $this->set('questions', $questions);
        $this->set('passedArgs',$passedArgs);
    }
    
    public function admin_delete($id = null)
    {
    	$this->loadModel('Question.Question');
    	if ($id)
    	{
    		$this->Question->delete($id);
    	}
    	 
    	$this->Session->setFlash( __d('question','Question has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	$this->redirect( '/admin/question/questions' );
    }
    
    public function admin_visiable()
    {
    	$this->loadModel('Question.Question');
    	$id = $this->request->data['id'];
    	$value = $this->request->data['value'];
    	if ($id)
    	{
    		$this->Question->id = $id;
    		$this->Question->save(array('visiable'=>$value));
    	}
    	die();
    }
    
    public function admin_approve($id = null)
    {
    	$this->loadModel('Question.Question');
    	 
    	if ($id)
    	{
    		$this->Question->id = $id;
    		$this->Question->save(array('approve'=>1));
    
    		$this->loadModel('Activity');
    		$question = $this->Question->findById($id);
    		$privacy = $question['Question']['privacy'];
    		$this->Activity->updateAll(array('privacy'=>$privacy,'share'=>1),array('action'=>'question_create','plugin'=>'Question','params'=>$id));
    		
    		$questionPointHistoryModel = MooCore::getInstance()->getModel('Question.QuestionPointHistory');
    		$data = array(
    				'type' => 'Create_Question',
    				'type_id' => $id,
    				'from_user_id' => $question['Question']['user_id'],
    				'user_id' => $question['Question']['user_id'],
    				'point' => Configure::read('Question.question_point_create_question')
    		);
    		$questionPointHistoryModel->save($data);
    
    		//Send mail to user
    		$question = $this->Question->findById($id);
    		$ssl_mode = Configure::read('core.ssl_mode');
    		$http = (!empty($ssl_mode)) ? 'https' :  'http';
    		$this->MooMail->send($question['User']['email'],'question_approve',
    				array(
    						'question_title' => $question['Question']['moo_title'],
    						'question_link' => $http.'://'.$_SERVER['SERVER_NAME'].$question['Question']['moo_href'],
    				)
    				);
    
    		$this->Session->setFlash( __d('question','Question has been approved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    		$this->redirect( '/admin/question/questions' );
    	}
    }
    
    public function admin_feature()
    {
    	$this->loadModel('Question.Question');
    	$id = $this->request->data['id'];
    	$value = $this->request->data['value'];
    	if ($id)
    	{
    		$this->Question->id = $id;
    		$this->Question->save(array('feature'=>$value));
    	}
    	die();
    	 
    }
    
    public function index($type = null)
    {
    	$this->set('title_for_layout', '');
    	if (!$type)
    	{
    		$type = 'all';
    	}
    	
    	$this->set('type',$type);
    	
    	if ($this->isApp())
    	{
    		App::uses('browseQuestionWidget', 'Question.Controller'.DS.'Widgets'.DS.'question');
    		$widget = new browseQuestionWidget(new ComponentCollection(),null);
    		$widget->beforeRender($this);
    	}
    }
    
	public function browse($type = null,$param = null)
    {
		$this->set('title_for_layout', __d('question','Questions'));
    	$this->loadModel('Question.Question');
    	$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
    	$url = ( !empty( $param ) )	? $type . '/' . $param : $type;
    	$uid = MooCore::getInstance()->getViewer(true);
    	$questions = array();
    	$total = 0;
		
    	switch ($type) {
    		case 'home':
    			$questions = $this->Question->getQuestions(array('order'=>'Question.feature DESC, Question.id DESC','type'=>$type,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Question->getTotalQuestions(array('type'=>$type,'user_id'=>$uid,'page'=>$page));
    			break;
    		case 'profile':
    			$questions = $this->Question->getQuestions(array('order'=>'Question.feature DESC, Question.id DESC','owner_id'=>$param,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Question->getTotalQuestions(array('owner_id'=>$param,'user_id'=>$uid,'page'=>$page));
    			break;
    	}
    	
    	$limit = Configure::read('Question.question_item_per_pages');
		$is_view_more = (($page - 1) * $limit  + count($questions)) < $total;
    	
		$this->set('is_view_more',$is_view_more);
    	$this->set('questions', $questions);
    	$this->set('type',$type);
    	$this->set('page',$page);
    	$this->set('param',$param);
		$this->set('url_more', '/questions/browse/' . h($url) . '/page:' . ( $page + 1 ) ) ;
    }
    
    public function badges()
    {
    	$this->set('title_for_layout', '');
    	$this->set('type','badges');
    }
    
    public function ratings()
    {
    	$this->set('title_for_layout', '');
    	$this->loadModel('Question.QuestionUser');
    	$this->set('type','ratings');
    	$scope = array();
    	$scope['conditions'] = $this->QuestionUser->addBlockCondition(array());    	
    	$scope['limit'] = Configure::read('Question.question_item_per_pages');
    	$scope['order'] = array('QuestionUser.total'=>'DESC');
    	
    	$this->Paginator->settings = $scope;
    	$users = $this->Paginator->paginate('QuestionUser');
    	$this->set('users',$users);
    }
    
    public function view($id = null)
    {
    	$this->set('title_for_layout', '');
    	$uid = MooCore::getInstance()->getViewer(true);
    	$extension = Configure::read('Question.question_filetype_allow');
    	$viewer = MooCore::getInstance()->getViewer();
    	$this->loadModel('Question.Question');
    	$this->loadModel('Question.QuestionAnswer');
    	
    	$this->Question->recursive = 2;
    	$question = $this->Question->findById($id);
    	if ($question['Category']['id'])
    	{
    		foreach ($question['Category']['nameTranslation'] as $translate)
    		{
    			if ($translate['locale'] == Configure::read('Config.language'))
    			{
    				$question['Category']['name'] = $translate['content'];
    				break;
    			}
    		}
    	}
    	$this->Question->recursive = 0;
    	
    	$this->_checkExistence($question);
    	
    	//check visiable
    	if (!$question['Question']['visiable']  && !($uid && $viewer['Role']['is_admin']))
    	{
    		$this->_checkExistence(null);
    	}
    	
    	//check approve
    	if (!$question['Question']['approve'] && $uid != $question['Question']['user_id'] && !($uid && $viewer['Role']['is_admin']))
    	{
    		$this->_checkExistence(null);
    	}
    	
    	$this->_checkPermission( array('aco' => 'question_view'));
    	$this->_checkPermission( array('user_block' => $question['Question']['user_id']) );
    	MooCore::getInstance()->setSubject($question);
    	
    	$areFriends = false;
    	if ($uid)
    	{
    		$this->loadModel('Friend');
    		$areFriends = $this->Friend->areFriends($uid, $question['User']['id']);
    	}
    	$this->_checkPrivacy($question['Question']['privacy'], $question['User']['id'], $areFriends);
    	
    	$this->loadModel("Question.QuestionTagMap");
    	$tags = $tags_old = $this->QuestionTagMap->getTag($question['Question']['id']);
    	
    	$this->set('title_for_layout', htmlspecialchars($question['Question']['title']));
    	$description = $this->getDescriptionForMeta($question['Question']['description']);
    	if ($description) {
    		$this->set('description_for_layout', $description);
    		if (count($tags))
    		{
    			$tmp = array();
    			foreach ($tags as $tag)
    			{
    				$tmp[] = $tag['QuestionTag']['title'];
    			}
    			
    			$tags = implode(",", $tmp).' ';
    		}
    		else
    		{
    			$tags = '';
    		}
    		$this->set('mooPageKeyword', $this->getKeywordsForMeta($tags.$description));
    	}    
    	
    	// set og:image
    	if ($question['Question']['thumbnail']){
    		$mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
    		$this->set('og_image', $mooHelper->getImageUrl($question));
    	}
    	
    	$scope = array();
    	$scope['conditions'] = array('QuestionAnswer.question_id'=>$id);
    	$scope['conditions'] = $this->QuestionAnswer->addBlockCondition($scope['conditions']);
    	$scope['limit'] = Configure::read('Question.question_item_per_pages');
    	$scope['order'] = array('QuestionAnswer.best_answers'=>'DESC');
    	$tab = isset($this->request->query['tab']) ? $this->request->query['tab'] : 'active';
    	switch ($tab)
    	{
    		case 'active':
    			$scope['order']['QuestionAnswer.active_time'] ='DESC';
    			break;
    		case 'vote':
    			$scope['order']['QuestionAnswer.vote_count'] ='DESC';
    			break;
    		case 'old':
    			$scope['order']['QuestionAnswer.created'] ='';
    			break;
    	}
    	$this->Paginator->settings = $scope;
    	$answers = $this->Paginator->paginate('QuestionAnswer');
    	
    	if ($uid != $question['Question']['user_id'])
    	{
    		$this->Question->id = $id;
    		$view_count = $question['Question']['view_count'] + 1;
    		$this->Question->save(array('view_count'=>$view_count));
    	}
    	
    	$this->set('tab',$tab);
    	$this->set('question',$question);
    	$this->set('answers',$answers);
    	$this->set('extension',$extension);
    	$this->set('tags',$tags_old);
    }
    
    public function post_answer()
    {
    	$this->loadModel("Question.Question");
    	$this->loadModel("Question.QuestionAnswer");
    	$this->loadModel("Question.QuestionAttachment");
    	
    	$question_id = isset($this->request->data['question_id']) ? $this->request->data['question_id'] : '';
    	$content = isset($this->request->data['content']) ? $this->request->data['content'] : '';
    	$attachments = isset($this->request->data['attachments']) ? $this->request->data['attachments'] : '';
    	if (!$question_id || !$content)
    	{
    		die();
    	}
    	
    	$question = $this->Question->findById($question_id);
    	if (!$question)
    	{
    		die();
    	}
    	
    	$this->checkPermissionView($question);
    	$helper = MooCore::getInstance()->getHelper("Question_Question");
    	$uid = MooCore::getInstance()->getViewer(true);
    	$viewer = MooCore::getInstance()->getViewer();
    	
    	if (!$helper->canAnswer($question,$uid))
    	{
    		die();
    	}
    	
    	
    	$this->QuestionAnswer->save(array(
    		'user_id' => $uid,
    		'question_id' => $question_id,
    		'description' => $content,
    		'active_time' => date("Y-m-d H:i:s")
    	));

   	 	//attachments
		$new_attachments = explode(',', $attachments);
		 
		if ($attachments && count($new_attachments))
		{
			$this->QuestionAttachment->updateAll(array('QuestionAttachment.type_id' => $question_id), array('QuestionAttachment.id' => $new_attachments));
		}
		
		//photos
		$this->loadModel("Photo");
		$photos = explode(',',$this->request->data['photo_ids']);
		$this->Photo->updateAll(array('Photo.target_id' => $question_id), array('Photo.id' => $photos,'Photo.type' => 'QuestionAnswer'));
		$result = $this->Photo->find("all",array(
				'recursive'=>1,
				'conditions' =>array(
						'Photo.id' => $photos,
				)));
		if($result){
			$view = new View($this);
			$mooHelper = $view->loadHelper('Moo');
			foreach ($result as $iPhoto){
				$iPhoto["Photo"]['moo_thumb'] = 'thumbnail';
				$mooHelper->getImageUrl($iPhoto, array('prefix' => '450'));
				$mooHelper->getImageUrl($iPhoto, array('prefix' => '1500'));
			}
		}
		
		$questionPointHistoryModel = MooCore::getInstance()->getModel('Question.QuestionPointHistory');
		$data = array(
				'type' => 'Create_Answer',
				'type_id' => $this->QuestionAnswer->id,
				'from_user_id' => $uid,
				'user_id' => $uid,
				'point' => Configure::read('Question.question_point_create_answer')
		);
		$questionPointHistoryModel->save($data);
		
		//notify
		if ($uid != $question['Question']['user_id'])
		{
			$this->loadModel("Notification");
			$this->Notification->save(array(
				'user_id' => $question['Question']['user_id'],
				'sender_id' => $uid,
				'action' => 'answer_question',
				'url' => $question['Question']['moo_url'],
				'params' => $question_id,
				'plugin' => 'Question',
			));
		}
		
		//add feed
		$this->loadModel("Activity");
		$this->Activity->clear();
		$this->Activity->save(array(
			'type'=>'User',
			'user_id' => $uid,
			'action' => 'answer_question',
			'item_type' => 'Question_Question',
			'item_id' => $question_id,
			'params'=> 'no-comments',
			'plugin' => 'Question',
			'privacy' => $viewer['User']['privacy'],
			'share' => 0,
		));
		
		$this->Session->setFlash(__d('question','Answer has been successfully added'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
		$this->redirect($question['Question']['moo_url']);
    }
    
    public function edit_answer($id = null)
    {
    	$this->set('title_for_layout', __d('question','Edit answer'));
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	$this->loadModel("Question.QuestionAnswer");
    	    	
    	if (!$id)
    	{
    		$this->_checkExistence(null);
    	}
    	
    	$answer = $this->QuestionAnswer->findById($id);
    	if (!$answer)
    	{
    		$this->_checkExistence(null);
    	}
    	if (!$helper->canEditAnswer($answer,MooCore::getInstance()->getViewer()))
    	{
    		$this->_checkExistence(null);
    	}
    	
    	$this->set('answer',$answer);
    }
    
    public function ajax_edit_answer()
    {
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	$this->loadModel("Question.QuestionAnswer");
    	$this->loadModel("Question.QuestionAttachment");
    	
    	 
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$content = isset($this->request->data['content']) ? $this->request->data['content'] : '';
    	$attachments = isset($this->request->data['attachments']) ? $this->request->data['attachments'] : '';
    	$result = array('status'=>false);
    	if (!$id || !$content)
    	{
    		echo json_encode($result);die();
    	}
    	 
    	$answer = $this->QuestionAnswer->findById($id);
    	if (!$answer)
    	{
    		echo json_encode($result);die();
    	}
    	
    	if (!$helper->canEditAnswer($answer,MooCore::getInstance()->getViewer()))
    	{
    		echo json_encode($result);die();
    	}
    	 
    	$uid = MooCore::getInstance()->getViewer(true);
    	
    	if (trim($content) != trim($answer['QuestionAnswer']['description']))
    	{
	    	$this->loadModel("Question.QuestionContentHistory");
	    	if (!$answer['QuestionAnswer']['edited'])
	    	{
	    		$this->QuestionContentHistory->save(array(
	    				'user_id' => $answer['QuestionAnswer']['user_id'],
	    				'type' => 'Answer',
	    				'content' =>  $answer['QuestionAnswer']['description'],
	    				'type_id' => $id,
	    				'created' => $answer['QuestionAnswer']['created']
	    		));
	    	}
	    	
	    	$this->QuestionContentHistory->clear();
	    	$this->QuestionContentHistory->save(array(
	    			'user_id' => $uid,
	    			'type' => 'Answer',
	    			'type_id' => $id,
	    			'content' =>$content,
	    	));
	    	 
	    	$this->QuestionAnswer->id =$id;
	    	$this->QuestionAnswer->save(array('edited'=>1,'description'=>$content));
	    	
	    	//attachments
	    	$new_attachments = explode(',', $attachments);
	    		
	    	if ($attachments && count($new_attachments))
	    	{
	    		$this->QuestionAttachment->updateAll(array('QuestionAttachment.type_id' => $id), array('QuestionAttachment.id' => $new_attachments));
	    	}
	    	
	    	//photos
	    	$this->loadModel("Photo");
	    	$photos = explode(',',$this->request->data['photo_ids']);
	    	$this->Photo->updateAll(array('Photo.target_id' => $answer['QuestionAnswer']['id']), array('Photo.id' => $photos,'Photo.type' => 'QuestionAnswer'));
	    	$result = $this->Photo->find("all",array(
	    		'recursive'=>1,
	    		'conditions' =>array(
	    			'Photo.id' => $photos,
	    	)));
	    	if($result){
	    		$view = new View($this);
	    		$mooHelper = $view->loadHelper('Moo');
	    		foreach ($result as $iPhoto){
	    			$iPhoto["Photo"]['moo_thumb'] = 'thumbnail';
	    			$mooHelper->getImageUrl($iPhoto, array('prefix' => '450'));
	    			$mooHelper->getImageUrl($iPhoto, array('prefix' => '1500'));
	    		}
	    	}
	    	
	    	$result['status'] = true;
    	}    	
    	echo json_encode($result);die();
    }
    
    public function ajax_vote()
    {
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	$uid = MooCore::getInstance()->getViewer(true);
    	
    	$type = isset($this->request->data['type']) ? $this->request->data['type'] : '';
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$up = isset($this->request->data['up']) ? $this->request->data['up'] : '0';
    	
    	$result = array('status'=>false);
    	
    	if (!$type || !$id || !in_array($up, array('0','1')))
    	{
    		echo json_encode($result);die();
    	}
    	
    	$this->loadModel("Question.QuestionVote");
    	$this->loadModel("Question.Question");
    	$object = $this->QuestionVote->getTypeModel($type,$id);
    	if (!$object)
    	{
    		echo json_encode($result);die();
    	}
    	
    	if ($type == 'Answer')
    	{
    		$question = $this->Question->findById($object['QuestionAnswer']['question_id']);
    		$this->checkPermissionView($question);
    	}
    	else
    	{
    		$this->checkPermissionView($object);
    	}
    	
    	//check permission up & down
    	if ($up == 1)
    	{
    		if ($helper->can('vote_up',MooCore::getInstance()->getViewer()) != QUESTION_CAN_ERROR_NONE)
    		{
    			echo json_encode($result);die();
    		}
    	}
    	else
    	{
    		if ($helper->can('vote_down',MooCore::getInstance()->getViewer()) != QUESTION_CAN_ERROR_NONE)
    		{
    			echo json_encode($result);die();
    		}
    	}
    	
    	$current_vote = $this->QuestionVote->find('first',array(
    		'conditions'=>array(
    			'QuestionVote.user_id'=> $uid,
    			'QuestionVote.type' => $type,
    			'QuestionVote.type_id' => $id,
    		)
    	));
    	$check = false;
    	if ($current_vote)
    	{
    		$this->QuestionVote->delete($current_vote['QuestionVote']['id']);
    		if ($current_vote['QuestionVote']['vote'] == $up)
    		{
    			$check = true;
    		}
    	}
    	
    	if (!$check)
    	{
	    	$this->QuestionVote->save(array(
	    		'user_id' => $uid,
	    		'type' => $type,
	    		'type_id' => $id,
	    		'vote' => $up, 
	    	));
    	}
    	
    	$object = $this->QuestionVote->getTypeModel($type,$id);
    	$result['status'] = true;
    	$result['vote_count'] = $object[key($object)]['vote_count'];
    	
    	echo json_encode($result);die();
    }
    
    public function ajax_comment()
    {
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	$uid = MooCore::getInstance()->getViewer(true);
    	 
    	$type = isset($this->request->data['type']) ? $this->request->data['type'] : '';
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$message = isset($this->request->data['message']) ? $this->request->data['message'] : '';
    	 
    	if (!$type || !$id || !$message)
    	{
    		die();
    	}
    	
    	$this->loadModel("Question.QuestionComment");
    	$this->loadModel("Question.QuestionAnswer");
    	$this->loadModel("Question.Question");
    	$object = $this->QuestionComment->getTypeModel($type,$id);
    	if (!$object)
    	{
    		die();
    	}
    	
    	if ($type == 'Answer')
    	{
    		$question = $this->Question->findById($object['QuestionAnswer']['question_id']);
    		$this->checkPermissionView($question);
    	}
    	else
    	{
    		$this->checkPermissionView($object);
    	}
    	
    	if ($helper->can('leave_comment',MooCore::getInstance()->getViewer()) != QUESTION_CAN_ERROR_NONE)
    	{
    		die();
    	}
    	
    	if ($type == 'Answer')
    	{
    		$this->QuestionAnswer->id = $id;
    		$this->QuestionAnswer->save(array('active_time'=>date('Y-m-d H:i:s')));
    	}
    	
    	$this->QuestionComment->clear();
    	$this->QuestionComment->save(array(
    			'user_id' => $uid,
    			'type' => $type,
    			'type_id' => $id,
    			'content' => $message,
    	));
    	$comment = $this->QuestionComment->read();
    	
    	$this->layout = 'ajax';
    	$this->set('comment', $comment);
    	$this->render('Question.Elements/comment');
    }
    
    public function ajax_delete_answer()
    {
    	$helper = MooCore::getInstance()->getHelper('Question_Question');    	
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$result = array('status'=>false);
    	if (!$id)
    	{
    		echo json_encode($result);die();
    	}
    	$this->loadModel("Question.QuestionAnswer");
    	$answer = $this->QuestionAnswer->findById($id);
    	$this->_checkExistence($answer);
    	
    	if (!$helper->canEditAnswer($answer,MooCore::getInstance()->getViewer()))
    	{
    		echo json_encode($result);die();
    	}
    	
    	$this->QuestionAnswer->delete($id);
    	$result['status'] = true;
    	echo json_encode($result);die();
    }
    
    public function ajax_favorite()
    {
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$result = array('status'=>false);
    	if (!$id)
    	{
    		echo json_encode($result);die();
    	}
    	$this->loadModel("Question.Question");
    	$this->loadModel("Question.QuestionFavorite");
    	$question = $this->Question->findById($id);    	
    	$this->checkPermissionView($question);
    	$uid = MooCore::getInstance()->getViewer(true);
    	 
    	if (!$uid || $uid == $question['Question']['user_id'])
    	{
    		echo json_encode($result);die();
    	}
    	
    	$favorite = $this->QuestionFavorite->checkFavorite($question['Question']['id'],$uid);
    	if ($favorite)
    	{
    		$result['favorite'] = false;
    		$this->QuestionFavorite->delete($favorite['QuestionFavorite']['id']);
    	}
    	else
    	{
    		$this->QuestionFavorite->clear();
    		$this->QuestionFavorite->save(array(
    			'question_id' => $id,
    			'user_id' => $uid
    		));
    		$result['favorite'] = true;
    	}
    	
    	$question = $this->Question->findById($id);
    	$result['status'] = true;
    	$result['count'] = $question['Question']['favorite_count'];
    	echo json_encode($result);die();
    }
    
    public function ajax_show_user_favorite($id)
    {
    	$uid = MooCore::getInstance()->getViewer(true);
    	$this->loadModel("Question.Question");
    	$this->loadModel("Question.QuestionFavorite");
    	$question = $this->Question->findById($id);
    	$this->_checkExistence($question);
    	if ($uid != $question['Question']['user_id'])
    		die();
    	 
    	$favorites = $this->QuestionFavorite->find('all',array(
    		'conditions'=>array('QuestionFavorite.question_id' => $id)
    	));
    	$this->set('favorites',$favorites);    	
    }
    
    public function ajax_mark_best_answer()
    {
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	$uid = MooCore::getInstance()->getViewer(true);
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$result = array('status'=>false);
    	if (!$id)
    	{
    		echo json_encode($result);die();
    	}
    	
    	$this->loadModel("Notification");
    	$this->loadModel("Question.QuestionAnswer");
    	$this->loadModel("Question.Question");
    	$this->loadModel("Question.QuestionPointHistory");
    	
    	$answer = $this->QuestionAnswer->findById($id);
    	$this->_checkExistence($answer);
    	
    	$question = $this->Question->findById($answer['QuestionAnswer']['question_id']);
    	$this->_checkExistence($question);
    	
    	if (!$helper->canMarkBestAnswer($question,$answer,MooCore::getInstance()->getViewer()))
    	{
    		echo json_encode($result);die();
    	}
    	
    	if ($answer['QuestionAnswer']['best_answers'])
    	{
    		$this->QuestionAnswer->id = $id;
    		$this->QuestionAnswer->save(array(
    			'best_answers'=> 0
    		));
    		
    		$this->Question->id = $question['Question']['id'];
    		$this->Question->save(array(
    			'has_best_answers' => 0
    		));
    		
    		$history = $this->QuestionPointHistory->find('first',array(
    			'conditions'=>array(
	    			'type'=>'Best_Answer',
	    			'type_id'=>$id
    			)
    		));
    		if ($history)
    		{
    			$this->QuestionPointHistory->delete($history['QuestionPointHistory']['id']);
    		}
    		
    		$this->Notification->deleteAll(array(
    			'Notification.action' => 'best_answer_question',
    			'params' => $question['Question']['id'],
    			'plugin' => 'Question',
    		));
    		
    		$result['status'] = true;
    		$result['active'] = false;
    	}
    	else
    	{
    		$answer_best = $this->QuestionAnswer->find('first',array(
    			'conditions'=>array(
	    			'question_id'=>$answer['QuestionAnswer']['question_id'],
	    			'best_answers'=>1
    			)
    		));
    		if ($answer_best)
    		{
	    		$history = $this->QuestionPointHistory->find('first',array(
	    			'conditions'=>array(
		    			'type'=>'Best_Answer',
		    			'type_id'=>$answer_best['QuestionAnswer']['id']
	    			)
	    		));
	    		
	    		if ($history)
	    		{
	    			$this->QuestionPointHistory->delete($history['QuestionPointHistory']['id']);
	    		}
	    		
	    		$this->QuestionAnswer->id = $answer_best['QuestionAnswer']['id'];
	    		$this->QuestionAnswer->save(array(
	    				'best_answers'=> 0,	    				
	    		));
	    		
	    		$this->Notification->deleteAll(array(
	    				'Notification.action' => 'best_answer_question',
	    				'params' => $question['Question']['id'],
	    				'plugin' => 'Question',
	    		));
    		}
    		
    		$this->Question->id = $question['Question']['id'];
    		$this->Question->save(array(
    			'has_best_answers' => 1
    		));
    		
    		$this->QuestionAnswer->id = $id;
    		$this->QuestionAnswer->save(array(
    			'best_answers'=> 1,
    			'best_answers_date' => date("Y-m-d H:i:s"),
    		));
    		
    		$this->QuestionPointHistory->clear();
    		$this->QuestionPointHistory->save(array(
    			'type'=>'Best_Answer',
    			'type_id'=>$id,
    			'from_user_id' => $uid,
    			'user_id' => $answer['QuestionAnswer']['user_id'],
    			'point' => Configure::read('Question.question_point_vote_best_answer')
    		));
    		
    		$this->Notification->save(array(
    				'user_id' => $answer['QuestionAnswer']['user_id'],
    				'sender_id' => $uid,
    				'action' => 'best_answer_question',
    				'url' => $question['Question']['moo_url'],
    				'params' => $question['Question']['id'],
    				'plugin' => 'Question',
    		));
    		
    		$result['status'] = true;
    		$result['active'] = true;
    	}
    	
    	echo json_encode($result);die();
    }
    
    public function ajax_load_comment()
    {
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	$uid = MooCore::getInstance()->getViewer(true);
    	 
    	$type = isset($this->request->data['type']) ? $this->request->data['type'] : '';
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';    	
    	 
    	if (!$type || !$id)
    	{
    		die();
    	}
    	
    	$this->loadModel("Question.QuestionComment");
    	$this->loadModel("Question.Question");
    	$object = $this->QuestionComment->getTypeModel($type,$id);
    	if (!$object)
    	{
    		die();
    	}
    	
    	$comment_count = 0;
    	
    	if ($type == 'Answer')
    	{
    		$question = $this->Question->findById($object['QuestionAnswer']['question_id']);
    		$this->checkPermissionView($question);
    		$comment_count = $object['QuestionAnswer']['comment_count'];
    	}
    	else
    	{
    		$this->checkPermissionView($object);
    		$comment_count = $object['Question']['comment_count'];
    	}
    	
    	$comments = $this->QuestionComment->find('all',array(
    		'conditions'=> array(
    			'type' => $type,
    			'type_id' => $id
    		),
    		'offset' => Configure::read("Question.question_item_per_pages"),
    		'limit' =>$comment_count
    	));
    	
    	$this->set('comments',$comments);
    	$this->set('type',$type);
    	$this->set('id',$id);
    }
    
    public function ajax_edit_comment()
    {
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$message = isset($this->request->data['message']) ? $this->request->data['message'] : '';
    	$result = array('status'=>false);    	
    	if (!$id || !$message)
    	{
    		echo json_encode($result);die();
    	}
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	 
    	$this->loadModel("Question.QuestionComment");
    	$comment = $this->QuestionComment->findById($id);
    	if (!$comment)
    	{
    		echo json_encode($result);die();
    	}
    	 
    	if (!$helper->canEditComment($comment,MooCore::getInstance()->getViewer()))
    	{
    		die();
    	}
    	
    	if (trim($message) != trim($comment['QuestionComment']['content']))
    	{
	    	$this->loadModel("Question.QuestionContentHistory");
	    	if (!$comment['QuestionComment']['edited'])
	    	{
	    		$this->QuestionContentHistory->save(array(
	    				'user_id' => $comment['QuestionComment']['user_id'],
	    				'type' => 'Comment',
	    				'content' =>  $comment['QuestionComment']['content'],
	    				'type_id' => $id,
	    				'created' => $comment['QuestionComment']['created']
	    		));
	    	}
	    	
	    	$uid = MooCore::getInstance()->getViewer(true);
	    	
	    	$this->QuestionContentHistory->clear();
	    	$this->QuestionContentHistory->save(array(
	    			'user_id' => $uid,
	    			'type' => 'Comment',
	    			'type_id' => $id,
	    			'content' =>$message,
	    	));
	    	
	    	$this->QuestionComment->id =$id;
	    	$this->QuestionComment->save(array('edited'=>1,'content'=>$message));
	    	$result['status'] = true;
    	}
    	
    	echo json_encode($result);die();
    }
    
    public function ajax_remove_comment()
    {
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	if (!$id)
    	{
    		die();		
    	}
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	
    	$this->loadModel("Question.QuestionComment");
    	$comment = $this->QuestionComment->findById($id);
    	if (!$comment)
    	{
    		die();
    	}
    	
    	if (!$helper->canEditComment($comment,MooCore::getInstance()->getViewer()))
    	{
    		die();
    	}
    	$this->QuestionComment->delete($id);
    	die();
    }
    
    public function content_history($type,$id)
    {
    	$this->loadModel('Question.QuestionContentHistory');
    	$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
    	$count = $this->QuestionContentHistory->getHistoryCount($type, $id);
    	$this->set('historiesCount', $count);
    	
    	$cond = array(
    			'type' => $type,
    			'type_id' => $id
    	);
    	
    	if ($page == 1)
    	{
    		$histories = $this->QuestionContentHistory->find('all',
    			array(
    					'conditions' => $cond,
    					'limit' => Configure::read("Question.question_item_per_pages"),
    					'page' => $page
    			));
    	}
    	else 
    	{
    		$histories = $this->QuestionContentHistory->find('all',
    				array(
    						'conditions' => $cond,
    						'limit' => $count - Configure::read("Question.question_item_per_pages"),
    						'offset' => Configure::read("Question.question_item_per_pages")
    				));
    	}
    	
    	$this->set('page', $page);
    	$this->set('histories', $histories);
    	$this->set('more_url', '/questions/content_history/' . $type . '/' . $id . '/page:' . ( $page + 1 ));
    }
    
    private function checkPermissionView($question)
    {
    
    	$uid = MooCore::getInstance()->getViewer(true);
    	$viewer = MooCore::getInstance()->getViewer();
    	$this->_checkExistence($question);
    	
    	$this->_checkPermission( array('aco' => 'question_view'));
    
    	//check visiable
    	if (!$question['Question']['visiable']  && !($uid && $viewer['Role']['is_admin']))
    	{
    		$this->_checkExistence(null);
    	}
    
    	$this->_checkPermission( array('aco' => 'question_view'));
    	$this->_checkPermission( array('user_block' => $question['Question']['user_id']) );
    
    	$areFriends = false;
    	if ($uid)
    	{
    		$this->loadModel('Friend');
    		$areFriends = $this->Friend->areFriends($uid, $question['User']['id']);
    	}
    	$this->_checkPrivacy($question['Question']['privacy'], $question['User']['id'], $areFriends);
    }
    
    private function _prepareDir($path) {
    	$path = WWW_ROOT . $path;
    
    	if (!file_exists($path)) {
    		mkdir($path, 0755, true);
    		file_put_contents($path . DS . 'index.html', '');
    	}
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
    
	public function create($id = null)
    {    	   	
    	$this->_checkPermission(array('aco' => 'question_create'));
    	$this->_checkPermission(array('confirm' => true));
    	
    	$extension = Configure::read('Question.question_filetype_allow');
    	$uid = MooCore::getInstance()->getViewer(true);    	
    	$this->loadModel('Question.Question');
    	$this->loadModel('Question.QuestionTagMap');
    	$this->loadModel('Question.QuestionAttachment');
    	$this->set('title_for_layout', __d('question','Create a new question'));
    	
    	$this->loadModel('Category');
    	$role_id = $this->_getUserRoleId();
    	$categories = $this->Category->getCategoriesList('Question',$role_id);
    	$this->set('categories',$categories);
    	
    	$is_edit = false;
    	if ($id)
    	{    		
    		$question = $this->Question->findById($id);    		
    		if ($question)
    		{
    			$this->_checkPermission(array('admins' => array( $question['User']['id'])));
    			
    			$this->Question->id = $id;
    			$is_edit = true;
    			$question['Question']['tags'] = $this->QuestionTagMap->getTag($id);    			
    			$this->set('title_for_layout', __d('question','Edit question'));
    		}
    	}
    	else
    	{
    		$question = $this->Question->initFields();
    		$question['Question']['tags'] = '';
    	}    	    	
    	$this->set('question',$question);
    	$this->set('is_edit',$is_edit);
    	$this->set('extension',$extension);
    	
    	if (!$this->request->is('post'))
    	{
    		return;
    	}    
    }
    
    public function save()
    {
    	$this->_checkPermission(array('aco' => 'question_create'));
    	$this->_checkPermission(array('confirm' => true));
    	
    	$uid = MooCore::getInstance()->getViewer(true);
    	$this->loadModel('Question.Question');
    	$this->loadModel('Question.QuestionTagMap');
    	$this->loadModel('Question.QuestionTag');
    	$this->loadModel('Question.QuestionAttachment');
    	
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	$is_edit = false;
    	$this->loadModel('Question.Question');
    	$question = array();
    	if ($id)
    	{    		
    		$question = $this->Question->findById($id);
    		if ($question)
    		{
    			$this->_checkPermission(array('admins' => array( $question['User']['id'])));
    			
    			$this->Question->id = $id;
    			$is_edit = true;
    			$question['Question']['tags'] = $this->QuestionTagMap->getTag($id);
    		}
    	}
    	if (!$is_edit)
    	{
    		$this->request->data['user_id'] = $uid;
    		$this->request->data['approve'] = Configure::read('Question.question_approval');
    	}
    	elseif ($is_edit && trim($this->request->data['description']) != trim($question['Question']['description']))
    	{
    		$this->request->data['edited'] = true;
    	}
    	
    	$this->Question->set($this->request->data);
        $this->_validateData($this->Question);
        $data = $this->request->data;
        
        if ($this->Question->save()) {
        	//update history
        	if ($is_edit && trim($this->request->data['description']) != trim($question['Question']['description']))
        	{        		
        		$this->loadModel("Question.QuestionContentHistory");
        		if (!$question['Question']['edited'])
        		{
        			$this->QuestionContentHistory->save(array(
        					'user_id' => $question['Question']['user_id'],
        					'type' => 'Question',
        					'content' =>  $question['Question']['description'],
        					'type_id' => $id,
        					'created' => $question['Question']['created']
        			));
        		}
        	
        		$this->QuestionContentHistory->clear();
        		$this->QuestionContentHistory->save(array(
        				'user_id' => $uid,
        				'type' => 'Question',
        				'type_id' => $id,
        				'content' =>$this->request->data['description'],
        		));
        	}
        	
        	//attachments
        	$new_attachments = explode(',', $data['attachments']);       	
        	
        	if (count($new_attachments))
        	{
        		$this->QuestionAttachment->updateAll(array('QuestionAttachment.type_id' => $this->Question->id), array('QuestionAttachment.id' => $new_attachments));
        	}
        	
        	//photos
        	$this->loadModel("Photo");
        	$photos = explode(',',$data['photo_ids']);
        	$this->Photo->updateAll(array('Photo.target_id' => $this->Question->id), array('Photo.id' => $photos,'Photo.type' => 'Question'));
        	$result = $this->Photo->find("all",array(
        		'recursive'=>1,
        		'conditions' =>array(
        			'Photo.id' => $photos,
        	)));
        	if($result){
        		$view = new View($this);
        		$mooHelper = $view->loadHelper('Moo');
        		foreach ($result as $iPhoto){
        			$iPhoto["Photo"]['moo_thumb'] = 'thumbnail';
        			$mooHelper->getImageUrl($iPhoto, array('prefix' => '450'));
        			$mooHelper->getImageUrl($iPhoto, array('prefix' => '1500'));
        		}
        	}
        	//tags        	
        	$new_tags = isset($data['tags']) ? $data['tags'] : array();
        	$delete_tags = array();
        	if (isset($question['Question']['tags']) && count($question['Question']['tags']))
        	{
        		$tag_ids = array();
        		foreach ($question['Question']['tags'] as $tag)
        		{
        			$tag_ids[] = $tag['QuestionTagMap']['tag_id'];
        		}
        		$tmp = $new_tags;
        		$new_tags = array_diff_assoc($tmp,$tag_ids);        		
        		$delete_tags = array_diff_assoc($tag_ids,$tmp);
        	}        	
        	
        	if (count($delete_tags))
        	{
        		foreach ($delete_tags as $tag_id)
        		{
        			$this->QuestionTagMap->deleteAll(array('QuestionTagMap.tag_id'=>$tag_id,'QuestionTagMap.question_id'=>$this->Question->id),false,true);
        		}
        	}
        	if (count($new_tags))
        	{
        		foreach ($new_tags as $tag_id)
        		{
        			if (strpos($tag_id, 'new_') === FALSE)
        			{
        				$tag_check =$this->QuestionTag->findById($tag_id);
        				if ($tag_check)
        				{
	        				$this->QuestionTagMap->clear();
	        				$this->QuestionTagMap->save(array(
	        					'tag_id' => $tag_id,
	        					'question_id' => $this->Question->id
	        				));
        				}
        			}
        			elseif ($helper->can('create_new_tag',MooCore::getInstance()->getViewer()) == QUESTION_CAN_ERROR_NONE)
        			{
        				$title = str_replace('new_','',$tag_id);
        				$tag_check = $this->QuestionTag->findByTitle($title);
        				$tag_new_id = 0;
        				if (!$tag_check)
        				{
	        				$this->QuestionTag->clear();
	        				$this->QuestionTag->save(array(
	        					'title' => str_replace('new_','',$tag_id),
	        					'user_id' => $uid					
	        				));
	        				$tag_new_id = $this->QuestionTag->id;
        				}
        				else
        				{
        					$tag_new_id = $tag_check['QuestionTag']['id'];
        				}
        				
        				$this->QuestionTagMap->clear();
        				$this->QuestionTagMap->save(array(
        					'tag_id' => $tag_new_id,
        					'question_id' => $this->Question->id
        				));
        			}
        		}
        	}
        	
        	//tag core
        	$tags = $this->QuestionTagMap->getTag($this->Question->id);
        	$tmp = array();
        	foreach ($tags as $tag)
        	{
        		$tmp[] = $tag['QuestionTag']['title'];
        	}
        	$this->loadModel('Tag');
        	$this->Tag->saveTags(implode(',', $tmp), $this->Question->id, 'Question_Question');
        	
        	//add activty
        	$this->loadModel('Activity');
        	
        	if (!$is_edit)
        	{
        		if (Configure::read('Question.question_approval'))
        			$privacy = $data['privacy'];
        		else
					$privacy = PRIVACY_ME;
				
				$this->Activity->save(array('type' => 'user',
						'action' => 'question_create',
						'user_id' => $uid,
						'plugin' => 'Question',
						'params' => $this->Question->id,
						'share' => $privacy != PRIVACY_ME ? 1 : 0,
						'privacy' => $privacy
				));
        	}
        	else
        	{
        		if (Configure::read('Question.question_approval') || $question['Question']['approve'])
        			$privacy = $data['privacy'];
        		else
        			$privacy = PRIVACY_ME;
        	
        		$this->Activity->updateAll(array('privacy'=>$privacy,'share' => $privacy != PRIVACY_ME ? 1 : 0),array('action'=>'question_create','params'=>$this->Question->id));
        	}
        	
        	
        	if (!$is_edit)
        	{
        		if (Configure::read('Question.question_approval'))
        		{
        			$questionPointHistoryModel = MooCore::getInstance()->getModel('Question.QuestionPointHistory');
        			$data = array(
        					'type' => 'Create_Question',
        					'type_id' => $this->Question->id,
        					'from_user_id' => $uid,
        					'user_id' => $uid,
        					'point' => Configure::read('Question.question_point_create_question')
        			);
        			$questionPointHistoryModel->save($data);
        			
        			$this->Session->setFlash(__d('question','Question has been successfully added'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        		}
        		else
        			$this->Session->setFlash(__d('question',"Question has been successfully added and is pending for admin's approval."), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        	}
        	else
        	{
        		$this->Session->setFlash(__d('question','Question has been successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        	}
        }       
        $question = $this->Question->read();
        $response['result'] = 1;   
        $response['href'] = $question['Question']['moo_href'];
			
		echo json_encode($response);
		exit;
    }
    
    public function delete($id = null)
    {
    	$this->loadModel('Question.Question');
    	$question = $this->Question->findById($id);
    	$this->_checkExistence($question);
    	$this->_checkPermission(array('admins' => array($question['User']['id'])));
    
    	$this->Question->delete($id);
    	$this->Session->setFlash( __d('question','Question has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	if (!$this->isApp())
    	{
    		$this->redirect( '/questions' );
    	}
    }
}