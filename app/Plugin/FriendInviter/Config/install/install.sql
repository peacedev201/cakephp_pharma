CREATE TABLE IF NOT EXISTS `{PREFIX}user_suggest_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `suggest_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suggest_code` (`suggest_code`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}invites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `recipient` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `send_request` int(10) NOT NULL,
  `timestamp` datetime NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `new_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `invite_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `service` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `social_profileid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `recipient` (`recipient`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES
('friendinviter', 'invite', 'Invite Friends');

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Friend Inviter Page', 'friend_inviters_index', '', '', '', '2016-08-22 00:00:00', '2016-08-22 00:00:00', 0, '', 0, '/friend_inviters', 'friend_inviters.index', '', '', 1, 0, 1, NULL, NULL, 0, 'plugin', 0, 0, 3),
('Pending Invites Page', 'friend_inviters_pending', '', '', '', '2016-08-22 00:00:00', '2016-08-22 00:00:00', 0, '', 0, '/friend_inviters/pending', 'friend_inviters.pending', '', '', 1, 0, 1, NULL, NULL, 0, 'plugin', 0, 0, 3);