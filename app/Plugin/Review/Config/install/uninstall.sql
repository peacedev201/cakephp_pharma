-- delete table
DROP TABLE IF EXISTS `{PREFIX}reviews`;
DROP TABLE IF EXISTS `{PREFIX}review_users`;

-- delete content
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Review';

DELETE FROM `{PREFIX}core_contents` WHERE `plugin`='Review';

DELETE FROM `{PREFIX}acos` WHERE `group`='review';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='Review';

DELETE FROM `{PREFIX}notifications` WHERE `plugin`='Review';