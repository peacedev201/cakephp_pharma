<?php
class StoreReview extends StoreAppModel 
{
    public $validationDomain = 'store';
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

    public $hasMany = array(
        'Photo' => array(
            'counterCache' => true,
            'foreignKey' => 'target_id',
            'conditions' => array(
                'Photo.target_id' => 'StoreReview.id',
                'Photo.type' => 'Store_Review'
            ),
            'dependent' => true
        )
    );
    
    public function getReviews($product_id, $page = 1, $limit = 0, $except_id = '')
    {
        $this->unbindModel(array(
            'belongsTo' => array('Store'),
        ), true);
        $mStoreReviewUseful = MooCore::getInstance()->getModel('Store.StoreReviewUseful');
        if($limit == 0)
        {
            $limit = Configure::read('Store.product_review_per_page');
        }
        $cond = array(
            'StoreReview.store_product_id' => $product_id,
            'StoreReview.parent_id' => 0
        );
        if((int)$except_id > 0)
        {
            $cond[] = 'StoreReview.id != '.$except_id;
        }
        $data = $this->find('threaded', array(
            'conditions' => $cond,
            'limit' => $limit,
            'order' => array('StoreReview.created' => 'DESC'),
            'page' => $page
        ));
        
        //find first review
        $first_review = $this->find('first', array(
            'conditions' => array(
                'StoreReview.store_product_id' => $product_id,
                'StoreReview.parent_id' => 0
            ),
            'limit' => $limit,
            'order' => array('StoreReview.created' => 'ASC'),
            'fields' => array('StoreReview.id')
        ));
        
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                $data[$k]['StoreReview']['set_useful'] = $mStoreReviewUseful->isSetUseful($item['StoreReview']['id']);
                $data[$k]['StoreReview']['first_review'] = false;
                if($item['StoreReview']['id'] == $first_review['StoreReview']['id'])
                {
                    $data[$k]['StoreReview']['first_review'] = true;
                }
            }
        }
        return $this->parseReplyData($data);
    }
    
    public function isReviewed($product_id)
    {
        return $this->hasAny(array(
            'StoreReview.store_product_id' => $product_id,
            'StoreReview.parent_id' => 0,
            'StoreReview.user_id' => MooCore::getInstance()->getViewer(true)
        ));
    }
    
    public function isReviewExist($id, $user_id = null, $product_id = null)
    {
        $cond = array(
            'StoreReview.id' => $id
        );
        if($user_id > 0)
        {
            $cond['StoreReview.user_id'] = $user_id;
        }
        if($product_id > 0)
        {
            $cond['StoreReview.store_product_id'] = $product_id;
        }
        return $this->hasAny($cond);
    }
    
    public function updateStoreReviewScore($product_id) 
    {
        $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        
        $this->unbindModel(array(
            'belongsTo' => array('User', 'Store'),
            'hasMany' => array('Photo'),
        ), true);
        
        $reviews = $this->find('first', array(
            'conditions' => array(
                'StoreReview.store_product_id' => $product_id,
                'StoreReview.parent_id' => 0
            ),
            'fields' => array('SUM(StoreReview.rating) as total_rating', 'COUNT(StoreReview.id) as total_vote')
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
        $mStoreProduct->updateAll(array(
            'StoreProduct.rating' => "'".$total_score."'",
            'StoreProduct.rating_count' => "'".$reviews['total_vote']."'"
        ), array(
            'StoreProduct.id' => $product_id
        ));
    }
    
    public function deleteReview($product_review_id = null, $product_id = null)
    {
        if($product_review_id > 0)
        {
            $mPhoto = MooCore::getInstance()->getModel('Photo.Photo');
            $review = $this->findById($product_review_id);
            if($this->delete($product_review_id))
            {
                //delete all replies photo
                $replies = $this->find('all', array(
                    'conditions' => array(
                        'StoreReview.parent_id' => $product_review_id
                    ),
                    'fields' => array('StoreReview.id')
                ));
                if($replies != null)
                {
                    foreach($replies as $reply)
                    {
                        $mPhoto->deleteAll(array(
                            'Photo.target_id' => $reply['StoreReview']['id'],
                            'Photo.type' => 'Store_Review'
                        ));
                        $this->delete($reply['StoreReview']['id']);
                    }
                }
                
                //delete all photos
                $mPhoto->deleteAll(array(
                    'Photo.target_id' => $product_review_id,
                    'Photo.type' => 'Store_Review'
                ));

                //update product counter
                if(!empty($review['StoreReview']['product_id']))
                {
                    $this->updateStoreReviewScore($review['StoreReview']['product_id']);
                }
                return true;
            }
        }
        else if($product_id > 0)
        {
            return $this->deleteAll(array(
                'StoreReview.store_product_id' => $product_id
            ));
        }
        return false;
    }
    
    public function getReviewRuler($product_id)
    {
        $this->unbindModel(array(
            'belongsTo' => array('User', 'Store'),
            'hasMany' => array('Photo'),
        ), true);
        $this->virtualFields = array(
            'star' => 'FLOOR(StoreReview.rating)',
            'total_rating' => 'SUM(StoreReview.rating)',
            'total_vote' => 'COUNT(StoreReview.rating)',
            //'percent' => 'COUNT(StoreReview.rating) / SUM(COUNT(StoreReview.rating))'
        );
        
        $total = $this->find('count', array(
            'conditions' => array(
                'StoreReview.store_product_id' => $product_id,
            )
        ));
        
        $data = $this->find('all', array(
            'conditions' => array(
                'StoreReview.store_product_id' => $product_id,
            ),
            'group' => array('StoreReview.star'),
            'order' => array('StoreReview.star' => 'DESC'),
            'fields' => array('StoreReview.star', 'StoreReview.total_rating', 'StoreReview.total_vote'/*, 'StoreReview.percent'*/)
        ));
        $result = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $item['StoreReview']['percent'] = $item['StoreReview']['total_vote'] / $total * 100;
                $result[$item['StoreReview']['star']] = $item['StoreReview'];
            }
        }
        return $result;
    }
    
    public function loadMyReviews($user_id, $page = 1, $block_user = true)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->unbindModel(array(
            'belongsTo' => array('StoreType', 'StorePackage', 'User'),
            'hasMany' => array('StoreTime', 'StorePhoto'),
            'hasAndBelongsToMany' => array('StorePayment')
        ), true);
        
        $cond = array(
            'StoreReview.user_id' => $user_id,
            'StoreReview.parent_id' => 0
        );
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => array('StoreReview.id' => 'DESC'),
            'limit' => Configure::read('Store.product_search_item_per_page'),
            'page' => $page
        ));
        $data = $this->parseReplyData($data);
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                $parent = $item['Store']['parent_id'] > 0 ? $mStore->findById($item['Store']['parent_id']) : null;
                $data[$k]['Store']['moo_parent'] = !empty($parent['Store']) ? $parent['Store'] : null;
                $data[$k]['Store']['moo_parent_cat'] = !empty($parent['StoreCategory']) ? $parent['StoreCategory'] : null;
            }
        }
        return $data;
    }
    
    public function parseReplyData($data, $single = false)
    {
        if($data != null)
        {
            $this->unbindModel(array(
                'belongsTo' => array('Store'),
            ), true);
            if($single)
            {
                $data['children'] = $this->find('all', array(
                    'conditions' => array(
                        'StoreReview.parent_id' => $data['StoreReview']['id']
                    ),
                    'order' => array('StoreReview.id' => 'DESC'),
                ));
            }
            else
            {
                foreach($data as $k => $item)
                {
                    $data[$k]['children'] = $this->find('all', array(
                        'conditions' => array(
                            'StoreReview.parent_id' => $item['StoreReview']['id']
                        ),
                        'order' => array('StoreReview.id' => 'DESC'),
                    ));
                }
            }
        }
        return $data;
    }

    public function updateUsefulCounter($review_id)
    {
        $mStoreReviewUseful = MooCore::getInstance()->getModel('Store.StoreReviewUseful');
        $total = $mStoreReviewUseful->find('count', array(
            'conditions' => array(
                'StoreReviewUseful.store_review_id' => $review_id
            )
        ));
        $this->updateAll(array(
            'StoreReview.useful_count' => $total
        ), array(
            'StoreReview.id' => $review_id
        ));
        return $total;
    }
    
    public function saveReviewActivity($product_id, $product_review_id)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        return $mStore->saveActivity($product_id, STORE_PRODUCT_ACTIVITY_REVIEW_ACTION, STORE_PRODUCT_ACTIVITY_REVIEW_ITEM, $product_review_id);
    }
    
    public function deleteReviewActivity($product_id, $product_review_id)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        return $mStore->deleteActivity($product_id, STORE_PRODUCT_ACTIVITY_REVIEW_ACTION, STORE_PRODUCT_ACTIVITY_REVIEW_ITEM, $product_review_id);
    }
    
    public function findLatestReview($product_id)
    {
        $this->unbindModel(array(
            'belongsTo' => array('Store'),
            'hasMany' => array('Photo'),
        ), true);
        return $this->find('first', array(
            'conditions' => array(
                'StoreReview.store_product_id' => $product_id,
                'StoreReview.parent_id' => 0
            ),
            'order' => array('StoreReview.id' => 'DESC')
        ));
    }
    
    public function totalMyStoreReview($user_id = null)
    {
        return $this->find('count', array(
            'conditions' => array(
                'StoreReview.parent_id' => 0,
                'StoreReview.user_id' => $user_id > 0 ? $user_id : MooCore::getInstance()->getViewer(true)
            )
        ));
    }
    
    /*public function updateUserReviewCounter()
    {
        $mUser = MooCore::getInstance()->getModel('User');
        $uid = MooCore::getInstance()->getViewer(true);
        $total = $this->find('count', array(
            'conditions' => array(
                'StoreReview.parent_id' => 0,
                'StoreReview.user_id' => $uid
            )
        ));
        $mUser->updateAll(array(
            'User.product_count' => $total
        ), array(
            'User.id' => $uid
        ));
    }*/
    
    public function getReviewDetail($id, $product_id = null, $parent_id = -1, $all = false)
    {
        if(!$all)
        {
            $this->unbindModel(array(
                'belongsTo' => array('Store'),
            ), true);
        }
        $cond = array(
            'StoreReview.id' => $id,
        );
        if($parent_id > -1)
        {
            $cond['StoreReview.parent_id'] = (int)$parent_id;
        }
        if($product_id > 0)
        {
            $cond['StoreReview.store_product_id'] = $product_id;
        }

        $data = $this->find('first', array(
            'conditions' => $cond
        ));
        return $this->parseReplyData($data, true);
    }
    
    public function isFirstReview($review_id, $product_id)
    {
        $data = $this->find('first', array(
            'conditions' => array(
                'StoreReview.store_product_id' => $product_id,
                'StoreReview.parent_id' => 0
            ),
            'order' => array('StoreReview.id' => 'ASC')
        ));
        if(!empty($data['StoreReview']['id']) && $data['StoreReview']['id'] == $review_id)
        {
            return true;
        }
        return false;
    }
    
    public function getWeeklyReview()
    {
        $mUserMedal = MooCore::getInstance()->getModel('Store.UserMedal');
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->recursive = -1;
        $last_sunday = strtotime('last Sunday +1 day');
        $last_monday = strtotime('last Sunday -7 days');
        $cond = array(
            'StoreReview.parent_id' => 0,
            'UNIX_TIMESTAMP(StoreReview.created) >= '.$last_monday,
            'UNIX_TIMESTAMP(StoreReview.created) <= '.$last_sunday,
            'StoreReview.useful_count > 0',
            'StoreReview.report_count' => 0
        );
        
        //get by location
        $around_params = $mStore->findStoreAround();
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
            'order' => array('StoreReview.useful_count' => 'DESC'),
            'group' => array('StoreReview.user_id'),
            'fields' => array('*', 'COUNT(StoreReview.id) as total')
        ));
        if($data != null)
        {
            $mMedal = MooCore::getInstance()->getModel('Store.Medal');
            $mUser = MooCore::getInstance()->getModel('User');
            $medal = $mMedal->findByKeyword(MEDAL_REVIEW_OF_THE_WEEK);
            foreach($data as $k => $review)
            {
                //auto get medel for top reviews
                if($medal != null && !$mUserMedal->checkHasMedal($review['StoreReview']['user_id'], $medal['Medal']['id']))
                {
                    if($mUserMedal->saveUserMedal($review['StoreReview']['user_id'], $medal['Medal']['id']))
                    {
                        $user = $mUser->findById($review['StoreReview']['user_id']);
                        $mUserMedal->sendBadgeNotification($review['StoreReview']['user_id'], $user['User']['moo_url'], $medal['Medal']['name']);
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
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $last_day = strtotime('last day of last month');
        $first_day = strtotime('first day of last month');
        $cond = array(
            'StoreReview.parent_id' => 0,
            'UNIX_TIMESTAMP(StoreReview.created) >= '.$first_day,
            'UNIX_TIMESTAMP(StoreReview.created) <= '.$last_day,
        );
        
        return $this->find('first', array(
            'conditions' => $cond,
            'limit' => 1,
            'order' => array('StoreReview.useful_count' => 'DESC')
        ));
    }
    
    public function totalUserUsefulReviews($user_id)
    {
        $this->recursive = -1;
        $cond = array(
            'StoreReview.parent_id' => 0,
            'StoreReview.user_id' => $user_id,
            'StoreReview.useful_count > 0',
            'StoreReview.report_count' => 0
        );
        
        return $this->find('count', array(
            'conditions' => $cond,
        ));
    }
    
    public function totalUserUnratedReviews($user_id)
    {
        $this->recursive = -1;
        $cond = array(
            'StoreReview.parent_id' => 0,
            'StoreReview.user_id' => $user_id,
            'StoreReview.useful_count' => 0,
            'StoreReview.report_count' => 0
        );
        
        return $this->find('count', array(
            'conditions' => $cond,
        ));
    }
    
    public function totalUserReportedReviews($user_id)
    {
        $this->recursive = -1;
        $cond = array(
            'StoreReview.parent_id' => 0,
            'StoreReview.user_id' => $user_id,
            'StoreReview.report_count > 0'
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
        $this->recursive = 2;
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->unbindModel(array(
            'belongsTo' => array('User', 'StoreType', 'StorePackage'),
            'hasMany' => array('StoreTime', 'StorePhoto'),
            'hasAndBelongsToMany' => array('StorePayment', 'StoreCategory')
        ), true);
        $cond = array('StoreReview.parent_id' => 0);
        
        if($user_id > 0)
        {
            $cond['StoreReview.user_id'] = $user_id;
        }
        
        $this->virtualFields = null;
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Store.product_recent_review_items'),
            'order' => array('StoreReview.created' => 'DESC')
        ));
    }
    
    public function getReviewOfDay($user_id = null)
    {
        $this->recursive = 2;
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->unbindModel(array(
            'belongsTo' => array('User', 'StoreType', 'StorePackage'),
            'hasMany' => array('StoreTime', 'StorePhoto'),
            'hasAndBelongsToMany' => array('StorePayment', 'StoreCategory')
        ), true);
        $cond = array(
            'StoreReview.parent_id' => 0,
            'DATE(StoreReview.created) = CURDATE()',
            'StoreReview.useful_count > 0'
        );
        
        if($user_id > 0)
        {
            $cond['StoreReview.user_id'] = $user_id;
        }
        
        $this->virtualFields = null;
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Store.product_review_of_day_items'),
            'order' => array('StoreReview.useful_count' => 'DESC', 'StoreReview.rating' => 'DESC'),
        ));
    }
}