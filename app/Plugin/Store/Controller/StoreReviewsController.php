<?php 
class StoreReviewsController extends StoreAppController{
    public $components = array('Paginator');
    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->loadModel('Store.Store');
        $this->loadModel('Store.StoreReview');
        $this->loadModel('Store.StoreReviewUseful');
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.StoreReviewPhoto');
        $this->loadModel('Store.StoreOrder');
    }
    
    public function review_dialog()
    {
        $data = $this->request->data;
        if(!empty($data['review_id']))
        {
            $product_review = $this->StoreReview->findById($data['review_id']);
        }
        else
        {
            $product_review = $this->StoreReview->initFields();
        }
        
        //parent review
        $parent_review = null;
        if(!empty($data['parent_id']))
        {
            $parent_review = $this->StoreReview->findById($data['parent_id']);
        }
        
        $this->set(array(
            'product_id' => $data['product_id'],
            'product_review' => $product_review,
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
        $product_id = $data['store_product_id'];
        $product = $this->StoreProduct->loadOnlyProduct($data['store_product_id']);
        $isReply = false;
        if($data['parent_id'] > 0)
        {
            $isReply = true;
        }
        if($uid == null)
        {
            $this->_jsonError(__d('store', 'Login or register to post your review'));
        }
        else if($product == null)
        {
            $this->_jsonError(__d('store', 'Product not found'));
        }
        else if(!$product['StoreProduct']['allow_review'])
        {
            $this->_jsonError(__d('store', 'Review function is currently unavailable for this product'));
        }
        else if(!$isReply && 
            !$this->StoreOrder->isBoughtProduct($product_id) && 
            Configure::read('Store.store_only_buyer_can_review') == 1 && 
            $product['StoreProduct']['new_price'] > 0)
        {
            $this->_jsonError(__d('store', 'Please buy product to post your review'));
        }
        else if($data['id'] > 0 && !$this->StoreReview->isReviewExist($data['id']))
        {
            $this->_jsonError(__d('store', 'Review not found'));
        }
        if($data['id'] > 0)
        {
            $this->StoreReview->id = $data['id'];
            $isEdit = true;
        }
        else
        {
            $data['user_id'] = $uid;
        }
        
        $is_reviewed = $this->StoreReview->isReviewed($data['store_product_id']);
        if(!$isReply &&(($isEdit && !$is_reviewed) ||
           (!$isEdit && $is_reviewed) ||
           $product['StoreProduct']['user_id'] == $uid))
        {
            $this->_jsonError(__d('store', "You don't have permission to review"));
        }
        if($isReply && $product['StoreProduct']['user_id'] != $uid)
        {
            $this->_jsonError(__d('store', "You don't have permission to review"));
        }
        
        $this->StoreReview->set($data);
        $this->_validateData($this->StoreReview);
        if($this->StoreReview->save($data))
        {
            $review_id = $this->StoreReview->id;
            
            //update product counter
            $this->StoreReview->updateStoreReviewScore($product_id);
            
            //save photo
            $this->save_photo($data, $review_id);
            
            //load review
            $review = $this->StoreReview->getReviewDetail($review_id, $product_id, $data['parent_id']);

            //activity & notification
            if(!$isEdit)
            {
                if($isReply) //reply
                {
                    $parent = $this->StoreReview->findById($data['parent_id']);
                    if($parent['StoreReview']['user_id'] != MooCore::getInstance()->getViewer(true))
                    {
                        $this->Store->sendNotification(
                            $parent['StoreReview']['user_id'], 
                            $uid, 
                            'product_reply', 
                            $product['StoreProduct']['moo_url'].'?review='.$parent['StoreReview']['id'].'#tab-reviews', 
                            $product['StoreProduct']['name']
                        );
                    }
                }
                else
                {
                    //save activities
                    $this->StoreReview->saveReviewActivity($product_id, $review_id);
            
                    //notification
                    $this->Store->sendNotification(
                        $product['StoreProduct']['user_id'], 
                        $uid,
                        'product_review',
                        $product['StoreProduct']['moo_url'].'?review='.$review_id.'#tab-reviews',
                        $product['StoreProduct']['name']
                    );
                }
            }
            
            $this->set(array(
                'product' => $product,
                'review' => $review,
                'just_now' => true,
                'is_reply' => $isReply,
                'user_id' => $uid,
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
            $this->_jsonError(__d('store', 'Can not post review, please try again'));
        }
    }
    
    private function save_photo($data, $product_review_id)
    {
        //delete
        if(!empty($data['photo_review_delete_id']))
        {
            $this->StoreReviewPhoto->deletePhotoList($data['photo_review_delete_id']);
        }
        
        //new
        if(!empty($data['attachments']))
        {
            $attachments = explode(',', $data['attachments']);
            foreach($attachments as $filename)
            {
                $this->StoreReviewPhoto->create();
                $this->StoreReviewPhoto->set(array(
                    'target_id' => $product_review_id,
                    'type' => 'Store_Review',
                    'user_id' => MooCore::getInstance()->getViewer(true),
                    'thumbnail' => $filename,
                    'caption' => ''
                ));
                $this->StoreReviewPhoto->save();
            }
        }
    }
    
    public function delete_review($product_review_id)
    {
        $cUser = $this->_getUser();
        $review = $this->StoreReview->getReviewDetail($product_review_id);
        if(!$cUser['Role']['is_admin'] && $review == null)
        {
            $this->_jsonError(__d('store', 'Review not found'));
        }
        else
        {
            if($this->StoreReview->deleteReview($product_review_id))
            {
                //remove all report 
                $this->loadModel('Report');
                $this->Report->deleteAll(array('Report.type' => 'Store_Review', 'Report.target_id' => $product_review_id));
                
                //update product score
                $this->StoreReview->updateStoreReviewScore($review['StoreReview']['store_product_id']);

                //activity
                $this->StoreReview->deleteReviewActivity($review['StoreReview']['store_product_id'], $product_review_id);
            
                //product
                $product = $this->StoreProduct->loadOnlyProduct($review['StoreReview']['store_product_id']);
                
                if($review['StoreReview']['parent_id'] > 0)
                {
                    $this->_jsonSuccess(__d('store', 'Your response has been deleted.'), false, array(
                        'redirect' => $product['Store']['moo_href']
                    ));
                }
                else
                {
                    $this->_jsonSuccess(__d('store', 'Your review has been deleted.'), false, array(
                        'redirect' => $product['Store']['moo_href']
                    ));
                }
            }
            $this->_jsonError(__d('store', 'Can not delete review, please try again'));
        }
    }
    
    public function load_product_reviews($product_id, $review_id = '')
    {
        $this->autoRender = false;
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $reviews = $this->StoreReview->getReviews($product_id, $page, 0, $review_id);
        if((int)$review_id > 0)
        {
            $focus_review = $this->StoreReview->getReviewDetail($review_id, $product_id);
            if($focus_review != null && $reviews != null)
            {
                array_unshift($reviews, $focus_review);
            }
            else if($focus_review != null && $reviews == null)
            {
                $reviews[] = $focus_review;
            }
        }
        $product = $this->StoreProduct->loadOnlyProduct($product_id);

        $this->set(array(
            'product' => $product,
            'reviews' => $reviews,
            'page' => $page,
            'more_review_url' => '/stores/store_reviews/load_product_reviews/'.$product_id.'/page:'.($page + 1)
        ));
        $this->render('Store.Elements/list/review_list');
    }
    
    public function useful($review_id)
    {
        $this->autoRender = false;
        if(MooCore::getInstance()->getViewer(true) == null)
        {
            $this->_jsonError(__d('store', 'Login or register to continue'));
        }
        else if(!$this->StoreReview->isReviewExist($review_id))
        {
            $this->_jsonError(__d('store', 'Review not found'));
        }
        else
        {
            $total = 0;
            $enable = 0;
            if($this->StoreReviewUseful->isSetUseful($review_id))
            {
                $enable = 1;
                $total = $this->StoreReviewUseful->deleteUseful($review_id);
            }
            else
            {
                $total = $this->StoreReviewUseful->addUseful($review_id);
            }
            $this->_jsonSuccess(__d('store', 'Success'), null, array(
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
        if(!$this->StoreReview->isReviewExist($data['review_id']))
        {
            $this->_jsonError(__d('store', 'Review or reply not found'));
        }
        else if($data['reason'] == null)
        {
            $this->_jsonError(__d('store', 'Reason can not be empty'));
        }
        else
        {
            $uid = $this->Auth->user('id');
            $review = $this->StoreReview->findById($data['review_id']);
            
            $aReport = array('type' => 'Store_Review', 'target_id' => $review['StoreReview']['id'], 'user_id' => $uid);
            
            $this->loadModel('Report');
            if($this->Report->hasAny($aReport)){
                $this->_jsonError(__d('store', 'Duplicated report'));
            }
            
            //save report
            $aReport['reason'] = $data['reason'];
            $this->Report->set($aReport);
            if ($this->Report->save()) {
                $mAdminNotification = MooCore::getInstance()->getModel('AdminNotification');
                $mAdminNotification->save(array(
                    'text' => __d('store', 'reported a product review'),
                    'user_id' => $uid,
                    'report_id' => $this->Report->id,
                    'message' => $data['reason'],
                    'url' => $review['StoreReview']['moo_href'].'?review='.$review['StoreReview']['id']
                ));

                $this->StoreReview->increaseCounter($data['review_id'], 'report_count');
                $this->Store->sendNotification(
                    $review['StoreReview']['user_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'product_review_report', 
                    $review['StoreReview']['moo_url'].'?review='.$review['StoreReview']['id'], 
                    $data['reason']
                );
                $this->_jsonSuccess(__d('store', 'Thank you! Your report has been submitted'));
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
            
            if($this->StoreReview->isReviewExist($review_id))
            {
                $this->StoreReview->decreaseCounter($review_id, 'report_count');
            }
            
            $this->loadModel('Report');
            $this->Report->delete($report_id);
            
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__d('store', 'Access denied'), 'default', array('class' => 'error-message'));
            $this->redirect('/pages/no-permission');
        }
    }
    
    public function show_like($product_review_id)
    {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $users = $this->StoreReviewUseful->getUserLike($product_review_id, $page);
        $this->set(array(
            'users' => $users,
            'total_user_like' => $this->StoreReviewUseful->totalUserLike($product_review_id),
            'page' => $page,
            'more_url' => '/product_review/show_like/'.$product_review_id.'/page:'.($page + 1)
        ));
        $this->render('Store.Elements/user_overlay_like');
    }
    
    public function total_product_review($product_id)
    {
        $product = $this->StoreProduct->loadOnlyProduct($product_id);
        
        $this->set(array(
            'product' => $product['StoreProduct']
        ));
        $this->render('Store.Elements/total_product_review');
    }
}