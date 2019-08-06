CREATE TABLE IF NOT EXISTS `{PREFIX}activitylogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_target_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `action` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `item_id` int(10) unsigned NOT NULL,
  `target_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `item_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `params` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `sender_id` (`user_target_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO `{PREFIX}tasks` (`title`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`, `enable`, `class`) VALUES
('Activity log clear', 'Activitylog', '86400', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', 'Activitylog_Task_Clear');