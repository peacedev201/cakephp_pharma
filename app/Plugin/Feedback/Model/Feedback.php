<?php
class Feedback extends FeedbackAppModel 
{
    public $mooFields = array('title','href','plugin','type','url', 'thumb');

    public $actsAs = array(
        'Hashtag',
    );
    
    public $belongsTo = array( 
        'User'  => array('counterCache' => true  ),
        'FeedbackStatus'    => array(
                    'className'     => 'FeedbackStatus',
                    'foreignKey'    => 'status_id'),
        'FeedbackCategory' => array(
                    'className'     => 'FeedbackCategory',
                    'foreignKey'    => 'category_id'),
        'FeedbackSeverity' => array(
                    'className'     => 'FeedbackSeverity',
                    'foreignKey'    => 'severity_id'),
        );

    public $hasMany = array(
        'FeedbackVote' => array(
                        'className' => 'FeedbackVote',
                        'foreignKey' => 'feedback_id',
                        'dependent'=> true
        ),
        'Tag' => array( 
                        'className' => 'Tag',   
                        'foreignKey' => 'target_id',
                        'conditions' => array('Tag.type' => 'Feedback_Feedback'),                      
                        'dependent'=> true
                    ),
        'Photo' => array( 
                        'className' => 'Photo',   
                        'foreignKey' => 'target_id',
                        'conditions' => array('Photo.type' => 'feedback'),                      
                        'dependent'=> true
                    ),
        'Comment' => array( 
                        'className' => 'Comment',   
                        'foreignKey' => 'target_id',
                        'conditions' => array('Comment.type' => 'Feedback_Feedback'),                      
                        'dependent'=> true
                    ),
        'Like' => array( 
                        'className' => 'Like',  
                        'foreignKey' => 'target_id',
                        'conditions' => array('Like.type' => 'Feedback_Feedback'),                     
                        'dependent'=> true
                    ),
        'FeedbackImage' => array( 
                        'className' => 'FeedbackImage',   
                        'foreignKey' => 'feedback_id',
                        'dependent'=> true
                    ),);
    
	public $validate = array(   
        'title' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Title is required'
        ),
        'body' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Description is required'
        ), 
        'email' =>   array(   
            'rule'     => 'email',
            'message'  => 'Invalid email'
        ), 
        'fullname' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Fill name is required'
        ), 
    );
    
    public function activeMenu($active)
    {
        $mCoreMenuItem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mCoreMenuItem->findByUrl('/feedbacks');
        if($menu != null)
        {
            $mCoreMenuItem->id = $menu['CoreMenuItem']['id'];
            $mCoreMenuItem->save(array(
                'is_active' => $active
            ));
        }
    }

    public function isIdExist($id)
    {
        return $this->hasAny(array('id' => $id));
    }
    
    public function isFeedbackExist($id)
    {
        $feedback = $this->findById($id);
        if($feedback != null && ($feedback['Feedback']['approved'] == 1 || ($feedback['Feedback']['approved'] == 0 && $feedback['Feedback']['user_id'] == MooCore::getInstance()->getViewer(true))))
        {
            return true;
        }
        return false;
    }

    public function deleteFeedback( $iId ){

        // $aFeedback = $this->findById($iId);
        
        $this->delete( $iId );
        
        $Comment = MooCore::getInstance()->getModel('Comment');
        $Like = MooCore::getInstance()->getModel('Like');

        $Comment->deleteAll( array( 'Comment.target_id' => $iId, 'Comment.type' => 'feedback' ), true, true );
        $Like->deleteAll( array( 'Like.target_id' => $iId, 'Like.type' => 'feedback' ), true, true );       

        $activityModel = MooCore::getInstance()->getModel('Activity');
         // delete activity
        $parentActivity = $activityModel->find('list', array('fields' => array('Activity.id') , 'conditions' =>
            array('Activity.item_type' => 'Feedback_Feedback', 'Activity.item_id' => $iId)));

        $activityModel->deleteAll( array( 'Activity.item_type' => 'Feedback_Feedback', 'Activity.item_id' => $iId ), true, true );
        $activityModel->deleteAll( array( 'Activity.target_id' => $iId, 'Activity.type' => 'Feedback' ), true, true );

        // delete child activity
        $activityModel->deleteAll(array('Activity.item_type' => 'Feedback_Feedback', 'Activity.parent_id' => $parentActivity));

        //delete share activity
        $activityModel->deleteAll(array('Activity.item_type' => 'Feedback_Feedback', 'Activity.parent_id' => $iId));
    }

    public function getFeedbacks( $type = null, $param = null, $page = 1, $limit = RESULTS_LIMIT )
    {
        $pp = Configure::read('Feedback.feedback_item_per_pages');
        if (!empty($pp))
            $limit = $pp;

        $cond = array();
               
        switch ( $type )
        {       
            case 'friends':             
                if ( $param )
                {
                    $friend = MooCore::getInstance()->getModel('Friend');
                    $friends = $friend->getFriends( $param );                                       
                    $cond = array( 'Feedback.user_id' => $friends,'Feedback.privacy <>' => PRIVACY_ME, 'Feedback.approved' => 1);
                }                   
                break;
                
            case 'my':  
                if ( $param )
                    $cond = array( 'Feedback.user_id' => $param );
                    
                break;
                
            case 'user':
                if ( $param )
                    $cond = array( 'Feedback.user_id' => $param, 'Feedback.approved' => 1);
                    
                break;

            case 'cat':  
                if ( $param )
                    $viewer = MooCore::getInstance()->getViewer();
                    $friend = MooCore::getInstance()->getModel('Friend');
                    $friends = $friend->getFriends(MooCore::getInstance()->getViewer(true) );
                    //if (!$viewer['Role']['is_admin']){
                        $cond = array( 
                            'OR' => array( 
                                array('Feedback.privacy' => PRIVACY_EVERYONE, 'Feedback.category_id' => $param, 'Feedback.approved' => 1), 
                                array( 'Feedback.user_id'=>MooCore::getInstance()->getViewer(true), 'Feedback.category_id' => $param, 'Feedback.approved' => 1),
                                array('Feedback.user_id'=>$friends ,'Feedback.privacy <>' => PRIVACY_ME, 'Feedback.category_id' => $param, 'Feedback.approved' => 1), 
                            )
                        );
                    //}
                break;

            case 'sta':  
                if ( $param )
                    $viewer = MooCore::getInstance()->getViewer();
                    $friend = MooCore::getInstance()->getModel('Friend');
                    $friends = $friend->getFriends(MooCore::getInstance()->getViewer(true) );
                    //if (!$viewer['Role']['is_admin']){
                        $cond = array( 
                            'OR' => array( 
                                array('Feedback.privacy' => PRIVACY_EVERYONE, 'Feedback.status_id' => $param,'Feedback.approved' => 1), 
                                array( 'Feedback.user_id'=>MooCore::getInstance()->getViewer(true), 'Feedback.status_id' => $param,'Feedback.approved' => 1),
                                array('Feedback.user_id'=>$friends ,'Feedback.privacy <>' => PRIVACY_ME, 'Feedback.status_id' => $param,'Feedback.approved' => 1), 
                            )
                        );
                    //}
                break;
                
            case 'search':
                if ( $param && strlen($param) > 1)
                    $cond = array( "Feedback.title LIKE '%".urldecode($param)."%'", 'Feedback.approved' => 1);
                    $viewer = MooCore::getInstance()->getViewer();
                    $friend = MooCore::getInstance()->getModel('Friend');
                    $friends = $friend->getFriends(MooCore::getInstance()->getViewer(true) );
                    //if (!$viewer['Role']['is_admin']){
                        $cond = array( 
                            'OR' => array( 
                                array('Feedback.privacy' => PRIVACY_EVERYONE, 'Feedback.approved' => 1, "Feedback.title LIKE '%".urldecode($param)."%'"), 
                                array( 'Feedback.user_id'=>MooCore::getInstance()->getViewer(true), 'Feedback.approved' => 1, "Feedback.title LIKE '%".urldecode($param)."%'"),
                                array('Feedback.user_id'=>$friends ,'Feedback.privacy <>' => PRIVACY_ME, 'Feedback.approved' => 1, "Feedback.title LIKE '%".urldecode($param)."%'"), 
                            )
                        );
                    //}
                break;
            
            default:
                $cond = array('Feedback.approved' => 1);
                $viewer = MooCore::getInstance()->getViewer();
                $friend = MooCore::getInstance()->getModel('Friend');
                $friends = $friend->getFriends(MooCore::getInstance()->getViewer(true) );
                //if (!$viewer['Role']['is_admin']){
                    $cond = array( 
                        'OR' => array( 
                            array('Feedback.privacy' => PRIVACY_EVERYONE, 'Feedback.approved' => 1), 
                            array( 'Feedback.user_id'=>MooCore::getInstance()->getViewer(true), 'Feedback.approved' => 1),
                            array('Feedback.user_id'=>$friends ,'Feedback.privacy <>' => PRIVACY_ME, 'Feedback.approved' => 1), 
                        )
                    );
                //}
        }
        /*if($type != 'my' ){
            $cond['Feedback.privacy'] = 1;
        }*/
        $cond = $this->addBlockCondition($cond);
        $feedbacks = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page , 'order' => 'Feedback.created DESC') );
        $feedbacks = $this->parseDataLanguage($feedbacks);
        return $feedbacks;
    }

    public function parseDataLanguage($data)
    {
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                if(empty($item['FeedbackCategory']['nameTranslation']))
                {
                    continue;
                }
                foreach ($item['FeedbackCategory']['nameTranslation'] as $nameTranslation) 
                {
                    if ($nameTranslation['locale'] == Configure::read('Config.language')) 
                    {
                        $data[$k]['FeedbackCategory']['name'] = $nameTranslation['content'];
                    }
                }

                if(empty($item['FeedbackSeverity']['nameTranslation']))
                {
                    continue;
                }
                foreach ($item['FeedbackSeverity']['nameTranslation'] as $nameTranslation) 
                {
                    if ($nameTranslation['locale'] == Configure::read('Config.language')) 
                    {
                        $data[$k]['FeedbackSeverity']['name'] = $nameTranslation['content'];
                    }
                }

                if(empty($item['FeedbackStatus']['nameTranslation']))
                {
                    continue;
                }
                foreach ($item['FeedbackStatus']['nameTranslation'] as $nameTranslation) 
                {
                    if ($nameTranslation['locale'] == Configure::read('Config.language')) 
                    {
                        $data[$k]['FeedbackStatus']['name'] = $nameTranslation['content'];
                    }
                }
                
            }
        }
        return $data;
    }

    public function parseResultLanguage($data)
    {
        if($data != null)
        {
            if ($data['FeedbackCategory']['id'])
            {
                foreach ($data['FeedbackCategory']['nameTranslation'] as $translate)
                {
                    if ($translate['locale'] == Configure::read('Config.language'))
                    {
                        $data['FeedbackCategory']['name'] = $translate['content'];
                    }
                }
            }

            if ($data['FeedbackSeverity']['id'])
            {
                foreach ($data['FeedbackSeverity']['nameTranslation'] as $translate)
                {
                    if ($translate['locale'] == Configure::read('Config.language'))
                    {
                        $data['FeedbackSeverity']['name'] = $translate['content'];
                    }
                }
            }

            if ($data['FeedbackStatus']['id'])
            {
                foreach ($data['FeedbackStatus']['nameTranslation'] as $translate)
                {
                    if ($translate['locale'] == Configure::read('Config.language'))
                    {
                        $data['FeedbackStatus']['name'] = $translate['content'];
                    }
                }
            }

        }
        return $data;
    }

    public function getHref($row)
    {
        $request = Router::getRequest();
        if (isset($row['title']) && isset($row['id']))
            return $request->base.'/feedback/feedbacks/view/'.$row['id'].'/'.seoUrl($row['title']);
        else 
            return '';
    }
    
    public function getUrl($row)
    {
        $request = Router::getRequest();
        if (isset($row['title']) && isset($row['id']))
            return '/feedbacks/feedbacks/view/'.$row['id'].'/'.seoUrl($row['title']);
        else 
            return '';
    }

    public function getThumb($row){

        return 'thumbnail';
    }
    
    public function updateCounter($id, $field = 'comment_count',$conditions = '',$model = 'Comment') 
    {
        if(empty($conditions))
        {
            $conditions = array('Comment.type' => 'Feedback_Feedback', 'Comment.target_id' => $id);
        }
        parent::updateCounter($id, $field, $conditions, $model);
    }
    
    public function isReachToLimitPost($ip_address)
    {
        if(Configure::read('Feedback.feedback_max_create_feedback') > 0)
        {
            if(MooCore::getInstance()->getViewer(true) > 0)
            {
                $total = $this->find('count', array(
                    'conditions' => array(
                        'Feedback.user_id' => MooCore::getInstance()->getViewer(true)
                    )
                ));
            }
            else
            {
                $total = $this->find('count', array(
                    'conditions' => array(
                        'Feedback.ip_address' => $ip_address
                    )
                ));
            }
            if($total >= Configure::read('Feedback.feedback_max_create_feedback'))
            {
                return true;
            }
        }
        return false;
    }
    
    public function isReachToPostFrequency($ip_address)
    {
        if(Configure::read('Feedback.feedback_post_frequency') > 0)
        {
            if(MooCore::getInstance()->getViewer(true) > 0)
            {
                $last_feedback = $this->find('first', array(
                    'conditions' => array(
                        'Feedback.user_id' => MooCore::getInstance()->getViewer(true)
                    ),
                    'order' => array('Feedback.created' => 'DESC')
                ));
            }
            else
            {
                $last_feedback = $this->find('first', array(
                    'conditions' => array(
                        'Feedback.ip_address' => $ip_address
                    ),
                    'order' => array('Feedback.created' => 'DESC')
                ));
            }
            if($last_feedback != null)
            {
                $postDate = strtotime($last_feedback['Feedback']['created'].' +'.Configure::read('Feedback.feedback_post_frequency').' minute');
                $curDate = strtotime(date('Y-m-d H:i:s'));
                if($postDate > $curDate)
                {
                    return date('i:s', $postDate - $curDate);
                }
            }
        }
        return false;
    }
    
    public function updateFeedbackView($id)
    {
        $feedback = $this->findById($id);
        if($feedback != null)
        {
            $this->updateAll(array(
                'Feedback.views' => "'".($feedback['Feedback']['views'] + 1)."'"
            ), array(
                'Feedback.id' => $id
            ));
        }
    }
    
    public function getEmailReceive()
    {
        $email = array();
        if(Configure::read('Feedback.feedback_email_receive') != null)
        {
            $email = explode(';', Configure::read('Feedback.feedback_email_receive'));
        }
        else
        {
            $email[] = Configure::read('core.site_email');
        }
        return $email;
    }
    
    public function totalApproveFeedback($category_id = null, $severity_id = null)
    {
        $cond = array(
            'Feedback.approved' => 1
        );
        if($category_id > 0)
        {
            $cond['Feedback.category_id'] = $category_id;
        }
        if($severity_id > 0)
        {
            $cond['Feedback.severity_id'] = $severity_id;
        }
        return $this->find('count', array(
            'conditions' => $cond
        ));
    }
    
    public function mostViewFeedback($num_item_show = 1)
    {
        $data =  $this->find('all', array(
            'conditions' => array(
                'Feedback.privacy' => 1,
                'Feedback.approved' => 1
            ),
            'order'      => array('total_votes DESC'),
            'limit'      => $num_item_show,
        )); 

        $data = $this->parseDataLanguage($data);

        return $data;
    }
    
    public function getFeedbackHashtags($tag, $limit = RESULTS_LIMIT,$page = 1){
        $mTag = MooCore::getInstance()->getModel('Tag');
        $ids = $mTag->find('list', array(
            'conditions' => array(
                "Tag.tag LIKE '%$tag%'"
            ),
            'fields' => array('Tag.target_id')
        ));
        
        $mHashTag = MooCore::getInstance()->getModel('HashTag');
        $HashTagIds = $mHashTag->find('list', array(
            'conditions' => array(
                "HashTag.hashtags LIKE '%$tag%'"
            ),
            'fields' => array('HashTag.item_id')
        ));
        
        $ids = !empty($HashTagIds) ? array_merge($ids, $HashTagIds) : $ids;
                
        $feedbacks = null;
        if($ids != null)
        {
            $feedbacks = $this->find('all', array(
                'conditions' => array(
                    'Feedback.id IN('. implode(',', $ids).')'
                ),
                'limit' => $limit, 
                'page' => $page
            ));
        }
        return $feedbacks;
    }
    
    public function getListTag($target_id)
    {
        $mTag = MooCore::getInstance()->getModel('Tag');
        return $mTag->find('list', array(
            'conditions' => array(
                'Tag.type' => 'Feedback_Feedback',
                'Tag.target_id' => $target_id
            ),
            'fields' => array('Tag.tag')
        ));
    }
    
    public function deleteFeedbackTag($target_id)
    {
        $mTag = MooCore::getInstance()->getModel('Tag');
        $tags = $this->getListTag($target_id);
        if($tags != null)
        {
            foreach($tags as $id => $tag)
            {
                $mTag->delete($id);
            }
        }
    }
    
    public function getTotalFeedbacks($params = array())
    {
    	$conditions = $this->getConditionsFeedbacks($params);
    	return $this->find('count',array('conditions'=>$conditions));
    }
    
     public function getCateogries($params = array())
    {
    	$categoryModel = MooCore::getInstance()->getModel('Category');
    	$cond = array('Category.type' => 'Feedback','Category.header'=>0,'Category.active'=>1);
    	$categories = $categoryModel->find('all',array('conditions' => $cond, 'order' => 'Category.type asc, Category.weight asc'));
        foreach ($categories as &$category)
        {
        	$category['Category']['item_count'] = $this->countFeedbackByCategory($category['Category']['id'],$params);
        }

        return $categories;
    }
    
    public function countFeedbackByCategory($category_id, $params = array()){
	$conditions = array(
            'Feedback.category_id' => $category_id,
        );
				
        $num = $this->find('count',array(
            'conditions' => $conditions
        ));
		
        return $num;
    }

}