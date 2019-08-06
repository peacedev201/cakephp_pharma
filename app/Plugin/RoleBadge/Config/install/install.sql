CREATE TABLE IF NOT EXISTS `{PREFIX}role_badges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `thumbnail` char(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `desktop_profile` char(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `desktop_feed` char(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_profile` char(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_feed` char(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_next_name` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

-- Adward --
CREATE TABLE IF NOT EXISTS `{PREFIX}award_badges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail` char(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_next_name` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `weight` smallint(5) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}award_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `award_badge_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;