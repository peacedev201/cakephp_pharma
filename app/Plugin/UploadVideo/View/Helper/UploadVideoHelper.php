<?php

App::uses('AppHelper', 'View/Helper');

class UploadVideoHelper extends AppHelper {

    public function __construct(\View $View, $settings = array()) {
        if ($this->checkStorageEnabled()) {
            $this->helpers = array('Storage.Storage');
        }

        parent::__construct($View, $settings);
    }

    public function getEnable() {
        return Configure::read('UploadVideo.uploadvideo_enabled');
    }

    public function getVideo($aVideo) {
        $sPath = 'uploads/videos/thumb/' . $aVideo['Video']['id'] . '/' . $aVideo['Video']['destination'];
        
        if ($this->checkStorageEnabled()) {
            $url = $this->Storage->getUrl($aVideo['Video']['id'], 0, 0, "uploadVideo", array('path' => $sPath));
        } else {
            $request = Router::getRequest();
            $url = FULL_BASE_URL . $request->webroot . $sPath;
        }

        return $url;
    }

    public function checkStorageEnabled() {
        $oModelPlugin = MooCore::getInstance()->getModel('Plugin');
        $aPlugin = $oModelPlugin->findByKey('Storage', array('enabled'));
        if (!empty($aPlugin)) {
            return true;
        }

        return false;
    }

}
