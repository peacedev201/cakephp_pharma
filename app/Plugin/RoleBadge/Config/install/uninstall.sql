-- delete table
DROP TABLE IF EXISTS `{PREFIX}role_badges`;

DROP TABLE IF EXISTS `{PREFIX}award_badges`;

DROP TABLE IF EXISTS `{PREFIX}award_users`;

-- delete content
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='RoleBadge';

DELETE FROM `{PREFIX}core_contents` WHERE `plugin`='RoleBadge';

DELETE FROM `{PREFIX}acos` WHERE `group`='role_badge';

DELETE FROM `{PREFIX}notifications` WHERE `plugin`='RoleBadge';