CREATE TABLE IF NOT EXISTS `{PREFIX}polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(512) NOT NULL,
  `created` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `like_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `dislike_count` int(11) NOT NULL DEFAULT '0',
  `privacy` int(11) NOT NULL DEFAULT '0',
  `create_new_answer` tinyint(1) NOT NULL DEFAULT '0',
  `visiable` tinyint(1) NOT NULL DEFAULT '1',
  `category_id` int(11) DEFAULT NULL,
  `thumbnail` varchar(512) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `answer_count` int(11) DEFAULT '0',
  `share_count` int(11) DEFAULT '0',
  `feature` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}poll_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}poll_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `total_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Poll Browse Pages', 'polls', '', '', '', '2015-09-23 00:00:00', '2015-09-23 00:00:00', 0, '', 0, '/polls', 'polls.index', '', '', 1, 0, 1, NULL, NULL, 0, 'plugin', 0, 0, 8),
('Poll Detail Pages', 'polls_view', '', '', '', '2015-09-23 00:00:00', '2015-09-23 00:00:00', 0, '', 0, '/polls/view/$id/{poll''s name}', 'polls.view', '', '', 1, 0, 3, NULL, NULL, 0, 'plugin', 0, 0, 6);

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Menu poll & Search', 'poll.menu', '[{"label":"Title","input":"text","value":"Menu poll & Search","name":"title"},{"label":"plugin","input":"hidden","value":"Poll","name":"plugin"}]', 1, 0, 'poll', '', 'Poll'),
('Browse Poll', 'poll.browse', '[{"label":"Title","input":"text","value":"Browse Poll","name":"title"},{"label":"plugin","input":"hidden","value":"Poll","name":"plugin"}]', 1, 0, 'poll', '', 'Poll'),
('Block Poll', 'poll.block', '[{"label":"Title","input":"text","value":"Block Poll","name":"title"},{"label":"Type","input":"select","value":{"popular":"Popular","feature":"Feature","Poll.id desc":"Recent","Poll.answer_count desc":"Answer Top","Poll.comment_count desc":"Comment Top","Poll.like_count desc":"Like Top","Poll.share_count desc":"Share Top"},"name":"order_type"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Poll","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'poll', '', 'Poll'),
('My Poll', 'poll.my', '[{"label":"Title","input":"text","value":"My Poll","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Poll","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'poll', '', 'Poll'),
('Profile Poll', 'poll.profile', '[{"label":"Title","input":"text","value":"Profile Poll","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Poll","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'poll', '', 'Poll'),
('Poll Tag', 'poll.tag', '[{"label":"Title","input":"text","value":"Tags","name":"title"},{"label":"plugin","input":"hidden","value":"Poll","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'poll', '', 'Poll');

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES
('poll', 'create', 'Create/Edit Poll'),
('poll', 'view', 'View Poll');