<?php

class BusinessVerifiesController extends BusinessAppController {

    public $paginate = array(
        'order' => 'BusinessVerify.modified DESC',
        'limit' => 10
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessVerify');
        $this->set('title_for_layout', __d('business', 'Business Verifies'));
    }

    ///////////////////////////////////backend///////////////////////////////////
    public function admin_index() {

        $sStatusFilter = !empty($this->request->query['status_filter']) ? $this->request->query['status_filter'] : 0;
        $sKeyword = !empty($this->request->query['keyword']) ? $this->request->query['keyword'] : '';

        $aCond = array();
        if (!empty($sKeyword)) {
            array_push($aCond, "Business.name LIKE '%" . $sKeyword . "%'");
        }

        $sType = 'INNER';
        if (!empty($sStatusFilter)) {
            $sType = 'LEFT';
        }

        $this->paginate['fields'] = array('BusinessVerify.*', 'Business.*', 'User.*');
        $this->paginate['order'] = 'Business.created DESC';
        $this->paginate['recursive'] = -1;
        $this->paginate['joins'] = array(
            array(
                'table' => 'business_verifies',
                'alias' => 'BusinessVerify',
                'type' => $sType,
                'foreignKey' => false,
                'conditions' => array('BusinessVerify.business_id = Business.id')
            ),
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'LEFT',
                'foreignKey' => false,
                'conditions' => array('User.id = BusinessVerify.user_id')
            )
        );

        if($sStatusFilter != 2)
        {
            $aCond['Business.verify'] = $sStatusFilter;
        }
        else
        {
            $aCond[] = '(BusinessVerify.business_id >0 || Business.verify = 1)';
        }
        $aBusinessVerifies = $this->paginate('Business', $aCond);

        $this->set(array(
            'keyword' => $sKeyword,
            'sStatusFilter' => $sStatusFilter,
            'aBusinesses' => $aBusinessVerifies,
            'title_for_layout' => __d('business', 'Verification Requests'),
        ));
    }

    public function admin_delete($id) {
        $this->autoRender = false;
        $aBusinessVerify = $this->BusinessVerify->findById($id);
        
        if (!empty($aBusinessVerify)) {
            
            $this->BusinessVerify->delete($id);
            
            if($aBusinessVerify['Business']['verify']){
                $this->Business->clear();
                $this->Business->id = $aBusinessVerify['Business']['id'];
                $this->Business->save(array('verify' => 0));
            }
            
            $this->Business->sendNotification(
                    $aBusinessVerify['BusinessVerify']['user_id'], MooCore::getInstance()->getViewer(true), 'business_reject_verify', $aBusinessVerify['Business']['moo_url'], $aBusinessVerify['Business']['name']
            );
            $this->Session->setFlash(__d('business', 'Verification request deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }
        
        $this->redirect($this->referer());
    }

    public function index() {
        
    }

    public function phone() {
        $uid = $this->Auth->user('id');
        $id = (int) $this->request->data['id'];
        if (!$this->Business->isBusinessExist($id)) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }
        $business = $this->Business->getOnlyBusiness($id);
        if (!$this->Business->permission($id, BUSINESS_PERMISSION_SEND_VERIFICATION_REQUEST, $business['Business']['moo_permissions'])) {
            $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
        }

        if ($this->request->is('post')) {
            $sPhoneNumber = $this->request->data['phone_number'];
            if (empty($sPhoneNumber)) {
                $this->Session->setFlash(__d('business', 'Phone number is required'), 'default', array('class' => 'error-message'));
                $this->redirect($this->referer());
            }

            // check request exit
            if (!$this->BusinessVerify->isRequestExit($id)) {
                $this->BusinessVerify->create();
                $aData = array(
                    'user_id' => $uid,
                    'business_id' => $id,
                    'phone_number' => $sPhoneNumber
                );
            } else {
                $aBusinessVerify = $this->BusinessVerify->findByBusinessId($id);
                $this->BusinessVerify->id = $aBusinessVerify['BusinessVerify']['id'];
                $aData = array(
                    'user_id' => $uid,
                    'phone_number' => $sPhoneNumber
                );
            }

            // add or edit request
            $this->BusinessVerify->save($aData);

            // send mail to admin
            $ssl_mode = Configure::read('core.ssl_mode');
            $http = (!empty($ssl_mode)) ? 'https' : 'http';

            $aUsers = $this->User->find('all', array('conditions' => array('Role.is_admin' => 1)));
            foreach ($aUsers as $aUser) {
                $this->MooMail->send($aUser, 'business_verify_phone', array(
                    'phone_number' => $sPhoneNumber,
                    'recipient_title' => $aUser['User']['moo_title'],
                    'business_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $business['Business']['moo_href']
                ));
            }

            $this->Session->setFlash(__d('business', "Your verification request has been successfully sent."));
            $this->redirect('/businesses/dashboard/verify/'.$id);
        }
    }

    public function documents() {
        $this->_checkPermission();
        $uid = $this->Auth->user('id');
        $id = (int) $this->request->data['id'];
        if (!$this->Business->isBusinessExist($id)) {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }
        $aBusiness = $this->Business->findById($id);
        if (!$this->Business->permission($id, BUSINESS_PERMISSION_MANAGE_PHOTO, $aBusiness['Business']['moo_permissions'])) {
            $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
        }
        if ($this->request->is('post')) {
            $oZip = new ZipArchive();
            $sZipName = md5(uniqid()) . ".zip";
            $sZipPath = 'uploads' . DS . 'zip';
            $this->_prepareDir($sZipPath);
            $oZip->open($sZipPath . DS . $sZipName, ZipArchive::CREATE);
            $aPhotoList = explode(',', $this->request->data['new_photos']);
            foreach ($aPhotoList as $sPhotoItem) {
                if (file_exists(WWW_ROOT . $sPhotoItem)) {
                    $oZip->addFile(WWW_ROOT . $sPhotoItem, $sPhotoItem);
                }
            }
            $oZip->close();

            // check request exit
            if (!$this->BusinessVerify->isRequestExit($id)) {
                $this->BusinessVerify->create();
                $aData = array(
                    'user_id' => $uid,
                    'business_id' => $id,
                    'document' => $sZipName
                );
            } else {
                $aBusinessVerify = $this->BusinessVerify->findByBusinessId($id);
                $this->BusinessVerify->id = $aBusinessVerify['BusinessVerify']['id'];
                $aData = array(
                    'user_id' => $uid,
                    'document' => $sZipName
                );
            }

            // add or edit request
            $this->BusinessVerify->save($aData);

            // send mail to admin
            $ssl_mode = Configure::read('core.ssl_mode');
            $http = (!empty($ssl_mode)) ? 'https' : 'http';

            $aUsers = $this->User->find('all', array('conditions' => array('Role.is_admin' => 1)));
            foreach ($aUsers as $aUser) {
                $this->MooMail->send($aUser, 'business_verify_documents', array(
                    'recipient_title' => $aUser['User']['moo_title'],
                    'attachment_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $this->request->base . '/uploads/zip/' . $sZipName,
                    'business_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $aBusiness['Business']['moo_href']
                ));
            }

            $this->Session->setFlash(__d('business', "Your verification request has been successfully sent."));
            $this->redirect($this->referer());
        }
    }

    public function ajax_upload() {
        $this->autoRender = false;
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'tmp';
        $this->_prepareDir($path);
        $result = $uploader->handleUpload(WWW_ROOT . $path);

        if (!empty($result['success'])) {
            // resize image
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $result['photo'] = $path . DS . $result['filename'];
        }

        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

    public function create_mail() {

        $this->loadModel('Mail.Mailtemplate');
        if (!$this->Mailtemplate->hasAny(array('type' => 'business_verify_documents'))) {
            $data['Mailtemplate'] = array(
                'type' => 'business_verify_documents',
                'plugin' => 'Business',
                'vars' => '[recipient_title],[business_link],[attachment_link]'
            );
            $this->Mailtemplate->save($data);

            $langs = $this->Language->find('all');
            foreach ($langs as $lang) {
                $language = $lang['Language']['key'];
                $this->Mailtemplate->locale = $language;
                $data_translate['subject'] = '[recipient_title] sent a request to get verified badget for this business page';
                $content = <<<EOF
			<p>[header]</p>
			<p>Request a verified badge has been sent from [recipient_title] for the below business page.</p>
                        <p>[business_link]</p>
			<p>Please check attached documents to review and verify.</p>
			<p>[attachment_link]</p>
			<p>[footer]</p>
EOF;
                $data_translate['content'] = $content;
                $this->Mailtemplate->save($data_translate);
            }
        }

        $this->Language->clear();
        $this->Mailtemplate->clear();

        if (!$this->Mailtemplate->hasAny(array('type' => 'business_verify_phone'))) {
            $data['Mailtemplate'] = array(
                'type' => 'business_verify_phone',
                'plugin' => 'Business',
                'vars' => '[recipient_title],[business_link],[phone_number]'
            );
            $this->Mailtemplate->save($data);

            $langs = $this->Language->find('all');
            foreach ($langs as $lang) {
                $language = $lang['Language']['key'];
                $this->Mailtemplate->locale = $language;
                $data_translate['subject'] = '[recipient_title] sent a request to get verified badget for this business page';
                $content = <<<EOF
			<p>[header]</p>
			<p>Request a verified badge has been sent from [recipient_title] for the below business page.</p>
                        <p>[business_link]</p>
			<p>Please call this phone number: [phone_number] to verify.</p>
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
