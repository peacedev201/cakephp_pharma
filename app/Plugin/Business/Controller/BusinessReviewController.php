<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessReviewController extends BusinessAppController {

    public $components = array('Paginator');
    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessReview');
        $this->loadModel('Business.BusinessReviewUseful');
        $this->loadModel('Business.BusinessFollow');
        $this->loadModel('Business.BusinessPhoto');
        $this->loadModel('Business.BusinessAdmin');
    }
    
    public function review_dialog()
    {
        $data = $this->request->data;
        if(!empty($data['review_id']))
        {
            $business_review = $this->BusinessReview->getReviewDetail($data['review_id']);
        }
        else
        {
            $business_review = $this->BusinessReview->initFields();
        }
        
        //parent review
        $parent_review = null;
        if(!empty($data['parent_id']))
        {
            $parent_review = $this->BusinessReview->getReviewDetail($data['parent_id']);
        }
        
        $this->set(array(
            'business_id' => $data['business_id'],
            'business_review' => $business_review,
            'parent_review' => $parent_review,
            'view_detail' => !empty($data['view_detail']) ? $data['view_detail'] : '',
            'my_review' => !empty($data['my_review']) ? $data['my_review'] : ''
        ));
        $this->render('/Elements/review_dialog');
    }
    
    public function do_review()
    {
        $data = $this->request->data;
        $isEdit = false;
        $uid = MooCore::getInstance()->getViewer(true);
        $business = $this->Business->getOnlyBusiness($data['business_id']);
        $package = $this->Business->getBusinessPackage($data['business_id']);
        $isReply = false;
        if($data['parent_id'] > 0)
        {
            $isReply = true;
        }
        if(!$this->isLoggedIn())
        {
            $this->_jsonError(__d('business', 'Login or register to post your review'));
        }
        else if(!$this->Business->isBusinessExist($data['business_id']))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if($isReply && !$package['response_review'])
        {
            $this->_jsonError($this->Business->upgradeMessage($business));
        }
        else if($isReply && !$this->Business->permission($data['business_id'], BUSINESS_PERMISSION_RESPONSE_REVIEW, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        else if($data['id'] > 0 && !$this->BusinessReview->isReviewExist($data['id']))
        {
            $this->_jsonError(__d('business', 'Review not found'));
        }
        /*else if(empty($data['id']) && empty($data['parent_id']) && $this->BusinessReview->isReviewed($data['business_id']))
        {
            $this->_jsonError(__d('business', 'You have already reviewed this business'));
        }*/
        /*else if($isReply && $business['Business']['user_id'] != $uid)
        {
            $this->_jsonError(__d('business', 'Only business owner can reply to reviews'));
        }*/
        if($data['id'] > 0)
        {
            $this->BusinessReview->id = $data['id'];
            $isEdit = true;
        }
        else
        {
            $data['user_id'] = $uid;
        }
        
        $this->BusinessReview->set($data);
        $this->_validateData($this->BusinessReview);
        if($this->BusinessReview->save($data))
        {
            $review_id = $this->BusinessReview->id;
            
            //update business counter
            $this->BusinessReview->updateBusinessReviewScore($data['business_id']);
            
            //update review counter for user
            //$this->BusinessReview->updateUserReviewCounter();
            
            //save photo
            $this->save_photo($data, $review_id);
            
            //load review
            MooCore::getInstance()->getModel('Business.Business');
            $review = $this->BusinessReview->getReviewDetail($review_id, $data['business_id'], $data['parent_id']);

            //activity & notification
            if(!$isEdit)
            {
                if($isReply) //reply
                {
                    $parent = $this->BusinessReview->getReviewDetail($data['parent_id']);
                    if($parent['BusinessReview']['user_id'] != MooCore::getInstance()->getViewer(true))
                    {
                        $this->Business->sendNotification(
                            $parent['BusinessReview']['user_id'], 
                            MooCore::getInstance()->getViewer(true), 
                            'business_reply', 
                            $business['Business']['moo_urlreview'].'?review='.$parent['BusinessReview']['id'], 
                            $parent['Business']['name']
                        );
                    }
                }
                else
                {
                    //save activities
                    $this->BusinessReview->saveReviewActivity($data['business_id'], $review_id);
            
                    //notification
                    $this->Business->sendBusinessNotification(
                        $business['Business']['id'], 
                        $business['Business']['parent_id'] == 0 ? 'business_review' : 'business_review_subpage', 
                        $uid,
                        $business['Business']['moo_urlreview'].'?review='.$review_id,
                        MooCore::getInstance()->getViewer(true)
                    );
                    
                    //send notification to followers
                    /* not send to followers
                    $this->BusinessFollow->sendFollowNotification(
                        $review['BusinessReview']['business_id'], 
                        'business_follow_review', 
                        MooCore::getInstance()->getViewer(true),
                        BUSINESS_DETAIL_LINK_REVIEW,
                        $review['Business']['name']
                    );
                    */
                }
            }
            
            if(!empty($data['view_detail']))
            {
                $message = $isReply ? __d('business', 'Your reply has been posted') : __d('business', 'Your review has been posted');
                echo $this->_jsonSuccess($message, true, array(
                    'location' => $business['Business']['moo_hrefreview']
                ));
            }

            $this->set(array(
                'review' => $review,
                'just_now' => true,
                'is_reply' => $isReply,
                'user_id' => $uid,
                'can_reply_review' => $this->Business->permission($data['business_id'], 'can_reply_review'),
                'can_delete_reply_review' => $this->Business->permission($data['business_id'], 'can_delete_reply_review'),
                'can_edit_reply_review' => $this->Business->permission($data['business_id'], 'can_edit_reply_review'),
                'can_response_review' => $package['response_review'] && $this->Business->permission($business['Business']['id'], BUSINESS_PERMISSION_RESPONSE_REVIEW, $business['Business']['moo_permissions']),
            ));
            if(isset($data['my_review']) && $data['my_review'] == 1)
            {
                $this->render('/Elements/misc/my_review_item');
            }
            else
            {
                $this->render('/Elements/misc/review_item');
            }
        }
        else
        {
            $this->_jsonError(__d('business', 'Can not post review, please try again'));
        }
    }
    
    private function save_photo($data, $business_review_id)
    {
        //delete
        if(!empty($data['photo_review_delete_id']))
        {
            $this->BusinessPhoto->deletePhotoList($data['photo_review_delete_id']);
        }
        
        //new
        if(!empty($data['attachments']))
        {
            $attachments = explode(',', $data['attachments']);
            foreach($attachments as $filename)
            {
                $this->BusinessPhoto->create();
                $this->BusinessPhoto->set(array(
                    'target_id' => $business_review_id,
                    'type' => 'Business_Review',
                    'user_id' => MooCore::getInstance()->getViewer(true),
                    'thumbnail' => $filename,
                    'caption' => ''
                ));
                $this->BusinessPhoto->save();
            }
        }
    }
    
    public function delete_review($business_review_id)
    {
        $cUser = $this->_getUser();
        $review = $this->BusinessReview->getReviewDetail($business_review_id);
        if(!$cUser['Role']['is_admin'] && $review == null && !$this->Business->permission($review['BusinessReview']['business_id'], BUSINESS_PERMISSION_RESPONSE_REVIEW))
        {
            $this->_jsonError(__d('business', 'Review not found'));
        }
        else
        {
            if($this->BusinessReview->deleteReview($business_review_id))
            {
                //remove all report 
                $this->loadModel('Report');
                $this->Report->deleteAll(array('Report.type' => 'Business_Review', 'Report.target_id' => $business_review_id));
                
                //update business score
                $this->BusinessReview->updateBusinessReviewScore($review['BusinessReview']['business_id']);
                
                //update review counter for user
                //$this->BusinessReview->updateUserReviewCounter();
                
                //activity
                $this->BusinessReview->deleteReviewActivity($review['BusinessReview']['business_id'], $business_review_id);
            
                //business
                $business = $this->Business->getOnlyBusiness($review['BusinessReview']['business_id']);
                
                if($review['BusinessReview']['parent_id'] > 0)
                {
                    $this->_jsonSuccess(__d('business', 'Your response has been deleted.'), false, array(
                        'redirect' => $business['Business']['moo_hrefreview']
                    ));
                }
                else
                {
                    $this->_jsonSuccess(__d('business', 'Your review has been deleted.'), false, array(
                        'redirect' => $business['Business']['moo_hrefreview']
                    ));
                }
            }
            $this->_jsonError(__d('business', 'Can not delete review, please try again'));
        }
    }
    
    public function load_business_reviews($business_id, $review_id = '')
    {
        $this->autoRender = false;
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $reviews = $this->BusinessReview->getReviews($business_id, $page, 0, $review_id);
        if((int)$review_id > 0 && $page == 1)
        {
            $focus_review = $this->BusinessReview->getReviewDetail($review_id, $business_id);
            if($focus_review != null && $reviews != null)
            {
                array_unshift($reviews, $focus_review);
            }
            else if($focus_review != null && $reviews == null)
            {
                $reviews[] = $focus_review;
            }
        }

        $package = $this->Business->getBusinessPackage($business_id);
        $business = $this->Business->getOnlyBusiness($business_id);

        $this->set(array(
            'business' => $business,
            'can_response_review' => $package['response_review'] && $this->Business->permission($business_id, BUSINESS_PERMISSION_RESPONSE_REVIEW, $business['Business']['moo_permissions']),
            'reviews' => $reviews,
            'page' => $page,
            'more_review_url' => '/business_review/load_business_reviews/'.$business_id.($review_id != '' ? '/'.$review_id : '').'/page:'.($page + 1),
            //'can_reply_review' => $this->Business->permission($business_id, 'can_reply_review'),
            //'can_delete_reply_review' => $this->Business->permission($business_id, 'can_delete_reply_review'),
            //'can_edit_reply_review' => $this->Business->permission($business_id, 'can_edit_reply_review')
        ));
        $this->render('/Elements/lists/review_list');
    }
    
    public function useful($review_id)
    {
        $this->autoRender = false;
        if(!$this->isLoggedIn())
        {
            $this->_jsonError(__d('business', 'Login or register to continue'));
        }
        else if(!$this->BusinessReview->isReviewExist($review_id))
        {
            $this->_jsonError(__d('business', 'Review not found'));
        }
        else
        {
            $total = 0;
            $enable = 0;
            if($this->BusinessReviewUseful->isSetUseful($review_id))
            {
                $enable = 1;
                $total = $this->BusinessReviewUseful->deleteUseful($review_id);
            }
            else
            {
                $total = $this->BusinessReviewUseful->addUseful($review_id);
            }
            $this->_jsonSuccess(__d('business', 'Success'), null, array(
                'total' => $total,
                'enable' => $enable
            ));
        }
    }
    
    public function report($review_id)
    {
        $this->_checkPermission();
        $this->set(array(
            'review_id' => $review_id
        ));
        $this->render('/Elements/report_review_dialog');
    }
    
    public function do_report()
    {
        $data = $this->request->data;
        if(!$this->BusinessReview->isReviewExist($data['review_id']))
        {
            $this->_jsonError(__d('business', 'Review or reply not found'));
        }
        else if($data['reason'] == null)
        {
            $this->_jsonError(__d('business', 'Reason can not be empty'));
        }
        else
        {
            $uid = $this->Auth->user('id');
            $review = $this->BusinessReview->getReviewDetail($data['review_id']);
            
            $aReport = array('type' => 'Business_Review', 'target_id' => $review['BusinessReview']['id'], 'user_id' => $uid);
            
            $this->loadModel('Report');
            if($this->Report->hasAny($aReport)){
                $this->_jsonError(__d('business', 'Duplicated report'));
            }
            
            //save report
            $aReport['reason'] = $data['reason'];
            $this->Report->set($aReport);
            if ($this->Report->save()) {
                $mAdminNotification = MooCore::getInstance()->getModel('AdminNotification');
                $mAdminNotification->save(array(
                    'text' => __d('business', 'reported a BusinessReview'),
                    'user_id' => $uid,
                    'report_id' => $this->Report->id,
                    'message' => $data['reason'],
                    'url' => $review['Business']['moo_hrefreview'].'?review='.$review['BusinessReview']['id']
                ));

                $this->BusinessReview->increaseCounter($data['review_id'], 'report_count');
                $this->Business->sendNotification(
                    $review['Business']['user_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_review_report', 
                    $review['Business']['moo_urlreview'].'?review='.$review['BusinessReview']['id'], 
                    $data['reason']
                );
                $this->_jsonSuccess(__d('business', 'Thank you! Your report has been submitted'));
            }
        }
    }
    
    public function delete_report($report_id, $review_id)
    {
        $cuser = $this->_getUser();
        if($cuser != null && $cuser['Role']['is_admin'])
        {
            $report_id = intval($report_id);
            $review_id = intval($review_id);
            
            if($this->BusinessReview->isReviewExist($review_id))
            {
                $this->BusinessReview->decreaseCounter($review_id, 'report_count');
            }
            
            $this->loadModel('Report');
            $this->Report->delete($report_id);
            
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__d('business', 'Access denied'), 'default', array('class' => 'error-message'));
            $this->redirect('/pages/no-permission');
        }
    }

    /*public function detail($business_id, $review_id)
    {
        if(!$this->BusinessReview->isReviewExist($review_id, null, $business_id))
        {
            $this->_redirectError(__d('business', 'Review is not found'), '/pages/error');
        }
        
        $review = $this->BusinessReview->getReviewDetail($review_id, $business_id);
        
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' :  'http';
        $sBusinessHref = $http . '://' . $_SERVER['SERVER_NAME'] . $this->request->base . '/business_review/detail/' . $business_id . '/' . $review_id;

        $this->set(array(
            'review' => $review,
            'sBusinessHref' => $sBusinessHref,
            'is_first_review' => $this->BusinessReview->isFirstReview($review_id, $business_id)
        ));
        
        $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
        $this->set('total_review', $mBusinessReview->totalMyBusinessReview($review['User']['id']));
        
        $this->set('title_for_layout', 'Business Review');
    }*/
    
    public function show_like($business_review_id)
    {
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		$users = $this->BusinessReviewUseful->getUserLike($business_review_id, $page);
		$this->set(array(
            'users' => $users,
            'total_user_like' => $this->BusinessReviewUseful->totalUserLike($business_review_id),
            'page' => $page,
            'more_url' => '/business_review/show_like/'.$business_review_id.'/page:'.($page + 1)
        ));
		$this->render('Business.Elements/user_overlay_like');
    }
}
