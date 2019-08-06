<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class VerifyProfileReasonsController extends VerifyProfileAppController {

    public $paginate = array(
        'limit' => RESULTS_LIMIT
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('VerifyProfile.VerifyReason');
    }

    public function admin_index() {
        $aReasons = $this->paginate('VerifyReason');
        $this->set('aReasons', $aReasons);
        $this->set('title_for_layout', __d('verify_profile', 'Profile Verify Reasons'));
    }

    public function admin_create($id) {
        $aReason = array();
        $bIsEdit = false;
        if (!empty($id)) {
            $bIsEdit = true;
            $aReason = $this->VerifyReason->findById($id);
        }

        $this->set('bIsEdit', $bIsEdit);
        $this->set('aReason', $aReason);
    }

    public function admin_save() {
        $this->autoRender = false;
        if (!empty($this->request->data['id'])) {
            $this->VerifyReason->id = $this->request->data['id'];
        }

        $this->VerifyReason->set($this->request->data);
        $this->_validateData($this->VerifyReason);
        $aResponse['message'] = __d('verify_profile', 'Error');
        if ($this->VerifyReason->save()) {
            $aResponse['result'] = 1;
            $this->Session->setFlash(__d('verify_profile', 'Reason has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        echo json_encode($aResponse);
    }

    public function admin_delete($id) {
        $this->autoRender = false;
        if (!empty($id)) {
            $aReason = $this->VerifyReason->findById($id);
            if (!empty($aReason)) {
                $this->VerifyReason->delete($id);
                $this->Session->setFlash(__d('verify_profile', 'Reason has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            }
        }

        $this->redirect(array(
            'plugin' => 'verify_profile',
            'controller' => 'verify_profile_reasons',
            'action' => 'admin_index'
        ));
    }

    public function admin_multi_delete() {
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($_POST['reasons'])) {
            foreach ($_POST['reasons'] as $iReasonId) {
                $this->VerifyReason->delete($iReasonId);
            }
            $this->Session->setFlash(__d('verify_profile', 'Reasons have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect(array(
            'plugin' => 'verify_profile',
            'controller' => 'verify_profile_reasons',
            'action' => 'admin_index'
        ));
    }

}
