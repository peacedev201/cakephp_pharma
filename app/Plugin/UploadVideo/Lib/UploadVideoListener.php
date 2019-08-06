<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('CakeEventListener', 'Event');

class UploadVideoListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'Plugin.Controller.Video.afterDeleteVideo' => 'deleteVideoOnVimeo',
            // version moo-301
            'ApiHelper.renderAFeed.wall_post_video' => 'feedWallPostVideo',
            'Video.View.Elements.groupUploadVideo' => 'renderGroupUploadVideo',
            'Video.View.Elements.uploadVideo' => 'renderUploadVideo',
            'View.Elements.activityForm.afterRenderItems' => 'renderUploadVideoActivityForm',
            'View.Elements.activityForm.renderReviewItems' => 'renderReviewVideoActivityForm',
            'Video.View.Elements.renderVideoUpload' => 'renderVideoUpload',
            'ActivitesController.processVideoUpload' => 'processVideoUpload',
            'View.MooApp.Videos.profile_user_video.checkUploadVideo' => 'checkUploadVideo',
            'View.MooApp.Videos.browse.checkUploadVideo' => 'checkUploadVideo',
        );
    }

    public function checkUploadVideo($oEvent) {
        $aCUser = MooCore::getInstance()->getViewer();
        $aUserAcos = explode(',', $aCUser['Role']['params']);
        if (!empty($aCUser) && in_array('video_upload', $aUserAcos)) {
            $oEvent->result['canUploadVideo'] = 1;
        }
    }

    public function processVideoUpload($oEvent) {
        $aActivity = $oEvent->data['item'];
        $oController = $oEvent->subject();

        if (!empty($aActivity)) {
            $iUserId = MooCore::getInstance()->getViewer(true);
            $oActivityModel = MooCore::getInstance()->getModel('Activity');
            $oVideoModel = MooCore::getInstance()->getModel('Video.Video');
            $aActivityParams = json_decode($aActivity['Activity']['params'], true);

            $sOriginalPath = WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $aActivityParams['video_destination'];
            $aPathInfo = pathinfo($sOriginalPath);

            $aVideoData = array(
                'user_id' => $iUserId,
                'title' => $aActivityParams['title'],
                'category_id' => $aActivityParams['category_id'],
                'thumb' => 'uploads' . DS . 'tmp' . DS . $aPathInfo['filename'] . '.jpg',
                'destination' => $aActivityParams['video_destination'],
                'description' => $aActivityParams['description'],
                'privacy' => $aActivity['Activity']['privacy'],
                'in_process' => 1,
                'pc_upload' => 1,
            );

            if ($aActivity['Activity']['type'] == 'Group_Group') {
                $oGroupModel = MooCore::getInstance()->getModel('Group.Group');
                $aGroup = $oGroupModel->findById($aActivity['Activity']['target_id']);

                $aVideoData['privacy'] = $aGroup['Group']['moo_privacy'];
                $aVideoData['group_id'] = $aActivity['Activity']['target_id'];
            }

            if ($aActivity['Activity']['type'] == 'Event_Event') {
                $oEventModel = MooCore::getInstance()->getModel('Event.Event');
                $aEvent = $oEventModel->findById($aActivity['Activity']['target_id']);

                $aVideoData['privacy'] = $aEvent['Event']['moo_privacy'];
                $aVideoData['group_id'] = $aActivity['Activity']['target_id'];
            }

            $oVideoModel->set($aVideoData);
            $oVideoModel->Behaviors->disable('Activity');
            if ($oVideoModel->save()) {
                $aVideo = $oVideoModel->read();

                $oCakeEvent = new CakeEvent('Plugin.Controller.Video.afterSave', $oController, array(
                    'uid' => $iUserId,
                    'id' => $aVideo['Video']['id'],
                    'privacy' => $aVideo['Video']['privacy']
                ));
                $oController->getEventManager()->dispatch($oCakeEvent);

                // Prepare dir
                $sVideoPath = 'uploads' . DS . 'videos' . DS . 'thumb' . DS . $aVideo['Video']['id'];
                $this->_prepareDir($sVideoPath);

                $sTmpPath = WWW_ROOT . 'uploads' . DS . 'tmp';
                rename($sTmpPath . DS . $aVideo['Video']['destination'], WWW_ROOT . $sVideoPath . DS . $aVideo['Video']['destination']);

                // hack activity to item
                $aDataActivity['Activity.params'] = "'item'";
                $aDataActivity['Activity.plugin'] = "'UploadVideo'";
                $aDataActivity['Activity.action'] = "'wall_post_video'";
                $aDataActivity['Activity.item_id'] = $aVideo['Video']['id'];

                $oActivityModel->clear();
                $oActivityModel->updateAll($aDataActivity, array('Activity.id' => $aActivity['Activity']['id']));
            }
        }
    }

    public function renderVideoUpload($oEvent) {
        $aData = $oEvent->data;
        $oView = $oEvent->subject();
        echo $oView->element('UploadVideo.view/video_snippet', array('video' => $aData['video']));
    }

    public function renderUploadVideoActivityForm($oEvent) {
        $oView = $oEvent->subject();
        $aCUser = MooCore::getInstance()->getViewer();
        $aUserAcos = explode(',', $aCUser['Role']['params']);
        if (!empty($aCUser) && in_array('video_upload', $aUserAcos)) {
            echo $oView->element('UploadVideo.view/activity_form');
        }
    }

    public function renderReviewVideoActivityForm($oEvent) {
        $aData = $oEvent->data;
        $oView = $oEvent->subject();
        $aCUser = MooCore::getInstance()->getViewer();
        $aUserAcos = explode(',', $aCUser['Role']['params']);
        if (!empty($aCUser) && in_array('video_upload', $aUserAcos)) {
            $oCategoryModel = MooCore::getInstance()->getModel('Category');
            $aVideoCategories = $oCategoryModel->getCategoriesList('Video');
            echo $oView->element('UploadVideo.view/review_form', array('type' => $aData['type'], 'target_id' => $aData['target_id'], 'categories' => $aVideoCategories));
        }
    }

    public function feedWallPostVideo($oEvent) {
        $oView = $oEvent->subject();
        $aData = $oEvent->data['data'];
        $sActorHtml = $oEvent->data['actorHtml'];
        $oVideoHelper = MooCore::getInstance()->getHelper('Video_Video');

        $aVideo = $oEvent->data['objectPlugin'];
        $aSubject = MooCore::getInstance()->getItemByType($aData['Activity']['type'], $aData['Activity']['target_id']);

        /* Check Feeling */
        $bFeeling = false;
        if (Configure::check('Feeling.feeling_enabled') && Configure::read('Feeling.feeling_enabled')):
            $oFeelingActivityModel = MooCore::getInstance()->getModel('Feeling.FeelingActivity');
            $aFeeling = $oFeelingActivityModel->get_felling($aData['Activity']);
            if (!empty($aFeeling)):
                $oCategoryModel = MooCore::getInstance()->getModel('Feeling.FeelingCategory');
                $aCategory = $oCategoryModel->findById($aFeeling['Feeling']['category_id']);
                if (!empty($aCategory)):
                    $bFeeling = true;
                endif;
            endif;
        endif;
        /* Check Feeling */

        $sTitle = $sTitleHtml = $aTarget = '';
        if (!empty($aSubject)) {
            list($sPluginName, $sName) = mooPluginSplit($aData['Activity']['type']);
            $bShowSubject = MooCore::getInstance()->checkShowSubjectActivity($aSubject);

            if ($bShowSubject) {
                $sTitle = $aData['User']['name'] . ' > ' . h($aSubject[$sName]['moo_title']);
                $sTitleHtml = $sActorHtml . ' > ' . $oView->Html->link(h($aSubject[$sName]['moo_title']), FULL_BASE_URL . $aSubject[$sName]['moo_href']);
            } else if ($bFeeling) {
                $sTitle = '';
                $sTitleHtml = $sActorHtml;
            } else {
                $sTitle = __('shared a new video');
                $sTitleHtml = $sActorHtml . ' ' . __('shared a new video');
            }

            $aTarget = array(
                'url' => FULL_BASE_URL . $aSubject[$sName]['moo_href'],
                'name' => $aSubject[$sName]['moo_title'],
                'id' => $aSubject[$sName]['id'],
                'type' => 'Group',
            );
        } else if ($bFeeling) {
            $sTitle = '';
            $sTitleHtml = $sActorHtml;
        } else {
            $sTitle = __('shared a new video');
            $sTitleHtml = $sActorHtml . ' ' . __('shared a new video');
        }

        $aVideoObject = array(
            'pcUpload' => 1,
            'source_id' => 0,
            'source' => 'local',
            'videoSource' => $oVideoHelper->getVideo($aVideo),
            'thumb' => $oVideoHelper->getImage($aVideo, array()),
            'title' => h($oView->Text->truncate($aVideo['Video']['title'], 140, array('exact' => false))),
            'description' => h($oView->Text->convert_clickable_links_for_hashtags($oView->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $aVideo['Video']['description'])), 200, array('eclipse' => '')), Configure::read('Video.video_hashtag_enabled'))),
        );

        $sTagUser = '';
        $sContentHtml = nl2br($oView->Text->autoLink($oView->Moo->parseSmilies($aData['Activity']['content']), array_merge(array('target' => '_blank', 'rel' => 'nofollow', 'escape' => false), array('no_replace_ssl' => 1))));
        if (!empty($aData['UserTagging']['users_taggings'])) {
            $sContentHtml = nl2br($oView->Text->autoLink($oView->Moo->parseSmilies($aData['Activity']['content']), array_merge(array('target' => '_blank', 'rel' => 'nofollow', 'escape' => false), array('no_replace_ssl' => 1)))) . $oView->MooPeople->with($aData['UserTagging']['id'], $aData['UserTagging']['users_taggings'], false);
            $sTagUser = $oView->MooPeople->getUserTagged($aData['UserTagging']['users_taggings']);
        }

        $aObject = array(
            'type' => 'Video_Video',
            'id' => $aVideo["Video"]['id'],
            'url' => FULL_BASE_URL . $aVideo['Video']['moo_href'],
            'contentHtml' => $sContentHtml,
            'videoObject' => $aVideoObject,
            'tagUser' => $sTagUser,
        );

        $oEvent->result['result'] = array(
            'action' => 'video_activity',
            'type' => 'create',
            'title' => $sTitle,
            'titleHtml' => $sTitleHtml,
            'objects' => $aObject,
            'target' => $aTarget,
        );
    }

    public function renderGroupUploadVideo($oEvent) {
        $oView = $oEvent->subject();
        $iGroupId = $oEvent->data['group_id'];
        $aCUser = MooCore::getInstance()->getViewer();
        $aUserAcos = explode(',', $aCUser['Role']['params']);
        if (!empty($aCUser) && in_array('video_upload', $aUserAcos)) {
            $oView->MooPopup->tag(array(
                'href' => $oView->Html->url(array("controller" => "upload_videos", "action" => "ajax_upload_group", "plugin" => "upload_video", $iGroupId)),
                'class' => 'button button-action topButton button-mobi-top',
                'innerHtml' => __('Upload Video'),
                'title' => __('Upload Video'),
            ));
        }
    }

    public function renderUploadVideo($oEvent) {
        $oView = $oEvent->subject();
        $aCUser = MooCore::getInstance()->getViewer();
        $aUserAcos = explode(',', $aCUser['Role']['params']);
        if (!empty($aCUser) && in_array('video_upload', $aUserAcos)) {
            echo $oView->element('UploadVideo.view/upload_button');
        }
    }

    public function beforeRender($oEvent) {
        if (Configure::read('UploadVideo.uploadvideo_enabled')) {
            $oView = $oEvent->subject();
            $oView->Helpers->Html->css(array('UploadVideo.main'), array('block' => 'css'));

            $min = "";
            if (Configure::read('debug') == 0) {
                $min = "min.";
            }

            $oView->Helpers->MooRequirejs->addPath(array(
                "mooUploadVideo" => $oView->Helpers->MooRequirejs->assetUrlJS("UploadVideo.js/main.{$min}js")
            ));

            $oView->addPhraseJs(array(
                'upload_video_phrase_0' => __d('upload_video', 'The video in your post is being processed. We will send you a notification when it is done.'),
                'upload_video_phrase_1' => __d('upload_video', 'Your video is uploaded successfully, please standby while we converting your video.'),
                'upload_video_phrase_2' => __d('upload_video', 'Drag or click here to upload video'),
                'upload_video_phrase_3' => __d('upload_video', 'Please select video to upload.'),
                'upload_video_phrase_4' => __d('upload_video', 'Processing Video'),
            ));

            $mooVideoConfig['videoSizeLimit'] = $this->_getVideoSizeLimit();
            $mooVideoConfig['videoExtentsion'] = MooCore::getInstance()->_getVideoAllowedExtension();
            $mooVideoConfig['videoUploadType'] = Configure::read('UploadVideo.vimeo_upload') ? 'vimeo' : 'convert';
            $oView->Helpers->Html->scriptBlock("var mooVideoConfig = " . json_encode($mooVideoConfig, true) . ";", array('inline' => false, 'block' => 'script'));
            
            // init modal
            $oView->Helpers->MooPopup->register('themeModal');
        }
    }

    public function deleteVideoOnVimeo($oEvent) {
        if ($oEvent->data['item']['Video']['source'] == 'vimeo') {
            $sClientId = Configure::read('UploadVideo.vimeo_key');
            $sClientSecret = Configure::read('UploadVideo.vimeo_secret');
            $sAccessToken = Configure::read('UploadVideo.vimeo_access_token');
            require_once APP_PATH . DS . 'Plugin' . DS . 'UploadVideo' . DS . 'vendor' . DS . 'vimeo' . DS . 'autoload.php';

            $oVimeo = new \Vimeo\Vimeo($sClientId, $sClientSecret);
            $oVimeo->setToken($sAccessToken);

            $oVimeo->request('/videos/' . $oEvent->data['item']['Video']['source_id'], array(), 'DELETE', false);
            return true;
        }
    }

    private function _getVideoSizeLimit() {
        $iPostMax = $this->__return_bytes((ini_get('post_max_size')) == -1 ? '9999M' : ini_get('post_max_size'));
        $iMemoryLimit = $this->__return_bytes((ini_get('memory_limit')) == -1 ? '9999M' : ini_get('memory_limit'));
        $iUploadMax = $this->__return_bytes((ini_get('upload_max_filesize')) == -1 ? '9999M' : ini_get('upload_max_filesize'));

        $aUser = MooCore::getInstance()->getViewer();
        $oPluginModel = MooCore::getInstance()->getModel('Plugin');
        
        $aLimitation = array();
        $aPlugin = $oPluginModel->findByKey('UploadVideo');
        
        if (!empty($aPlugin) && $aPlugin['Plugin']['version'] >= '1.7.1') {
            $oVideoLimitationModel = MooCore::getInstance()->getModel('UploadVideo.UploadVideoLimitations');
            $aLimitation = $oVideoLimitationModel->findByRoleId($aUser['Role']['id']);
        }

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
