<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class AwardBadgesController extends RoleBadgeAppController {

    public $paginate = array(
        'limit' => RESULTS_LIMIT
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('RoleBadge.AwardBadge');
    }

    public function add_badge($uid = null) {
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'role_badge_assign_badge'));

        if ($uid == $this->Auth->user('id')) {
            $this->set('msg', __d('role_badge', 'Access denied'));
            echo $this->render('/Elements/error');
            exit;
        }

        $this->loadModel('RoleBadge.AwardUser');
        $aAwardBadges = $this->AwardBadge->find('all');
        $aAwardUsers = $this->AwardUser->find('list', array('fields' => array('AwardUser.award_badge_id'), 'conditions' => array('AwardUser.user_id' => $uid)));

        $this->set('aAwardBadges', $aAwardBadges);
        $this->set('aAwardUsers', $aAwardUsers);
        $this->set('uid', $uid);
    }

    public function add_badge_save() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'role_badge_assign_badge'));

        if ($this->request->is('post')) {
            $aData = $this->request->data;
            if ($aData['user_id'] == $this->Auth->user('id')) {
                exit;
            }

            $this->loadModel('RoleBadge.AwardUser');
            $aAwardUsers = $this->AwardUser->findByUserId($aData['user_id']);
            if (!empty($aAwardUsers)) {
                $this->AwardUser->deleteAll(array('AwardUser.user_id' => $aData['user_id']), false);
            }

            if ($aData['award_badge_id']) {
                foreach ($aData['award_badge_id'] as $iAwardBadgeId) {
                    $this->AwardUser->create();
                    $this->AwardUser->save(array(
                        'user_id' => $aData['user_id'],
                        'award_badge_id' => $iAwardBadgeId
                    ));
                }
            }

            $this->loadModel('Notification');
            $this->Notification->record(array('recipients' => $aData['user_id'],
                'url' => '/users/view/' . $aData['user_id'],
                'sender_id' => $this->Auth->user('id'),
                'action' => 'award_assign',
                'plugin' => 'RoleBadge'
            ));
        }
    }
    
    public function profile($id = null){
        $iUserId = intval($id);
        $this->_checkPermission(array('confirm' => true));
        
        
        $aCurrentUser = $this->_getUser(); //$aCurrentUser['id']
        $aObjectUser = $this->User->findById($iUserId); //$aObjectUser['User']['id']

        $this->loadModel('RoleBadge.AwardUser');
        $aUserBadges = $this->AwardUser->getProfileBadges($iUserId);

        $bShowAssign = true;
        $aCurrentUserAcos = $this->_getUserRoleParams();
        if (empty($aObjectUser) || empty($aCurrentUser) || (!in_array('role_badge_assign_badge', $aCurrentUserAcos) && !$aCurrentUser['Role']['is_admin']) || (!empty($aCurrentUser) && $aCurrentUser['id'] == $aObjectUser['User']['id'])) {
            $bShowAssign = false;
        }

        $this->set('iUserId', $iUserId);
        $this->set('aUserBadges', $aUserBadges);
        $this->set('bShowAssign', $bShowAssign);
        
        // Render Widget
        $this->render('RoleBadge.Widgets/badges/award');
    }

    public function admin_index() {
        $aAwardBadges = $this->paginate('AwardBadge');
        $this->set('aAwardBadges', $aAwardBadges);

        $this->set('title_for_layout', __d('role_badge', 'Awards Manager'));
    }

    public function admin_create($id = null) {
        $iAwardBadgeId = intval($id);

        if (!empty($iAwardBadgeId)) {
            $aAwardBadge = $this->AwardBadge->findById($iAwardBadgeId);
            $this->_checkExistence($aAwardBadge);
        } else {
            $aAwardBadge = $this->AwardBadge->initFields();
        }

        $this->set('aAwardBadge', $aAwardBadge);
    }

    public function admin_save_validate() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $this->AwardBadge->set($this->request->data);
        $this->_validateData($this->AwardBadge);

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_save() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($this->request->data['id'])) {
            $this->AwardBadge->id = $this->request->data['id'];
        }

        if (!empty($_FILES['thumbnail'])) {

            $sPath = 'uploads' . DS . 'tmp';
            $this->_prepareDir($sPath);
            $maxFileSize = MooCore::getInstance()->_getMaxFileSize();
            $allowedExtensions = MooCore::getInstance()->_getPhotoAllowedExtension();

            $sPathUpload = 'role_badge' . DS . 'img' . DS . 'setting';
            $this->_prepareDir($sPathUpload);

            App::import('Vendor', 'qqFileUploader');
            $oUploader = new qqFileUploader($allowedExtensions, $maxFileSize, 'thumbnail');

            $result = $oUploader->handleUpload($sPath);
            if (!empty($result['success'])) {
                App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                $oPhoto = PhpThumbFactory::create($sPath . DS . $result['filename']);

                $oPhoto->resize(9999, 50)->save($sPathUpload . DS . $result['filename']);
                $this->request->data['thumbnail'] = $result['filename'];
            }
        }

        if (!empty($this->request->data['thumbnail'])) {
            $this->AwardBadge->set($this->request->data);
            $this->AwardBadge->save();

            if (empty($this->request->data['id'])) {
                foreach (array_keys($this->Language->getLanguages()) as $sKey) {
                    $this->AwardBadge->locale = $sKey;
                    $this->AwardBadge->save(array('name' => $this->request->data['name'], 'description' => $this->request->data['description']));
                }
            }

            $this->Session->setFlash(__d('role_badge', 'Award has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        } else if (empty($this->request->data['id'])) {
            $this->Session->setFlash(__d('role_badge', 'Icon is not upload or failed in process'), 'default', array('class' => 'Metronic-alerts alert alert-danger'), 'flash');
        }

        $this->redirect(Router::url(array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_index'), true));
    }

    public function admin_delete($id) {
        $iAwardBadgeId = intval($id);
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $aAwardBadge = $this->AwardBadge->findById($iAwardBadgeId);
        $this->_checkExistence($aAwardBadge);
        
        $this->AwardBadge->deleteAwardBadge($iAwardBadgeId);
        $this->AwardBadge->clear();

        $this->Session->setFlash(__d('role_badge', 'Award has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect(Router::url(array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_index'), true));
    }

    public function admin_ajax_translate($field = 'name', $id = null) {
        if (!empty($id)) {
            $iAwardBadgeId = intval($id);
            $aAwardBadge = $this->AwardBadge->findById($iAwardBadgeId);

            $this->set('field', $field);
            $this->set('aAwardBadge', $aAwardBadge);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }

    public function admin_ajax_translate_save() {
        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                $sContentDb = '';
                $this->AwardBadge->id = $this->request->data['id'];
                foreach ($this->request->data[$this->request->data['field']] as $sKey => $sContent) {
                    if (!empty($sContent)) {
                        $this->AwardBadge->locale = $sKey;
                        
                        if ($sKey == Configure::read('Config.language')) {
                            $sContentDb = $sContent;
                        }
                        
                        if ($this->AwardBadge->saveField($this->request->data['field'], $sContent)) {
                            $response['result'] = 1;
                        } else {
                            $response['result'] = 0;
                        }
                    } else {
                        $response['result'] = 0;
                    }
                }
                
                if (empty($sContentDb)) {
                    $this->AwardBadge->saveField($this->request->data['field'], $sContentDb);
                }
            } else {
                $response['result'] = 0;
            }
        } else {
            $response['result'] = 0;
        }

        if (empty($response['result'])) {
            $response['message'] = __d('role_badge', 'Type input invalid');
        }

        echo json_encode($response);
    }

    public function admin_save_order() {
        $this->autoRender = false;
        foreach ($this->request->data['adwardIds'] as $iAdwardId => $iWeight) {
            $this->AwardBadge->id = $iAdwardId;
            $this->AwardBadge->saveField('weight', $iWeight);
        }

        $this->Session->setFlash(__d('role_badge', 'Order saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

}
