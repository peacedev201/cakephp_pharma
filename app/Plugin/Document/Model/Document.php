<?php

App::uses('DocumentAppModel','Document.Model');
class Document extends DocumentAppModel {
    
    public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumbnail' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}documents{DS}{field}{DS}',
        		'thumbnailSizes' => array(
                    'size' => array('111')
                )
            )
        ),
        'Hashtag'=>array(
            'field_created_get_hashtag'=>'description',
            'field_updated_get_hashtag'=>'description',
        ),
		'Storage.Storage' => array(
            'type'=>array(
				'documents'=>'thumbnail',
				'documents_files'=>'download_url',
			),
        ),
    );
    
    public $validationDomain = 'document';
	
	public $document_update_all = array();
	
	public $friend_list = array();
    
    public $belongsTo = array( 'User','Category','DocumentLicense');
    
    public $mooFields = array('title','href','plugin','type','url', 'thumb','privacy');
	
	public $hasMany = array( 'Comment' => array( 
											'className' => 'Comment',	
											'foreignKey' => 'target_id',
											'conditions' => array('Comment.type' => 'Document_Document'),						
											'dependent'=> true
										),
						  	 'Like' => array( 
											'className' => 'Like',	
											'foreignKey' => 'target_id',
											'conditions' => array('Like.type' => 'Document_Document'),						
											'dependent'=> true
										),
						  	 'Tag' => array( 
											'className' => 'Tag',	
											'foreignKey' => 'target_id',
											'conditions' => array('Tag.type' => 'Document_Document'),						
											'dependent'=> true
										)
						); 
	
	public $order = 'Document.id desc';
	
	public $validate = array(	
		'title' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Title is required',
		),
		'category_id' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Category is required',
		),	
		'document_file' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Document file is required',
		),	
		'document_license_id' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'License is required',
		),			
		'tags' => array(
			'validateTag' => array(
				'rule' => array('validateTag'),
				'message' => 'No special characters ( /,?,#,%,...) allowed in Tags',
			)
		)
	);
	
	public function updateCounter($id, $field = 'comment_count',$conditions = '',$model = 'Comment') {
		if(empty($conditions)){
			$conditions = array('Comment.type' => 'Document_Document', 'Comment.target_id' => $id);
		}
		parent::updateCounter($id,$field, $conditions, $model);
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
		if (!isset($this->friend_list[$id]))
		{
			$friendModel = MooCore::getInstance()->getModel('Friend');
    		$this->friend_list[$id] = $friendModel->getFriends($id);
		}
		return $this->friend_list[$id];
	}
	
	public function getPrivacy($row){
		if (isset($row['privacy'])){
			return $row['privacy'];
		}
		return false;
	}
	
	public function getTitle(&$row)
	{
		if (isset($row['title']))
		{
			$row['title'] = htmlspecialchars($row['title']);
			return $row['title'];
		}
		return '';
	}
	
    public function getHref($row)
    {
    	$request = Router::getRequest();
    	if (isset($row['id']))
    		return $request->base.'/documents/view/'.$row['id'].'/'.seoUrl($row['moo_title']);
    		
    	return false;
    }
    
    public function getThumb($row){
        return 'thumbnail';
    }
    
	public function countDocumentByCategory($category_id, $params = array()){
		if (count($params))
		{
        	$params['category'] = $category_id;
        	$conditions = $this->getConditionsDocuments($params);
		}
		else 
		{
			$conditions = array(
                'Document.category_id' => $category_id,
            );
		}
		
		$num = $this->find('count',array(
            'conditions' => $conditions
		));
		
        return $num;
    }
    
    public function beforeDelete($cascade = true)
    {
    	$item = $this->findById($this->id);
    	if ($item)
    	{
    		$activityModel = MooCore::getInstance()->getModel('Activity');
            $parentActivity = $activityModel->find('list', array('fields' => array('Activity.id') , 'conditions' => 
                array('Activity.item_type' => 'Document_Document', 'Activity.item_id' => $item['Document']['id'])));
            
            $activityModel->deleteAll(array('Activity.item_type' => 'Document_Document', 'Activity.parent_id' => $parentActivity));
            
            $activityModel->deleteAll(array('Activity.item_type' => 'Document_Document', 'Activity.parent_id' => $this->id));
            
            $link = APP . 'webroot' . DS .'uploads' . DS .'documents' . DS . 'files' . DS. $item['Document']['download_url'];
            unset($link);
    	}
    	
    	parent::beforeDelete($cascade);
    }

    public function afterComment($data,$uid)
    {
		$this->increaseCounter( $data['target_id'] );
    }
    
    public function getCateogries($params = array())
    {
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$cond = array('Category.type' => 'Document','Category.header'=>0,'Category.active'=>1);
    	$categories = $categoryModel->find('all',array('conditions' => $cond, 'order' => 'Category.type asc, Category.weight asc'));
        foreach ($categories as &$category)
        {
        	$category['Category']['item_count'] = $this->countDocumentByCategory($category['Category']['id'],$params);
        }
        
        return $categories;
    }
    
    public function countDocumentByLicense($id)
    {
    	$num = $this->find('count',array(
            'conditions' => array(
                'Document.document_license_id' => $id,
            )
        ));
        return $num;
    }
    
    public function getLikeDocumentByUser($document_id,$uid)
    {
    	if (!$uid)
    	{
    		return false;
    	}
    	$likeModel = MooCore::getInstance()->getModel('Like');
    	return $likeModel->find( 'first', array( 'conditions' => array( 'Like.type' => 'Document_Document', 
								'Like.target_id' => $document_id, 
								'Like.user_id' => $uid 
		)));
    }
    
    public function getTotalDocuments($params = array())
    {
    	$conditions = $this->getConditionsDocuments($params);
    	return $this->find('count',array('conditions'=>$conditions));
    }
    
    public function getConditionsDocuments($params = array())
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
	    			array('Document.user_id' => $params['user_id']),
	    			array('Document.privacy' => PRIVACY_EVERYONE,'Document.approve' => 1)
	    		);
	    		if (count($friend_ids))
	    		{
	    			$conditions['OR'][] = array('Document.user_id'=>$friend_ids,'Document.approve' => 1,'Document.privacy'=>PRIVACY_FRIENDS);
	    		}
	    	}
	    	else
	    	{
	    		$conditions['Document.privacy'] = PRIVACY_EVERYONE;
	    		$conditions['Document.approve'] = 1;
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
    				$conditions['Document.user_id'] = $params['user_id'];
    				break;
    			case 'friend':
    				if (!count($friend_ids))
    					$friend_ids = array(0);
    				$conditions['Document.user_id'] = $friend_ids;
    				break;
    		}
    	}
    	if (isset($params['feature']) && $params['feature'])
    	{
    		$conditions['Document.feature'] = $params['feature']; 
    	}
    	if (isset($params['category']) && $params['category'])
    	{
    		$conditions['Document.category_id'] = $params['category']; 
    	}
    	
    	if (isset($params['owner_id']) && $params['owner_id'])
    	{
    		$conditions['Document.user_id'] = $params['owner_id']; 
    	}
    	
    	if (isset($params['ids']))
    	{
    		$conditions['Document.id'] = $params['ids'];
    	}
    
    	if (isset($params['interval']) && $params['interval'])
    	{
    		$conditions['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Document.created'] = intval($params['interval']);
    	}
    	
    	if (isset($params['search']) && $params['search'])
    	{    		
			$conditions['AND'] = array(
				'OR' => array('Document.title LIKE '=>'%'.$params['search'].'%','Document.description LIKE '=>'%'.$params['search'].'%')
			);
    	}
    	
    	return $this->addBlockCondition($conditions);
    }
    
    public function getDocuments($params = array())
    {
    	$conditions = $this->getConditionsDocuments($params);
    	$page = 1;
    	if (isset($params['page']) && $params['page'])
    	{
    		$page = $params['page'];
    	}
    	$limit = Configure::read('Document.document_item_per_pages');
    	if (isset($params['limit']) && $params['limit'])
    	{
    		$limit = $params['limit'];
    	}
    	$order = array('Document.id desc');
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
