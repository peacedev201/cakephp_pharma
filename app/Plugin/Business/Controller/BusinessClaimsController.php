<?php

/*
  Define is_claim
  is_claim: 0 not claim
  is_claim: 1 claimed
  is_claim: 2 enabled submit review
  is_claim: 3 waitting admin review
 * end
 */

class BusinessClaimsController extends BusinessAppController {

    public $paginate = array(
        'limit' => 10,
        'order' => 'Business.created DESC',
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessType');
        $this->loadModel('Business.BusinessTime');
        $this->loadModel('Business.BusinessPayment');
        $this->loadModel('Business.BusinessPaymentType');
        $this->loadModel('Business.BusinessLocation');
        $this->loadModel('Business.BusinessCategory');
        $this->loadModel('Business.BusinessPhoto');
        $this->loadModel('Business.BusinessCategoryItem');
        $this->loadModel('Business.BusinessStore');
        $this->set('title_for_layout', __d('business', 'Business Claims'));
    }

    ///////////////////////////////////backend///////////////////////////////////
    public function admin_index() {
        $sKeyword = !empty($this->request->query['keyword']) ? $this->request->query['keyword'] : '';
        $aCond = array('Business.parent_id' => 0, 'Business.is_claim' => 3);
        if (!empty($sKeyword)) {
            array_push($aCond, "Business.name LIKE '%" . $sKeyword . "%'");
        }

        $this->Business->bindModel(array(
            'belongsTo' => array(
                'User' => array('counterCache' => true)
            ),
        ));
        $aBusinesses = $this->paginate('Business', $aCond);
        $this->set(array(
            'title_for_layout' => __d('business', 'Claim Requests'),
            'keyword' => $sKeyword,
            'aBusinesses' => $aBusinesses,
        ));
    }

    public function index() {
        $this->_checkPermission(array('confirm' => true));
        $id = (int) $this->request->params['id'];
        if (!$this->Business->isBusinessExist($id)) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }

        $aBusiness = $this->Business->findById($id);
        $iCUserId = (int) MooCore::getInstance()->getViewer(true);
        $userBusiness = $this->User->findById($aBusiness['Business']['user_id']);
        $aAcoBusiness = explode(',', $userBusiness['Role']['params']);
        if (!in_array('business_claim', $aAcoBusiness) || !$this->Business->bCheckClaimBusiness($aBusiness, $iCUserId)) {
            $this->redirect($aBusiness['Business']['moo_url']);
        }

        $this->set(array('id' => $id));
    }

    public function create($id) {

        $this->_checkPermission(array('confirm' => true));
        if (!$this->Business->isBusinessExist($id)) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }

        $aBusiness = $this->Business->findById($id);
        $iCUserId = (int) MooCore::getInstance()->getViewer(true);
        $userBusiness = $this->User->findById($aBusiness['Business']['user_id']);
        $aAcoBusiness = explode(',', $userBusiness['Role']['params']);
        if (!in_array('business_claim', $aAcoBusiness) || !$this->Business->bCheckClaimBusiness($aBusiness, $iCUserId)) {
            $this->redirect($aBusiness['Business']['moo_url']);
        }

        //$business = $this->Business->initFields();
        $business = $this->Business->getBusiness($aBusiness['Business']['id'], null, $aBusiness['Business']['parent_id']);
		$original_business = $business;
        $business['Business']['id'] = null;

        //type
        $businessTypes = $this->BusinessType->getBusinessTypeList();

        //payment
        $businessPayments = $this->BusinessPayment->getBusinessPayment();

        //list days in week
        $days = $this->Business->getListDayInWeek();
        
        //time business
        $times_open = $this->Business->getListTimeOpen();
        $times_close = $this->Business->getListTimeClose();

        //photos
        $branch_photos = $this->BusinessPhoto->getBusinessAlbumPhotos($aBusiness['Business']['id'], true);

        $this->set(array(
            'days' => $days,
            'times_open' => $times_open,
            'times_close' => $times_close,
            'claim_id' => $id,
            'business' => $business,
            'businessTypes' => $businessTypes,
            'businessPayments' => $businessPayments,
			'original_business' => $original_business,
        ));
    }

    public function submit() {

        $this->_checkPermission();
        $id = (int) $this->request->data['id'];
        if (!$this->Business->isBusinessExist($id, MooCore::getInstance()->getViewer(true))) {
            $this->_jsonError(__d('business', 'Business not found'), false, array(
                'location' => $this->request->base . '/businesses/dashboard/edit/' . $id
            ));
        }

        $aBusiness = $this->Business->findById($id);
        if ($aBusiness['Business']['is_claim'] != 2) {
            $this->_jsonError(__d('business', 'Business not found'), false, array(
                'location' => $this->request->base . '/businesses/dashboard/edit/' . $id
            ));
        }

        if ($this->request->is('post')) {

            // Update this business
            $this->Business->id = $id;
            $this->Business->set(array('is_claim' => 3));
            $this->Business->save();


            // send notification to admin
            $aData = array();
            $this->loadModel('Notification');
            $aUsers = $this->User->find('all', array('conditions' => array('Role.is_admin' => 1)));
            foreach ($aUsers as $aUser) {
                $aData[] = array(
                    'user_id' => $aUser['User']['id'],
                    'sender_id' => MooCore::getInstance()->getViewer(true),
                    'action' => 'business_claim_request',
                    'url' => '/businesses/dashboard/edit/' . $id,
                    'plugin' => 'Business',
                    'params' => ''
                );
            }
            $this->Notification->saveAll($aData);

            // return
            $this->_jsonSuccess(__d('business', 'Successfully saved'), false, array(
                'location' => $this->request->base . '/businesses/dashboard/edit/' . $id
            ));
        }
    }

    public function remove($id) {
        $this->_checkPermission();
        if (!$this->Business->isBusinessExist($id, MooCore::getInstance()->getViewer(true))) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }

        $aBusiness = $this->Business->findById($id);
        if ($aBusiness['Business']['is_claim'] == 1) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }

        $this->Business->deleteBusiness($id);

        $this->redirect('/businesses');
    }

    public function reject($id) {
        $this->_checkPermission(array('admin' => true));

        if (!$this->Business->isBusinessExist($id)) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }

        $aBusiness = $this->Business->findById($id);
        if ($aBusiness['Business']['is_claim'] == 1) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }

        // add notification
        $iClaimId = $aBusiness['Business']['claim_id'];
        $aBusinessClaim = $this->Business->findById($iClaimId);

        $this->loadModel('Notification');
        $this->Notification->record(array(
            'recipients' => $aBusiness['Business']['user_id'],
            'sender_id' => MooCore::getInstance()->getViewer(true),
            'action' => 'business_claim_request_reject',
            'url' => $aBusinessClaim['Business']['moo_url'],
            'params' => $aBusinessClaim['Business']['name'],
            'plugin' => 'Business'
        ));

        $this->Business->deleteBusiness($id);

        $this->redirect('/admin/business/business_claims');
    }

    public function review() {
        $this->_checkPermission(array('admin' => true));

        $id = (int) $this->request->data['id'];
        if (!$this->Business->isBusinessExist($id)) {
            $this->_jsonError(__d('business', 'Business not found'), false, array(
                'location' => $this->request->base . '/businesses/dashboard/edit/' . $id
            ));
        }

        $aBusiness = $this->Business->findById($id);
        if ($aBusiness['Business']['is_claim'] == 1) {
            $this->_jsonError(__d('business', 'Business not found'), false, array(
                'location' => $this->request->base . '/businesses/dashboard/edit/' . $id
            ));
        }

        if ($this->request->is('post')) {

            $iClaimId = $aBusiness['Business']['claim_id'];
            $aBusinessClaim = $this->Business->findById($iClaimId);

            if ($aBusinessClaim['Business']['is_claim'] == 1) {
                $this->_jsonError(__d('business', 'Business not found'), false, array(
                    'location' => $this->request->base . '/businesses/dashboard/edit/' . $id
                ));
            }

            $data = $this->request->data;
            $tempBusinessId = $data['id'];
            $cat_id = array_filter($data['category_id']);
            if ($cat_id == null) {
                $this->_jsonError(__d('business', 'Please select at least a category'));
            }

            $data['always_open'] = !empty($data['always_open']) ? $data['always_open'] : 0;

            //get lng lat
            $data['lat'] = $data['lng'] = 0;
            if ($data['address'] != null) {
                $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
                $lnglat = $businessHelper->getLngLatByAddress($data['address']);
                $data['lng'] = $lnglat['lng'];
                $data['lat'] = $lnglat['lat'];

                //get location id
                $addressDetail = $businessHelper->getAddressDetail($data['lat'], $data['lng']);
                $data['postal_code'] = $addressDetail['postal_code'];
                $data['business_location_id'] = $this->BusinessLocation->autoAddBusinessLocation($addressDetail['country'], $addressDetail['region']);
            }

            $data['is_claim'] = 0;
            /*if (!in_array('business_claim', $this->_getUserRoleParams())) {
                $data['is_claim'] = 1;
            }*/

            $data['parent_id'] = 0;
            $data['user_id'] = $aBusiness['Business']['user_id'];
            unset($data['id'], $data['claim_id']);

            //validate
            $this->Business->id = $iClaimId;
            $this->Business->set($data);
            $this->_validateData($this->Business);
            
            //current storage image
            $storage_image = $this->Business->getCurrentStorageImage($iClaimId);
            
            //copy s3 image
            if($aBusiness['Business']['logo'] != $aBusinessClaim['Business']['logo'])
            {
                $this->Business->copyClaimStorageImage($tempBusinessId, $aBusiness['Business']['logo']);
            }
            else if(!empty($storage_image))
            {
                $this->Business->copyClaimStorageImage(null, $aBusinessClaim['Business']['logo'], $storage_image);
            }

            //save
            if ($this->Business->save($data)) {
                $business_id = $this->Business->id;
                $aBusiness = $this->Business->read();

                //save time
                if (empty($data['always_open']) || $data['always_open'] != 1) {
                    $this->save_time($data, $business_id);
                } else {
                    $this->BusinessTime->deleteByBusiness($business_id);
                }

                //save payment type
                $this->save_payment_type($data, $business_id);

                //save category
                $this->save_business_category_item($data, $business_id);

                //update business counter for location
                $this->BusinessLocation->updateBusinessCounter($data['business_location_id']);

                //update category business counter
                $this->BusinessCategory->updateBusinessCounter($business_id);

                //register location for search
                $this->BusinessLocation->registerLocationKeyword($data['address'], $data['lat'], $data['lng']);

                //notification to user approved and delete other claimed
                $this->loadModel('Notification');
                $this->Notification->create();
                $this->Notification->save(array(
                    'user_id' => $aBusiness['Business']['user_id'],
                    'sender_id' => MooCore::getInstance()->getViewer(true),
                    'action' => 'business_claim_request_approved',
                    'url' => $aBusiness['Business']['moo_url'],
                    'params' => $aBusiness['Business']['name'],
                    'plugin' => 'Business'
                ));

                $aBusinessClaimeds = $this->Business->getBusinessByClaimed($iClaimId);
                if (!empty($aBusinessClaimeds)) {
                    foreach ($aBusinessClaimeds as $aBusinessClaimed) {
                        if ($aBusinessClaimed['Business']['user_id'] != $aBusiness['Business']['user_id']) {
                            $this->Notification->create();
                            $this->Notification->save(array(
                                'user_id' => $aBusinessClaimed['Business']['user_id'],
                                'sender_id' => MooCore::getInstance()->getViewer(true),
                                'action' => 'business_claim_request_reject',
                                'url' => $aBusiness['Business']['moo_url'],
                                'params' => $aBusiness['Business']['name'],
                                'plugin' => 'Business'
                            ));
                        }

                        $this->Business->deleteBusiness($aBusinessClaimed['Business']['id']);
                    }
                }

                /* Remove and Send notification to admin page and owner old */

                // Admin page
                $this->loadModel('Business.BusinessAdmin');
                $aBusinessAdmins = $this->BusinessAdmin->getAdminList($aBusinessClaim['Business']['id']);
                if (!empty($aBusinessAdmins)) {
                    foreach ($aBusinessAdmins as $aBusinessAdmin) {
                        $this->Notification->create();
                        $this->Notification->save(array(
                            'user_id' => $aBusinessAdmin['BusinessAdmin']['user_id'],
                            'sender_id' => MooCore::getInstance()->getViewer(true),
                            'action' => 'business_claim_remove_product',
                            'url' => $aBusiness['Business']['moo_url'],
                            'params' => $aBusiness['Business']['name'],
                            'plugin' => 'Business'
                        ));
                    }
                }

                // Owner Old
                $this->Notification->create();
                $this->Notification->save(array(
                    'user_id' => $aBusinessClaim['Business']['user_id'],
                    'sender_id' => MooCore::getInstance()->getViewer(true),
                    'action' => 'business_claim_remove_product',
                    'url' => $aBusiness['Business']['moo_url'],
                    'params' => $aBusiness['Business']['name'],
                    'plugin' => 'Business'
                ));

                /* update owner */

                // activities
                $this->Business->updateOwnerActivities($business_id, $aBusiness['Business']['user_id'], $aBusinessClaim['Business']['user_id']);

                // photos
                $this->Business->updateOwnerAlbumPhotos($aBusiness['Business']['album_id'], $aBusiness['Business']['user_id'], $aBusinessClaim['Business']['user_id']);

                // branches
                $this->Business->updateOwnerBranches($business_id, $aBusiness['Business']['user_id'], $aBusinessClaim['Business']['user_id'], $aBusinessClaim['Business']['id']);

                //remove business from store
                if($this->BusinessStore->isIntegrateStore())
                {
                    $this->BusinessStore->removeBusinessFromStore($business_id);
                }
                
                //return
                $this->_jsonSuccess(__d('business', 'Successfully saved'), false, array(
                    'location' => $this->request->base . '/businesses/dashboard/edit/' . $business_id
                ));
            }
        }
    }

    private function save_time($data, $business_id) {
        $this->BusinessTime->deleteByBusiness($business_id);
        if (!empty($data['day'])) {
            foreach ($data['day'] as $k => $day) {
                $array_open = explode(' ', $data['time_open'][$k]);
                $array_close = explode(' ', $data['time_close'][$k]);
                $this->BusinessTime->create();
                $this->BusinessTime->save(array(
                    'business_id' => $business_id,
                    'day' => $day,
                    'time_open' => $array_open[1],
                    'time_close' => $array_close[1],
                    'next_day' => $array_close[0]
                ));
            }
        }
    }

    private function save_payment_type($data, $business_id) {
        $this->BusinessPaymentType->deleteByBusiness($business_id);
        if (!empty($data['business_payment_id'])) {
            foreach ($data['business_payment_id'] as $business_payment_id) {
                $this->BusinessPaymentType->create();
                $this->BusinessPaymentType->save(array(
                    'business_id' => $business_id,
                    'business_payment_id' => $business_payment_id,
                ));
            }
        }
    }

    private function save_business_category_item($data, $business_id) {
        $this->BusinessCategoryItem->deleteByBusiness($business_id);
        if (!empty($data['category_id'])) {
            $business = $this->Business->findById($business_id);
            $data['category_id'] = array_unique($data['category_id']);
            foreach ($data['category_id'] as $k => $business_category_id) {
                if ($business_category_id > 0) {
                    $this->BusinessCategoryItem->create();
                    $this->BusinessCategoryItem->save(array(
                        'business_id' => $business_id,
                        'business_category_id' => $business_category_id,
                    ));
                }
            }
        }
    }

    public function create_mail() {

        $this->loadModel('Mail.Mailtemplate');
        if (!$this->Mailtemplate->hasAny(array('type' => 'business_claim_request'))) {
            $data['Mailtemplate'] = array(
                'type' => 'business_claim_request',
                'plugin' => 'Business',
                'vars' => '[recipient_title],[business_title],[business_link]'
            );
            $this->Mailtemplate->save($data);

            $langs = $this->Language->find('all');
            foreach ($langs as $lang) {
                $language = $lang['Language']['key'];
                $this->Mailtemplate->locale = $language;
                $data_translate['subject'] = '[recipient_title] claimed the business - [business_title]. Please review!';
                $content = <<<EOF
			<p>[header]</p>
			<p>[business_title] has been claimed by [recipient_title], please click on the below link to review.</p>
                        <p>[business_link]</p>
			<p>[footer]</p>
EOF;
                $data_translate['content'] = $content;
                $this->Mailtemplate->save($data_translate);
            }
        }

        echo 'Success!';
        die;
    }

}
