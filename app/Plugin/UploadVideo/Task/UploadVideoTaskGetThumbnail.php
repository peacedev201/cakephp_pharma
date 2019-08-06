<?php

App::import('Cron.Task', 'CronTaskAbstract');
require_once APP_PATH . DS . 'Plugin' . DS . 'UploadVideo' . DS . 'vendor' . DS . 'vimeo' . DS . 'autoload.php';

class UploadVideoTaskGetThumbnail extends CronTaskAbstract {

    public function execute() {

        $oVideoModel = MooCore::getInstance()->getModel('Video.Video');
        $oActivityModel = MooCore::getInstance()->getModel('Activity');

        $oGroupModel = MooCore::getInstance()->getModel('Group.Group');
        $oNotificationModel = MooCore::getInstance()->getModel('Notification');

        $sClientId = Configure::read('UploadVideo.vimeo_key');
        $sClientSecret = Configure::read('UploadVideo.vimeo_secret');
        $sAccessToken = Configure::read('UploadVideo.vimeo_access_token');

        $oVimeo = new \Vimeo\Vimeo($sClientId, $sClientSecret);
        $oVimeo->setToken($sAccessToken);


        $aVideos = $oVideoModel->find('all', array('conditions' => array('Video.in_process' => 1, 'Video.source' => 'vimeo', 'Video.check_thumb_vimeo' => 0), 'order' => 'Video.id', 'limit' => 5));

        foreach ($aVideos as $aVideo) {
            if (empty($aVideo['Video']['source_id'])) {
                $oVideoModel->deleteVideo($aVideo);
                continue;
            }

            $aVideoResponse = $oVimeo->request('/videos/' . $aVideo['Video']['source_id'], array(), 'GET', false);
            if (empty($aVideoResponse) || $aVideoResponse['body']['status'] != 'available' || $aVideoResponse['body']['pictures']['active'] != true) {
                continue;
            }

            $iCountImage = count($aVideoResponse['body']['pictures']['sizes']);

            $aData = array(
                'thumb' => $aVideoResponse['body']['pictures']['sizes'][$iCountImage - 1]['link'],
                'check_thumb_vimeo' => 1,
                'in_process' => 0,
            );

            $oVideoModel->clear();
            $oVideoModel->id = $aVideo['Video']['id'];

            $oVideoModel->save($aData);
            $aActivity = $oActivityModel->find('first', array('conditions' => array('Activity.item_type' => 'Video_Video', 'Activity.item_id' => $aVideo['Video']['id'])));

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
                    $aGroup = $oGroupModel->findById($aVideo['Video']['group_id']);
                    if (!empty($aGroup) && $aGroup['Group']['type'] == PRIVACY_PRIVATE) {
                        $aData['share'] = false;
                    }

                    $aData['target_id'] = $aVideo['Video']['group_id'];
                    $aData['type'] = 'Group_Group';
                }

                $oActivityModel->create();
                $oActivityModel->save($aData);

                $oNotificationModel->record(array(
                    'action' => 'converting_video_succeed',
                    'sender_id' => $aVideo['Video']['user_id'],
                    'recipients' => $aVideo['Video']['user_id'],
                    'url' => $aVideo['Video']['moo_url'],
                    'plugin' => 'UploadVideo'
                ));
            } else {
                $oActivityModel->clear();
                $oActivityModel->updateAll(array('Activity.status' => '"' . ACTIVITY_OK . '"'), array('Activity.id' => $aActivity['Activity']['id']));

                $oNotificationModel->record(array(
                    'action' => 'converting_video_succeed',
                    'sender_id' => $aVideo['Video']['user_id'],
                    'recipients' => $aVideo['Video']['user_id'],
                    'url' => '/users/view/' . $aVideo['Video']['user_id'] . '/activity_id:' . $aActivity['Activity']['id'],
                    'plugin' => 'UploadVideo'
                ));
            }
        }
    }

}
