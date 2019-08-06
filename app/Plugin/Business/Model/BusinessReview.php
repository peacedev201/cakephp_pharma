<?php
class BusinessReview extends BusinessAppModel 
{
    public $validationDomain = 'business';
    public $mooFields = array('title','href','plugin','type','url', 'thumb');
    public $recursive = 2;
    public $actsAs = array('Tree' => 'nested');
    public $validate = array(   
        'rating' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please select rating'
        ),
        'content' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please input your review'
        )
    );
    
    public $belongsTo = array( 
        'User'  => array('counterCache' => true),
    );
    
    public function getReviews($business_id, $page = 1, $limit = 0, $except_id = '')
    {
        $this->bindModel(array(
            'hasMany' => array(
                'Photo' => array(
                    'className' => 'Photo.Photo',
                    'counterCache' => true,
                    'foreignKey' => 'target_id',
                    'conditions' => array(
                        'Photo.target_id' => 'BusinessReview.id',
                        'Photo.type' => 'Business_Review'
                    ),
                    'dependent' => true
                )
            )
        ));
        $mBusinessReviewUseful = MooCore::getInstance()->getModel('Business.BusinessReviewUseful');
        if($limit == 0)
        {
            $limit = Configure::read('Business.business_review_per_page');
        }
        $cond = array(
            'BusinessReview.business_id' => $business_id,
            'BusinessReview.parent_id' => 0
        );
        if((int)$except_id > 0)
        {
            $cond[] = 'BusinessReview.id != '.$except_id;
        }
        $data = $this->find('threaded', array(
            'conditions' => $cond,
            'limit' => $limit,
            'order' => array('BusinessReview.created' => 'DESC'),
            'page' => $page
        ));
        
        //find first review
        $first_review = $this->find('first', array(
            'conditions' => array(
                'BusinessReview.business_id' => $business_id,
                'BusinessReview.parent_id' => 0
            ),
            'limit' => $limit,
            'order' => array('BusinessReview.created' => 'ASC'),
            'fields' => array('BusinessReview.id')
        ));
        
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                $data[$k]['BusinessReview']['set_useful'] = $mBusinessReviewUseful->isSetUseful($item['BusinessReview']['id']);
                $data[$k]['BusinessReview']['first_review'] = false;
                if($item['BusinessReview']['id'] == $first_review['BusinessReview']['id'])
                {
                    $data[$k]['BusinessReview']['first_review'] = true;
                }
            }
        }
        return $this->parseReplyData($data);
    }
    
    public function isReviewed($business_id)
    {
        return $this->hasAny(array(
            'business_id' => $business_id,
            'parent_id' => 0,
            'BusinessReview.user_id' => MooCore::getInstance()->getViewer(true)
        ));
    }
    
    public function isReviewExist($id, $user_id = null, $business_id = null)
    {
        $cond = array(
            'BusinessReview.id' => $id
        );
        if($user_id > 0)
        {
            $cond['BusinessReview.user_id'] = $user_id;
        }
        if($business_id > 0)
        {
            $cond['BusinessReview.business_id'] = $business_id;
        }
        return $this->hasAny($cond);
    }
    
    public function updateBusinessReviewScore($business_id) 
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        
        $reviews = $this->find('first', array(
            'conditions' => array(
                'BusinessReview.business_id' => $business_id,
                'BusinessReview.parent_id' => 0
            ),
            'fields' => array('SUM(BusinessReview.rating) as total_rating', 'COUNT(BusinessReview.id) as total_vote')
        ));

        $average_point = 0 ;
        $total_score = 0;
        if(!empty($reviews))
        {
            $reviews = $reviews[0];
            if($reviews['total_vote'] > 0)
            {
                /*R = average for the movie as a number from 0 to 10 (mean) = (Rating)
                v = number of votes for the movie = (votes)
                m = minimum votes required to be listed in the Top 250 (currently 25,000)
                C = the mean vote across the whole report (currently 7.0)
                W = weighted rating
                W = (R*v + C*m) / v + m*/
                
                /*$average_point = round($reviews['total_rating']/$reviews['total_vote'], 2); 
                $min_vote = 5;
                $total_score = (2.5 * $reviews['total_vote'] + $average_point * $min_vote) / ($reviews['total_vote'] + $min_vote);
                $total_score = round($total_score, 2);*/
                $total_score = round($reviews['total_rating']/$reviews['total_vote'], 2); ;
            }
        }
        $mBusiness->updateAll(array(
            'Business.total_score' => "'".$total_score."'",
            'Business.review_count' => "'".$reviews['total_vote']."'"
        ), array(
            'Business.id' => $business_id
        ));
    }
    
    public function deleteReview($business_review_id = null, $business_id = null)
    {
        if($business_review_id > 0)
        {
            $mPhoto = MooCore::getInstance()->getModel('Photo.Photo');
            $review = $this->findById($business_review_id);
            if($this->delete($business_review_id))
            {
                //delete all replies photo
                $replies = $this->find('all', array(
                    'conditions' => array(
                        'BusinessReview.parent_id' => $business_review_id
                    ),
                    'fields' => array('BusinessReview.id')
                ));
                if($replies != null)
                {
                    foreach($replies as $reply)
                    {
                        $mPhoto->deleteAll(array(
                            'Photo.target_id' => $reply['BusinessReview']['id'],
                            'Photo.type' => 'Business_Review'
                        ));
                        $this->delete($reply['BusinessReview']['id']);
                    }
                }
                
                //delete all photos
                $mPhoto->deleteAll(array(
                    'Photo.target_id' => $business_review_id,
                    'Photo.type' => 'Business_Review'
                ));

                //update business counter
                if(!empty($review['BusinessReview']['business_id']))
                {
                    $this->updateBusinessReviewScore($review['BusinessReview']['business_id']);
                }
                return true;
            }
        }
        else if($business_id > 0)
        {
            return $this->deleteAll(array(
                'BusinessReview.business_id' => $business_id
            ));
        }
        return false;
    }
    
    public function getReviewRuler($business_id)
    {
        $this->virtualFields = array(
            'star' => 'FLOOR(BusinessReview.rating)',
            'total_rating' => 'SUM(BusinessReview.rating)',
            'total_vote' => 'COUNT(BusinessReview.rating)',
            //'percent' => 'COUNT(BusinessReview.rating) / SUM(COUNT(BusinessReview.rating))'
        );
        
        $total = $this->find('count', array(
            'conditions' => array(
                'BusinessReview.business_id' => $business_id,
            )
        ));
        
        $data = $this->find('all', array(
            'conditions' => array(
                'BusinessReview.business_id' => $business_id,
            ),
            'group' => array('BusinessReview.star'),
            'order' => array('BusinessReview.star' => 'DESC'),
            'fields' => array('BusinessReview.star', 'BusinessReview.total_rating', 'BusinessReview.total_vote'/*, 'BusinessReview.percent'*/)
        ));
        $result = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $item['BusinessReview']['percent'] = $item['BusinessReview']['total_vote'] / $total * 100;
                $result[$item['BusinessReview']['star']] = $item['BusinessReview'];
            }
        }
        return $result;
    }
    
    public function loadMyReviews($user_id, $page = 1, $block_user = true)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $this->bindModel(array(
            'belongsTo' => array(
                'Business' => array('className' => 'Business.Business')
            ),
            'hasMany' => array(
                'Photo' => array(
                    'className' => 'Photo.Photo',
                    'counterCache' => true,
                    'foreignKey' => 'target_id',
                    'conditions' => array(
                        'Photo.target_id' => 'BusinessReview.id',
                        'Photo.type' => 'Business_Review'
                    ),
                    'dependent' => true
                )
            )
        ));
        $cond = array(
            'BusinessReview.user_id' => $user_id,
            'BusinessReview.parent_id' => 0
        );
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => array('BusinessReview.id' => 'DESC'),
            'limit' => Configure::read('Business.business_search_item_per_page'),
            'page' => $page
        ));
        $data = $this->parseReplyData($data);
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                $parent = $item['Business']['parent_id'] > 0 ? $mBusiness->findById($item['Business']['parent_id']) : null;
                $data[$k]['Business']['moo_parent'] = !empty($parent['Business']) ? $parent['Business'] : null;
                $data[$k]['Business']['moo_parent_cat'] = !empty($parent['BusinessCategory']) ? $parent['BusinessCategory'] : null;
            }
        }
        return $data;
    }
    
    public function parseReplyData($data, $single = false)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Business' => array('className' => 'Business.Business')
            ),
            'hasMany' => array(
                'Photo' => array(
                    'className' => 'Photo.Photo',
                    'counterCache' => true,
                    'foreignKey' => 'target_id',
                    'conditions' => array(
                        'Photo.target_id' => 'BusinessReview.id',
                        'Photo.type' => 'Business_Review'
                    ),
                    'dependent' => true
                )
            )
        ));
        if($data != null)
        {
            if($single)
            {
                $data['children'] = $this->find('all', array(
                    'conditions' => array(
                        'BusinessReview.parent_id' => $data['BusinessReview']['id']
                    ),
                    'order' => array('BusinessReview.id' => 'DESC'),
                ));
            }
            else
            {
                foreach($data as $k => $item)
                {
                    $data[$k]['children'] = $this->find('all', array(
                        'conditions' => array(
                            'BusinessReview.parent_id' => $item['BusinessReview']['id']
                        ),
                        'order' => array('BusinessReview.id' => 'DESC'),
                    ));
                }
            }
        }
        return $data;
    }

    public function updateUsefulCounter($review_id)
    {
        $mBusinessReviewUseful = MooCore::getInstance()->getModel('Business.BusinessReviewUseful');
        $total = $mBusinessReviewUseful->find('count', array(
            'conditions' => array(
                'BusinessReviewUseful.business_review_id' => $review_id
            )
        ));
        $this->updateAll(array(
            'BusinessReview.useful_count' => $total
        ), array(
            'BusinessReview.id' => $review_id
        ));
        return $total;
    }
    
    public function saveReviewActivity($business_id, $business_review_id, $branch_id = null)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->saveActivity($business_id, BUSINESS_ACTIVITY_REVIEW_ACTION, BUSINESS_ACTIVITY_REVIEW_ITEM, $business_review_id, $branch_id);
    }
    
    public function deleteReviewActivity($business_id, $business_review_id, $branch_id = ' ')
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->deleteActivity($business_id, BUSINESS_ACTIVITY_REVIEW_ACTION, BUSINESS_ACTIVITY_REVIEW_ITEM, $business_review_id, $branch_id);
    }
    
    public function findLatestReview($business_id)
    {
        return $this->find('first', array(
            'conditions' => array(
                'BusinessReview.business_id' => $business_id,
                'BusinessReview.parent_id' => 0
            ),
            'order' => array('BusinessReview.id' => 'DESC')
        ));
    }
    
    public function totalMyBusinessReview($user_id = null)
    {
        return $this->find('count', array(
            'conditions' => array(
                'BusinessReview.parent_id' => 0,
                'BusinessReview.user_id' => $user_id > 0 ? $user_id : MooCore::getInstance()->getViewer(true)
            )
        ));
    }
    
    /*public function updateUserReviewCounter()
    {
        $mUser = MooCore::getInstance()->getModel('User');
        $uid = MooCore::getInstance()->getViewer(true);
        $total = $this->find('count', array(
            'conditions' => array(
                'BusinessReview.parent_id' => 0,
                'BusinessReview.user_id' => $uid
            )
        ));
        $mUser->updateAll(array(
            'User.business_count' => $total
        ), array(
            'User.id' => $uid
        ));
    }*/
    
    public function getReviewDetail($id, $business_id = null, $parent_id = -1, $all = false)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Business' => array('className' => 'Business.Business')
            ),
            'hasMany' => array(
                'Photo' => array(
                    'className' => 'Photo.Photo',
                    'counterCache' => true,
                    'foreignKey' => 'target_id',
                    'conditions' => array(
                        'Photo.target_id' => 'BusinessReview.id',
                        'Photo.type' => 'Business_Review'
                    ),
                    'dependent' => true
                )
            )
        ));
        $cond = array(
            'BusinessReview.id' => $id,
        );
        if($parent_id > -1)
        {
            $cond['BusinessReview.parent_id'] = (int)$parent_id;
        }
        if($business_id > 0)
        {
            $cond['BusinessReview.business_id'] = $business_id;
        }

        $data = $this->find('first', array(
            'conditions' => $cond
        ));
        return $this->parseReplyData($data, true);
    }
    
    public function isFirstReview($review_id, $business_id)
    {
        $data = $this->find('first', array(
            'conditions' => array(
                'BusinessReview.business_id' => $business_id,
                'BusinessReview.parent_id' => 0
            ),
            'order' => array('BusinessReview.id' => 'ASC')
        ));
        if(!empty($data['BusinessReview']['id']) && $data['BusinessReview']['id'] == $review_id)
        {
            return true;
        }
        return false;
    }
    
    public function getWeeklyReview()
    {
        $mUserMedal = MooCore::getInstance()->getModel('Business.UserMedal');
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $mBusiness->recursive = -1;
        $last_sunday = strtotime('last Sunday +1 day');
        $last_monday = strtotime('last Sunday -7 days');
        $cond = array(
            'BusinessReview.parent_id' => 0,
            'UNIX_TIMESTAMP(BusinessReview.created) >= '.$last_monday,
            'UNIX_TIMESTAMP(BusinessReview.created) <= '.$last_sunday,
            'BusinessReview.useful_count > 0',
            'BusinessReview.report_count' => 0
        );
        
        //get by location
        $around_params = $mBusiness->findBusinessAround();
        if(isset($around_params['conditions']))
        {
            $cond = array_merge($cond, $around_params['conditions']);
        }
        if(isset($around_params['virtual_field']))
        {
            $this->virtualFields = array(
                'distance' => $around_params['virtual_field']
            );
        }

        $data = $this->find('all', array(
            'conditions' => $cond,
            'limit' => 5,
            'order' => array('BusinessReview.useful_count' => 'DESC'),
            'group' => array('BusinessReview.user_id'),
            'fields' => array('*', 'COUNT(BusinessReview.id) as total')
        ));
        if($data != null)
        {
            $mMedal = MooCore::getInstance()->getModel('Business.Medal');
            $mUser = MooCore::getInstance()->getModel('User');
            $medal = $mMedal->findByKeyword(MEDAL_REVIEW_OF_THE_WEEK);
            foreach($data as $k => $review)
            {
                //auto get medel for top reviews
                if($medal != null && !$mUserMedal->checkHasMedal($review['BusinessReview']['user_id'], $medal['Medal']['id']))
                {
                    if($mUserMedal->saveUserMedal($review['BusinessReview']['user_id'], $medal['Medal']['id']))
                    {
                        $user = $mUser->findById($review['BusinessReview']['user_id']);
                        $mUserMedal->sendBadgeNotification($review['BusinessReview']['user_id'], $user['User']['moo_url'], $medal['Medal']['name']);
                    }
                }
                
                $data[$k]['Statistic'] = $this->userReviewStatistic($review['User']['id']);
                $data[$k]['Medals'] = $mUserMedal->getUserMedals($review['User']['id']);
            }
        }
        return $data;
    }
    
    public function getReviewOfMonth()
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $last_day = strtotime('last day of last month');
        $first_day = strtotime('first day of last month');
        $cond = array(
            'BusinessReview.parent_id' => 0,
            'UNIX_TIMESTAMP(BusinessReview.created) >= '.$first_day,
            'UNIX_TIMESTAMP(BusinessReview.created) <= '.$last_day,
        );
        
        return $this->find('first', array(
            'conditions' => $cond,
            'limit' => 1,
            'order' => array('BusinessReview.useful_count' => 'DESC')
        ));
    }
    
    public function totalUserUsefulReviews($user_id)
    {
        $this->recursive = -1;
        $cond = array(
            'BusinessReview.parent_id' => 0,
            'BusinessReview.user_id' => $user_id,
            'BusinessReview.useful_count > 0',
            'BusinessReview.report_count' => 0
        );
        
        return $this->find('count', array(
            'conditions' => $cond,
        ));
    }
    
    public function totalUserUnratedReviews($user_id)
    {
        $this->recursive = -1;
        $cond = array(
            'BusinessReview.parent_id' => 0,
            'BusinessReview.user_id' => $user_id,
            'BusinessReview.useful_count' => 0,
            'BusinessReview.report_count' => 0
        );
        
        return $this->find('count', array(
            'conditions' => $cond,
        ));
    }
    
    public function totalUserReportedReviews($user_id)
    {
        $this->recursive = -1;
        $cond = array(
            'BusinessReview.parent_id' => 0,
            'BusinessReview.user_id' => $user_id,
            'BusinessReview.report_count > 0'
        );
        
        return $this->find('count', array(
            'conditions' => $cond,
        ));
    }
    
    public function userReviewStatistic($user_id)
    {
        $total_useful = $this->totalUserUsefulReviews($user_id);
        $total_unrated = $this->totalUserUnratedReviews($user_id);
        $total_reported = $this->totalUserReportedReviews($user_id);
        $total = $total_useful + $total_unrated + $total_reported;
        $per_useful = $total ? round($total_useful / $total * 100) : 0;
        $per_unrated = $total ? round($total_unrated / $total * 100) : 0;
        $per_reported = $total ? round($total_reported / $total * 100) : 0;
        return array(
            'total_useful' => $total_useful,
            'total_unrated' => $total_unrated,
            'total_reported' => $total_reported,
            'per_useful' => $per_useful,
            'per_unrated' => $per_unrated,
            'per_reported' => $per_reported,
        );
    }
    
    public function getRecentReview($user_id = null, $block_user = true)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Business' => array('className' => 'Business.Business')
            ),
            'hasMany' => array(
                'Photo' => array(
                    'className' => 'Photo.Photo',
                    'counterCache' => true,
                    'foreignKey' => 'target_id',
                    'conditions' => array(
                        'Photo.target_id' => 'BusinessReview.id',
                        'Photo.type' => 'Business_Review'
                    ),
                    'dependent' => true
                )
            )
        ));
        $cond = array('BusinessReview.parent_id' => 0);
        
        if($user_id > 0)
        {
            $cond['BusinessReview.user_id'] = $user_id;
        }
        
        $this->virtualFields = null;
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Business.business_recent_review_items'),
            'order' => array('BusinessReview.created' => 'DESC')
        ));
    }
    
    public function getReviewOfDay($user_id = null)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Business' => array('className' => 'Business.Business')
            )
        ));
        $cond = array(
            'BusinessReview.parent_id' => 0,
            'DATE(BusinessReview.created) = CURDATE()',
            'BusinessReview.useful_count > 0'
        );
        
        if($user_id > 0)
        {
            $cond['BusinessReview.user_id'] = $user_id;
        }
        
        $this->virtualFields = null;
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Business.business_review_of_day_items'),
            'order' => array('BusinessReview.useful_count' => 'DESC', 'BusinessReview.rating' => 'DESC'),
        ));
    }
}