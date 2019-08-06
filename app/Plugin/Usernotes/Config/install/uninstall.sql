DROP TABLE IF EXISTS `{PREFIX}usernotes`;
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Usernotes';
DELETE FROM `{PREFIX}core_contents` WHERE `plugin`='Usernotes';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'usernotess.index');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'usernotess.index';

DELETE FROM `{PREFIX}acos` WHERE `group` = 'usernotes';