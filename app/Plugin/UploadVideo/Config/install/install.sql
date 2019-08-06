-- Add 
ALTER TABLE `{PREFIX}videos` ADD `check_thumb_vimeo` TINYINT(2) NOT NULL DEFAULT '0' AFTER `group_id`;

-- Task
INSERT INTO `{PREFIX}tasks` (`id`, `title`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`, `enable`, `class`) 
VALUES (NULL, 'Get Thumbnail Vimeo', 'UploadVideo', '60', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', 'UploadVideo_Task_GetThumbnail');

-- Create
CREATE TABLE IF NOT EXISTS `{PREFIX}video_processes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(11) unsigned NOT NULL,
  `system_pid` int(11) unsigned NOT NULL DEFAULT '0',
  `started` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}video_limitations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `per_type` enum('D','M','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'D',
  `value` int(11) unsigned NOT NULL DEFAULT '0',
  `size` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;