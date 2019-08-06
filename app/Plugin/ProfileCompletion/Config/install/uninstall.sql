DROP TABLE IF EXISTS `{PREFIX}profile_completions`;
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='ProfileCompletion';
DELETE FROM `{PREFIX}core_contents` WHERE `plugin`='ProfileCompletion';