CREATE TABLE IF NOT EXISTS `{PREFIX}documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT '0',
  `title` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(128) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `like_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `privacy` int(11) DEFAULT NULL,
  `comment_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dislike_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `view_count` int(11) DEFAULT '0',  
  `download_count` int(11) DEFAULT '0',
  `download_url` varchar(128) DEFAULT NULL,
  `feature` tinyint(1) DEFAULT NULL,
  `file_name` varchar(128) DEFAULT NULL,
  `visiable` tinyint(1) NOT NULL DEFAULT '1',
  `document_license_id` int(11) DEFAULT '0',
  `approve` tinyint(1) DEFAULT NULL,
  `share_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}document_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11;


INSERT INTO `{PREFIX}document_licenses` (`id`, `title`, `url`, `name`) VALUES
(1, 'Unspecified - no licensing information associated', '', ''),
(2, 'By attribution (by)', 'http://creativecommons.org/licenses/by/3.0/', 'Creative Commons Attribution 3.0 Unported License.'),
(3, 'By attribution, non-commercial (by-nc)', 'http://creativecommons.org/licenses/by-nc/3.0/', 'Creative Commons Attribution-Noncommercial 3.0 Unported License.'),
(4, 'By attribution, non-commercial, non-derivative (by-nc-nd)', 'http://creativecommons.org/licenses/by-nc-nd/3.0/', 'Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported License.'),
(5, 'By attribution, non-commercial, share alike (by-nc-sa)', 'http://creativecommons.org/licenses/by-nc-sa/3.0/', 'Creative Commons Attribution-Noncommercial-Share Alike 3.0 Unported License.'),
(6, 'By attribution, non-derivative (by-nd)', 'http://creativecommons.org/licenses/by-nd/3.0/', 'Creative Commons Attribution-No Derivative Works 3.0 Unported License.'),
(7, 'By attribution, share alike (by-sa)', 'http://creativecommons.org/licenses/by-sa/3.0/', 'Creative Commons Attribution-Share Alike 3.0 Unported License.'),
(8, 'Public domain', '', 'This document has been released into the public domain.'),
(9, 'Copyright - all rights reserved', '', 'This document is © [year] by [user] - all rights reserved.');

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Menu document & Search', 'document.menu', '[{"label":"Title","input":"text","value":"Menu document & Search","name":"title"},{"label":"plugin","input":"hidden","value":"Document","name":"plugin"}]', 1, 0, 'document', '', 'Document'),
('Browse Document', 'document.browse', '[{"label":"Title","input":"text","value":"Browse Document","name":"title"},{"label":"plugin","input":"hidden","value":"Document","name":"plugin"}]', 1, 0, 'document', '', 'Document'),
('Feature Document', 'document.feature', '[{"label":"Title","input":"text","value":"Feature Document","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Document","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'document', '', 'Document'),
('Block Document', 'document.block', '[{"label":"Title","input":"text","value":"Block Document","name":"title"},{"label":"Type","input":"select","value":{"feature":"Feature","popular":"Popular","Document.id desc":"Recent","Document.view_count desc":"View Top","Document.download_count desc":"Download Top","Document.comment_count desc":"Comment Top","Document.like_count desc":"Like Top","Document.share_count desc":"Share Top"},"name":"order_type"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Document","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'document', '', 'Document'),
('My Document', 'document.my', '[{"label":"Title","input":"text","value":"My Document","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Document","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'document', '', 'Document'),
('Profile Document', 'document.profile', '[{"label":"Title","input":"text","value":"Profile Document","name":"title"},{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},{"label":"plugin","input":"hidden","value":"Document","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'document', '', 'Document'),
('Document Tag', 'document.tag', '[{"label":"Title","input":"text","value":"Tags","name":"title"},{"label":"plugin","input":"hidden","value":"Document","name":"plugin"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"}]', 1, 0, 'document', '', 'Document');

INSERT INTO `{PREFIX}pages` (`title`, `alias`, `content`, `permission`, `params`, `created`, `modified`, `menu`, `icon_class`, `weight`, `url`, `uri`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `type`, `search`, `theme_id`, `core_content_count`) VALUES
('Document Browse Pages', 'documents', '', '', '', '2015-09-23 00:00:00', '2015-09-23 00:00:00', 0, '', 0, '/documents', 'documents.index', '', '', 1, 0, 1, NULL, NULL, 0, 'plugin', 0, 0, 8),
('Document Detail Pages', 'documents_view', '', '', '', '2015-09-23 00:00:00', '2015-09-23 00:00:00', 0, '', 0, '/documents/view/$id/{document''s name}', 'documents.view', '', '', 1, 0, 3, NULL, NULL, 0, 'plugin', 0, 0, 6);

INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES
('document', 'create', 'Create/Edit Document'),
('document', 'view', 'View Document');