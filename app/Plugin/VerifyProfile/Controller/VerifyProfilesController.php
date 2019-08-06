<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class VerifyProfilesController extends VerifyProfileAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('VerifyProfile.VerifyProfile');
    }

    public function index() {
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'verify_profile_verify'));

        $cuser = $this->_getUser();
        $uid = $this->Auth->user('id');
        if (!Configure::read('VerifyProfile.verify_profile_document_request_verification') || $cuser['role_id'] == ROLE_ADMIN) {
            $this->redirect($cuser['moo_url']);
        }

        $sStatus = $this->VerifyProfile->getStatus($uid);
        if ($sStatus == "pending") {
            $this->Session->setFlash(__d('verify_profile', 'Your verification request has been successfully sent. We will review your request and will notify you once the review process is completed.'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            $this->redirect($cuser['moo_url']);
        } else if ($sStatus == "verified") {
            $this->Session->setFlash(__d('verify_profile', 'Your account verified.'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            $this->redirect($cuser['moo_url']);
        }

        $this->set('title_for_layout', __d('verify_profile', 'Verification'));
    }

    public function save() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'verify_profile_verify'));

        if ($this->request->is('post')) {
            $this->loadModel('Activity');
            $this->loadModel('Photo.Album');
            $aPhotoList = explode(',', $this->request->data['new_photos']);
            if (count($aPhotoList) != Configure::read('VerifyProfile.verify_profile_document')) {
                $this->Session->setFlash(__d('verify_profile', 'The required number of document for verification request is %s', Configure::read('VerifyProfile.verify_profile_document')), 'default', array('class' => 'error-message'));
                $this->redirect($this->referer());
            }

            $uid = $this->Auth->user('id');
            $this->VerifyProfile->create();
            $this->VerifyProfile->save(array(
                'user_id' => $uid,
                'status' => 'pending'
            ));

            $this->loadModel('Photo.Photo');
            $this->request->data['user_id'] = $uid;
            $this->request->data['type'] = 'Verify_Profile';
            $this->request->data['target_id'] = $this->VerifyProfile->id;

            $aPhotoId = array();
            foreach ($aPhotoList as $sPhotoItem) {
                if (!empty($sPhotoItem)) {
                    $this->request->data['thumbnail'] = $sPhotoItem;
                    $this->Photo->create();
                    $this->Photo->set($this->request->data);
                    $this->Photo->save();
                    array_push($aPhotoId, $this->Photo->id);
                }
            }

            $this->VerifyProfile->save(array(
                'images' => join(',', $aPhotoId)
            ));

            $this->_adminNotification();

            if ($this->isApp()) {
                $this->redirect('/profile/verify/success_verification');
            } else {
                $this->redirect($this->referer());
            }
        }
    }

    public function ajax_request_verification() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'verify_profile_verify'));

        if ($this->request->is('post')) {
            $iUserId = $this->Auth->user('id');
            $aVerifyProfile = $this->VerifyProfile->find('first', array('conditions' => array('VerifyProfile.user_id' => $iUserId)));
            if (empty($aVerifyProfile)) {
                $this->VerifyProfile->create();
                $this->VerifyProfile->save(array(
                    'user_id' => $iUserId,
                    'status' => 'pending'
                ));
                $this->_adminNotification();
            }
        }
    }

    public function ajax_reverification() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));

        if ($this->request->is('post')) {
            $iUserId = $this->Auth->user('id');
            $aVerifyProfile = $this->VerifyProfile->find('first', array('conditions' => array('VerifyProfile.user_id' => $iUserId)));
            if (!empty($aVerifyProfile)) {
                $this->VerifyProfile->delete($aVerifyProfile['VerifyProfile']['id']);
            }
        }
    }

    public function ajax_verify() {
        $this->autoRender = false;
        $this->_checkPermission(array('admin' => true));

        if ($this->request->is('post')) {
            $iUserId = $this->request->data['id'];
            $aVerifyProfile = $this->VerifyProfile->find('first', array('conditions' => array('VerifyProfile.user_id' => $iUserId)));
            if (empty($aVerifyProfile)) {
                $this->VerifyProfile->create();
                $this->VerifyProfile->save(array(
                    'user_id' => $iUserId,
                    'status' => 'pending'
                ));
                $aVerifyProfile = $this->VerifyProfile->read();
            }
            $this->VerifyProfile->updateStatus($aVerifyProfile, 'verify', array(), $this->Auth->user('id'));
        }
    }

    public function ajax_unverify($id) {
        $this->_checkPermission(array('admin' => true));
        $aUser = $this->User->findById($id);

        if (empty($id) || empty($aUser)) {
            $this->set('msg', __d('verify_profile', 'Error'));
            echo $this->render('/Elements/error');
            exit;
        }

        $this->loadModel('VerifyProfile.VerifyReason');
        $aReasons = $this->VerifyReason->find('all');
        $this->set('aReasons', $aReasons);
        $this->set('user_id', $id);
    }

    public function ajax_unverify_process() {
        $this->autoRender = false;
        $this->_checkPermission(array('admin' => true));

        if ($this->request->is('post')) {

            // check validate
            $bError = false;
            if (!empty($this->request->data['other_reason']) && empty($this->request->data['other_reason_content'])) {
                $bError = true;
                $aResponse['message'] = __d('verify_profile', 'Please enter the reason');
            } else if (empty($this->request->data['other_reason_content']) && empty($this->request->data['reason'])) {
                $bError = true;
                $aResponse['message'] = __d('verify_profile', 'Please select at least one reason');
            }

            $iUserId = $this->request->data['user_id'];
            $aVerifyProfile = $this->VerifyProfile->find('first', array('conditions' => array('VerifyProfile.user_id' => $iUserId)));
            if (empty($aVerifyProfile)) {
                $bError = true;
                $aResponse['message'] = __d('verify_profile', 'Error');
            }

            if (!$bError) {
                $aReasonsContent = array();
                if (isset($this->request->data['reason'])) {
                    $aReasons = $this->request->data['reason'];
                    $this->loadModel('VerifyProfile.VerifyReason');
                    foreach ($aReasons as $iReasonId) {
                        $aReason = $this->VerifyReason->findById($iReasonId);
                        $aReasonsContent[] = $aReason['VerifyReason']['description'];
                    }
                }

                if (!empty($this->request->data['other_reason']) && !empty($this->request->data['other_reason_content'])) {
                    $aReasonsContent[] = h($this->request->data['other_reason_content']);
                }

                $aResponse['result'] = 1;
                $this->VerifyProfile->updateStatus($aVerifyProfile, 'unverify', $aReasonsContent, $this->Auth->user('id'));
            }

            echo json_encode($aResponse);
        }
    }

    public function ajax_sample($sType) {
        if (!in_array($sType, array('passport', 'driver', 'card', 'deny'))) {
            $this->autoRender = false;
        }

        $sTitleSample = '';
        if ($sType == 'passport') {
            $sTitleSample = __d('verify_profile', "Passport's Sample Image");
        }
        if ($sType == 'driver') {
            $sTitleSample = __d('verify_profile', "Driver License's Sample Image");
        }
        if ($sType == 'card') {
            $sTitleSample = __d('verify_profile', "ID Card's Sample Image");
        }
        if ($sType == 'deny') {
            $sTitleSample = __d('verify_profile', "Deny Photo's Sample Image");
        }

        $this->set('sType', $sType);
        $this->set('sTitleSample', $sTitleSample);
    }

    public function ajax_avatar() {
        $this->render('/Users/ajax_avatar');
    }

    public function ajax_upload() {
        $this->autoRender = false;
        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();
        $allowedExtensions = MooCore::getInstance()->_getPhotoAllowedExtension();

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        $path = 'uploads' . DS . 'tmp';
        $this->_prepareDir($path);
        $result = $uploader->handleUpload(WWW_ROOT . $path);

        if (!empty($result['success'])) {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);

            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $result['filename']);
            $result['photo'] = $path . DS . $result['filename'];
        }

        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function verification() {
        if (!$this->isApp()) {
            $this->redirect('/home');
        }
        
        $this->_checkPermission(array('confirm' => true));
        $this->set('title_for_layout', __d('verify_profile', 'Verification'));

        $sStatus = $this->VerifyProfile->getStatus($this->Auth->user('id'));
        if (($sStatus === FALSE || $sStatus == 'unverified') && Configure::read('VerifyProfile.verify_profile_document_request_verification')) {
            $this->redirect('/profile/verify');
        }
        
        if ($sStatus == 'pending') {
            $this->set('title_for_layout', __d('verify_profile', 'Verification Pending'));
        }

        $this->set('sStatus', $sStatus);
    }
    
    public function verification_by_admin($id) {
        if (!$this->isApp()) {
            $this->redirect('/home');
        }
        
        $iUserId = intval($id);
        $aSUser = $this->User->findById($iUserId);
        $this->_checkPermission(array('admin' => true));
        $sStatus = $this->VerifyProfile->getStatus($iUserId);
        $this->set('title_for_layout', __d('verify_profile', 'Verification'));
        
        if ($sStatus == 'pending') {
            $this->set('title_for_layout', __d('verify_profile', 'Verification Pending'));
        }
        
        if ($sStatus == 'verified') {
            $this->redirect('/profile/verify/success_verify/' . $iUserId);
        }

        $this->set('aSUser', $aSUser);
        $this->set('sStatus', $sStatus);
    }
    
    public function success_verify($id) {
        if (!$this->isApp()) {
            $this->redirect('/home');
        }
        
        $iUserId = intval($id);
        $aSUser = $this->User->findById($iUserId);
        $this->_checkPermission(array('confirm' => true));
        $this->set('title_for_layout', __d('verify_profile', 'Verification'));
        
        $this->set('aSUser', $aSUser);
    }
    
    public function deny_verification($id) {
        if (!$this->isApp()) {
            $this->redirect('/home');
        }
        
        $iUserId = intval($id);
        $aSUser = $this->User->findById($iUserId);
        $this->_checkPermission(array('confirm' => true));
        $this->set('title_for_layout', __d('verify_profile', 'Verification'));
        
        $this->set('aSUser', $aSUser);
    }
    
    public function success_verification() {
        if (!$this->isApp()) {
            $this->redirect('/home');
        }
        
        $this->_checkPermission(array('confirm' => true));
        $this->set('title_for_layout', __d('verify_profile', 'Verification'));
    }

    private function _adminNotification() {
        $uid = $this->Auth->user('id');
        $cuser = $this->_getUser();

        // Notification to admin in admincp
        $this->loadModel('AdminNotification');
        $this->AdminNotification->save(array(
            'user_id' => $uid,
            'url' => $cuser['moo_href'],
            'text' => __d('verify_profile', 'requested to verify profile!'),
        ));

        // Notification to admin
        $aData = array();
        $this->loadModel('Notification');
        $aUsers = $this->User->find('all', array('conditions' => array('Role.is_super' => 1)));
        foreach ($aUsers as $aUser) {
            $aData[] = array(
                'sender_id' => $uid,
                'user_id' => $aUser['User']['id'],
                'action' => 'verify_profile_request',
                'plugin' => 'VerifyProfile',
                'url' => $cuser['moo_url'],
                'params' => ''
            );
        }
        $this->Notification->saveAll($aData);
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

}
