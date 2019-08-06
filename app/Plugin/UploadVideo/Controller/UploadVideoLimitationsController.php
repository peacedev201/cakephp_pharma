<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class UploadVideoLimitationsController extends UploadVideoAppController {

    public $paginate = array(
        'limit' => RESULTS_LIMIT
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('UploadVideo.UploadVideoLimitations');
    }

    public function admin_index() {
        $aLimitations = $this->paginate('UploadVideoLimitations');
        $this->set('aLimitations', $aLimitations);
        $this->set('title_for_layout', __d('upload_video', 'Video Upload Limitation'));
    }

    public function admin_create($id = null) {
        $iLimitId = intval($id);
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($iLimitId)) {
            $aLimitation = $this->UploadVideoLimitations->findById($iLimitId);
            $this->_checkExistence($aLimitation);
        } else {
            $aLimitation = $this->UploadVideoLimitations->initFields();
            $aRoleId = $this->UploadVideoLimitations->find('list', array('fields' => array('role_id')));
        }

        $aCond = array();
        if (!empty($aRoleId)) {
            $aCond = array('Role.id NOT IN (' . implode(', ', $aRoleId) . ')');
        }

        $this->loadModel('Role');
        $aRoles = $this->Role->find('list', array('conditions' => $aCond, 'fields' => array('Role.id', 'Role.name')));

        $this->set('aRoles', $aRoles);
        $this->set('aLimitation', $aLimitation);
    }

    public function admin_save_validate() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $this->UploadVideoLimitations->set($this->request->data);
        $this->_validateData($this->UploadVideoLimitations);

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_save() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($this->request->data['id'])) {
            $this->UploadVideoLimitations->id = $this->request->data['id'];
        }

        $this->UploadVideoLimitations->set($this->request->data);
        $this->UploadVideoLimitations->save();

        $this->Session->setFlash(__d('upload_video', 'Limitation has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect(Router::url(array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_index'), true));
    }
    
    public function admin_delete($id) {
        $iLimitId = intval($id);
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $aLimitation = $this->UploadVideoLimitations->findById($iLimitId);
        $this->_checkExistence($aLimitation);

        $this->UploadVideoLimitations->delete($id);

        $this->Session->setFlash(__d('upload_video', 'Limitation has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect(Router::url(array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_index'), true));
    }

}
