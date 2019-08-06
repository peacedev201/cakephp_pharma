CREATE TABLE IF NOT EXISTS `{PREFIX}feedback` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `privacy` tinyint(2) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `category_id` int(10) DEFAULT NULL,
  `severity_id` int(10) DEFAULT NULL,
  `status_id` int(10) DEFAULT NULL,
  `status_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `album_id` int(10) NOT NULL DEFAULT '0',
  `views` int(10) NOT NULL DEFAULT '0',
  `comment_count` int(10) unsigned NOT NULL DEFAULT '0',
  `dislike_count` int(5) unsigned NOT NULL DEFAULT '0',
  `like_count` int(5) unsigned NOT NULL DEFAULT '0',
  `share_count` int(5) unsigned NOT NULL DEFAULT '0',
  `total_votes` int(10) unsigned NOT NULL DEFAULT '0',
  `total_images` int(10) NOT NULL DEFAULT '0',
  `featured` tinyint(2) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(50) NOT NULL,
  `anonymous_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `anonymous_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}feedback_blockips` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `blockip_address` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `blockip_comment` tinyint(2) NOT NULL DEFAULT '0',
  `blockip_feedback` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}feedback_blockusers` (
  `id` int(10) NOT NULL,
  `block_comment` tinyint(2) NOT NULL DEFAULT '0',
  `block_feedback` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}feedback_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `use_time` tinyint(5) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `{PREFIX}feedback_categories` (`id`, `name`, `description`, `use_time`, `is_active`) VALUES
(1, 'Default category', 'Default category', 0, 1);

CREATE TABLE IF NOT EXISTS `{PREFIX}feedback_images` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `feedback_id` int(10) NOT NULL,
  `name` varchar(225) NOT NULL,
  `image_url` varchar(225) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_id` (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}feedback_severities` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `use_time` int(5) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}feedback_statuses` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `default_comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `use_time` tinyint(5) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}feedback_votes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `feedback_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','create_feedback','Can create feedback');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','view_feedback_listing','Can view feedback listing');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','can_upload_photo','Can upload photo');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','approve_feedback_before_public','Approve feedback before they are publicly displayed');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','approve_feedback','Can approve feedback');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','delete_own_feedback','Can delete own feedback');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','delete_all_feedbacks','Can delete all feedbacks');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','edit_all_feedbacks','Can edit all feedbacks');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','edit_own_feedback','Can edit own feedback');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('feedback','set_status','Can set status');