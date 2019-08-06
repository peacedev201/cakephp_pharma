<?php
define('STICKER_CATEGORY_ICON_WIDTH', 28);
define('STICKER_CATEGORY_ICON_HEIGHT', 28);
define('STICKER_STICKER_ICON_WIDTH', 34);
define('STICKER_STICKER_ICON_HEIGHT', 34);
define('STICKER_UPLOAD_PATH', 'uploads'.DS.'sticker');
define('STICKER_UPLOAD_PATH_TEMP', 'uploads'.DS.'tmp');
define('STICKER_UPLOAD_PATH_CATEGORY', STICKER_UPLOAD_PATH.DS.'categories');
define('STICKER_UPLOAD_PATH_STICKER', STICKER_UPLOAD_PATH.DS.'stickers');
define('STICKER_UPLOAD_PATH_STICKER_APP', STICKER_UPLOAD_PATH.DS.'stickers/%s/app');

define('STICKER_UPLOAD_URL', 'uploads/sticker');
define('STICKER_UPLOAD_URL_TEMP', 'uploads/tmp');
define('STICKER_UPLOAD_URL_CATEGORY', STICKER_UPLOAD_URL.'/categories');
define('STICKER_UPLOAD_URL_STICKER', STICKER_UPLOAD_URL.'/stickers');

define('STICKER_IMAGE_EXTENSION', 'jpg,jpeg,png,gif');
define('STICKER_IMAGE_TYPE_IMAGE', 'image');
define('STICKER_IMAGE_TYPE_ANIMATION', 'animation');
define('STICKER_IMAGE_TYPE_GIF', 'gif');

define('STICKER_ACTIVITY_ACTION', 'sticker_create');
define('STICKER_MAX_LOG_ITEM', 16);