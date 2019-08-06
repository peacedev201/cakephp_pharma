<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class RoleBadgePluginsController extends RoleBadgeAppController {

    public $paginate = array(
        'limit' => RESULTS_LIMIT
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('RoleBadge.RoleBadge');
    }

    public function admin_index() {
        $aRoleBadges = $this->paginate('RoleBadge');
        $this->set('aRoleBadges', $aRoleBadges);
        $this->set('title_for_layout', __d('role_badge', 'User Badges'));
    }

    public function admin_create($id = null) {
        $iBadgeId = intval($id);
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($iBadgeId)) {
            $aRoleBadge = $this->RoleBadge->findById($iBadgeId);
            $this->_checkExistence($aRoleBadge);
        } else {
            $aRoleBadge = $this->RoleBadge->initFields();
            $aRoleId = $this->RoleBadge->find('list', array('fields' => array('role_id')));
        }

        $aCond = array();
        if (!empty($aRoleId)) {
            $aCond = array('Role.id NOT IN (' . implode(', ', $aRoleId) . ')');
        }

        $this->loadModel('Role');
        $aRoles = $this->Role->find('list', array('conditions' => $aCond, 'fields' => array('Role.id', 'Role.name')));

        $this->set('aRoles', $aRoles);
        $this->set('aRoleBadge', $aRoleBadge);
    }

    public function admin_save_validate() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $this->RoleBadge->set($this->request->data);
        $this->_validateData($this->RoleBadge);

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_save() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($this->request->data['id'])) {
            $this->RoleBadge->id = $this->request->data['id'];
        }

        if (!empty($this->request->data['thumbnail_default'])) {
            $this->loadModel('Role');
            $aRole = $this->Role->findById($this->request->data['role_id']);

            $sDesktopProfile = 'member_desktop_profile_60b2b1ba9ad018a5a443f4e68cd2f077.png';
            $sDesktopFeed = 'member_desktop_feed_60b2b1ba9ad018a5a443f4e68cd2f077.png';
            $sMobileProfile = 'member_mobile_profile_60b2b1ba9ad018a5a443f4e68cd2f077.png';
            $sMobileFeed = 'member_mobile_feed_60b2b1ba9ad018a5a443f4e68cd2f077.png';

            if ($aRole['Role']['is_admin']) {
                $sDesktopProfile = 'admin_desktop_profile_4aff2da9ad018a5a443er546cd2f077.png';
                $sDesktopFeed = 'admin_desktop_feed_4aff2da9ad018a5a443er546cd2f077.png';
                $sMobileProfile = 'admin_mobile_profile_4aff2da9ad018a5a443er546cd2f077.png';
                $sMobileFeed = 'admin_mobile_feed_4aff2da9ad018a5a443er546cd2f077.png';
            } else if ($aRole['Role']['id'] == ROLE_GUEST) {
                $sDesktopProfile = 'guest_default_31732dc2a4aff2da4b8ac9a036f60e77.png';
                $sDesktopFeed = 'guest_default_31732dc2a4aff2da4b8ac9a036f60e77.png';
                $sMobileProfile = 'guest_default_31732dc2a4aff2da4b8ac9a036f60e77.png';
                $sMobileFeed = 'guest_default_31732dc2a4aff2da4b8ac9a036f60e77.png';
            }

            $this->request->data['desktop_profile'] = $sDesktopProfile;
            $this->request->data['desktop_feed'] = $sDesktopFeed;
            $this->request->data['mobile_profile'] = $sMobileProfile;
            $this->request->data['mobile_feed'] = $sMobileFeed;
        } else if (!empty($_FILES['desktop_profile']) || !empty($_FILES['desktop_feed']) || !empty($_FILES['mobile_profile']) || !empty($_FILES['mobile_feed'])) {
            $sPath = 'uploads' . DS . 'tmp';
            $this->_prepareDir($sPath);
            $maxFileSize = MooCore::getInstance()->_getMaxFileSize();
            $allowedExtensions = MooCore::getInstance()->_getPhotoAllowedExtension();

            $sPathUpload = 'role_badge' . DS . 'img' . DS . 'setting';
            $this->_prepareDir($sPathUpload);

            App::import('Vendor', 'qqFileUploader');

            if (!empty($_FILES['desktop_profile'])) {
                $oUploaderDesktopProfile = new qqFileUploader($allowedExtensions, $maxFileSize, 'desktop_profile');
                $aResultDesktopProfile = $oUploaderDesktopProfile->handleUpload($sPath);
                if (!empty($aResultDesktopProfile['success'])) {
                    App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                    $oPhoto = PhpThumbFactory::create($sPath . DS . $aResultDesktopProfile['filename']);

                    $oPhoto->resize(9999, 26)->save($sPathUpload . DS . $aResultDesktopProfile['filename']);
                    $this->request->data['desktop_profile'] = $aResultDesktopProfile['filename'];
                }
            }

            if (!empty($_FILES['desktop_feed'])) {
                $oUploaderDesktopFeed = new qqFileUploader($allowedExtensions, $maxFileSize, 'desktop_feed');
                $aResultDesktopFeed = $oUploaderDesktopFeed->handleUpload($sPath);
                if (!empty($aResultDesktopFeed['success'])) {
                    App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                    $oPhoto = PhpThumbFactory::create($sPath . DS . $aResultDesktopFeed['filename']);

                    $oPhoto->resize(9999, 14)->save($sPathUpload . DS . $aResultDesktopFeed['filename']);
                    $this->request->data['desktop_feed'] = $aResultDesktopFeed['filename'];
                }
            }

            if (!empty($_FILES['mobile_profile'])) {
                $oUploaderMobileProfile = new qqFileUploader($allowedExtensions, $maxFileSize, 'mobile_profile');
                $aResultMobileProfile = $oUploaderMobileProfile->handleUpload($sPath);
                if (!empty($aResultMobileProfile['success'])) {
                    App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                    $oPhoto = PhpThumbFactory::create($sPath . DS . $aResultMobileProfile['filename']);

                    $oPhoto->resize(9999, 50)->save($sPathUpload . DS . $aResultMobileProfile['filename']);
                    $this->request->data['mobile_profile'] = $aResultMobileProfile['filename'];
                }
            }

            if (!empty($_FILES['mobile_feed'])) {
                $oUploaderMobileFeed = new qqFileUploader($allowedExtensions, $maxFileSize, 'mobile_feed');
                $aResultMobileFeed = $oUploaderMobileFeed->handleUpload($sPath);
                if (!empty($aResultMobileFeed['success'])) {
                    App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                    $oPhoto = PhpThumbFactory::create($sPath . DS . $aResultMobileFeed['filename']);

                    $oPhoto->resize(9999, 28)->save($sPathUpload . DS . $aResultMobileFeed['filename']);
                    $this->request->data['mobile_feed'] = $aResultMobileFeed['filename'];
                }
            }
        }

        if (!empty($this->request->data['desktop_profile']) && !empty($this->request->data['desktop_feed']) && !empty($this->request->data['mobile_profile']) && !empty($this->request->data['mobile_feed'])) {
            $this->RoleBadge->set($this->request->data);
            $this->RoleBadge->save();

            $this->Session->setFlash(__d('role_badge', 'Badge has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        } else if (empty($this->request->data['id'])) {
            $this->Session->setFlash(__d('role_badge', 'Icon is not upload or failed in process'), 'default', array('class' => 'Metronic-alerts alert alert-danger'), 'flash');
        }

        $this->redirect(Router::url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_index'), true));
    }

    public function admin_delete($id) {
        $iBadgeId = intval($id);
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $aRoleBadge = $this->RoleBadge->findById($iBadgeId);
        $this->_checkExistence($aRoleBadge);

        $this->RoleBadge->delete($id);

        $this->Session->setFlash(__d('role_badge', 'Badge has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect(Router::url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_index'), true));
    }

    public function admin_multi_delete() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($_POST['role_badges'])) {
            foreach ($_POST['role_badges'] as $iReasonId) {
                $this->RoleBadge->delete($iReasonId);
            }
            $this->Session->setFlash(__d('role_badge', 'Badges has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect(Router::url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_index'), true));
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

}
