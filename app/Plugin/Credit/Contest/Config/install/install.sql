CREATE TABLE IF NOT EXISTS `{PREFIX}contests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type` ENUM('photo', 'music', 'video') NOT NULL DEFAULT  'photo',
  `approve_status` enum('approved','denied','pending') NOT NULL DEFAULT 'pending',
  `contest_status` enum('closed','draft','published') NOT NULL DEFAULT 'draft',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `award` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `term_and_condition` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from` date DEFAULT NULL,
  `from_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `duration_start` datetime NOT NULL,
  `to` date DEFAULT NULL,
  `to_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `duration_end` datetime NOT NULL,
  `s_from` date DEFAULT NULL,
  `s_from_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `submission_start` datetime NOT NULL,
  `s_to` date DEFAULT NULL,
  `s_to_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `submission_end` datetime NOT NULL,
  `v_from` date DEFAULT NULL,
  `v_from_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `voting_start` datetime NOT NULL,
  `v_to` date DEFAULT NULL,
  `v_to_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `voting_end` datetime NOT NULL,
  `timezone` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'America/New_York',
  `vote_without_join` tinyint(1) NOT NULL DEFAULT '1',
  `auto_approve` tinyint(1) NOT NULL DEFAULT '1',
  `submit_entry_fee` decimal(16,2) unsigned NOT NULL DEFAULT '0',
  `credit` decimal(16,2) unsigned NOT NULL DEFAULT '0',
  `win_percent` decimal(16,2) unsigned NOT NULL DEFAULT '0',
  `request_delete` tinyint(1) NOT NULL DEFAULT '0',
  `maximum_entry` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `contest_candidate_count` SMALLINT(5) NOT NULL DEFAULT '0',
  `contest_entry_count` SMALLINT(5) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `dislike_count` int(11) NOT NULL DEFAULT '0',
  `share_count` int(11) DEFAULT '0',  
  `privacy` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}contest_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) unsigned NOT NULL,
  `caption` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `source` ENUM(  'photo', 'music', 'youtube', 'vimeo', 'upload') NOT NULL DEFAULT  'photo',
  `source_id` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `approved_date` datetime DEFAULT NULL,
  `entry_status` enum('pending','published','win') NOT NULL DEFAULT 'pending',
  `give_award_status` int(11) NOT NULL DEFAULT '0',
  `follow_count` int(11) unsigned NOT NULL DEFAULT '0',
  `favourite_count` int(11) unsigned NOT NULL DEFAULT '0',
  `contest_vote_count` int(11) unsigned NOT NULL DEFAULT '0',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `dislike_count` int(11) NOT NULL DEFAULT '0',
  `share_count` int(11) DEFAULT '0',  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}contest_candidates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `contest_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}contest_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `contest_entry_id` int(11) unsigned NOT NULL DEFAULT '0',
  `contest_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}contest_settings` (
  `id` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `{PREFIX}contest_settings`(`id`, `value`) values('contest_integrate_credit', '0');

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Contest Browse Page', 'contests_index', '', '', '', '2015-09-23 00:00:00', '2015-09-23 00:00:00', 0, '', 0, '/contests', 'contests.index', '', '', 1, 0, 0, NULL, NULL, 0, 'plugin', 0, 0, 8),
('Contest Detail Page', 'contests_view', '', '', '', '2015-09-23 00:00:00', '2015-09-23 00:00:00', 0, '', 0, '/contests/view/$id/{contest''s name}', 'contests.view', '', '', 1, 0, 0, NULL, NULL, 0, 'plugin', 0, 0, 8),
('Contest Entry Detail Page', 'contests_entry', '', '', '', '2015-09-23 00:00:00', '2015-09-23 00:00:00', 0, '', 0, '/contests/entry/$id/{contest''s name}', 'contests.entry', '', '', 1, 0, 0, NULL, NULL, 0, 'plugin', 0, 0, 8);

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Featured Contests', 'contest.featured', '[{"label":"Title","input":"text","value":"Featured Contests","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'contest', '', 'Contest'),
('Popular Contests', 'contest.popular', '[{"label":"Title","input":"text","value":"Popular Contests","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'contest', '', 'Contest'),
('Top Contests', 'contest.top', '[{"label":"Title","input":"text","value":"Top Contests","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'contest', '', 'Contest'),
('Recent Contests', 'contest.recent', '[{"label":"Title","input":"text","value":"Recent Contests","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'contest', '', 'Contest'),
('Most Voted Entries', 'contest.vote', '[{"label":"Title","input":"text","value":"Most Voted Entries","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'contest', '', 'Contest'),
('Most View Entries', 'contest.view', '[{"label":"Title","input":"text","value":"Most View Entries","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'contest', '', 'Contest'),
('Contest Winner', 'contest.winner', '[{"label":"Title","input":"text","value":"Winner","name":"title"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'contest', '', 'Contest'),
('Menu contest & Search', 'contest.menu', '[{"label":"Title","input":"text","value":"Menu contest & Search","name":"title"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"}]', 1, 0, 'contest', '', 'Contest'),
('Browse Contests', 'contest.browse', '[{"label":"Title","input":"text","value":"Browse Contests","name":"title"},{"label":"plugin","input":"hidden","value":"Contest","name":"plugin"}]', 1, 0, 'contest', '', 'Contest');

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES
('contest', 'create', 'Create/Edit Contest'),
('contest', 'view', 'View Contest');

INSERT INTO `{PREFIX}tasks` (`title`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`, `enable`, `class`) VALUES
('Contest Duration', 'Contest', '3600', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', 'Contest_Task_Cron');