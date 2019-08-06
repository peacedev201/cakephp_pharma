DROP TABLE IF EXISTS `{PREFIX}sp_sharings`;

DELETE FROM `{PREFIX}settings` WHERE `group_id` IN (SELECT `id` FROM `{PREFIX}setting_groups` WHERE `module_id` = 'SocialPublisher');

DELETE FROM `{PREFIX}setting_groups` WHERE `module_id` = 'SocialPublisher';



