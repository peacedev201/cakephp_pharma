CREATE TABLE IF NOT EXISTS `{PREFIX}spotlight_users` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `user_id` int(10) NOT NULL,
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `created` datetime NOT NULL,
    `end_date` datetime NOT NULL,
    `ordering` tinyint(6) DEFAULT '99',
    `status` enum('initial','active','pending','expired','refunded','failed','cancel','process','inactive') COLLATE utf8_unicode_ci DEFAULT 'initial',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}spotlight_transactions` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `spotlight_user_id` int(10) NOT NULL,
    `user_id` int(10) NOT NULL,
    `transaction_id` int(10) NOT NULL DEFAULT '0',
    `status` enum('initial','completed','pending','expired','refunded','failed','cancel','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'initial',
    `amount` decimal(16,2) NOT NULL DEFAULT '0.00',
    `created` datetime DEFAULT NULL,
    `type` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Top Spotlight', 'spotlights.top', '{"0":{"label":"Title","input":"text","value":"Top Spotlight","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"Spotlight","name":"plugin"}}', 1, 0, 'spotlights', '', 'Spotlight');

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES('spotlight', 'use', 'Use Spotlight');