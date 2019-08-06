
INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Credit Home Page', 'credits', '', '', 'a:1:{s:8:"comments";s:1:"1";}', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, '/credits', 'credits.index', '', '', 1, 0, 1, NULL, NULL, 0, 'plugin', 0, 0, 10);

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Buy Credits', 'credits.buy', '{"0":{"label":"Title","input":"text","value":"Buy Credits","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"Credit","name":"plugin"}}', 1, 0, 'credits', '', 'Credit');

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Send Credits', 'credits.send', '{"0":{"label":"Title","input":"text","value":"Send Credits","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"Credit","name":"plugin"}}', 1, 0, 'credits', '', 'Credit');

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Earn Credits', 'credits.options', '{"0":{"label":"Title","input":"text","value":"Earn Credits","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"Credit","name":"plugin"}}', 1, 0, 'credits', '', 'Credit');

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Credit Statistics', 'credits.rank', '{"0":{"label":"Title","input":"text","value":"Credit Statistics","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"Credit","name":"plugin"}}', 1, 0, 'credits', '', 'Credit');

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Credit Rank', 'credits.badge', '{"0":{"label":"Title","input":"text","value":"Credit Rank","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"Credit","name":"plugin"}}', 1, 0, 'credits', '', 'Credit');

CREATE TABLE IF NOT EXISTS `{PREFIX}credit_ranks` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(150) COLLATE utf8_unicode_ci NOT NULL,
	`credit` decimal(16,2) NOT NULL DEFAULT '0.00',
	`description` LONGTEXT COLLATE utf8_unicode_ci NOT NULL,
	`photo` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
	`enable` TINYINT(1) NOT NULL,
	`notify` TINYINT(1) DEFAULT '1',
	PRIMARY KEY (`id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}credit_sells` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`credit` decimal(16,2) NOT NULL DEFAULT '0.00',
	`price` DECIMAL(16,2) NOT NULL DEFAULT '0.00',
	PRIMARY KEY (`id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `{PREFIX}credit_balances` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `current_credit` decimal(16,2) NOT NULL DEFAULT '0.00',
  `earned_credit` decimal(16,2) NOT NULL DEFAULT '0.00',
  `spent_credit` decimal(16,2) NOT NULL DEFAULT '0.00',
  `frozen_credit` int(10) NOT NULL DEFAULT '0',
  `rank_id` int(11) DEFAULT '0',
  `num_withdraw` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}credit_orders` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL,
	`sell_id` INT(11) NOT NULL,
	`credit` decimal(16,2) NOT NULL DEFAULT '0.00',
	`price` DECIMAL(16,2) NOT NULL DEFAULT '0.00',
	`creation_date` datetime NOT NULL,
	`transation_id` varchar(150) NULL,
  `type` varchar(150) COLLATE utf8_unicode_ci DEFAULT '',
  `status` enum('initial','completed','pending','expired','refunded','failed','cancel','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'initial',
	PRIMARY KEY (`id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}credit_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `action_id` int(11) UNSIGNED NOT NULL,
  `object_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` int(11) UNSIGNED NOT NULL,
  `credit` decimal(16,2) NOT NULL DEFAULT '0.00',
  `creation_date` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}credit_faqs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `question` text COLLATE utf8_unicode_ci NOT NULL,
  `answer` text COLLATE utf8_unicode_ci,
  `active` tinyint(1) DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}credit_faqs` (`id`, `user_id`, `question`, `answer`, `active`, `created`) VALUES
(1, 1, 'What is credit?', '<p>Credits are a virtual currency you can use to send virtual gifts to friends, upgrade membership...</p>', 1, '2016-07-07 08:56:56'),
(2, 1, 'How do I get Credits?', '<p>You can earn credits by doing various activities on the site such as adding blog, creat new event..., etc. Also you can purchase credits using PayPal account.</p>', 1, '2016-07-07 08:57:16');

CREATE TABLE `{PREFIX}credit_actiontypes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `action_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action_type_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action_module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit` decimal(16,2) NOT NULL DEFAULT '0.00',
  `max_credit` decimal(16,0) NOT NULL DEFAULT '0',
  `rollover_period` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
  `type` enum('model','none') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `plugin` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Credit',
  `show` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}credit_actiontypes` (`id`, `action_type`, `action_type_name`, `action_module`, `credit`, `max_credit`, `rollover_period`, `type`, `plugin`, `show`) VALUES
(1, 'blog', 'Write new entry', 'Blog', '10', '100', 1, 'model', 'Credit', 1),
(2, 'group', 'Create Group', 'Group', '10', '100', 1, 'model', 'Credit', 1),
(3, 'event', 'Create Event', 'Event', '10', '100', 1, 'model', 'Credit', 1),
(4, 'topic', 'Create Topic', 'Topic', '10', '100', 1, 'model', 'Credit', 1),
(5, 'video', 'Post Video', 'Video', '10', '100', 1, 'model', 'Credit', 1),
(6, 'photo', 'Upload Photo', 'Photo', '10', '100', 1, 'model', 'Credit', 1),
(7, 'friend', 'Add friends', 'User', '10', '100', 1, 'model', 'Credit', 1),
(8, 'user', 'Sign Up', 'User', '10', '100', 0, 'model', 'Credit', 1),
(9, 'like', 'Like', 'Core', '1', '100', 1, 'model', 'Credit', 1),
(10, 'comment', 'Comment', 'Core', '1', '100', 1, 'model', 'Credit', 1),
(11, 'share', 'Share', 'Core', '1', '100', 1, 'none', 'Credit', 1),
(12, 'activity_comment', 'Comment status', 'Core', '1', '100', 1, 'model', 'Credit', 1),
(13, 'transfer_to', 'Sending credits', 'Credit', '0', '0', 0, 'none', 'Credit', 0),
(14, 'transfer_from', 'Receiving credits', 'Credit', '0', '0', 0, 'none', 'Credit', 0),
(15, 'give_credits', 'Admin giving you credits', 'Credit', '0', '0', 0, 'none', 'Credit', 0),
(16, 'set_credits', 'Admin set your credits', 'Credit', '10', '0', 0, 'none', 'Credit', 0),
(17, 'buy_credits', 'Buying credits', 'Credit', '10', '0', 0, 'none', 'Credit', 0),
(18, 'group_user', 'Join Group', 'Group', '10', '100', 1, 'model', 'Credit', 1),
(19, 'request-withdraw', 'Admin approved your withdrawal request', 'Credit', '0', '0', 0, 'none', 'Credit', 0),
(20, 'payment', 'Pay item with credit', 'Credit', '0', '0', 0, 'none', 'Credit', 0),
(21, 'refund_credits', 'Admin refunding you credits', 'Credit', '0', '0', 0, 'none', 'Credit', 0),
(22, 'post_new_feed', 'Post news feed', 'Activity', '9', '100', 1, 'model', 'Credit', 1),
(23, 'friend_inviter', 'Friend Inviter', 'Friend Inviter', '10', '1000', 0, 'none', 'Credit', 1);

CREATE TABLE `{PREFIX}credit_withdraws` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `amount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `completed_date` datetime NOT NULL,
  `status` smallint(1) NOT NULL,
  `payment` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `payment_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `total` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES('credit', 'use', 'Use Credits');

INSERT INTO `{PREFIX}gateways` (`name`, `description`, `enabled`, `plugin`, `test_mode`, `ipn_log`, `config`) VALUES
('Credit', 'Credit system', 0, 'Credit', 0, 0, '');
