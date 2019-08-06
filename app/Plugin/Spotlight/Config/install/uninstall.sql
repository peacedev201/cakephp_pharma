DROP TABLE IF EXISTS `{PREFIX}spotlight_users`;
DROP TABLE IF EXISTS `{PREFIX}spotlight_transactions`;
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Spotlight';

DELETE FROM `{PREFIX}core_contents` WHERE `plugin` = 'Spotlight';

DELETE FROM `{PREFIX}acos` WHERE `group` = 'spotlight';

DELETE FROM `{PREFIX}notifications` WHERE `plugin`='Spotlight';