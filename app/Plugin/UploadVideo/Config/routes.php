<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

if (Configure::check('UploadVideo.uploadvideo_enabled') && Configure::read('UploadVideo.uploadvideo_enabled')) {
    Router::connect("/upload_videos/:action/*", array('plugin' => 'upload_video', 'controller' => 'upload_videos'));
    Router::connect("/upload_videos/*", array('plugin' => 'upload_video', 'controller' => 'upload_videos', 'action' => 'index'));
    Router::connect('/upload/videos', array('plugin' => 'upload_video', 'controller' => 'upload_videos', 'action' => 'ajax_upload'));
    
    // version 3.0.2
    Router::connect('/video/cron/run', array('plugin' => 'upload_video', 'controller' => 'upload_video_crons', 'action' => 'run'));
}
