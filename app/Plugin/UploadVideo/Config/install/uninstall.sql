-- delete content
DELETE FROM `{PREFIX}acos` WHERE `key` = 'upload' AND `group` = 'video';

DELETE FROM `{PREFIX}notifications` WHERE `plugin` = 'UploadVideo';

DELETE FROM `{PREFIX}tasks` WHERE `plugin` = 'UploadVideo';

ALTER TABLE `{PREFIX}videos` DROP `check_thumb_vimeo`;

-- delete table
DROP TABLE IF EXISTS `{PREFIX}video_processes`;

DROP TABLE IF EXISTS `{PREFIX}video_limitations`;
