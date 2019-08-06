<?php 
class BusinessController extends BusinessAppController
{    
    public $components = array('Paginator');
    
    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->url = $this->request->base.'/businesses/';
        $this->url_dashboard = $this->request->base.'/businesses/dashboard/';
        $this->admin_url = $this->request->base.'/admin/business/business/';
        $this->set('url', $this->url);
        $this->set('url_dashboard', $this->url_dashboard);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessType');
        $this->loadModel('Business.BusinessTime');
        $this->loadModel('Business.BusinessPayment');
        $this->loadModel('Business.BusinessPaymentType');
        $this->loadModel('Business.BusinessLocation');
        $this->loadModel('Business.BusinessCategory');
        $this->loadModel('Business.BusinessCategoryItem');
        $this->loadModel('Business.BusinessPhoto');
        $this->loadModel('Business.BusinessAddress');
        $this->loadModel('Business.BusinessPackage');
        $this->loadModel('Business.BusinessAdmin');
        $this->loadModel('Business.BusinessFollow');
        $this->loadModel('Business.BusinessPaid');
        $this->loadModel('Business.BusinessReview');
        $this->loadModel('Photo.Album');
        $this->loadModel('Business.BusinessCheckin');
        $this->loadModel('User');
        $this->loadModel('Language');
        $this->loadModel('Business.BusinessStore');
    }
    
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        
    }
    
    ///////////////////////////////////backend///////////////////////////////////
    public function admin_index()
    {
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $status_filter = isset($this->request->query['status']) ? $this->request->query['status'] : BUSINESS_STATUS_PENDING;
        
        $businesses = $this->Business->loadManageBusinessList($this, $keyword, $status_filter);
        
        //status
        $status = array(
            BUSINESS_STATUS_PENDING => __d('business', 'Pending'),
            BUSINESS_STATUS_APPROVED => __d('business', 'Approve'),
            BUSINESS_STATUS_REJECTED => __d('business', 'Rejected'),
        );

        $this->set(array(
            'title_for_layout' => __d('business', 'Manage Businesss'),
            'businesses' => $businesses,
            'keyword' => $keyword,
            'status_filter' => $status_filter,
            'status' => $status
        ));
    }
    
    public function admin_delete($id = null)
    {
        if(!empty($this->request->data['cid']))
        {
            foreach($this->request->data['cid'] as $id)
            {
                $business = $this->Business->getOnlyBusiness($id);
                $this->Business->deleteBusiness($id);
                
                //delete activity
                if($business['Business']['parent_id'] > 0)
                {
                    $this->Business->deleteBranchActivity($business['Business']['parent_id'], $id);
                }
            }
        }
        else if(!empty($id))
        {
            $business = $this->Business->getOnlyBusiness($id);
            $this->Business->deleteBusiness($id);
            
            //delete activity
            if($business['Business']['parent_id'] > 0)
            {
                $this->Business->deleteBranchActivity($business['Business']['parent_id'], $id);
            }
        }
        $this->_redirectSuccess(__d('business', 'Successfully deleted'), $this->referer());
    }
    
    public function admin_status($status, $id)
    {
        if(!$this->Business->isBusinessExist($id))
        {
            $this->_redirectError(__d('business', 'Business not found'), '/admin/business/');
        }
        else
        {
            if($this->Business->changeStatus($status, $id))
            {
                $business = $this->Business->getOnlyBusiness($id);
                
                //update business location counter
                $this->BusinessLocation->updateBusinessCounter($business['Business']['business_location_id']);
                
                //notification
                $this->Business->sendNotification(
                    $business['Business']['creator_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_'.  strtolower($status).($business['Business']['parent_id'] > 0 ? '_subpage' : ''), 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );
                
                if($status == BUSINESS_STATUS_APPROVED)
                {
                    //activity
                    if($business['Business']['parent_id'] > 0)
                    {
                        $this->Business->saveBranchActivity($business['Business']['parent_id'], $business['Business']['id']);
                        
                        //notification
                        $this->Business->sendBusinessNotification(
                            $business['Business']['parent_id'], 
                            'business_create_branch', 
                            $business['Business']['creator_id'],
                            $business['Business']['moo_url'],
                            $business['Business']['creator_id']
                        );
                    }
                    else 
                    {
                        $this->Business->saveCreateBusinessActivity($id);
                    }
                
                    //send mail
                    $this->loadModel('User');
                    $aUser = $this->User->findById($business['Business']['user_id']);
                    $ssl_mode = Configure::read('core.ssl_mode');
                    $http = (!empty($ssl_mode)) ? 'https' : 'http';
                    $this->MooMail->send($aUser, $business['Business']['parent_id'] > 0 ? 'business_subpage_approve' : 'business_approve', array(                     
                        'business_title' => $business['Business']['moo_title'],
                        'link' => $http . '://' . $_SERVER['SERVER_NAME'] . $business['Business']['moo_href']
                    ));
                }
                
                $this->_redirectSuccess(__d('business', 'Successfully change status'), $this->referer());
            }
            $this->_redirectError(__d('business', 'Cannot change status! Please try again'), $this->referer());
        }
    }
    
    public function admin_verify($id, $value = null)
    {
    	if( !$this->Business->isBusinessExist($id) )
        {
            $this->_redirectError(__d('business', 'Business not found'), $this->referer());
    	}
        else
        {
            $this->Business->id = $id;
            if($this->Business->save(array('verify' => $value)))
            {
                $text = 'verify';
                if($value == 0)
                {
                    $text = 'unverify';
                    // delete request if unverify
                    $this->loadModel('Business.BusinessVerify');
                    $aBusinessVerify = $this->BusinessVerify->findByBusinessId($id);
                    if (!empty($aBusinessVerify))
                    {
                        $this->BusinessVerify->delete($aBusinessVerify['BusinessVerify']['id']);
                    }
                }
                $business = $this->Business->getOnlyBusiness($id);
                
                //update sub page verify
                $this->Business->updateSubPageVerify($id, $value);
                
                //activity
                if($business['Business']['status'] == BUSINESS_STATUS_APPROVED)
                {
                    $this->Business->saveVerifyActivity($id);
                }
                
                //notification
                $this->Business->sendNotification(
                    $business['Business']['user_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_'.$text, 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );
                $this->_redirectSuccess(__d('business', 'Successfully updated'), $this->referer());
            }
            $this->_redirectError(__d('business', 'Something went wrong! Please try again'), $this->referer());
    	}
    }
    
    public function admin_reject_dialog($business_id)
    {
        $this->set(array(
            'business_id' => $business_id
        ));
        $this->render('/Elements/admin_reject_dialog');
    }
    
    public function admin_featured_dialog($id)
    {
        $this->set(array(
            'business_id' => $id
        ));
    }
    
    public function admin_reject_business()
    {
        $this->reject_business();
    }
    
    public function admin_unfeatured($id)
    {
        if( !$this->Business->isBusinessExist($id) )
        {
            $this->_redirectError(__d('business', 'Business not found'), $this->referer());
    	}
        else
        {
            $this->Business->id = $id;
	    	if($this->Business->save(array('featured' => 0)))
            {
                $this->BusinessPaid->resetBusinessStatus('initial', $id, BUSINESS_PAID_TYPE);
                $this->_redirectSuccess(__d('business', 'This business has been set un-featured'), $this->referer());
            }
            $this->_redirectError(__d('business', 'Can not set featured, please try again.'), $this->referer());
        }
    }
    
    public function admin_featured()
    {
        $data = $this->request->data;
        if(!$this->Business->isBusinessExist($data['business_id']))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if(!is_numeric($data['day']))
        {
            $this->_jsonError(__d('business', 'Invalid day number'));
        }
        else
        {
            $this->Business->id = $data['business_id'];
	    	if($this->Business->save(array('featured' => 1)))
            {
                //save paid
                $this->BusinessPaid->resetBusinessStatus('initial', $data['business_id'], BUSINESS_PAID_TYPE);
                $curdate = date('Y-m-d H:i:s');
                $this->BusinessPaid->save(array(
                    'user_id' => MooCore::getInstance()->getViewer(true),
                    'business_id' => $data['business_id'],
                    'pay_type' => BUSINESS_PAID_TYPE,
                    'feature_day' => $data['day'],
                    'status' => 'active',
                    'active' => 1,
                    'business_transaction_id' => 0,
                    'expiration_date' => date('Y-m-d H:i:s', strtotime($curdate.' + '.$data['day'].' day'))
                ));
                
                //notification
                $business = $this->Business->findById($data['business_id']);
                $this->Business->sendNotification(
                    $business['Business']['user_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_featured', 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );
                
                $this->_jsonSuccess(__d('business', 'This business has been set featured'), true);
            }
            $this->_jsonError(__d('business', 'Can not set featured, please try again.'));
        }
    }
    
    ///////////////////////////////////frontend///////////////////////////////////
    public function index()
    {
        $task = !empty($this->request->params['task']) ? $this->request->params['task'] : '';
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $user_id = MooCore::getInstance()->getViewer(true);
        $this->set(array(
			'task' => $task,
			'title_for_layout' => ''
		));
        switch($task)
        {
            case 'my':
                /*if ($user_id == null) {
                    $this->_redirectError(__d('business', 'Please login to continue'), '/users/member_login');
                }*/
                $user_id = isset($this->request->params['pass'][0]) ? $this->request->params['pass'][0] : MooCore::getInstance()->getViewer(true);
                $businesses = $this->Business->loadMyBusiness($user_id, $page);
                $this->set('businesses', $businesses);
                $this->set('more_url', '/businesses/my/'.$user_id.'/page:'.($page + 1));
                if($page > 1)
                {
                    $this->render('/Elements/lists/my_business_list');
                }
                break;
            case 'my_reviews':
                if ($user_id == null) {
                    $this->_redirectError(__d('business', 'Please login to continue'), '/users/member_login');
                }
                $reviews = $this->BusinessReview->loadMyReviews($user_id, $page);
                $this->set('reviews', $reviews);
                $this->set('user_id', $user_id);
                break;
            case 'my_following':
                if ($user_id == null) {
                    $this->_redirectError(__d('business', 'Please login to continue'), '/users/member_login');
                }
                $businesses = $this->Business->loadMyFollowing($user_id, $page);
                $this->set('businesses', $businesses);
                $this->set('more_url', '/businesses/my_following/page:'.($page + 1));
                if($page > 1)
                {
                    $this->render('/Elements/lists/my_following_list');
                }
                break;
            case 'my_favourites':
                if ($user_id == null) {
                    $this->_redirectError(__d('business', 'Please login to continue'), '/users/member_login');
                }
                $businesses = $this->Business->loadMyFavourites($user_id, $page);
                $this->set('businesses', $businesses);
                $this->set('more_url', '/businesses/my_favourites/page:'.($page + 1));
                if($page > 1)
                {
                    $this->render('/Elements/lists/my_favourite_list');
                }
                break;
            default :
                $businesses = $this->Business->getAllBusinessPaging($this);
                $this->set('businesses', $businesses);
                $this->set('can_create_business', in_array(BUSINESS_ROLE_CREATE_BUSINESS, $this->role_param) ? true : false);
                $this->set('locations', $this->Business->getMultiLocations($businesses));
        }
    }
    
    public function view($param = null)
    {
        if(!in_array(BUSINESS_ROLE_VIEW_BUSINESS, $this->role_param))
        {
            $this->_redirectError(__d('business', 'You don\'t have permission to view business.'), '/businesses');
        }
        list($business_id, $item_id) = getIdFromUrl($param);
        $cUser = $this->_getUser();
        $tab = getTabFromUrl($this->request->url);
        if((int)$item_id > 0 && !$this->Business->isBusinessExist($business_id))
        {
            $error_message = __d('business', 'Sub page not found');
            $this->_redirectError($error_message, '/pages/error');
        }
        if(((int)$item_id > 0 && !$this->Business->isBusinessExist($item_id))|| !$this->Business->isBusinessExist($business_id))
        {
            $error_message = __d('business', 'Business not found');
            $this->_redirectError($error_message, '/pages/error');
        }
        if(($cUser != null && $cUser['Role']['is_admin']) || 
            $this->Business->isBusinessOwner($business_id) ||
            $this->BusinessAdmin->isBusinessAdmin($business_id, MooCore::getInstance()->getViewer(true)) ||
            $this->Business->isBusinessApproved($business_id))
        {
            $business = $this->Business->getBusiness($business_id, null, (int)$item_id);

            if(!empty($business['Business']['claim_id'])){
                $aBusiness = $this->Business->findById($business['Business']['claim_id']);
                $aBusinessClaim = $this->Business->getBusiness($business['Business']['claim_id'], null, $aBusiness['Business']['parent_id']);
                $this->redirect($aBusinessClaim['Business']['moo_url']);
            }
            
            $business_photos = $this->BusinessPhoto->getBusinessAlbumPhotos($business_id);
            
            //cat breadcrumb
            $cat_paths = array();
            if(!empty($business['BusinessCategory']))
            {
                $cat_exist = false;
                $temp_parent_cat = null;
                foreach($business['BusinessCategory'] as $item)
                {
                    if($item['parent_id'] > 0)
                    {
                        $cat_exist = true;
                        $cat_paths = $this->BusinessCategory->getPath($item['id']);
                        break;
                    }
                    else if($temp_parent_cat == null && $item['parent_id'] == 0)
                    {
                        $temp_parent_cat = $item;
                    }
                }
                
                if(!$cat_exist)
                {
                    $cat_paths = array(array('BusinessCategory' => $temp_parent_cat));
                }
            }
            
            //parent business
            $parent_business = $business['Business']['parent_id'] > 0 ? $this->Business->getOnlyBusiness($business['Business']['parent_id']) : null;
            
            //check has store
            $store = array();
            if($this->BusinessStore->isIntegrateStore())
            {
                $store = $this->BusinessStore->getStoreFromBusinessId($business_id);
            }
            
            $this->set(array(
                'tab' => getTabFromUrl($this->request->url),
                'cuser' => $this->_getUser(),
                'business' => $business,
                'store' => $store,
                'parent_business' => $parent_business,
                'business_photos' => $business_photos,
                'cat_paths' => $cat_paths
            ));
            
            $aCategories = array();
            foreach ($business['BusinessCategory'] as $aCategory){
                $aCategories[] = $aCategory['name'];
            }
            $sCategory = implode(' - ', $aCategories);
            
            $aLocation = $this->BusinessLocation->findById($business['Business']['business_location_id']);
            $this->set('title_for_layout', __d('business', '%s - %s %s', htmlspecialchars($business['Business']['name']), htmlspecialchars($sCategory), isset($aLocation['BusinessLocation']) ? __d('business', 'in ') . $aLocation['BusinessLocation']['name'] : ''));
            $this->set('title_for_property', __d('business', '%s - %s %s', htmlspecialchars($business['Business']['name']), htmlspecialchars($sCategory), isset($aLocation['BusinessLocation']) ? __d('business', 'in ') . $aLocation['BusinessLocation']['name'] : ''));
            $description_for_layout = strip_tags($business['Business']['description']);
            //$description_for_layout = String::truncate($description_for_layout, 200);
            $this->set('description_for_layout', htmlspecialchars($description_for_layout));
            $this->set('description_for_property', htmlspecialchars($description_for_layout));
            $this->set("is_busines_admin", $this->BusinessAdmin->isBusinessAdmin($business_id, MooCore::getInstance()->getViewer(true)));
            
            //permission
            if($business['Business']['parent_id'] > 0)
            {
                $parent = $this->Business->getOnlyBusiness($business['Business']['parent_id']);
                $this->set('permission_can_ban', $this->Business->permission($business['Business']['parent_id'], BUSINESS_PERMISSION_BAN, $parent['Business']['moo_permissions']));
                $this->set('permission_can_manage_subpages', $this->Business->permission($business['Business']['parent_id'], BUSINESS_PERMISSION_MANAGE_SUBPAGE, $parent['Business']['moo_permissions']));
            }
            else 
            {
                $this->set('permission_can_ban', $this->Business->permission($business_id, BUSINESS_PERMISSION_BAN, $business['Business']['moo_permissions']));
                $this->set('permission_can_manage_subpages', $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_SUBPAGE, $business['Business']['moo_permissions']));
            }
            
            
            // set og:image
            if(!empty($business['Business']['logo'])){
                $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
                $this->set('og_image', $businessHelper->getPhoto($business['Business'], array('prefix' => BUSINESS_IMAGE_SEO_WIDTH . '_', 'tag' => false)));
            }
            
            //rating 
            $rulers = $this->BusinessReview->getReviewRuler($business_id);
            
            //check reviewed
            $is_reviewed = $this->BusinessReview->isReviewed($business_id);
            
            $this->set('rulers', $rulers);
            $this->set('is_reviewed', $is_reviewed);
            
            //cliam
            $userBusiness = $this->User->findById($business['Business']['user_id']);

            $bClaim = true;
            $aAcoBusiness = explode(',', $userBusiness['Role']['params']);
            if (!in_array('business_claim', $aAcoBusiness) || !$this->Business->bCheckClaimBusiness($business, $cUser['id'])) {
                $bClaim = false;
            }

            $this->set('bClaim', $bClaim);
            
            $this->set('is_followed', $this->BusinessFollow->isFollowed($business_id));
            $this->set('hasBusiness', $this->Business->checkHasBusiness(MooCore::getInstance()->getViewer(true)));
            $this->set('is_favourite', $this->Business->isFavourited(MooCore::getInstance()->getViewer(true), $business_id));
            $this->set('is_banned', $this->BusinessFollow->isBanned($business_id));
            $this->set('bBusinessAdmin', (bool) $this->BusinessAdmin->isBusinessAdmin($business_id));
            
            //check tab
            list($business_id, $item_id, $seoname) = getIdFromUrl($this->request->params['pass'][0]);
            $review_id = isset($this->request->query['review']) ? $this->request->query['review'] : '';
            $business = $this->Business->getOnlyBusiness($business_id);
            $package = $this->Business->getBusinessPackage($business_id);
            $uid = MooCore::getInstance()->getViewer(true);

            switch($tab)
            {
                case BUSINESS_DETAIL_LINK_CONTACT:
                    if(!$package['contact_form'])
                    {
                        $this->_redirectError($this->Business->upgradeMessage($business), $business['Business']['moo_url']);
                    }
                    break;
            }
            
            //permission
            $permission_can_manage_photos = $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_PHOTO, $business['Business']['moo_permissions']);
            $permission_can_manage_products = $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_PRODUCT, $business['Business']['moo_permissions']);
            $permission_can_manage_subpages = $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_SUBPAGE, $business['Business']['moo_permissions']);
            $permission_can_upgrade_page = $this->Business->permission($business_id, BUSINESS_PERMISSION_UPGRADE_PAGE, $business['Business']['moo_permissions']);
            $permission_can_featured_page = $this->Business->permission($business_id, BUSINESS_PERMISSION_FEATURE_PAGE, $business['Business']['moo_permissions']);
            $permission_can_send_verification_request = $this->Business->permission($business_id, BUSINESS_PERMISSION_SEND_VERIFICATION_REQUEST, $business['Business']['moo_permissions']);
            $permission_can_manage_admins = $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_ADMIN, $business['Business']['moo_permissions']);
            $permission_can_edit_page = $this->Business->permission($business_id, BUSINESS_PERMISSION_EDIT_PAGE, $business['Business']['moo_permissions']);

            $this->set('is_reviewed', $this->BusinessReview->isReviewed($business_id));
            //$this->setData('can_create_review', $mBusiness->permission($business_id, 'can_create_review'));
            $this->set('item_id', $item_id);
            $this->set('review_id', $review_id);
            $this->set('tab', $tab);
            $this->set('seoname', $seoname);
            $this->set('isBusinessOwner', $this->Business->isBusinessOwner($business_id));
            $this->set('permission_can_manage_photos', $permission_can_manage_photos);
            $this->set('permission_can_manage_products', $permission_can_manage_products);
            $this->set('permission_can_manage_subpages', $permission_can_manage_subpages);
            $this->set('permission_can_upgrade_page', $permission_can_upgrade_page);
            $this->set('permission_can_featured_page', $permission_can_featured_page);
            $this->set('permission_can_send_verification_request', $permission_can_send_verification_request);
            $this->set('permission_can_manage_admins', $permission_can_manage_admins);
            $this->set('permission_can_edit_page', $permission_can_edit_page);
        }
        else
        {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }
    }
    
    public function load_info()
    {
        $data = $this->request->data;
        $business = $this->Business->getOnlyBusiness($data['id']);
        if($business != null)
        {
            switch($data['task'])
            {
                case 'tel':
                    echo $business['Business']['phone'];
                    exit;
                    break;
                case 'fax':
                    echo $business['Business']['fax'];
                    exit;
                    break;
                case 'website':
                    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
                    echo '<a href="' . $mooHelper->getFullUrl($business['Business']['website']) . '" target="_blank">' . $business['Business']['website'] . '</a>';
                    exit;
            }
        }
        echo '';exit;
    }
    
    public function create()
    {
		$this->set(array(
			'title_for_layout' => ''
		));
        if(!$this->isLoggedIn())
        {
            $this->_redirectError(__d('business', 'Please log in to continue'), '/users/member_login');
        }
		$this->_checkPermission(array('confirm' => true));
        if(!in_array(BUSINESS_ROLE_CREATE_BUSINESS, $this->role_param))
        {
            $this->_redirectError(__d('business', 'You don\'t have permission to create business.'), '/businesses');
        }
        $business = $this->Business->initFields();

        //type
        $businessTypes = $this->BusinessType->getBusinessTypeList(true);

        //payment
        $businessPayments = $this->BusinessPayment->getBusinessPayment();

        //list days in week
        $days = $this->Business->getListDayInWeek();

        //time business
        $times_open = $this->Business->getListTimeOpen();
        $times_close = $this->Business->getListTimeClose();

        $this->set(array(
            'business' => $business,
            'businessTypes' => $businessTypes,
            'businessPayments' => $businessPayments,
            'days' => $days,
            'times_open' => $times_open,
            'times_close' => $times_close,
            'allow_create' => true,
            'title_for_layout' => __d('business', 'Add new Business')
        ));
    }
    
    public function save()
    {
        if(!in_array(BUSINESS_ROLE_CREATE_BUSINESS, $this->role_param))
        {
            $this->_jsonError(__d('business', 'You don\'t have permission to create business.'));
        }
        $cUser = $this->_getUser();
        $uid = MooCore::getInstance()->getViewer(true);
        $data = $this->request->data;

        //validate
        $business = $this->Business->getOnlyBusiness($data['id']);
        if((int)$data['id'] > 0 && !$this->Business->permission($data['id'], BUSINESS_PERMISSION_EDIT_PAGE, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        
        //validate category
        if(count($data['business_category']) != count(array_filter($data['business_category'])))
        {
            $this->_jsonError(__d('business', 'Please input category'));
        }
        else if(empty($data['business_type_id']) || !$this->BusinessType->isBusinessTypeExist($data['business_type_id']))
        {
            $this->_jsonError(__d('business', 'Please select business type'));
        }
        
        /*if((int)$data['id'] > 0 && (empty($cUser) || (!$cUser['Role']['is_admin'] && !$this->Business->isBusinessOwner($data['id']))))
        {
            if(!$this->BusinessAdmin->isBusinessAdmin($data['id']))
            {
                $this->_jsonError(__d('business', 'You don \'t have permission to edit business'));
            }
        }*/
        
        $isEdit = false;
        //if((int)$data['id'] > 0 && (($this->Business->isBusinessExist($data['id'], $uid)) || ($cUser != null && $cUser['Role']['is_admin'])))
        if((int)$data['id'] > 0)
        {
            $this->Business->id = $data['id'];
            $isEdit = true;
        }
        else
        {
            $data['user_id'] = $uid;
            $data['creator_id'] = $uid;
            $data['business_package_id'] = $this->BusinessPackage->getDefaultPackage();
        }
        $data['always_open'] = !empty($data['always_open']) ? $data['always_open'] : 0;
        
        //get lng lat
        $data['lat'] = $data['lng'] = 0;
        $addressDetail = array();
        if($data['address'] != null)
        {
            $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
            $addressDetail = $businessHelper->getAddressDetail(null, null, $data['address']);
            $data['lng'] = $addressDetail['lng'];
            $data['lat'] = $addressDetail['lat'];

            //get location id
            //$addressDetail = $businessHelper->getAddressDetail($data['lat'], $data['lng']);
            $data['postal_code'] = $addressDetail['postal_code'];
            $data['business_location_id'] = $this->BusinessLocation->autoAddBusinessLocation($addressDetail['country'], $addressDetail['region']);
        }
        
        if(!$isEdit && !empty($data['claim_id'])){
            $data['is_claim'] = 3;
        } elseif(!empty($data['claim_id'])){
            $data['is_claim'] = 2;
        }
        
        if (!empty($business) && $data['logo'] == $business['Business']['logo']) {
            unset($data['logo']);
        }
        
        //validate
        $this->Business->set($data);
        $this->_validateData($this->Business);
        
        //create album if not exist
       /* if(!$isEdit)
        {
            $data['album_id'] = $this->Business->createAlbum($data['name']);
        }
        else
        {
            $business = $this->Business->getOnlyBusiness($data['id']);
            if(empty($business['Business']['album_id']))
            {
                $data['album_id'] = $this->Business->createAlbum($data['name']);
            }
        }*/
        
        //check auto approve
        if(!$isEdit && Configure::read('Business.business_auto_approve') && empty($data['claim_id']))
        {
            $data['status'] = BUSINESS_STATUS_APPROVED;
        }
            
        //save
        if($this->Business->save($data))
        {
            $business_id = $this->Business->id;
            
            if(!$isEdit)
            {
                $this->Business->applyDefaultPackage($business_id);
            }
            
            // Check claim and send email to admin if create
            if(!$isEdit && !empty($data['claim_id'])){
                $aBusiness = $this->Business->read();
                
                // send mail to admin
                $ssl_mode = Configure::read('core.ssl_mode');
                $http = (!empty($ssl_mode)) ? 'https' : 'http';

                $aUsers = $this->User->find('all', array('conditions' => array('Role.is_admin' => 1)));
                foreach ($aUsers as $aUser) {
                    $this->MooMail->send($aUser, 'business_claim_request', array(
                        'sender_title' => $cUser['moo_title'],
                        'business_title' => $aBusiness['Business']['moo_title'],
                        'business_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $aBusiness['Business']['moo_href']
                    ));
                }
                
            }
            
            //save time
            if(empty($data['always_open']) || $data['always_open'] != 1)
            {
                $this->save_time($data, $business_id);
            }
            else
            {
                $this->BusinessTime->deleteByBusiness($business_id);
            }
            
            //save payment type
            $this->save_payment_type($data, $business_id);
            
            //save category
            $this->save_business_category_item($data, $business_id);
           
            //register location for search
            $this->BusinessLocation->registerLocationKeyword(
                $data['address'], 
                $data['lat'], 
                $data['lng'], 
                !empty($addressDetail['postal_code']) ? $addressDetail['postal_code'] : null, 
                !empty($addressDetail['country']) ? $addressDetail['country'] : null, 
                !empty($addressDetail['region']) ? $addressDetail['region'] : null, 
                !empty($addressDetail['city']) ? $addressDetail['city'] : null
            );
          
            //send notification to admin
            if(!$isEdit && (!Configure::read('Business.business_auto_approve') || !empty($data['claim_id'])))
            {
                $sType = 'business_added';
                $sHref = '/admin/business';
                if(!empty($data['claim_id'])){
                    $sType = 'business_claim';
                    $sHref = '/admin/business/business_claims';
                }
                
                $aUsers = $this->User->find('all', array('conditions' => array('Role.is_admin' => 1)));
                foreach ($aUsers as $aUser) {
                    if($aUser['User']['id'] != $data['user_id'])
                    {
                        $this->Business->sendNotification(
                            $aUser['User']['id'], 
                            $data['user_id'], 
                            $sType, 
                            $sHref,
                            $data['name']
                        );
                    }
                }
            }
            
            //activity
            if(Configure::read('Business.business_auto_approve') && empty($data['claim_id']) && !$isEdit)
            {
                $this->Business->saveCreateBusinessActivity($business_id);
            }

            //return to page
            $return_url = $this->url_dashboard.'edit/'.$business_id;
            $pending = false;
            if(!Configure::read('Business.business_auto_approve') && empty($data['claim_id']) && !$isEdit)
            {
                $return_url = $this->url.'pending/'.$business_id;
                $pending = true;
            }
            $this->_jsonSuccess(__d('business', 'Successfully saved'), true, array(
                'location' => $return_url,
                'pending' => $pending,
                'claim' => !empty($data['claim_id']) ? true : false
            ));
        }
        $this->_jsonError(__d('business', 'Can not save business, please try again'));
    }
    
    private function save_time($data, $business_id)
    {
        $this->BusinessTime->deleteByBusiness($business_id);
        if(!empty($data['day']))
        {
            foreach($data['day'] as $k => $day)
            {
                $array_open = explode(' ', $data['time_open'][$k]);
                $array_close = explode(' ', $data['time_close'][$k]);
                $this->BusinessTime->create();
                $this->BusinessTime->save(array(
                    'business_id' => $business_id,
                    'day' => $day,
                    'time_open' => !empty($array_open[1]) ? $array_open[1] : "",
                    'time_close' => !empty($array_close[1]) ? $array_close[1] : "",
                    'next_day' => $array_close[0]
                ));
            }
        }
    }
    
    private function save_payment_type($data, $business_id)
    {
        $this->BusinessPaymentType->deleteByBusiness($business_id);
        if(!empty($data['business_payment_id']))
        {
            foreach($data['business_payment_id'] as $business_payment_id)
            {
                $this->BusinessPaymentType->create();
                $this->BusinessPaymentType->save(array(
                    'business_id' => $business_id,
                    'business_payment_id' => $business_payment_id,
                ));
            }
        }
    }
    
    private function save_business_category_item($data, $business_id)
    {
        $old_cat = $this->BusinessCategoryItem->getListCategoryId($business_id);
        $this->BusinessCategoryItem->deleteByBusiness($business_id);
        $exist_keys = array();
        $exist_category = array();
        if(!empty($data['category_id']))
        {
            $business = $this->Business->findById($business_id);
            $data['category_id'] = array_unique($data['category_id']);
            foreach($data['category_id'] as $k => $business_category_id)
            {
                if($business_category_id > 0)
                {
                    $exist_keys[] = $k;
                    $exist_category[] = $business_category_id;
                    $this->BusinessCategoryItem->create();
                    $this->BusinessCategoryItem->save(array(
                        'business_id' => $business_id,
                        'business_category_id' => $business_category_id,
                    ));
                }
            }
        }
        
        // add new category
        if(!empty($data['business_category']))
        {
            foreach($data['business_category'] as $k => $business_category)
            {
                if(in_array($k, $exist_keys))
                {
                    continue;
                }
                $category = $this->BusinessCategory->findByName($business_category);
                if($category != null && in_array($category['BusinessCategory']['id'], $exist_category))
                {
                    continue;
                }
                if($category != null)
                {
                    $business_category_id = $category['BusinessCategory']['id'];
                }
                else
                {
                    $this->BusinessCategory->create();
                    if($this->BusinessCategory->save(array(
                        'name' => $business_category,
                        'user_create' => 1 
                    )))
                    {
                        $business_category_id = $this->BusinessCategory->id;
                        foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                            $this->BusinessCategory->locale = $lKey;
                            $this->BusinessCategory->saveField('name', $business_category);
                        }
                    }
                }
                
                $this->BusinessCategoryItem->create();
                $this->BusinessCategoryItem->save(array(
                    'business_id' => $business_id,
                    'business_category_id' => $business_category_id,
                ));
            }
        }
    }
    
    public function pending($business_id)
    {
        $this->set('business_id', $business_id);
    }

    public function upload_photo()
    {
        $this->autoRender = false;
        $allowedExtensions = explode(',', BUSINESS_EXT_PHOTO);
        App::import('Vendor', 'qqFileUploader');
        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(BUSINESS_FILE_PATH);

        if (!empty($result['success'])) 
        {
            //resize image
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create(BUSINESS_FILE_URL.$result['filename']);
            $photo->adaptiveResize(BUSINESS_IMAGE_SEO_WIDTH, BUSINESS_IMAGE_SEO_HEIGHT)->save(BUSINESS_FILE_PATH.'/'.BUSINESS_IMAGE_SEO_WIDTH.'_'.$result['filename']);
            $photo->adaptiveResize(BUSINESS_IMAGE_SMALL_WIDTH, BUSINESS_IMAGE_SMALL_HEIGHT)->save(BUSINESS_FILE_PATH.'/'.BUSINESS_IMAGE_SMALL_WIDTH.'_'.$result['filename']);
            $photo->adaptiveResize(BUSINESS_IMAGE_THUMB_WIDTH, BUSINESS_IMAGE_THUMB_HEIGHT)->save(BUSINESS_FILE_PATH.'/'.BUSINESS_IMAGE_THUMB_WIDTH.'_'.$result['filename']);
            $result['thumb'] = $this->request->base.'/'.BUSINESS_FILE_URL.$result['filename'];
            
            $result['url'] = BUSINESS_FILE_URL;
            $result['path'] = $this->request->base.'/'.BUSINESS_FILE_URL.$result['filename'];
            $result['file'] = BUSINESS_FILE_PATH . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }
    
    public function suggest_category()
    {
        $data = $this->BusinessCategory->suggestCategory($this->request->data['keyword']);
        echo json_encode($data);
        exit;
    }
    
    public function view_map()
    {
        $address = $this->request->data['address'];
        if(empty($address))
        {
            $this->set(array(
                'empty_address' => __d('business', 'Please input address'),
            ));
        }
        else
        {
            $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
            $lnglat = $businessHelper->getLngLatByAddress($address);
            
            $this->set(array(
                'address' => $address,
                'lng' => $lnglat['lng'],
                'lat' => $lnglat['lat'],
                'direction' => isset($this->request->data['direction']) ? $this->request->data['direction'] : 0
            ));
        }
    }
    
    public function change_detail_status($business_id, $status)
    {
        $cUser = $this->_getUser();
        if(!$this->Business->isBusinessExist($business_id))
        {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }
        else if($cUser == null || !$cUser['Role']['is_admin'])
        {
            $this->_redirectError(__d('business', 'You don \'t have permission to change business status'), $this->referer());
        }
        else if($status == BUSINESS_STATUS_VERIFY || $status == BUSINESS_STATUS_UNVERIFY)
        {
            $value = 0;
            if($status == BUSINESS_STATUS_VERIFY)
            {
                $value = 1;
            }
            $this->Business->id = $business_id;
	    	if($this->Business->save(array('verify' => $value)))
            {
                $business = $this->Business->findById($business_id);
                
                //update business location counter
                $this->BusinessLocation->updateBusinessCounter($business['Business']['business_location_id']);
                
                //activity
                $this->Business->saveVerifyActivity($business_id);
                
                //notification
                $this->Business->sendNotification(
                    $business['Business']['user_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_'.$status, 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );
                if($status == BUSINESS_STATUS_VERIFY)
                {
                    $this->_redirectSuccess(__d('business', 'This business has been verified'), $this->referer());
                }
                $this->_redirectSuccess(__d('business', 'This business has been unverified'), $this->referer());
            }
        }
        else
        {
            if($this->Business->changeStatus($status, $business_id))
            {
                $business = $this->Business->findById($business_id);
                //notification
                $this->Business->sendNotification(
                    $business['Business']['user_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_'.  strtolower($status), 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );

                //update related counter
                $this->Business->updateRelatedCounter($business_id);
                
                if($status == BUSINESS_STATUS_APPROVED)
                {
                    //updare related data
                    $this->Business->relatedBusinessData($business_id, null, true);
                
                    $this->Business->enableBusinessActivity($business_id);
                }
                else
                {
                    //updare related data
                    $this->Business->relatedBusinessData($business_id);
                    
                    $this->Business->enableBusinessActivity($business_id, 0);
                }
                
                $this->_redirectSuccess(sprintf(__d('business', 'This business has been %s'), $status), $this->referer());
            }
        }
        $this->_redirectError(__d('business', 'Cannot change status, please try again'), $this->referer());
    }
    
    public function load_map()
    {
        $this->autoLayout = false;
        $this->set(array(
            'address' => urldecode($this->request->query['address']),
            'lat' => $this->request->query['lat'],
            'lng' => $this->request->query['lng'],
            'position' => !empty($this->request->query['position']) ? $this->request->query['position'] : '',
            'no_marker' => isset($this->request->query['no_marker']) ? $this->request->query['no_marker'] : 0,
            'scrollwheel' => isset($this->request->query['scrollwheel']) ? $this->request->query['scrollwheel'] : 1,
            'hide_info' => isset($this->request->query['hide_info']) ? $this->request->query['hide_info'] : 0,
            'direction' => isset($this->request->query['direction']) ? $this->request->query['direction'] : 0
        ));
    }
    
    public function reject_dialog($business_id)
    {
        $this->set(array(
            'business_id' => $business_id
        ));
        $this->render('/Elements/reject_dialog');
    }
    
    public function reject_business()
    {
        $cUser = $this->_getUser();
        $data = $this->request->data;
        $business_id = $data['business_id'];
        if(!$this->Business->isBusinessExist($business_id))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if($cUser == null || !$cUser['Role']['is_admin'])
        {
            $this->_jsonError(__d('business', 'You don \'t have permission to change business status'));
        }
        else if(empty($data['reason']))
        {
            $this->_jsonError(__d('business', 'Reason can be empty'));
        }
        else 
        {
            if($this->Business->changeStatus(BUSINESS_STATUS_REJECTED, $business_id))
            {
                $business = $this->Business->findById($business_id);
                //notification
                $this->Business->sendNotification(
                    $business['Business']['creator_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_'.strtolower(BUSINESS_STATUS_REJECTED).($business['Business']['parent_id'] > 0 ? '_subpage' : ''), 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );
                
                //updare related data
                $this->Business->relatedBusinessData($business_id);
                
                //disable business activity
                $this->Business->enableBusinessActivity($business_id, 0);
                
                //update related counter
                $this->Business->updateRelatedCounter($business_id);

                //send email
                $ssl_mode = Configure::read('core.ssl_mode');
                $http = (!empty($ssl_mode)) ? 'https' : 'http';
                $this->MooMail->send($business['User']['email'], $business['Business']['parent_id'] > 0 ? 'business_subpage_reject' : 'business_reject', array(          
                    'link' => Router::url('/', true ).$business['Business']['moo_url'],
                    'reason' => $data['reason'],
                    'link' => $http . '://' . $_SERVER['SERVER_NAME'] . $business['Business']['moo_href']
                ));

                $this->_jsonSuccess(__d('business', 'This business has been rejected'), true, array(
                    'location' => $business['Business']['moo_href']
                ));
            }
            $this->_jsonError(__d('business', 'Somwthing went wrong! Please try again'));
        }
    }
    
    public function search($param = null, $param_id = null)
    {
        $sort_by = null;
        $keyword_location = '';
        $keyword = '';
        $distance = '';
		$this->set(array(
			'title_for_layout' => ''
		));
        if(isset($this->request->query['sort_by']))
        {
            $sort_by = $this->request->query['sort_by'];
        }
        if(isset($this->request->query['distance']))
        {
            $distance = $this->request->query['distance'];
        }
        
        $pos = strpos($param, '_in_');
        if($pos !== false)
        {
            //$keyword = unLinkUrl(substr($param, 0, $pos));
            $keyword = CakeSession::read(BUSINESS_SEARCH_KEYWORD);
            $keyword_location = unLinkUrl(substr($param, $pos + 3));
            $param = array(
                'keyword' => $keyword,
                'keyword_location' => $keyword_location,
                'distance' => $distance
            );
            $this->BusinessLocation->setDefaultLocation($keyword_location, null, true);
            
            //breadcrumb
            $breadcrumb = $this->BusinessLocation->findByName($keyword_location);
            
            //load business data
            list($show_distance, $businesses) = $this->Business->getBusinessPaging($this, $param, $sort_by);
        }
        else if(substr($param, 0, 3) == 'in_')
        {
            $keyword_location = unLinkUrl(substr($param, $pos + 3));
            $param = array(
                'distance' => $distance
            );
			if($param_id > 0)
			{
				$param['location_id'] = $param_id;
			}
			else
			{
				$param['keyword_location'] = $keyword_location;
			}
            $this->BusinessLocation->setDefaultLocation($keyword_location, null, true);
            
            //breadcrumb
            $breadcrumb = $this->BusinessLocation->findByName($keyword_location);

            //load business data
            list($show_distance, $businesses) = $this->Business->getBusinessPaging($this, $param, $sort_by);
        }
        else if($param != null && $param_id != null)
        {
            $keyword = str_replace("-", " ", $param);
            $param = array(
                'category_id' => $param_id,
            );
            
            //breadcrumb
            $breadcrumb = $this->BusinessCategory->getCategories($param_id);
            
            //load business data
            list($show_distance, $businesses) = $this->Business->getBusinessPaging($this, $param, $sort_by);
        }
        else if($param != null)
        {
            //breadcrumb
            $breadcrumb = $this->BusinessCategory->getBreadCrumb($param_id);
            
            //$keyword = str_replace("_", " ", $param);
            $keyword = CakeSession::read(BUSINESS_SEARCH_KEYWORD);
            $param = array(
                'keyword' => $keyword,
            );
            list($show_distance, $businesses) = $this->Business->getBusinessPaging($this, $param, $sort_by);
        
        }
        else
        {
            $param = array(
                'keyword' => '',
            );
            list($show_distance, $businesses) = $this->Business->getBusinessPaging($this, $param, $sort_by);
            //$this->_redirectError(__d('business', 'Invalid search'), '/businesses');
        }
        
        //filter
        $filter = array(
            BUSINESS_SEARCH_BY_DISTANCE => __d('business', 'Distance'),
            BUSINESS_SEARCH_BY_RATING => __d('business', 'Rating'),
            BUSINESS_SEARCH_BY_DATE => __d('business', 'Added date')
        );
        if(!$show_distance)
        {
            unset($filter[BUSINESS_SEARCH_BY_DISTANCE]);
        }

        $all = !empty($this->request->query['all']) ? $this->request->query['all'] : false;
        $this->set(array(
            'breadcrumb' => !empty($breadcrumb) ? $breadcrumb : '',
            'current_link' => $this->request->base.$this->request->here(false),
            'businesses' => $businesses,
            'filter' => $filter,
            'locations' => $this->Business->getMultiLocations($businesses),
            'keyword' => $keyword,
            'keyword_location' => $keyword_location,
            'sort_by' => $sort_by,
            'all' => $all
        ));
        
        $sSeoDescription = __d('business', 'Business Search results %s %s', !empty($keyword) ? __d('business', 'for ') . $keyword : '', !empty($keyword_location) && !$all ? __d('business', 'in ') . $keyword_location : '');
        $this->set('title_for_layout', $sSeoDescription);
        $this->set('title_for_property', $sSeoDescription);
        $this->set('description_for_layout', $sSeoDescription);
        $this->set('description_for_property', $sSeoDescription);
    }
    
    public function search_data()
    {
        $action_name = $this->request->params['action'];
        $viewer = MooCore::getInstance()->getViewer();
        $params = array();
        if (!empty($controller->request->query)) {
            $params = $controller->request->query;
        }

        $search_keyword_location = CakeSession::check(BUSINESS_SEARCH_KEYWORD_LOCATION) ? CakeSession::read(BUSINESS_SEARCH_KEYWORD_LOCATION) : CakeSession::read(BUSINESS_DEFAULT_LOCATION_NAME);
        return array(
            'viewer' => $viewer,
            'params' => $params,
            'action_name' => $action_name,
            'search_keyword_location' => $search_keyword_location,
        );
    }
    
    public function suggest_global_location()
    {
        $keywrod = $this->request->data['keyword'];
        $no_address = !empty($this->request->data['no_address']) ? $this->request->data['no_address'] : '';
        $data = $this->BusinessLocation->suggestGlobalLocation($keywrod, 1);
        echo json_encode($data);
        exit;
    }
    
    public function suggest_global_category()
    {
        $data = array();
        $page = isset($this->request->data['page']) ? $this->request->data['page'] : 1;
        $data_category = $page == 1 ? $this->BusinessCategory->suggestCategory($this->request->data['keyword'], true) : array();
        $data_business = $this->Business->suggestBusiness($this->request->data['keyword'], $page);

        if($data_business != null && $data_category != null)
        {
            $data = array_merge($data_category, $data_business);
        }
        else if($data_business != null)
        {
            $data = $data_business;
        }
        else if($data_category != null)
        {
            $data = $data_category;
        }
        echo json_encode($data);
        exit;
    }
    
    public function global_search()
    {
        $data = $this->request->data;
        /*if(empty($data['keyword']) && empty($data['keyword_location']))
        {
            $this->_jsonError(__d('business', 'Please input keyword'));
        }
        else if(!empty($data['keyword']) && strlen($data['keyword']) < 3)
        {
            $this->_jsonError(__d('business', 'Keyword length must be greater than 2'));
        }*/
        /*else if(!empty($data['distance']) && $data['keyword_location'] == null)
        {
            $this->_jsonError(__d('business', 'Please input location'));
        }*/
        /*else
        {*/
            CakeSession::write(BUSINESS_SEARCH_KEYWORD, htmlentities($data['keyword']));
            CakeSession::write(BUSINESS_SEARCH_KEYWORD_LOCATION, htmlentities($data['keyword_location']));
            $link = $this->request->base.'/business_search/';
            if(!empty($data['keyword']) && !empty($data['keyword_location']))
            {
                $link = $this->request->base.'/business_search/'.linkUrl($data['keyword']).'_in_'.linkUrl($data['keyword_location']);
            }
            else if(!empty($data['keyword']))
            {
                $link = $this->request->base.'/business_search/'.linkUrl($data['keyword']);
            }
            else if(!empty($data['keyword_location']))
            {
                $link = $this->request->base.'/business_search/in_'.linkUrl($data['keyword_location']);
            }
            $param = array();
            if(!empty($data['cat_name']))
            {
                $param[] = 'cat_name=1';
            }
            if(!empty($data['listing']))
            {
                $param[] = 'listing=1';
            }
            if(!empty($data['distance']))
            {
                $param[] = 'distance='.$data['distance'];
            }
            if($param != null)
            {
                $link .= '?'.implode('&', $param);
            }
            
            //remember cat keyword
            if(!empty($data['remember']))
            {
                //$this->BusinessCategory->rememberCatKeyword($data['keyword_location'], $data['keyword']);
            }
            
            $this->BusinessLocation->setDefaultLocation($data['keyword_location'], null, true);
            $this->_jsonSuccess(__d('business', 'success'), false, array(
                'location' => $link
            ));
        //}
    }
    
    public function delete($business_id)
    {
        $cuser = $this->_getUser();
        if(empty($cuser) || (!$cuser['Role']['is_admin'] && !$this->Business->isBusinessOwner($business_id)))
        {
            $this->_redirectError(__d('business', 'You don\'t have permission to delete this business or business not found'), '/pages/error');
        }
        else
        {
            if($this->Business->deleteBusiness($business_id))
            {
                $this->_redirectSuccess(__d('business', 'Business has been deleted'), '/businesses/my');
            }
            $this->_redirectError(__d('business', 'Can not delete business, please try again'), '/businesses/my');
        }
    }
    
    public function submit_for_reviewing($business_id)
    {
        $cuser = $this->_getUser();
        if(empty($cuser) || (!$cuser['Role']['is_admin'] && !$this->Business->isBusinessOwner($business_id)))
        {
            $this->_redirectError(__d('business', 'Business not found or you don\'t have permission'), '/pages/error');
        }
        else
        {
            $business = $this->Business->findById($business_id);
            
            //notification
            if($business != null)
            {
                $this->Business->updateAll(array(
                    'Business.status' => "'".BUSINESS_STATUS_PENDING."'"
                ), array(
                    'Business.id' => $business_id
                ));
                
                $admin = $this->Business->getAdminUser();
                
                $this->Business->sendNotification(
                    $admin['User']['id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_submit_for_reviewing', 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );
                $this->_redirectSuccess(__d('business', 'Sucessfully submited'), '/businesses/dashboard/edit/'.$business_id);
            }
            $this->_redirectError(__d('business', 'Can not submit, please try again.'), '/businesses/dashboard/edit/'.$business_id);
        }
    }
    
    public function send_contact()
    {
        $data = $this->request->data;
        if(!$this->Business->isBusinessExist($data['business_id']))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if(empty($data['name']))
        {
            $this->_jsonError(__d('business', 'Name can not be empty'));
        }
        else if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {
            $this->_jsonError(__d('business', 'Invalid email'));
        }
        else if(empty($data['message']))
        {
            $this->_jsonError(__d('business', 'Message can not be empty'));
        }
        else
        {
            // check captcha
            if(Configure::read('core.recaptcha') && Configure::read('core.recaptcha_privatekey') != null)
            {
                $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
                App::import('Vendor', 'recaptchalib');
                $reCaptcha = new ReCaptcha($recaptcha_privatekey);
                $resp = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
                );
                if ($resp != null && !$resp->success) 
                {
                    $this->_jsonError(__d('business', 'Invalid security code'));
                }
            }
        
            //send contact
            $business = $this->Business->getOnlyBusiness($data['business_id']);
            if($business != null)
            {
                $business = $business['Business'];
                $mail_from = Configure::read('core.site_email');
                $mail_name = Configure::read('core.site_name');
                Configure::write('core.site_email', $data['email']);
                Configure::write('core.site_name', $data['name']);
                Configure::write('Mail.mail_name', $data['name']);
                Configure::write('Mail.mail_from', $data['email']);
                $mooMailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
                $mooMailComponent->send($business['email'], 'business_contact', array(
                    'name' => $data['name'],
                    'business_name' => $business['name'],
                    'business_link' => Router::url('/', true ).$business['moo_url'],
                    'message' => $data['message'],
                    'sender_email' => $data['email'],
                ));
                Configure::write('core.site_email', $mail_from);
                Configure::write('core.site_name', $mail_name);
                Configure::write('Mail.mail_from', $mail_from);
                Configure::write('Mail.mail_name', $mail_name);
                $this->_jsonSuccess(__d('business', 'Your message has been sent'));
            }
            $this->_jsonError(__d('business', 'Can not send message, please try again.'));
        }
    }
    
    public function advanced_search_dialog()
    {
        $miles = Configure::read('Business.business_advanced_search_distance');
        $miles = !empty($miles) ? explode(',', $miles) : null;
        
        $this->set(array(
            'miles' => $miles
        ));
        $this->render('Business.Elements/advanced_search_dialog');
    }
    
    public function claim_search_dialog()
    {
        $this->render('Business.Elements/claim_search_dialog');
    }
    
    public function suggest_claim_business()
    {
        $data_business = $this->Business->suggestBusiness($this->request->data['keyword']);
        echo json_encode($data_business);
        exit;
    }
    
    public function landing_create_business()
    {
        $current_location = $this->BusinessLocation->getCurrentLocationMap();
        $weather = $this->BusinessLocation->getLocationWeather($current_location['address']);
        return array(
            'weather' => $weather
        );
    }
    
    public function add_favourite($business_id = null)
    {
        $this->autoRender = false;
        $cuser = $this->_getUser();
        if (!$cuser) {
            $this->_jsonError(__d('business', 'Please login to continue'));
        }
        
        $uid = MooCore::getInstance()->getViewer(true);
        $business_id = !empty($this->request->data['id']) ? $this->request->data['id'] : $business_id;
        $package = $this->Business->getBusinessPackage($business_id);
        if(!$package['favourite'])
        {
            $business = $this->Business->getOnlyBusiness($business_id);
            $this->_jsonError($this->Business->upgradeMessage($business));
        }
        if(!$this->Business->isFavourited($uid, $business_id))
        {
            if($this->Business->addFavourite($uid, $business_id))
            {
                $this->_jsonSuccess(__d('business', 'Successfully added'), false, array(
                    'text' => __d('business', 'Remove from favorite'),
                    'icon' => '<i class="material-icons unfavourite">favorite</i>'
                ));
            }
            $this->_jsonError(__d('business', 'Can not add to favorite, please try again!'));
        }
        else
        {
            if($this->Business->removeFavourite($uid, $business_id))
            {
                $this->_jsonSuccess(__d('business', 'Successfully removed'), false, array(
                    'text' => __d('business', 'Add to favorite'),
                    'icon' => '<i class="material-icons favourite">favorite</i>'
                ));
            }
            $this->_jsonError(__d('business', 'Can not remove from favorite, please try again!'));
        }
    }
    
    public function favourites()
    {
        $businesses = $this->Business->loadMyFavourites();

        $this->set(array(
            'businesses' => $businesses,
            'user_id' => MooCore::getInstance()->getViewer(true),
            'more_url' => '/business/myfavourites/page:2'
        ));
    }
    
    public function checkin_dialog($business_id)
    {
        if(!$this->isLoggedIn())
        {
            $this->_jsonError(__d('business', 'Login or register to continue'), false, array('require_login' => 1));
        }
        else if(!$this->Business->isBusinessExist($business_id))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        
        $business = $this->Business->findById($business_id);
        $this->set(array(
            'business_id' => $business_id,
            'business' => $business
        ));
        $this->render('Business.Elements/checkin_dialog');
    }
    
    public function checkin_list_dialog($business_id)
    {
        $users = $this->BusinessCheckin->getPeopleCheckin($business_id);
        
        $this->set(array(
            'users' => $users,
            'more_url' => '/business/ajax_checkin_list/'.$business_id.'/page:2'
        ));
        $this->render('Business.Elements/checkin_list_dialog');
    }
    
    public function ajax_checkin_list($business_id)
    {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $users = $this->BusinessCheckin->getPeopleCheckin($business_id, $page);
        
        $this->set(array(
            'users' => $users,
            'more_url' => '/business/ajax_checkin_list/'.$business_id.'/page:'.($page + 1)
        ));
        $this->render('/Elements/lists/checkin_list');
    }

    ///////////////////////////////////dashboard///////////////////////////////////
    public function dashboard($task = 'edit', $business_id, $item_id = null)
    {
		$this->set(array(
			'title_for_layout' => ''
		));
        if(!$this->isLoggedIn())
        {
            $this->_redirectError(__d('business', 'Please login to continue'), '/users/member_login');
        }
        $business = $this->Business->getBusiness($business_id);
        if(empty($business))
        {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }
        
        if(!$this->Business->isBusinessExist($business_id, null, 0) && !$this->BusinessAdmin->isBusinessAdmin($business_id, MooCore::getInstance()->getViewer(true)))
        {
            $error_message = __d('business', 'Business not found');
            $this->_redirectError($error_message, '/pages/error');
        }
        $package = $this->Business->getBusinessPackage($business_id);

        //permission
        $permission_can_manage_photos = $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_PHOTO, $business['Business']['moo_permissions']);
        $permission_can_manage_subpages = $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_SUBPAGE, $business['Business']['moo_permissions']);
        $permission_can_upgrade_page = $this->Business->permission($business_id, BUSINESS_PERMISSION_UPGRADE_PAGE, $business['Business']['moo_permissions']);
        $permission_can_featured_page = $this->Business->permission($business_id, BUSINESS_PERMISSION_FEATURE_PAGE, $business['Business']['moo_permissions']);
        $permission_can_send_verification_request = $this->Business->permission($business_id, BUSINESS_PERMISSION_SEND_VERIFICATION_REQUEST, $business['Business']['moo_permissions']);
        $permission_can_manage_admins = $this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_ADMIN, $business['Business']['moo_permissions']);
        $permission_can_edit_page = $this->Business->permission($business_id, BUSINESS_PERMISSION_EDIT_PAGE, $business['Business']['moo_permissions']);
        
        //show meesage for unapprove business
        if(($business['Business']['status'] != BUSINESS_STATUS_APPROVED || $business['Business']['claim_id'] != 0) && $task != 'edit')
        {
            $element = 'dashboard/pending_business';
        }
        else
        {
            switch($task)
            {
                case 'edit':
                    $cuser = $this->_getUser();
                    if(!$this->Business->isBusinessOwner($business_id) && !$cuser['Role']['is_admin'] && !$this->BusinessAdmin->isBusinessAdmin($business_id))
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }
                    $this->dashboard_edit($business_id);
                    $element = 'edit_business';
                    break;
                case 'business_photos':
                    if(!$permission_can_manage_photos)
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }
                    $this->dashboard_photos($business_id);
                    $element = 'dashboard/business_photos';
                    break;
                case 'branches':
                    if(!$permission_can_manage_subpages)
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }
                    $this->dashboard_branches($business_id, $item_id);
                    $element = 'dashboard/branches';
                    break;
                case 'create_branch':
                    if(!$permission_can_manage_subpages)
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }
                    $this->_checkPermission(array('confirm' => true));
                    $this->dashboard_create_branch($business_id, $item_id);
                    $element = 'dashboard/create_branch';
                    break;
                case 'admins':
                    if(!$package['manage_admin'])
                    {
                        $this->_redirectError($this->Business->upgradeMessage($business), '/businesses/dashboard/edit/'.$business_id);
                    }
                    if(!$permission_can_manage_admins)
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }

                    $this->dashboard_admins($business_id);
                    $element = 'dashboard/admins';
                    break;
                case 'permissions':
                    $cuser = $this->_getUser();
                    if(!$package['manage_admin'])
                    {
                        $this->_redirectError($this->Business->upgradeMessage($business), '/businesses/dashboard/edit/'.$business_id);
                    }
                    if(!$this->Business->isBusinessOwner($business_id) && !$cuser['Role']['is_admin'])
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }

                    $this->dashboard_permissions($business_id, $package);
                    $element = 'dashboard/permissions';
                    break;
                case 'feature':
                    if(!$permission_can_featured_page)
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }
                    $this->dashboard_feature($business_id);
                    $element = 'dashboard/business_feature';
                    break;
                case 'upgrade':
                    if(!$permission_can_upgrade_page)
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }
                    $this->dashboard_upgrade($business_id);
                    $element = 'dashboard/business_upgrade';
                    break;
                case 'verify':
                    if(!$package['send_verification_request'])
                    {
                        $this->_redirectError($this->Business->upgradeMessage($business), '/businesses/dashboard/edit/'.$business_id);
                    }
                    if(!$permission_can_send_verification_request)
                    {
                        $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
                    }
                    $this->dashboard_verify($business_id);
                    $element = 'dashboard/business_verify';
                    break;
                default :
                    $this->_redirectError(__d('business', 'Page not found'), '/pages/error');
            }
        }
        
        $this->set(array(
            'active_dashboard' => $this->request->params['pass'][0],
            'bBusinessAdmin' => (bool) $this->BusinessAdmin->isBusinessAdmin($business_id),
            'business_id' => $business_id,
            'business' => $business,
            'element' => $element,
            'permission_can_manage_photos' => $permission_can_manage_photos,
            'permission_can_manage_subpages' => $permission_can_manage_subpages,
            'permission_can_upgrade_page' => $permission_can_upgrade_page,
            'permission_can_featured_page' => $permission_can_featured_page,
            'permission_can_send_verification_request' => $permission_can_send_verification_request,
            'permission_can_manage_admins' => $permission_can_manage_admins,
            'permission_can_edit_page' => $permission_can_edit_page
        ));
    }
    
    private function dashboard_verify($business_id)
    {
        $business = $this->Business->getBusiness($business_id);
        $this->set(array(
            'business' => $business,
            'title_for_layout' => __d('business', 'Send Verification')
        ));
    }
    
    private function dashboard_edit($business_id)
    {
        $business = $this->Business->getBusiness($business_id);
        
        //type
        $businessTypes = $this->BusinessType->getBusinessTypeList();
        
        //payment
        $businessPayments = $this->BusinessPayment->getBusinessPayment();

        //list days in week
        $days = $this->Business->getListDayInWeek();

        //time business
        $times_open = $this->Business->getListTimeOpen();
        $times_close = $this->Business->getListTimeClose();
        
        $this->set(array(
            'business' => $business,
            'businessTypes' => $businessTypes,
            'businessPayments' => $businessPayments,
            'days' => $days,
            'times_open' => $times_open,
            'times_close' => $times_close,
            'title_for_layout' => __d('business', 'Edit Business')
        ));
    }
    
    private function dashboard_upgrade($business_id)
    {
        $business = $this->Business->findById($business_id);
        $this->_checkExistence($business);
        $businessHelper  = MooCore::getInstance()->getHelper('Business_Business');
        if(!$businessHelper->canUpgradePackage($business)) {
            $this->Session->setFlash(__d('business', 'Business can not upgrade package'), 'default', array('class' => 'error-message'));
            return $this->redirect('/pages/no-permission');
        }
        $packages = $this->BusinessPackage->getUpgradePackages();
        $this->set('packages', $packages);
        $this->set('business', $business);
        $currency = Configure::read('Config.currency');
        $this->set('currency', $currency);
    }
    
    private function dashboard_feature($business_id)
    {
        $business = $this->Business->findById($business_id);
        $this->_checkExistence($business);
        $businessHelper  = MooCore::getInstance()->getHelper('Business_Business');
        if(!$businessHelper->canFeaturedBusiness($business)) {
            $this->Session->setFlash(__d('business', 'Business can not set feature'), 'default', array('class' => 'error-message'));
            return $this->redirect('/pages/no-permission');
        }
        $expired_time = $businessHelper->getExpiredTime($business_id);
        $featured_price = Configure::read('Business.featured_price') ;
        $currency = Configure::read('Config.currency');
        $this->set('business', $business);
        $this->set('expired_time', $expired_time);
        $this->set('featured_price', $featured_price);
        $this->set('currency', $currency);
    }
    
    private function dashboard_photos($id = null)
    {
        $business = $this->Business->getBusinessPackage($id);
        $photos = $this->BusinessPhoto->getBusinessAlbumPhotos($id, true);
        $this->set(array(
            'business' => $business,
            'photos' => $photos,
            'title_for_layout' => __d('business', 'Manage Photos')
        ));
    }

    private function dashboard_branches($business_id)
    {
        $branches = $this->Business->loadBusinessBranches($this, $business_id);

        $this->set(array(
            'business_id' => $business_id,
            'branches' => $branches,
            'can_create_branch' => $this->Business->isAllowModule($business_id, BUSINESS_MODULE_BRANCH),
            'title_for_layout' => __d('business', 'Manage branches')
        ));
    }

    private function dashboard_create_branch($business_id, $branch_id = null)
    {
        if($this->Business->isBusinessExist($branch_id, null, $business_id))
        {
            $branch = $this->Business->getBusiness($branch_id, null, $business_id);
        }
        else
        {
            $branch = $this->Business->initFields();
        }

        //payment
        $branchPayments = $this->BusinessPayment->getBusinessPayment();

        //list days in week
        $days = $this->Business->getListDayInWeek();

        //time business
        $times_open = $this->Business->getListTimeOpen();
        $times_close = $this->Business->getListTimeClose();
        
        //photos
        $branch_photos = $this->BusinessPhoto->getBusinessAlbumPhotos($branch_id, true);
        
        //type
        $businessTypes = $this->BusinessType->getBusinessTypeList();
        
        $this->set(array(
            'business_id' => $business_id,
            'branch' => $branch,
            'branchPayments' => $branchPayments,
            'days' => $days,
            'times_open' => $times_open,
            'times_close' => $times_close,
            'branch_photos' => $branch_photos,
            'businessTypes' => $businessTypes,
        ));
    }
    
    private function dashboard_admins($business_id)
    {
        $this->set(array(
            'business_id' => $business_id,
            'title_for_layout' => __d('business', 'Manage Admins')
        ));
    }
    
    private function dashboard_permissions($business_id, $package)
    {
        $this->set(array(
            'permissions' => $this->Business->getBusinessPermission($package),
            'title_for_layout' => __d('business', 'Permissions Manager')
        ));
    }
    
    public function view_parking()
    {
        $address = $this->request->data['address'];
        if(empty($address))
        {
            exit();
        }
      
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $address_detail = $businessHelper->getAddressDetail(null , null, $address);
        
        $address = strtolower($address_detail['address']);
            
        $address = str_replace(' ','_',$address);
       
        $this->set(array(
            'address' => $address,
        ));

    }

    public function change_cover($business_id)
    {
        $cUser = $this->_getUser();
        if(($cUser != null && $cUser['Role']['is_admin']) || 
            $this->Business->isBusinessOwner($business_id) ||
            $this->BusinessAdmin->isBusinessAdmin($business_id, MooCore::getInstance()->getViewer(true)))
        {
            
            $business = $this->Business->getOnlyBusiness($business_id);
                                    
            $this->set('business_id', $business_id);
            $this->set('business', $business);
        } else {
            $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
        }
    }		
}