<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

require_once APP_PATH . DS . 'Plugin' . DS . 'UploadVideo' . DS . 'vendor' . DS . 'vimeo' . DS . 'autoload.php';

class UploadVideoCronsController extends UploadVideoAppController {

    public $check_subscription = false;
    public $check_force_login = false;

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Video.Video');
        $this->loadModel('UploadVideo.UploadVideoProcess');
    }

    public function run() {
        $this->autoRender = false;
        $iTimeStart = microtime(true);

        set_time_limit(0);
        $sPrevAbort = ignore_user_abort(true);

        $aVideoId = $this->UploadVideoProcess->find('list', array('fields' => array('UploadVideoProcess.video_id')));

        $aVideoCond = array('Video.in_process' => 1, 'Video.pc_upload' => 1);
        if (!empty($aVideoId)) {
            $aVideoCond['NOT'] = array('Video.id' => $aVideoId);
        }

        $aVideo = $this->Video->find('first', array('conditions' => $aVideoCond, 'order' => 'Video.id'));

        if (!empty($aVideo)) {
            // Inject current process identifier
            $this->UploadVideoProcess->create();
            $this->UploadVideoProcess->set(array(
                'video_id' => (int) $aVideo['Video']['id'],
                'system_pid' => (int) (function_exists('posix_getpid') ? posix_getpid() : 0),
                'started' => time(),
            ));

            if ($this->UploadVideoProcess->save()) {
                if (Configure::check('UploadVideo.vimeo_upload') && Configure::read('UploadVideo.vimeo_upload')) {
                    $this->_proceess_vimeo($aVideo);
                } else {
                    $this->_proceess_ffmpeg($aVideo);
                }

                // Remove process finished
                $this->UploadVideoProcess->deleteAll(array('UploadVideoProcess.video_id' => $aVideo['Video']['id']));
            }
        }

        $iTimeEnd = microtime(true);
        $iTimeRun = $iTimeEnd - $iTimeStart;

        // Restore abort
        ignore_user_abort($sPrevAbort);
        echo 'Cron successfull on: ' . $iTimeRun;
    }

    protected function _proceess_vimeo($aVideo) {
        $sClientId = Configure::read('UploadVideo.vimeo_key');
        $sClientSecret = Configure::read('UploadVideo.vimeo_secret');
        $sAccessToken = Configure::read('UploadVideo.vimeo_access_token');
        $sOriginalPath = 'uploads' . DS . 'videos' . DS . 'thumb' . DS . $aVideo['Video']['id'] . DS . $aVideo['Video']['destination'];

        $oVimeo = new \Vimeo\Vimeo($sClientId, $sClientSecret);

        $oVimeo->setToken($sAccessToken);
        $aResponse = $oVimeo->upload($sOriginalPath, false);

        if (!empty($aResponse['moo_error'])) {
            $this->log(print_r($aResponse['moo_error'], true));
            return false;
        }

        $aVimeoInfo = $oVimeo->request($aResponse['vimeo_uri']);
        $sUri = isset($aVimeoInfo['body']['uri']) ? $aVimeoInfo['body']['uri'] : '';
        $iVimeoId = str_replace("/videos/", "", $sUri);

        $this->Video->clear();
        $this->Video->id = $aVideo['Video']['id'];
        $this->Video->set(array(
            'thumb' => '',
            'pc_upload' => 0,
            'in_process' => 1,
            'source' => 'vimeo',
            'source_id' => $iVimeoId,
        ));

        $this->Video->save();
        $iAffected = $this->Video->getAffectedRows();

        if (1 !== $iAffected) {
            // Log
            if (Configure::read('debug')) {
                $this->log(sprintf('Execution Failed Video [%d]', $aVideo['Video']['id']));
            }
            return false;
        }

        // Remove video uploaded
        unlink(WWW_ROOT . $sOriginalPath);
    }

    protected function _proceess_ffmpeg($aVideo) {
        $sNameVideo = md5($aVideo['Video']['title'] . $aVideo['Video']['id']);
        $sVideoPath = 'uploads' . DS . 'videos' . DS . 'thumb' . DS . $aVideo['Video']['id'];
        $this->_prepareDir($sVideoPath);
        
        $sOutputPath = WWW_ROOT . $sVideoPath . DS . $sNameVideo . ".mp4";
        $sOriginalPath = WWW_ROOT . $sVideoPath . DS . $aVideo['Video']['destination'];

        // Convet and get original thumbnail
        $this->_convert_video_ffmpeg($sOutputPath, $sOriginalPath);
        $this->_convert_thumbnail_ffmpeg(WWW_ROOT . $sVideoPath . DS . $aVideo['Video']['thumb'], $sOriginalPath);

        // Remove video uploaded
        unlink($sOriginalPath);

        // Update data
        $this->Video->id = $aVideo['Video']['id'];
        $this->Video->set(array('in_process' => 0, 'destination' => $sNameVideo . ".mp4"));

        $this->Video->save();
        $iAffected = $this->Video->getAffectedRows();

        if (1 !== $iAffected) {
            // Log
            if (Configure::read('debug')) {
                $this->log(sprintf('Execution Failed Video [%d]', $aVideo['Video']['id']));
            }
            return false;
        }

        $this->loadModel('Activity');
        $this->loadModel('Notification');
        $aActivity = $this->Activity->find('first', array('conditions' => array('Activity.item_type' => 'Video_Video', 'Activity.item_id' => $aVideo['Video']['id'])));

        if (empty($aActivity)) {
            // Create Activity
            $aData = array(
                'type' => 'user',
                'action' => 'video_create',
                'item_type' => 'Video_Video',
                'item_id' => $aVideo['Video']['id'],
                'privacy' => $aVideo['Video']['privacy'],
                'user_id' => $aVideo['Video']['user_id'],
                'plugin' => 'Video',
                'params' => 'item',
                'share' => true,
            );

            if (!empty($aVideo['Video']['group_id'])) {
                $this->loadModel('Group.Group');
                $aGroup = $this->Group->findById($aVideo['Video']['group_id']);
                if (!empty($aGroup) && $aGroup['Group']['type'] == PRIVACY_PRIVATE) {
                    $aData['share'] = false;
                }

                $aData['target_id'] = $aVideo['Video']['group_id'];
                $aData['type'] = 'Group_Group';
            }

            $this->Activity->create();
            $this->Activity->save($aData);

            $this->Notification->record(array(
                'action' => 'converting_video_succeed',
                'sender_id' => $aVideo['Video']['user_id'],
                'recipients' => $aVideo['Video']['user_id'],
                'url' => $aVideo['Video']['moo_url'],
                'plugin' => 'UploadVideo'
            ));
        } else {
            $this->Activity->clear();
            $this->Activity->updateAll(array('Activity.status' => '"' . ACTIVITY_OK . '"'), array('Activity.id' => $aActivity['Activity']['id']));

            $this->Notification->record(array(
                'action' => 'converting_video_succeed',
                'sender_id' => $aVideo['Video']['user_id'],
                'recipients' => $aVideo['Video']['user_id'],
                'url' => '/users/view/' . $aVideo['Video']['user_id'] . '/activity_id:' . $aActivity['Activity']['id'],
                'plugin' => 'UploadVideo'
            ));
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
