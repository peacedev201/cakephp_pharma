CREATE TABLE IF NOT EXISTS `{PREFIX}reactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `total_count` smallint(5) NOT NULL DEFAULT '0',
  `like_count` smallint(5) NOT NULL DEFAULT '0',
  `love_count` smallint(5) NOT NULL DEFAULT '0',
  `haha_count` smallint(5) NOT NULL DEFAULT '0',
  `wow_count` smallint(5) NOT NULL DEFAULT '0',
  `sad_count` smallint(5) NOT NULL DEFAULT '0',
  `angry_count` smallint(5) NOT NULL DEFAULT '0',
  `cool_count` smallint(5) NOT NULL DEFAULT '0',
  `confused_count` smallint(5) NOT NULL DEFAULT '0',
  `is_update` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

ALTER TABLE `{PREFIX}reactions` ADD INDEX `target_id` (`target_id`, `type`);

ALTER TABLE `{PREFIX}likes` ADD `reaction` TINYINT NOT NULL DEFAULT '1';