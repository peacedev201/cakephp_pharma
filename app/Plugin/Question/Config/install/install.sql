CREATE TABLE IF NOT EXISTS `{PREFIX}questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `favorite_count` int(11) NOT NULL DEFAULT '0',
  `vote_count` float NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `answer_count` int(11) NOT NULL DEFAULT '0',
  `visiable` tinyint(1) NOT NULL DEFAULT '1',
  `privacy` int(11) NOT NULL DEFAULT '0',
  `feature` tinyint(1) NOT NULL DEFAULT '0',
  `has_best_answers` tinyint(1) NOT NULL DEFAULT '0',
  `thumbnail` varchar(512) NOT NULL,
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `approve` tinyint(1) NOT NULL DEFAULT '1',
  `edited` tinyint(1) NOT NULL DEFAULT '0',
  `share_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `best_answers` tinyint(1) NOT NULL DEFAULT '0',
  `best_answers_date` datetime DEFAULT NULL,
  `vote_count` float NOT NULL DEFAULT '0',
  `active_time` datetime NOT NULL,
  `edited` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `type` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `filename` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `original_filename` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `downloads` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) NOT NULL,
  `background_color` varchar(16) NOT NULL,
  `text_color` varchar(16) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `permission` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `type_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `created` datetime NOT NULL,
  `edited` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_content_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `created` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `{PREFIX}question_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_point_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `type_id` int(11) NOT NULL,
  `point` float NOT NULL DEFAULT '0',
  `from_user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,  
  `status` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_tag_maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`tag_id`),
  KEY `id` (`id`,`question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,  
  `total` float NOT NULL DEFAULT '0',
  `total_best_answer` float NOT NULL DEFAULT '0',
  `total_question` int(11) NOT NULL DEFAULT '0',
  `total_answer` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}question_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `type_id` int(11) NOT NULL,
  `vote` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Browse Question', 'question.browse', '[{"label":"Title","input":"text","value":"Browse Question","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"}]', 1, 0, 'question', '', 'Question'),
('Menu question', 'question.menu', '[{"label":"Title","input":"text","value":"Menu question","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"}]', 1, 0, 'question', '', 'Question'),
('Popular Question Tag', 'question.tags', '[{"label":"Title","input":"text","value":"Tags","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Most Popular Q&A Tags', 'question.browse_tags', '[{"label":"Title","input":"text","value":"Browse Tags Question","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"}]', 1, 0, 'question', '', 'Question'),
('Top Question Contributors', 'question.top_users', '[{"label":"Title","input":"text","value":"Question Top Users","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Top Q&A Contributors', 'question.top_point_users', '[{"label":"Title","input":"text","value":"Question Top Point Users","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Top Answer Contributors', 'question.top_answer_users', '[{"label":"Title","input":"text","value":"Top Answer Users","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Browse Badges Question', 'question.browse_badges', '[{"label":"Title","input":"text","value":"Browse Badges Question","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"}]', 1, 0, 'question', '', 'Question'),
('How do I get points', 'question.how_do_collect_points', '[{"label":"Title","input":"text","value":"How do I collect points?","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Browse Ratings Question', 'question.browse_ratings', '[{"label":"Title","input":"text","value":"Browse Ratings Question","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"}]', 1, 0, 'question', '', 'Question'),
('Block Question', 'question.block', '[{"label":"Title","input":"text","value":"Question Poll","name":"title"},{"label":"Type","input":"select","value":{"popular":"Popular","feature":"Feature","Question.id desc":"Recent","Question.vote_count desc":"Vote Top","Question.answer_count desc":"Answer Top","Question.share_count desc":"Share Top","Question.view_count desc":"View Top","Question.favorite_count desc":"Favorite Top"},"name":"order_type"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Question Related Tags', 'question.related', '[{"label":"Title","input":"text","value":"Question Related","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('My Question', 'question.my', '[{"label":"Title","input":"text","value":"My Question","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Profile Question', 'question.profile', '[{"label":"Title","input":"text","value":"Profile Question","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question'),
('Question Info', 'question.profile_info', '[{"label":"Title","input":"text","value":"Question Info","name":"title"},{"label":"plugin","input":"hidden","value":"Question","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'question', '', 'Question');

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Questions Browse Page', 'questions_index', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, '/questions', 'questions.index', '', '', 1, 0, 6, NULL, NULL, 0, 'plugin', 0, 0, 11),
('Questions Badges Page', 'questions_badges', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, '/questions/badges', 'questions.badges', '', '', 1, 0, 6, NULL, NULL, 0, 'plugin', 0, 0, 8),
('Questions Detail Page', 'questions_view', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, '/questions/view/$id/{question''s name}', 'questions.view', '', '', 1, 0, 6, NULL, NULL, 0, 'plugin', 0, 0, 6),
('Questions Ratings Page', 'questions_ratings', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, '/questions/ratings', 'questions.ratings', '', '', 1, 0, 6, NULL, NULL, 0, 'plugin', 0, 0, 9),
('Question Tags Page', 'question_question_tags', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, '/question/question_tags', 'question_tags.index', '', '', 1, 0, 6, NULL, NULL, 0, 'plugin', 0, 0, 7);

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES
('question', 'create', 'Create/Edit Question'),
('question', 'view', 'View Question');