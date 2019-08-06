<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

if (Configure::check('UploadVideo.uploadvideo_enabled') && Configure::read('UploadVideo.uploadvideo_enabled')) {
    App::uses('UploadVideoListener', 'UploadVideo.Lib');
    CakeEventManager::instance()->attach(new UploadVideoListener());
}

// Define
define('UPLOAD_SERVER', 0);
define('UPLOAD_VIMEO', 1);
define('UPLOAD_VIDEO_ENABLE', 1);
define('UPLOAD_VIDEO_DISABLE', 0);
