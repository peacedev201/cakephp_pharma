CREATE TABLE IF NOT EXISTS `{PREFIX}reviews` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `rating_avg` decimal(10,2) NOT NULL DEFAULT '0.00',
  `review_count` int(11) unsigned NOT NULL DEFAULT '0',
  `review_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}review_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `review_id` int(11) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `rating` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photos` char(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;