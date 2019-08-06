CREATE TABLE `{PREFIX}usernotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}core_blocks` ( `name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Note', 'usernotes.notes', '[\r\n   {\r\n      "label":"Title",\r\n      "input":"text",\r\n      "value":"Note",\r\n      "name":"title"\r\n   },\r\n   {\r\n      "label":"Title",\r\n      "input":"checkbox",\r\n      "value":"Enable Title",\r\n      "name":"title_enable"\r\n   },\r\n   {\r\n      "label":"plugin",\r\n      "input":"hidden",\r\n      "value":"Usernotes",\r\n      "name":"plugin"\r\n   }\r\n]', 1, 0, 'usernotes', '', 'Usernotes');

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Usernotes Home Page', 'usernotess', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, '/usernotess', 'usernotess.index', '', '', 1, 0, 2, NULL, NULL, 0, 'plugin', 0, 0, 10);
INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES('usernotes', 'can_write_note', 'Can write a note');