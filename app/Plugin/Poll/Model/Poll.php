<?php

App::uses('PollAppModel','Poll.Model');
class Poll extends PollAppModel {
    
    public $actsAs = array(
        'Activity' => array(
            'type' => 'user',
            'action_afterCreated' => 'poll_create',
            'item_type' => 'Poll_Poll',
            'query' => 1,
            'params' => 'item',
        	'share' => true,
        ),
    	'MooUpload.Upload' => array(
			'thumbnail' => array(
				'path' => '{ROOT}webroot{DS}uploads{DS}polls{DS}{field}{DS}',
			)
		),
		'Storage.Storage' => array(
            'type'=>array(
				'polls'=>'thumbnail',
			),
        ),
    );
    
    public $validationDomain = 'poll';
    
    public $belongsTo = array( 'User','Category');
    
    public $mooFields = array('title','href','plugin','type','url','thumb','privacy');
	
	public $hasMany = array( 'Comment' => array( 
											'className' => 'Comment',	
											'foreignKey' => 'target_id',
											'conditions' => array('Comment.type' => 'Poll_Poll'),						
											'dependent'=> true
										),
						  	 'Like' => array( 
											'className' => 'Like',	
											'foreignKey' => 'target_id',
											'conditions' => array('Like.type' => 'Poll_Poll'),						
											'dependent'=> true
										),
							'Tag' => array(
									'className' => 'Tag',
									'foreignKey' => 'target_id',
									'conditions' => array('Tag.type' => 'Poll_Poll'),
									'dependent'=> true
							)
						); 
	
	public $order = 'Poll.id desc';
	
	public $validate = array(	
		'title' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Title is required',
		),
		'category_id' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Category is required',
		),
		'answers' => 	array(
				'checkAnswers' => array(
						'rule' => array('checkAnswers')
				)
		),
		'tags' => array(
			'validateTag' => array(
					'rule' => array('validateTag'),
					'message' => 'No special characters ( /,?,#,%,...) allowed in Tags',
			)
		)
	);
	
	public function getTitle(&$row)
	{
		if (isset($row['title']))
		{
			$row['title'] = htmlspecialchars($row['title']);
			return $row['title'];
		}
		return '';
	}
	
	public function updateCountAnswer($poll_id)
	{
		$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
		$sum = $itemModel->find('first', 
			array(
				'conditions' => array('PollItem.poll_id' => $poll_id),
				'fields' => array('sum(PollItem.total_user) as total_sum')
			)
		);
		$this->clear();
		$this->id = $poll_id;
		$this->save(array(
			'answer_count' => $sum[0]['total_sum']
		));
	}
	
	public function getPrivacy($row){
		if (isset($row['privacy'])){
			return $row['privacy'];
		}
		return false;
	}
	
	public function beforeDelete($cascade = true)
	{
		$item = $this->findById($this->id);
		if ($item)
		{
			$activityModel = MooCore::getInstance()->getModel('Activity');
			$parentActivity = $activityModel->find('list', array('fields' => array('Activity.id') , 'conditions' =>
					array('Activity.item_type' => 'Poll_Poll', 'Activity.item_id' => $item['Poll']['id'])));
	
			$activityModel->deleteAll(array('Activity.item_type' => 'Poll_Poll', 'Activity.parent_id' => $parentActivity));
	
			$activityModel->deleteAll(array('Activity.item_type' => 'Poll_Poll', 'Activity.parent_id' => $this->id));
			
			$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
			$itemModel->deleteAll(array('PollItem.poll_id' => $this->id));

			$answerModel = MooCore::getInstance()->getModel('Poll.PollAnswer');
			$answerModel->deleteAll(array('PollAnswer.poll_id' => $this->id));
		}
		 
		parent::beforeDelete($cascade);
	}
	
	public function updateCounter($id, $field = 'comment_count',$conditions = '',$model = 'Comment') {
		if(empty($conditions)){
			$conditions = array('Comment.type' => 'Poll_Poll', 'Comment.target_id' => $id);
		}
		parent::updateCounter($id,$field, $conditions, $model);
	}
	
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id,$table,$ds);
		$this->validate['answers']['checkAnswers']['message'] = str_replace('[number]', Configure::read('Poll.poll_min_answer'), __d('poll','You need to write at least [number] answers')); 
	}
	
	public function checkAnswers($values)
	{
		if (isset($values['answers']) && (count($values['answers']) < Configure::read('Poll.poll_min_answer') ))
		{
			return false;
		}
		$count = 0;
		foreach ($values['answers'] as $answer)
		{
			if ($answer['text'])
				$count++;
		}
		
		if ($count >= Configure::read('Poll.poll_min_answer'))
			return true;
		
		return false;
	}
	
	public function getThumb($row){
		return 'thumbnail';
	}
	
	public function afterDeleteComment($comment)
	{
		if ($comment['Comment']['target_id'])
		{
			$this->decreaseCounter($comment['Comment']['target_id']);
		}
	}
	
	public function getFriends($id)
	{
		$helper = MooCore::getInstance()->getHelper('Poll_Poll');
		return $helper->getFriends($id);
	}
	
    public function getHref($row)
    {
    	$request = Router::getRequest();
    	if (isset($row['id']))
    		return $request->base.'/polls/view/'.$row['id'].'/'.seoUrl($row['moo_title']);
    		
    	return false;
    }
    
	public function countPollByCategory($category_id, $params = array()){
		if (count($params))
		{
        	$params['category'] = $category_id;
        	$conditions = $this->getConditionsPolls($params);
		}
		else 
		{
			$conditions = array(
                'Poll.category_id' => $category_id,
            );
		}
		
		$num = $this->find('count',array(
            'conditions' => $conditions
		));
		
        return $num;
    }

    public function afterComment($data,$uid)
    {
		$this->increaseCounter( $data['target_id'] );
    }
    
    public function getCateogries($params = array())
    {
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$cond = array('Category.type' => 'Poll','Category.header'=>0,'Category.active'=>1);
    	$categories = $categoryModel->find('all',array('conditions' => $cond, 'order' => 'Category.type asc, Category.weight asc'));
        foreach ($categories as &$category)
        {
        	$category['Category']['item_count'] = $this->countPollByCategory($category['Category']['id'],$params);
        }
        
        return $categories;
    }
    
    public function getLikePollByUser($poll_id,$uid)
    {
    	if (!$uid)
    	{
    		return false;
    	}
    	$likeModel = MooCore::getInstance()->getModel('Like');
    	return $likeModel->find( 'first', array( 'conditions' => array( 'Like.type' => 'Poll_Poll', 
								'Like.target_id' => $poll_id, 
								'Like.user_id' => $uid 
		)));
    }
    
    public function getTotalPolls($params = array())
    {
    	$conditions = $this->getConditionsPolls($params);
    	return $this->find('count',array('conditions'=>$conditions));
    }
    
    public function getConditionsPolls($params = array())
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
	    			array('Poll.user_id' => $params['user_id']),
	    			array('Poll.privacy' => PRIVACY_EVERYONE)
	    		);
	    		if (count($friend_ids))
	    		{
	    			$conditions['OR'][] = array('Poll.user_id'=>$friend_ids,'Poll.privacy'=>PRIVACY_FRIENDS);
	    		}
	    	}
	    	else
	    	{
	    		$conditions['Poll.privacy'] = PRIVACY_EVERYONE;
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
    				$conditions['Poll.user_id'] = $params['user_id'];
    				break;
    			case 'friend':
    				if (!count($friend_ids))
    					$friend_ids = array(0);
    				$conditions['Poll.user_id'] = $friend_ids;
    				break;
    		}
    	}
    	if (isset($params['feature']) && $params['feature'])
    	{
    		$conditions['Poll.feature'] = $params['feature']; 
    	}
    	if (isset($params['category']) && $params['category'])
    	{
    		$conditions['Poll.category_id'] = $params['category']; 
    	}
    	
    	if (isset($params['owner_id']) && $params['owner_id'])
    	{
    		$conditions['Poll.user_id'] = $params['owner_id']; 
    	}
    	
    	if (isset($params['feature']) && $params['feature'])
    	{
    		$conditions['Poll.feature'] = 1;
    	}
    	
    	if (isset($params['ids']))
    	{
    		$conditions['Poll.id'] = $params['ids'];
    	}
    
    	if (isset($params['interval']) && $params['interval'])
    	{
    		$conditions['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Poll.created'] = intval($params['interval']);
    	}
    	
    	if (isset($params['search']) && $params['search'])
    	{    		
			$conditions['AND'] = array(
				'OR' => array('Poll.title LIKE '=>'%'.$params['search'].'%')
			);
    	}
		
    	return $this->addBlockCondition($conditions);
    }
    
    public function getPolls($params = array())
    {
    	$conditions = $this->getConditionsPolls($params);
    	$page = 1;
    	if (isset($params['page']) && $params['page'])
    	{
    		$page = $params['page'];
    	}
    	$limit = Configure::read('Poll.poll_item_per_pages');
    	if (isset($params['limit']) && $params['limit'])
    	{
    		$limit = $params['limit'];
    	}
    	$order = array('Poll.id desc');
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
