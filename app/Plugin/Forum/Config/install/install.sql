CREATE TABLE  IF NOT EXISTS `{PREFIX}forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `count_view` int(11) NOT NULL DEFAULT '0',
  `count_topic` int(11) DEFAULT '0',
  `count_reply` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `thumb` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permission` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `moderator` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `last_topic_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `{PREFIX}forum_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumb` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `search_desc` text COLLATE utf8_unicode_ci,
  `count_view` int(11) NOT NULL DEFAULT '0',
  `count_reply` int(11) NOT NULL DEFAULT '0',
  `count_user` int(11) NOT NULL DEFAULT '0',
  `count_thank` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `thumb` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `last_reply_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `ping` tinyint(1) NOT NULL DEFAULT '0',
  `ping_expire` datetime DEFAULT NULL,
  `sort_date` datetime DEFAULT NULL,
  `user_edited` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_subscribes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_thanks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `target_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_topic_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `target_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `file_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `download_url` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_pins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `forum_topic_id` int(11) NOT NULL,
  `amount` float NOT NULL DEFAULT '0',
  `currency` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gateway_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `data` text,
  `modified` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `forum_topic_id` int(11) NOT NULL,
  `reason` text,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Forum My Contribution', 'forum.my_contribution', '[{"label":"Title","input":"text","value":"My Contribution","name":"title"},{"label":"plugin","input":"hidden","value":"Forum","name":"plugin"}]', 1, 0, 'forum', '', 'Forum'),
('Recent Forum topics', 'forum.recent_topic', '[{"label":"Title","input":"text","value":"Recent topics","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Forum","name":"plugin"}]', 1, 0, 'forum', '', 'Forum'),
('Most popular forum topics', 'forum.most_popular', '[{"label":"Title","input":"text","value":"Most popular topics","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Forum","name":"plugin"}]', 1, 0, 'forum', '', 'Forum'),
('Most viewed forum topics', 'forum.most_view', '[{"label":"Title","input":"text","value":"Most viewed topics","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Forum","name":"plugin"}]', 1, 0, 'forum', '', 'Forum'),
('Most active forum members', 'forum.most_member', '[{"label":"Title","input":"text","value":"Most active members","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Forum","name":"plugin"}]', 1, 0, 'forum', '', 'Forum'),
('Forum Global Search', 'forum.search_topic', '[{"label":"Title","input":"text","value":"Forum Global Search","name":"title"},{"label":"plugin","input":"hidden","value":"Forum","name":"plugin"}]', 1, 0, 'forum', '', 'Forum'),
('Forum Tag', 'forum.tag', '[{"label":"Title","input":"text","value":"Tags","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Forum","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'forum', '', 'forum');

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Forum Topics Browse Page', 'forum_topics', '', '', '', '2018-01-01 00:00:00', '2018-01-01 00:00:00', 0, '', 0, '/forums/topic', 'forum_topics.index', '', '', 1, 0, 2, NULL, NULL, 0, 'plugin', 0, 0, 1),
('Forum Topics Detail Page', 'forum_topics_detail', '', '', '', '2018-01-01 00:00:00', '2018-01-01 00:00:00', 0, '', 0, '/forums/topic/view', 'forum_topics.view', '', '', 1, 0, 2, NULL, NULL, 0, 'plugin', 0, 0, 2),
('Forum Topics Search Page', 'forum_topics_search', '', '', '', '2018-01-01 00:00:00', '2018-01-01 00:00:00', 0, '', 0, '/forums/topic/search', 'forum_topics.search', '', '', 1, 0, 2, NULL, NULL, 0, 'plugin', 0, 0, 3),
('Forum Details Page', 'forum_details', '', '', '', '2018-01-01 00:00:00', '2018-01-01 00:00:00', 0, '', 0, '/forums/view', 'forums.view', '', '', 1, 0, 2, NULL, NULL, 0, 'plugin', 0, 0, 3),
('Forum Browse Page', 'forums_index', '', '', '', '2018-01-01 00:00:00', '2018-01-01 00:00:00', 0, '', 0, '/forums/index', 'forums.index', '', '', 1, 0, 2, NULL, NULL, 0, 'plugin', 0, 0, 3);

ALTER TABLE `{PREFIX}users` ADD `signature` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `{PREFIX}users` ADD `show_signature` TINYINT(2) NOT NULL DEFAULT '1' ;

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES
('forum', 'create', 'Can create and edit their own topics'),
('forum', 'view', 'Can view topics');

INSERT INTO `{PREFIX}tasks` (`title`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`, `enable`, `class`) VALUES
('Forum topic expire', 'Forum', '21600', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', 'Forum_Task_Expire');

INSERT INTO `{PREFIX}forum_categories` (`id`, `name`, `thumb`, `order`) VALUES
(1, 'Forums', '', 1);