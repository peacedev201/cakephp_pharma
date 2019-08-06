CREATE TABLE `{PREFIX}profile_completions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_name` varchar(255) DEFAULT NULL,
  `field_value` decimal(10,2) UNSIGNED DEFAULT '0',
  `profile_type_id` int(11) UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES ('Profile Completeness', 'profile_completions.profile', '[{"label":"Title","input":"text","value":"Profile Completeness","name":"title"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},
{"label":"plugin","input":"hidden","value":"ProfileCompletion","name":"plugin"}]', '1', '0', 'profile_completion', '', 'ProfileCompletion');
