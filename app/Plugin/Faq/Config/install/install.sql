CREATE TABLE IF NOT EXISTS `{PREFIX}faqs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(10) NOT NULL,
  `sub_category_id` int(10) NOT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL,
  `permission` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `per_usefull` float NOT NULL,
  `total_yes` tinyint(10) UNSIGNED NOT NULL,
  `total_no` tinyint(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `user_id` int(10) NOT NULL,
  `modified` datetime NOT NULL,
  `alow_comment` tinyint(3) NOT NULL,
  `permanent_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment_count` int(11) NOT NULL,
  `faq_count` int(11) NOT NULL,
  `order` int(11) NOT NULL,
   `privacy` tinyint(3) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}faq_help_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `privacy` tinyint(3) NOT NULL,
  `active` tinyint(3) NOT NULL,
  `count` int(3) NOT NULL,
  `order` int(11) NOT NULL,
  `faq_count` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}faq_results` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `faq_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `vote` smallint(2) NOT NULL,
  `helpfull_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Faq header', 'faq.header', '[{"label":"Title","input":"text","value":"Header Faq","name":"title"},{"label":"plugin","input":"hidden","value":"Faq","name":"plugin"}]', '1', '0', 'faq', '', 'Faq'),
('Faq menu', 'faq.menu', '[{"label":"Title","input":"text","value":"Menu Faq","name":"title"},{"label":"plugin","input":"hidden","value":"Faq","name":"plugin"}]', '1', '0', 'faq', '', 'Faq'),
('Faq browse', 'faq.browse', '[{"label":"Title","input":"text","value":"Browse Faq","name":"title"},{"label":"plugin","input":"hidden","value":"Faq","name":"plugin"}]', '1', '0', 'faq', '', 'Faq'),
('Faq similar', 'faq.similar', '[{"label":"Title","input":"text","value":"Similar Faq","name":"title"},{"label":"plugin","input":"hidden","value":"Faq","name":"plugin"}]', '1', '0', 'faq', '', 'Faq');

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
( 'Faq Browse Pages', 'faqs', '', '', '', '', '', '0', '', '0', '/faqs', 'faqs.index', '', '', '1', '0', '6', NULL, NULL, '0', 'plugin', '0', '0', '9'),
( 'Faq Detail Page', 'faqs_view', '', '', '', '', '', '0', '', '0', '/faqs/view/$id/{faq''s name}', 'faqs.view', '', '', '1', '0', '6', NULL, NULL, 0, 'plugin', '0', '0', '6');

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES
('faq', 'create', 'Create/Edit FAQ'),
('faq', 'view', 'View FAQ');