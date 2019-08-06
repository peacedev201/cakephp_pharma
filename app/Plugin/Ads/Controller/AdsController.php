<?php

class AdsController extends AdsAppController {

    public $components = array('Paginator', 'Ads.Paypal');
    public $check_force_login = false;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->url = $this->request->base . '/ads/';
        $this->admin_url = $this->request->base . '/admin/ads/';
        $this->set('url', $this->url);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Ads.AdsCampaign');
        $this->loadModel('Ads.AdsPlacement');
        $this->loadModel('Ads.AdsTransaction');
        $this->loadModel('Ads.AdsReport');
    }

    public function beforeRender() {

        $user = AuthComponent::user();
        $is_force_login = Configure::read('core.force_login');
        if (empty($user) && $is_force_login) {
            $this->redirect('/users/member_login');
            exit;
        }
    }

    ////////////////////////////////////////////backend////////////////////////////////////////////
    public function admin_index() {
        $placement = isset($this->request->query['placement']) ? $this->request->query['placement'] : null;
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $search_status = isset($this->request->query['search_status']) ? $this->request->query['search_status'] : null;
        $ads_campaign = $this->AdsCampaign->loadAdsCampaignPaging($this, $keyword, $search_status, $placement);
        $ads_campaign = $this->AdsCampaign->parseAdsData($ads_campaign);
        $this->set(array(
            'ads_campaigns' => $ads_campaign,
            'keyword' => $keyword,
            'search_status' => $search_status,
            'title_for_layout' => __d('ads', 'Ad Campaigns')
        ));
    }

    public function admin_create($id = null) {
        if (!empty($id)) {
            $ads_campaign = $this->AdsCampaign->getAdsCampaignDetail($id);
        } else {
            $ads_campaign = $this->AdsCampaign->initFields();
        }
        //list role
        $roles = $this->AdsCampaign->getAllRole();

        //gender
        $gender = array('male' => __d('ads', 'Male'), 'famale' => __d('ads', 'Female'));

        //ads placement
        $ads_placement = $this->AdsPlacement->getAdsPlacementList(true);
        //hour
        $hour = array();
        for ($i = 0; $i <= 23; $i++) {
            $hour[$i] = $i;
        }

        //minute
        $minute = array();
        for ($i = 0; $i <= 59; $i++) {
            $minute[$i] = $i;
        }

        $this->set(array(
            'ads_campaign' => $ads_campaign['AdsCampaign'],
            'roles' => $roles,
            'gender' => $gender,
            'ads_placement' => $ads_placement,
            'hour' => $hour,
            'minute' => $minute,
            'title_for_layout' => __d('ads', 'Ad Campaigns')
        ));
    }

    public function admin_save() {
        $data = $this->request->data;
        $isEdit = false;
        if ((int) $data['id'] > 0 && $this->AdsCampaign->checkAdsCampaignExist($data['id'])) {
            $isEdit = true;
            $this->AdsCampaign->id = $data['id'];
        } else {
            // get id user who create campaign, if user not exist create new account
            $data['user_id'] = $this->autoCreateAccount($data);
        }
        //$data['role_id'] = (empty($data['everyone'])) ? implode(',', $_POST['permissions']) : '';
        $this->AdsCampaign->set($data);
        $this->_validateData($this->AdsCampaign);

        if ((int) $data['id'] < 1 && $this->AdsPlacement->checkPlacementReachLimit($data['ads_placement_id'])) {
            $this->_jsonError(__d('ads', 'Ads Placement has reached to limit, you cannot create new campaign.'));
        } else if ($this->AdsCampaign->save($data)) {

            if (!$isEdit) {
                // set payment
                $this->setPayment($this->AdsCampaign->id);
                //send email
                /* $this->MooMail->send(Configure::read('core.site_email'), 'user_create_ad', array(          
                  'review_url' => Router::url('/', true ).'admin/ads/?search_status=&keyword='.$data['name'],
                  'user_note' => $data['note']
                  )); */
                $this->MooMail->send($data['email'], 'user_create_ad_user', array(
                    'site_name' => Configure::read('core.site_name'),
                ));
                $this->_jsonSuccess(sprintf(__d('ads', 'Thanks for advertising on %s ! We\'re reviewing your ad and will get back to you soon.'), Configure::read('core.site_name')), true, array(
                    'location' => $this->admin_url
                ));
            } else {
                $this->_jsonSuccess(sprintf(__d('ads', 'Successfully saved.'), Configure::read('core.site_name')), true, array(
                    'location' => $this->admin_url
                ));
            }
        } else {
            $this->_jsonError(__d('ads', 'Something went wrong, please try again'));
        }
    }

    public function admin_action($action, $id) {

        if (!$this->AdsCampaign->checkAdsCampaignExist($id)) {
            $this->_redirectError(__d('ads', 'Campaign not found'), '/admin/ads/');
        } else if (!$this->AdsCampaign->checkAdsCampaignAllowAction($id, $action)) {
            $this->_redirectError(__d('ads', 'Invalid action'), '/admin/ads/');
        } else {
            switch ($action) {
                case 'active':
                    if ($this->AdsCampaign->updateAdsCampaignStatus($id, $action)) {
                        $this->AdsCampaign->activeAdsCampaign($id, true);
                        $this->_redirectSuccess(__d('ads', 'Successfully updated'), '/admin/ads/');
                    }
                    break;
                case 'disable':
                    if ($this->AdsCampaign->updateAdsCampaignStatus($id, $action)) {
                        $this->_redirectSuccess(__d('ads', 'Successfully updated'), '/admin/ads/');
                    }
                    break;
                case 'delete':
                    if ($this->AdsCampaign->deleteAdsCampaignStatus($id)) {
                        $this->_redirectSuccess(__d('ads', 'Successfully deleted'), '/admin/ads/');
                    }
                    break;
                case 'payment':
                    $ads_campaign = $this->AdsCampaign->getAdsCampaignDetail($id);
                    $ads_placement = $ads_campaign['AdsPlacement'];
                    $ads_campaign = $ads_campaign['AdsCampaign'];
                    $verification_code = $this->AdsTransaction->createVerificationCode($id);
                    $data = array(
                        'ads_campaign_id' => $ads_campaign['id'],
                        'ads_placement_id' => $ads_placement['id'],
                        'ads_campaign_name' => $ads_campaign['name'],
                        'ads_placement_name' => $ads_placement['name'],
                        'email' => $ads_campaign['email'],
                        'price' => $ads_placement['price'],
                        'currency' => Configure::read('Ads.currency_code'),
                        'currency_symbol' => Configure::read('Ads.currency_symbol'),
                        'status' => ADS_TRANSACTION_PENDING,
                        'verification_code' => $verification_code
                    );
                    if ($this->AdsTransaction->save($data)) {
                        $this->AdsTransaction->clearOldTransactionVerification($ads_campaign['id'], $this->AdsTransaction->id);

                        //send email
                        $this->MooMail->send($ads_campaign['email'], 'send_payment_request', array(
                             'payment_url' => Router::url('/', true) . 'ads/ads_gateways/' . $ads_campaign['id']
                        ));

                        $this->_redirectSuccess(__d('ads', 'Successfully sent payment request'), '/admin/ads/');
                    }
                    break;
            }
            $this->_redirectError(__d('ads', 'This action does not exist'), '/admin/ads/');
        }
    }

    public function admin_delete() {
        if (!empty($this->request->data['cid'])) {
            foreach ($this->request->data['cid'] as $id) {
                if ($this->AdsCampaign->checkAdsCampaignExist($id) &&
                        $this->AdsCampaign->checkAdsCampaignAllowAction($id, 'delete')) {
                    $this->AdsCampaign->deleteAdsCampaignStatus($id);
                }
            }
        }
        $this->_redirectSuccess(__d('ads', 'Successfully deleted'), '/admin/ads/');
    }

    ////////////////////////////////////////////frontend////////////////////////////////////////////
    public function create($ad_placement_id = null) {
        $viewer = MooCore::getInstance()->getViewer();
        $can_add_ad = $this->AdsCampaign->adsCheckUserRoles(ROLE_ADS_CAN_ADD_ADS);
		$is_admin = false;
		if(!empty($viewer) && $viewer['Role']['is_admin']){
			$is_admin  = true;
		}
         if(!$can_add_ad){
            $this->redirect('/pages/no-permission');exit;
        }

        //load ads_campaign
        $ads_campaign = $this->AdsCampaign->initFields();
        //list role
        $roles = $this->AdsCampaign->getAllRole();
//        $roles = $this->AdsCampaign->getListRole();
        //gender
        $gender = array('male' => __d('ads', 'Male'), 'famale' => __d('ads', 'Female'));

        //ads placement
        $ads_placement = $this->AdsPlacement->getAdsPlacementList();

        //hour
        $hour = array();
        for ($i = 0; $i <= 23; $i++) {
            $hour[$i] = $i;
        }

        //minute
        $minute = array();
        for ($i = 0; $i <= 59; $i++) {
            $minute[$i] = $i;
        }

        //logged in user
        $user = $this->_getUser();

        $this->set(array(
            'ads_campaign' => $ads_campaign['AdsCampaign'],
            'ad_placement_id' => $ad_placement_id,
            'roles' => $roles,
            'gender' => $gender,
            'ads_placement' => $ads_placement,
            'hour' => $hour,
            'minute' => $minute,
            'user' => $user
        ));
    }

    public function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function autoCreateAccount($data) {
        $userInfo = AuthComponent::user();
        if (empty($userInfo)) {
            $this->loadModel('User');
            $emailExist = $this->AdsCampaign->checkUserExistByEmail($data['email']);
            if ($emailExist) {
                $this->_jsonError(__d('ads', 'Email already exist'));
            } else {
                // create account
                $password = $this->randomPassword();
                $newUser = $this->AdsCampaign->createMooAccount($data['email'], $data['client_name'], $password);
                if ($newUser) {
                   
                    // auto addfriend with account setting in administrator
                    $this->loadModel('Friend');
                    $auto_add_friend = Configure::read('core.auto_add_friend');
                    if (!empty($auto_add_friend)) {
                        $list_friend = explode(',', $auto_add_friend);
                        $this->Friend->autoFriends($newUser['User']['id'], $list_friend);
                    }
                     // sending mail
                    $this->MooMail->send($data['email'], 'user_ads_create_account', array(
                        'site_name' => Configure::read('core.site_name'),
                        'email' => $data['email'],
                        'pass' => $password
                    ));
                    return $newUser['User']['id'];
                }
            }
        } else {
            return $userInfo['id'];
        }
    }

    public function save() {
        $data = $this->request->data;
        //$permissions = !empty($data['permissions']) ? implode(',', $_POST['permissions']) : '';
        //$data['role_id'] = (empty($data['everyone'])) ? $permissions : '';
        $this->AdsCampaign->set($data);
        $this->_validateData($this->AdsCampaign);
        $role_can_add_ads  = $this->AdsCampaign->adsCheckUserRoles(ROLE_ADS_CAN_ADD_ADS);
        $role_hide_all_ads  = $this->AdsCampaign->adsCheckUserRoles(ROLE_ADS_HIDE_ALL_ADS);
        if(!$role_can_add_ads){
             $this->_jsonError(__d('ads', 'Access Denied'));
        }
        // get id user who create campaign, if user not exist create new account
        $data['user_id'] = $this->autoCreateAccount($data);

        if ($this->AdsPlacement->checkPlacementReachLimit($data['ads_placement_id'])) {
            $this->_jsonError(__d('ads', 'Ads Placement has reached to limit, you cannot create new campaign.'));
        }
        /* else if(empty($data['everyone']) && empty($permissions))
          {
          $this->_jsonError(__d('ads', 'You must select at least one user group.'));
          } */ else if ($this->AdsCampaign->save($data)) {

            //send email
            $this->MooMail->send(Configure::read('core.site_email'), 'user_create_ad', array(
                'review_url' => Router::url('/', true) . 'admin/ads/?search_status=&keyword=' . $data['name'],
                'user_note' => $data['note']
            ));
            // create payment payment
            $this->setPayment($this->AdsCampaign->id);

            $this->MooMail->send($data['email'], 'user_create_ad_user', array(
                'site_name' => Configure::read('core.site_name'),
            ));
            $this->_jsonSuccess(sprintf(__d('ads', 'Thanks for advertising on %s ! We\'re reviewing your ad and will get back to you soon.'), Configure::read('core.site_name')), true, array(
                'location' => $this->url . 'create'
            ));
        } else {
            $this->_jsonError(__d('ads', 'Something went wrong, please try again'));
        }
    }

    public function upload_banner($max_width = null, $max_height = null) {
        $this->autoRender = false;
        // save this picture to album
        $path = 'uploads' . DS . 'commercial';
        $url = 'uploads/commercial/';

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        //check demension
        if ($max_width != null && $max_height != null) {
            $image_info = getimagesize($_FILES["qqfile"]["tmp_name"]);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            if ($image_width != $max_width || $image_height != $max_height) {
                $result['success'] = 0;
                $result['message'] = sprintf(__d('addonsstore', 'Banner dimension must be %sx%s pixel!'), $max_width, $max_height);
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
                exit;
            }
        }

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            $result['url'] = $url;
            $result['path'] = $this->request->base . '/uploads/commercial/' . $result['filename'];
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    public function transaction($verification_code) {
        $verification_code = trim($verification_code);
        if (empty($verification_code) || !$this->AdsTransaction->checkVerificationCodeExist($verification_code)) {
            $this->_redirectError(__d('ads', 'Verification code does not exist'), '/');
        } else if (!$this->AdsTransaction->checkPaypalConfig()) {
            $this->_redirectError(__d('ads', 'No gateway config, please contact with admin for more info.'), '/');
        } else {
            $ads_transaction = $this->AdsTransaction->getTransactionByVerificationCode($verification_code);
            $ads_placement = $ads_transaction['AdsPlacement'];
            $ads_campaign = $ads_transaction['AdsCampaign'];
            $ads_transaction = $ads_transaction['AdsTransaction'];
            
            $item_name = 'Ad ' . $ads_campaign['id'];
            $this->Paypal->setConfig();
            $params = array(	
                'item_name' => $item_name,
                'item_number' => $ads_transaction['id'],
                'amount' => $ads_placement['price'],
                'currency_code' => Configure::read('Ads.currency_code'),
                'notify_url' => Router::url('/', true ).'ads/notify_transaction',
                'return' => Router::url('/', true ).'ads/success_transaction',
                'cancel_return' => Router::url('/', true ).'ads/cancel_transaction',
                'custom' => $item_name,
            ); 
            $url = $this->Paypal->getUrl($params);
            $this->redirect($url);
        }
    }

    public function notify_transaction() {
        $this->AdsTransaction->verifyTransaction();
    }

    public function cancel_transaction() {
        $this->_redirectSuccess(__d('ads', 'You have just canceled transaction'), '/');
    }

    public function success_transaction() {
        $this->_redirectSuccess(__d('ads', 'Thank you for your payment, your ad is on process'), '/');
    }

    public function report($code, $from_date = null, $to_date = null) {
        $ads_campaign_id = $this->AdsCampaign->decodeAdCampaignId($code);
        if (!$this->AdsCampaign->checkAdsCampaignExist($ads_campaign_id)) {
            $this->_redirectError(__d('ads', 'Campaign not found'), '/');
        } else {
            $ads_campaign = $this->AdsCampaign->getAdsCampaignDetail($ads_campaign_id);
            $this->set(array(
                'ads_campaign' => $ads_campaign,
                'from_date' => $from_date,
                'to_date' => $to_date
            ));
        }
    }

    public function load_report() {
        $data = $this->request->data;
        if (!$this->AdsCampaign->checkAdsCampaignExist($data['ads_campaign_id'])) {
            echo __('Ads', 'Campaign not found');
            exit;
        } else {
            $from_date = !empty($data['from_date']) ? date('Y-m-d', strtotime($data['from_date'])) : '';
            $to_date = !empty($data['to_date']) ? date('Y-m-d', strtotime($data['to_date'])) : '';
            $reports = $this->AdsReport->loadReport($data['ads_campaign_id'], $from_date, $to_date);
            $ads_campaign = $this->AdsCampaign->getAdsCampaignDetail($data['ads_campaign_id']);
            $roles = $this->AdsCampaign->getListRole();

            $this->set(array(
                'reports' => $reports,
                'roles' => $roles,
                'ads_campaign' => $ads_campaign['AdsCampaign']
            ));
            $this->render('Ads.Elements/report_detail');
        }
    }

    public function update_click_count() {
        $this->autoRender = false;
        $data = $this->params;
        if (count($data['pass']) == 1) {
            $id = $data['pass'][0];
            $this->loadModel('Ads.AdsReport');
            $this->loadModel('Ads.AdsCampaign');
            $this->loadModel('Ads.AdsPlacement');
            $campaign = $this->AdsCampaign->getAdsCampaignDetail($id);

            if ($campaign) {
                if (($campaign['AdsPlacement']['click_limit'] > 0 && $campaign['AdsCampaign']['click_count'] < $campaign['AdsPlacement']['click_limit']) ||
                        $campaign['AdsPlacement']['click_limit'] == 0) {
                    $check = $this->AdsPlacement->_checkConditionCampaign($campaign);
                    $campaign_id = $campaign['AdsCampaign']['id'];
                    if ($check && $campaign['AdsCampaign']['item_status'] == 'active') {
                        $this->AdsReport->clear();
                        $this->AdsReport->_updateViewCount($campaign_id, 'click');
                    }
                }
                if (strpos($campaign['AdsCampaign']['link'], 'http://') === 0 ||
                        strpos($campaign['AdsCampaign']['link'], 'https://') === 0) {
                    $this->redirect($campaign['AdsCampaign']['link']);
                } else {
                    $this->redirect('http://' . $campaign['AdsCampaign']['link']);
                }
            }

            $this->redirect($this->referer());
        }
    }

    public function getAdsLinkReport($ads_id) {
        $conds = array(
            'AdsReport.ads_campaign_id' => $ads_id
        );
        $link = Router::url('/', true) . 'ads/report/' . base64_encode(base64_encode($ads_id . 'AdsPlugin')) . '/';
        return $link;
    }

    public function load_my_ads($user_id) {
        $ads = array();
        $aPaymentStatus = array('0' => __d('ads', 'No'), '1' => __d('ads', 'Yes'));
        $aAdsStatus = array('0' => __d('ads', 'Show'), '1' => __d('ads', 'Hide'));
        $ads = $this->AdsCampaign->loadAdsCampaignDetail($user_id);
        if ($ads) {
            foreach ($ads as $k => $ad) {
                $isExpired = $this->AdsCampaign->checkAdsExpired($ad);
                $ads[$k]['AdsCampaign']['is_expired'] = $isExpired;
                $ads[$k]['AdsCampaign']['link_report'] = $this->getAdsLinkReport($ad['AdsCampaign']['id']);
            }
        }

        $is_load_more = false;
        $total_ads = $this->AdsCampaign->countTotalAdsCamExist($user_id);
        if ($total_ads > ADS_LIMIT_LOAD_MY_ADS) {
            $is_load_more = true;
        }
        $this->set('aAds', $ads);
        $this->set('aPaymentStatus', $aPaymentStatus);
        $this->set('aAdsStatus', $aAdsStatus);
        $this->set('user_id', $user_id);
        $this->set('is_load_more', $is_load_more);
    }

    public function my_ads_load_more() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $data = $this->request->query;
            $user_id = AuthComponent::user('id');
            $aAdsStatus = array('0' => __d('ads', 'Show'), '1' => __d('ads', 'Hide'));
            $aPaymentStatus = array('0' => 'Not', '1' => 'Paid');
            if (!empty($data['search'])) {
                // search result
                $ads = $this->AdsCampaign->loadAdsByTitle($user_id, $data['search']); // searching many ads
            } else {
                $ads = $this->AdsCampaign->loadAdsCampaignDetail($user_id, $data['offset']); // load more one ads
            }
            foreach ($ads as $key => $ad) {
                $isExpired = $this->AdsCampaign->checkAdsExpired($ad);
                $ads[$key]['AdsCampaign']['is_expired'] = $isExpired;
                $ads[$key]['AdsCampaign']['link_report'] = $this->getAdsLinkReport($ad['AdsCampaign']['id']);
            }
            $this->set('aAds', $ads);
            $this->set('aPaymentStatus', $aPaymentStatus);
            $this->set('aAdsStatus', $aAdsStatus);
            $this->render('Ads.Elements/myAds/load_more_ads');
        }
    }

    public function handle_ads_action() {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $data = $this->request->query;
            $id = $data['id'];
            $user_id = $data['user_id'];
            $mss = '';
            $isSuccess = 0;
            switch ($data['action']) {
                case 'delete':
                    $isSuccess = $this->AdsCampaign->deleteAdsCampaignStatus($id);
                    $mss = __d('ads', 'Successfully deleted');
                    $response['total_ads'] = $this->AdsCampaign->countTotalAdsCamExist($user_id);
                    break;
                case 'hide':
                    $isSuccess = $this->AdsCampaign->setStatusItemCampaign($id, 1);
                    $mss = __d('ads', 'Successfully updated');
                    break;
                case 'show':
                    $isSuccess = $this->AdsCampaign->setStatusItemCampaign($id, 0);
                    $mss = __d('ads', 'Successfully updated');
                    break;
            }
            if ($isSuccess) {
                $response['result'] = 1;
                $response['message'] = $mss;
            } else {
                $response['result'] = 0;
                $response['message'] = __d('ads', 'Action was not successfull');
            }

            echo json_encode($response);
            exit;
        }
    }

    public function setPayment($id) {
        if ($this->AdsTransaction->checkAdsTransitionExist($id)) {
            return false;
        }
        $ads_campaign = $this->AdsCampaign->getAdsCampaignDetail($id);
        $ads_placement = $ads_campaign['AdsPlacement'];
        $ads_campaign = $ads_campaign['AdsCampaign'];
        $verification_code = $this->AdsTransaction->createVerificationCode($id);
        $data = array(
            'ads_campaign_id' => $ads_campaign['id'],
            'ads_placement_id' => $ads_placement['id'],
            'ads_campaign_name' => $ads_campaign['name'],
            'ads_placement_name' => $ads_placement['name'],
            'email' => $ads_campaign['email'],
            'price' => $ads_placement['price'],
            'currency' => Configure::read('Ads.currency_code'),
            'currency_symbol' => Configure::read('Ads.currency_symbol'),
            'status' => ADS_TRANSACTION_PENDING,
            'verification_code' => $verification_code
        );
        if ($this->AdsTransaction->save($data)) {
            $this->AdsTransaction->clearOldTransactionVerification($ads_campaign['id'], $this->AdsTransaction->id);
            //send email
            $this->MooMail->send($ads_campaign['email'], 'send_payment_request', array(
               'payment_url' => Router::url('/', true) . 'ads/ads_gateways/' . $id,
            ));
        }
    }

    public function update_view_count($campaign_id) {
        $this->autoRender = false;
        if ($campaign_id) {
            $campaign = $this->AdsCampaign->getAdsCampaignDetail($campaign_id);
            if ($campaign) {
                if ($campaign['AdsCampaign']['view_count'] < $campaign['AdsPlacement']['view_limit'] &&
                        $campaign['AdsCampaign']['click_count'] < $campaign['AdsPlacement']['click_limit']) {
                    $check = $this->AdsPlacement->_checkConditionCampaign($campaign);
                    $campaign_id = $campaign['AdsCampaign']['id'];
                    if ($check && $campaign['AdsCampaign']['item_status'] == 'active') {
                        if (!isset($_SESSION['Ads']['View'][$campaign_id])) {
                            $_SESSION['Ads']['View'][$campaign_id] = time();
                            $this->AdsReport->clear();
                            $this->AdsReport->_updateViewCount($campaign_id, 'view');
                        } elseif (time() - $_SESSION['Ads']['View'][$campaign_id] > ADS_SESSION_EXPIRE) {
                            unset($_SESSION['Ads']['View'][$campaign_id]);
                        }
                    }
                }
            }
        }
    }
	public function ads_gateways($ads_campaign_id){
		
        if (empty($ads_campaign_id)) {
            return false;
        }
        $this->loadModel('Ads.AdsTransaction');
        $transaction = $this->AdsTransaction->getWaitingTransactionForActive($ads_campaign_id);
        $adsCampaign = $this->AdsCampaign->getAdsCampaignDetail($ads_campaign_id);
        if (empty($transaction) || empty($adsCampaign)) {
            return false;
        }

        $adsPlacement = $adsCampaign['AdsPlacement'];
        $adsCampaign = $adsCampaign['AdsCampaign'];
        $this->loadModel('PaymentGateway.Gateway');
        $gateways = array();
        $gateways = $this->Gateway->find('all', array('conditions' => array('enabled' => "1", 'Plugin != ' => 'PaypalAdaptive')));
        $currency = Configure::read('Config.currency'); 

        $this->set(compact('transaction','gateways','adsCampaign','adsPlacement','currency'));
    }
    function purchase_ads($type=null,$transaction_id=null){
        $this->autoRender = false;
        if (empty($transaction_id )) {
            return false;
        }
        $gateway_id = $this->request->data['gateway_id'];
        $this->loadModel('PaymentGateway.Gateway');
        $gateway = $this->Gateway->findById($gateway_id);
        $plugin = $gateway['Gateway']['plugin'];
        $this->AdsTransaction->id = $transaction_id;
        $this->AdsTransaction->save(array('type'=>$type));
        $helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
	return $this->redirect($helperGateway->getUrlProcess() . '/Ads_Ads_Transaction/' . $transaction_id);
		
    }
    public function success(){
		$this->Session->setFlash(__d('ads', 'Thank you for your payment, your ad was activated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
		return $this->redirect('/');
    }

    public function fail(){
		$this->Session->setFlash(__d('ads', 'Your payment has been canceled'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
		return $this->redirect('/');
    }
    
    public function change_url(){
        $this->autoRender = false;
        echo Router::url("/",true).'ads/js/main.js';
    }

}
