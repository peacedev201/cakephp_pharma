CREATE TABLE IF NOT EXISTS `{PREFIX}sliders` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`slider_name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
	`duration` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`width` INT(11) NOT NULL DEFAULT '0',
	`height` INT(11) NOT NULL DEFAULT '0',
	`transition_speed` INT(11) NOT NULL DEFAULT '0',
	`show_navigation` TINYINT(1) NOT NULL DEFAULT '0',
	`navigation_color` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`navigation_hover_color` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`navigation_hightlight_color` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`navigation_type` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`show_control` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`control_color` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`control_background_color` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`position_control` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`transition_effect` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`show_progress` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`progress_color` VARCHAR(150) COLLATE utf8_unicode_ci NULL,
	`pause_on_hover` TINYINT(1) NOT NULL DEFAULT '0',
	`background_caption` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
	`opacity` FLOAT NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}slides` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`slider_id` INT(11) NOT NULL,
	`slide_name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
	`image` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
	`text` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `font_size` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `caption_font_size` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `caption_color` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_tab` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `{PREFIX}sliders` ADD `position_navigation` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL;

INSERT INTO `{PREFIX}core_blocks`(`name`, `path_view`,`params`,`is_active`,`plugin_id`,`group`)
VALUES('Slideshow', 'sliders.slideshow','[{"label":"Title","input":"text","value":"slideshow","name":"title"},{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},{"label":"plugin","input":"hidden","value":"Slider","name":"plugin"}]',1,0,'slider');
