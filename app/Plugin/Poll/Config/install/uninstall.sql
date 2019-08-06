DROP TABLE IF EXISTS `{PREFIX}polls`;
DROP TABLE IF EXISTS `{PREFIX}poll_answers`;
DROP TABLE IF EXISTS `{PREFIX}poll_items`;

DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Poll';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'polls.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'polls.view');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'polls.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'polls.view';

DELETE FROM `{PREFIX}acos` WHERE `group`='poll';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='Poll';

DELETE FROM `{PREFIX}comments` WHERE `type`='Poll_Poll';

DELETE FROM `{PREFIX}likes` WHERE `type`='Poll_Poll';

DELETE FROM `{PREFIX}tags` WHERE `type`='Poll_Poll';