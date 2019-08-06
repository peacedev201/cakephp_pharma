DROP TABLE IF EXISTS `{PREFIX}sliders`;
DROP TABLE IF EXISTS `{PREFIX}slides`;
DELETE FROM `{PREFIX}core_contents` WHERE `name` = 'sliders.slideshow';
DELETE FROM `{PREFIX}core_blocks` WHERE `path_view` = 'sliders.slideshow';
DELETE FROM `{PREFIX}i18n` WHERE `model` = 'Slide';