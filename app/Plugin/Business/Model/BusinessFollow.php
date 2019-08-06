<?php
class BusinessFollow extends BusinessAppModel 
{
    public $belongsTo = array( 
        'User'  => array('counterCache' => true),
        'Business' => array('className' => 'Business.Business')
    );

    public function isFollowed($business_id)
    {
        return $this->hasAny(array(
            'BusinessFollow.business_id' => $business_id,
            'BusinessFollow.user_id' => MooCore::getInstance()->getViewer(true)
        ));
    }
    
    public function isBanned($business_id)
    {
        return $this->hasAny(array(
            'BusinessFollow.business_id' => $business_id,
            'BusinessFollow.user_id' => MooCore::getInstance()->getViewer(true),
            'BusinessFollow.is_banned' => 1
        ));
    }
    
    public function follow($business_id)
    {
        return $this->save(array(
            'business_id' => $business_id,
            'user_id' => MooCore::getInstance()->getViewer(true)
        ));
    }
    
    public function unFollow($business_id)
    {
        return $this->deleteAll(array(
            'BusinessFollow.business_id' => $business_id,
            'BusinessFollow.user_id' => MooCore::getInstance()->getViewer(true)
        ));
    }
    
    public function totalBusinessFollow()
    {
        return $this->find('count', array(
            'conditions' => array(
                'BusinessFollow.user_id' => MooCore::getInstance()->getViewer(true)
            )
        ));
    }
    public function getFollowerIds($business_id){
        return  $this->find('list', array(
            'conditions' => array(
                'BusinessFollow.business_id' => $business_id,
                'BusinessFollow.is_banned' => 0
            ),
            'fields' => array('BusinessFollow.user_id'),
        ));
    }
    public function getBusinessFollowList($page = 1)
    {
        $business_ids =  $this->find('list', array(
            'conditions' => array(
                'BusinessFollow.user_id' => MooCore::getInstance()->getViewer(true)
            ),
            'fields' => array('BusinessFollow.business_id'),
            'limit' => Configure::read('Business.business_follow_item_per_page'),
            'page' => $page
        ));
        
        //load businesses
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        if($business_ids != null)
        {
            return $mBusiness->find('all', array(
                'conditions' => array(
                    'Business.id IN('.implode(',', $business_ids).')'
                )
            ));
        }
        return null;
    }
    
    public function deleteFollower($business_id, $user_id = null)
    {
        $cond = array(
            'BusinessFollow.business_id' => $business_id,
        );
        if($user_id > 0)
        {
            $cond['BusinessFollow.user_id'] = $user_id;
        }
        return $this->deleteAll($cond);
    }
    
    public function sendFollowNotification($business_id, $task, $sender_id = null, $link = '')
    {
        //get business
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $business = $mBusiness->getOnlyBusiness($business_id);
        
        //find followers
        $user_ids = $this->find('list', array(
            'conditions' => array(
                'BusinessFollow.business_id' => $business_id,
                'BusinessFollow.is_banned' => 0
            ),
            'fields' => array('BusinessFollow.user_id')
        ));

        //send notification
        if($user_ids != null && $business != null)
        {
            $business = $business['Business'];
            foreach($user_ids as $user_id)
            {
                if($sender_id != $user_id)
                {
                    $mBusiness->sendNotification(
                        $user_id, 
                        !empty($sender_id) ? $sender_id : $business['user_id'], 
                        $task, 
                        $link != null ? $link : $business['moo_url'], 
                        $business['name']
                    );
                }
            }
        }
    }
    public function updateBusinessFollowCounter($business_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        
        //find total follower
        $total = $this->find('count', array(
            'conditions' => array(
                'BusinessFollow.business_id' => $business_id
            )
        ));

        //update business follow count
        $mBusiness->updateAll(array(
            'Business.follow_count' => $total
        ), array(
            'Business.id' => $business_id
        ));
    }
    
    public function getBusinessFollowers($business_id, $search, $page = 1)
    {
        $this->unbindModel(array(
            'belongsTo' => array('Business.Business')
        ), true);
        
        $cond = array(
            'BusinessFollow.business_id' => $business_id,
        );
        
        //search
        if(!empty($search['follower_type']))
        {
            switch($search['follower_type'])
            {
                case 0:
                    $cond[] = '(BusinessFollow.is_banned = 0 OR BusinessFollow.is_banned = 1)';
                    break;
                case 1:
                    $cond['BusinessFollow.is_banned'] = 1;
                    break;
                case 2:
                    $cond['BusinessFollow.is_banned'] = 0;
                    break;
            }
        }
        
        if(!empty($search['keyword']))
        {
            $cond[] = "User.name LIKE '%".$search['keyword']."%'";
        }
        
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Business.business_follower_item_per_page'),
            'order' => array('BusinessFollow.id' => 'DESC'),
            'page' => $page
        ));
    }
    
    public function unBan($business_id, $user_id)
    {
        $this->updateAll(array(
            'BusinessFollow.is_banned' => 0
        ), array(
            'BusinessFollow.business_id' => $business_id,
            'BusinessFollow.user_id' => $user_id
        ));
    }
}