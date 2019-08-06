<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

require_once APP_PATH . DS . 'Plugin' . DS . 'UploadVideo' . DS . 'vendor' . DS . 'vimeo' . DS . 'autoload.php';

class UploadVideosController extends UploadVideoAppController {

    public $paginate = array(
        'limit' => RESULTS_LIMIT,
        'findType' => 'translated',
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Video.Video');
        $this->loadModel('Category');
        $this->loadModel('Friend');
        $this->loadModel('Tag');
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
    }

    public function index() {
        
    }
    
    public function limitation() {
        $this->_checkPermission(array('confirm' => true));
        $oHelperSubscription = MooCore::getInstance()->getHelper('Subscription_Subscription');
        
        $bSubscribe = false;
        $aUser = MooCore::getInstance()->getViewer();
        if ($oHelperSubscription->checkEnableSubscription() && $aUser['Role']['is_super'] != 1) {
            $this->loadModel('Subscription.Subscribe');
            $aSubscribe = $this->Subscribe->find('first', array('conditions' => array('Subscribe.user_id' => $this->Auth->user('id'), 'Subscribe.active' => 1), 'limit' => 1));

            if (!empty($aSubscribe)) {
                $bSubscribe = true;
            }
        }
        
        $this->set('bSubscribe', $bSubscribe);
        $bFeed = !empty($this->request->query('isFeed')) ? true : false;
        
        if ($this->isApp() && $bFeed) {
            $this->render('UploadVideo.UploadVideos/limitation_app_feed');
        }
    }

    public function ajax_upload() {
        $this->_checkPermission(array('aco' => 'video_upload'));

        // Check Limitation
        $aUser = MooCore::getInstance()->getViewer();
        $this->loadModel('UploadVideo.UploadVideoLimitations');
        if ($this->UploadVideoLimitations->checkLimitation($aUser)) {
            $this->redirect(array('plugin' => 'upload_video', 'controller' => 'upload_videos', 'action' => 'limitation'));
        }

        $categories = $this->Category->getCategoriesList('Video');
        $this->set('categories', $categories);
    }

    public function ajax_upload_group($group_id = null) {
        if (empty($group_id)) {
            exit;
        }

        $this->loadModel('Group.Group');
        $aGroup = $this->Group->findById($group_id);
        $this->_checkExistence($aGroup);

        $this->_checkPermission(array('aco' => 'video_upload'));

        // Check Limitation
        $aUser = MooCore::getInstance()->getViewer();
        $this->loadModel('UploadVideo.UploadVideoLimitations');
        if ($this->UploadVideoLimitations->checkLimitation($aUser)) {
            $this->redirect(array('plugin' => 'upload_video', 'controller' => 'upload_videos', 'action' => 'limitation'));
        }

        $categories = $this->Category->getCategoriesList('Video');
        $this->set(compact('group_id', 'categories'));
    }

    public function process_upload() {
        $this->autoRender = false;
        
        // Check Limitation
        $aUser = MooCore::getInstance()->getViewer();
        $this->loadModel('UploadVideo.UploadVideoLimitations');
        if ($this->UploadVideoLimitations->checkLimitation($aUser)) {
            echo json_encode(array('limitation' => 1));
            exit();
        }
        
        $videoSizeLimit = $this->_getVideoSizeLimit();
        $videoExtensions = MooCore::getInstance()->_getVideoAllowedExtension();

        App::import('Vendor', 'qqFileUploader');
        $oUploader = new qqFileUploader($videoExtensions, $videoSizeLimit);

        $sPathTmp = 'uploads' . DS . 'tmp';
        $this->_prepareDir($sPathTmp);

        $aResult = $oUploader->handleUpload(WWW_ROOT . $sPathTmp);

        if (!empty($aResult['success'])) {
            $sOriginalPath = WWW_ROOT . $sPathTmp . DS . $aResult['filename'];
            $aPathInfo = pathinfo($sOriginalPath);
            $sFileName = $aPathInfo['filename'];

            $sThumbPath = $sPathTmp . DS . $sFileName . '.jpg';
            $this->_convert_thumbnail_ffmpeg(WWW_ROOT . $sThumbPath, $sOriginalPath, true);

            $aResult['thumb'] = FULL_BASE_URL . $this->request->webroot . 'uploads/tmp/' . $sFileName . '.jpg';
        }

        echo htmlspecialchars(json_encode($aResult), ENT_NOQUOTES);
    }

    public function save_upload() {
        $this->autoRender = false;
        $iUserId = $this->Auth->user('id');
        $this->_checkPermission(array('confirm' => true));

        $this->request->data['user_id'] = $iUserId;
        $this->request->data['in_process'] = 1;
        $this->request->data['pc_upload'] = 1;

        if (!empty($this->request->data['group_id'])) {
            // find group
            $this->loadModel('Group.Group');
            $aGroup = $this->Group->findById($this->request->data['group_id']);
            $iGroupPrivacy = isset($aGroup['Group']['type']) ? $aGroup['Group']['type'] : PRIVACY_EVERYONE;

            $this->request->data['category_id'] = 0;
            $this->request->data['type'] = 'Group_Group';
            $this->request->data['target_id'] = $this->request->data['group_id'];
            $this->request->data['privacy'] = ($iGroupPrivacy == 3) ? 1 : $iGroupPrivacy;
        }

        $sOriginalPath = WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['destination'];
        $aPathInfo = pathinfo($sOriginalPath);

        $this->request->data['thumb'] = 'uploads' . DS . 'tmp' . DS . $aPathInfo['filename'] . '.jpg';
        $this->Video->set($this->request->data);

        // validate data
        $this->_validateData($this->Video);
        $this->Video->Behaviors->disable('Activity');
        if ($this->Video->save()) {
            $aVideo = $this->Video->read();

            $oCakeEvent = new CakeEvent('Plugin.Controller.Video.afterSave', $this, array(
                'uid' => $iUserId,
                'id' => $aVideo['Video']['id'],
                'privacy' => $aVideo['Video']['privacy']
            ));
            $this->getEventManager()->dispatch($oCakeEvent);

            // Prepare dir
            $sVideoPath = 'uploads' . DS . 'videos' . DS . 'thumb' . DS . $aVideo['Video']['id'];
            $this->_prepareDir($sVideoPath);

            $sTmpPath = WWW_ROOT . 'uploads' . DS . 'tmp';
            rename($sTmpPath . DS . $aVideo['Video']['destination'], WWW_ROOT . $sVideoPath . DS . $aVideo['Video']['destination']);

            $response['result'] = 1;
            echo json_encode($response);
        }
    }

    public function successfully() {
        if (!$this->isApp()) {
            $this->redirect('/home');
        }

        $this->_checkPermission(array('confirm' => true));
        $this->set('title_for_layout', __('Upload Video'));
    }

    public function admin_index() {
        $cond = array();
        if (!empty($this->request->data['keyword'])) {
            $cond['MATCH(Video.title) AGAINST(? IN BOOLEAN MODE)'] = $this->request->data['keyword'];
        }

        $cond['Video.pc_upload'] = 1;
        $videos = $this->paginate('Video', $cond);
        $categories = $this->Category->getCategoriesList('Video');

        $this->set('videos', $videos);
        $this->set('categories', $categories);
        $this->set('title_for_layout', __d('upload_video', 'Upload Videos Manager'));
    }

    public function admin_delete($id = null) {
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($_POST['videos'])) {
            $videos = $this->Video->findAllById($_POST['videos']);

            foreach ($videos as $video) {
                $this->Video->deleteVideo($video);

                $cakeEvent = new CakeEvent('Plugin.Controller.Video.afterDeleteVideo', $this, array('item' => $video));
                $this->getEventManager()->dispatch($cakeEvent);
            }

            $this->Session->setFlash(__('Videos have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect(array('plugin' => 'upload_video', 'controller' => 'upload_videos', 'action' => 'admin_index'));
    }

    private function _getVideoSizeLimit() {
        $iPostMax = $this->__return_bytes((ini_get('post_max_size')) == -1 ? '9999M' : ini_get('post_max_size'));
        $iMemoryLimit = $this->__return_bytes((ini_get('memory_limit')) == -1 ? '9999M' : ini_get('memory_limit'));
        $iUploadMax = $this->__return_bytes((ini_get('upload_max_filesize')) == -1 ? '9999M' : ini_get('upload_max_filesize'));

        $aUser = MooCore::getInstance()->getViewer();
        $oVideoLimitationModel = MooCore::getInstance()->getModel('UploadVideo.UploadVideoLimitations');
        $aLimitation = $oVideoLimitationModel->findByRoleId($aUser['Role']['id']);

        if (!empty($aLimitation)) {
            if (!empty($aLimitation['UploadVideoLimitations']['size'])) {
                $sSetting = $aLimitation['UploadVideoLimitations']['size'] . 'M';
                $iSetting = $this->__return_bytes($sSetting);
            } else {
                return min($iPostMax, $iMemoryLimit, $iUploadMax);
            }
        } else {
            $sSetting = Configure::read('UploadVideo.video_common_setting_max_upload') . 'M';
            $iSetting = $this->__return_bytes($sSetting);
        }

        return min($iPostMax, $iMemoryLimit, $iUploadMax, $iSetting);
    }

    private function __return_bytes($valMB) {
        $val = trim($valMB);
        $last = strtolower($val[strlen($val) - 1]);
        $number = substr($val, 0, -1);
        switch ($last) {
            case 'g':
                return $number * pow(1024, 3);
            case 'm':
                return $number * pow(1024, 2);
            case 'k':
                return $number * 1024;
            default:
                return $val;
        }
    }

    private function _prepareDir($sPath) {
        $sPath = WWW_ROOT . $sPath;
        if (!file_exists($sPath)) {
            mkdir($sPath, 0755, true);
            file_put_contents($sPath . DS . 'index.html', '');
        }
    }

}
