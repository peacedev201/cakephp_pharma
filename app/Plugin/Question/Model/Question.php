<?php

App::uses('QuestionAppModel','Question.Model');
class Question extends QuestionAppModel {
	public $actsAs = array(
		'Hashtag'=>array(
				'field_created_get_hashtag'=>'description',
				'field_updated_get_hashtag'=>'description',
		),
		'MooUpload.Upload' => array(
			'thumbnail' => array(
					'path' => '{ROOT}webroot{DS}uploads{DS}questions{DS}{field}{DS}',
			)
		),
		'Storage.Storage' => array(
            'type'=>array(
				'questions'=>'thumbnail'
			),
        ),
    );
    
	public $validationDomain = 'question';
	public $belongsTo = array( 'User','Category');
	public $mooFields = array('title','href','plugin','type','url','thumb');
	public $order = 'Question.id desc';
	public $friend_list = array();	
	
	public $validate = array(	
		'title' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Title is required',
		),
		'description' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Description is required',
		),
		'category_id' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Category is required',
		),
		'category_id' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Category is required',
		)		
	);
	
	public function getThumb($row){
		return 'thumbnail';
	}

	public function getFriends($id)
	{
		if (!isset($this->friend_list[$id]))
		{
			$friendModel = MooCore::getInstance()->getModel('Friend');
    		$this->friend_list[$id] = $friendModel->getFriends($id);
		}
		return $this->friend_list[$id];
	}
	
	public function afterSave($created, $options = array())
	{		
		parent::afterSave($created, $options);
		if ($created)
		{
			$user_id = $this->data['Question']['user_id'];
			$userModel = MooCore::getInstance()->getModel('Question.QuestionUser');
			$user = $userModel->getUser($user_id);
			$this->query("UPDATE ".$this->tablePrefix."question_users SET `total_question`=`total_question` + 1 WHERE user_id =" . intval($user_id));
			
		}
	}
	
	public function getTotalQuestions($params = array())
	{
		$conditions = $this->getConditionsQuestions($params);
		return $this->find('count',array('conditions'=>$conditions));
	}
	
	public function getTitle(&$row)
    {
    	return h($row['title']);
    }
	
 	public function getHref($row)
    {
    	$request = Router::getRequest();
    	if (isset($row['id']))
    		return $request->base.'/questions/view/'.$row['id'].'/'.seoUrl($row['moo_title']);
    		
    	return false;
    }
    
    public function delete($id = NULL, $cascade = true) 
    {
    	$question = $this->findById($id);
    	//delete answer
    	$answerModel = MooCore::getInstance()->getModel("Question.QuestionAnswer");
    	$answers = $answerModel->find("all",array("conditions"=>array("QuestionAnswer.question_id"=>$id)));
    	foreach ($answers as $answer)
    	{
    		$answerModel->delete($answer['QuestionAnswer']['id']);
    	}
    	//delete comment
    	$commentModel = MooCore::getInstance()->getModel("Question.QuestionComment");
    	$commentModel->deleteAll(array('QuestionComment.type' => "Question","QuestionComment.type_id"=>$id), false);
    	//delete tag
    	$tagModel = MooCore::getInstance()->getModel("Question.QuestionTagMap");
    	$tags = $tagModel->getTag($id);
    	foreach ($tags as $tag)
    	{
    		$tagModel->delete($tag['QuestionTagMap']['id']);
    	}
    	$tagCoreModel = MooCore::getInstance()->getModel("Tag");
    	$tagCoreModel->deleteAll(array(
    		array('Tag.type'=>'Question_Question','Tag.target_id'=>$id)
    	));
    	
    	//delete attach
    	$attachModel = MooCore::getInstance()->getModel("Question.QuestionAttachment");
    	$attachs = $attachModel->getAttachments('Question',$id);
    	
    	foreach ($attachs as $attach)
    	{
    		$attachModel->deleteAttachment($attach);
    	}
    	
    	//delete favorites
    	$favoriteModel = MooCore::getInstance()->getModel("Question.QuestionFavorite");
    	$favoriteModel->deleteAll(array('QuestionFavorite.question_id' => $id), false);
    	
    	//down question count 
    	$this->query("UPDATE ".$this->tablePrefix."question_users SET `total_question`=`total_question` - 1 WHERE user_id =" . intval($question['Question']['user_id']));
    	
    	//delete vote
    	$voteModel = MooCore::getInstance()->getModel("Question.QuestionVote");
    	$votes = $voteModel->find("all",array(
    		"conditions"=>array("QuestionVote.type_id"=>$id,"QuestionVote.type"=>"Question")
    	));
    	foreach ($votes as $vote)
    	{
    		$voteModel->delete($vote['QuestionVote']['id']);
    	}
    	
    	//delete point history
    	$questionPointHistoryModel = MooCore::getInstance()->getModel('Question.QuestionPointHistory');
    	$history = $questionPointHistoryModel->find('first',array(
    		'conditions' => array(
    			'QuestionPointHistory.type' => 'Create_Question',    			
    			'QuestionPointHistory.type_id' => $id,
    		)
    	));
    	if ($history)
    		$questionPointHistoryModel->delete($history['QuestionPointHistory']['id']);
    	
    	//delete feed
    	$activtyModel = MooCore::getInstance()->getModel("Activity");
    	$activity = $activtyModel->find('first',array(
    		'conditions' => array(
    			'action' => 'question_create',
    			'plugin' => 'Question',
    			'params' => $id,
    		)
    	));
    	if ($activity)
    	{
    		$activtyModel->delete($activity['Activity']['id']);
    		$activtyModel->deleteAll(array('Activity.action' => 'question_create_share', 'Activity.parent_id' => $activity['Activity']['id']));    		
    	}
    	
    	$activtyModel->deleteAll(array('Activity.action'=>'question_item_detail_share','Activity.parent_id'=>$id));
    	
    	$activtyModel->deleteAll(array('Activity.action'=>'answer_question','Activity.item_id'=>$id));
    	
    	parent::delete($id);
    }
    
	public function getConditionsQuestions($params = array())
    {
    	$viewer = MooCore::getInstance()->getViewer();
		$friend_ids = array();
		$conditions = array();
		if (!$viewer || !$viewer['Role']['is_admin'])
		{
			$conditions = array('visiable' => 1);   
			if (isset($params['user_id']) && $params['user_id'])
			{    		
				$friend_ids = $this->getFriends($params['user_id']);
				$conditions['OR'] = array(
					array('Question.user_id' => $params['user_id']),
					array('Question.privacy' => PRIVACY_EVERYONE, 'Question.approve' => 1)
				);
				if (count($friend_ids))
				{
					$conditions['OR'][] = array('Question.user_id'=>$friend_ids,  'Question.approve' => 1 ,'Question.privacy'=>PRIVACY_FRIENDS);
				}
			}
			else
			{
				$conditions['Question.privacy'] = PRIVACY_EVERYONE;
				$conditions['Question.approve'] = 1;
			}	
		}
		
		if ($viewer && $viewer['Role']['is_admin'])
		{
			$friend_ids = $this->getFriends($viewer['User']['id']);
		}
		
		if (isset($params['type']) && $params['type'])
		{
			switch ($params['type']) {
				case 'all':
					break;    			
				case 'my':
				case 'home':	
					$conditions['Question.user_id'] = $params['user_id'];
					break;
				case 'friend':
					if (!count($friend_ids))
						$friend_ids = array(0);
					$conditions['Question.user_id'] = $friend_ids;
					break;
			}
		}
		if (isset($params['feature']) && $params['feature'])
		{
			$conditions['Question.feature'] = $params['feature']; 
		}
		if (isset($params['category']) && $params['category'])
		{
			$conditions['Question.category_id'] = $params['category']; 
		}
		
		if (isset($params['owner_id']) && $params['owner_id'])
		{
			$conditions['Question.user_id'] = $params['owner_id']; 
		}
		
		if (isset($params['ids']))
		{
			$conditions['Question.id'] = $params['ids'];
		}
		
		if (isset($params['interval']) && $params['interval'])
		{
			$conditions['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Question.created'] = intval($params['interval']);
		}
		
		if (isset($params['search']) && $params['search'])
		{    		
			$conditions['AND'] = array(
				'OR' => array('Question.title LIKE '=>'%'.$params['search'].'%')
			);
		}
		
		return $this->addBlockCondition($conditions);
    }
    
    public function countQuestionByCategory($category_id){    	
    	$conditions = array(
    		'Question.category_id' => $category_id,
    	);
    
    	$num = $this->find('count',array(
    			'conditions' => $conditions
    	));
    
    	return $num;
    }
    
	public function getQuestions($params = array())
    {
    	$conditions = $this->getConditionsQuestions($params);
    	$page = 1;
    	if (isset($params['page']) && $params['page'])
    	{
    		$page = $params['page'];
    	}
    	$limit = Configure::read('Question.question_item_per_pages');
    	if (isset($params['limit']) && $params['limit'])
    	{
    		$limit = $params['limit'];
    	}
    	$order = array('Question.id desc');
    	if (isset($params['order']))
    	{
    		$order = array($params['order']);
    	}
    	return $this->find('all',array(
    		'conditions' => $conditions,
    		'limit' => $limit,
    		'page' => $page,
    		'order' => $order
    	));
    }
}
