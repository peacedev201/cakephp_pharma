DROP TABLE IF EXISTS `{PREFIX}contests`;
DROP TABLE IF EXISTS `{PREFIX}contest_settings`;
DROP TABLE IF EXISTS `{PREFIX}contest_entries`;
DROP TABLE IF EXISTS `{PREFIX}contest_candidates`;
DROP TABLE IF EXISTS `{PREFIX}contest_votes`;

DELETE FROM `{PREFIX}categories` WHERE `type`='Contest';
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Contest';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'contests.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'contests.view');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'contests.entry');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'contests.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'contests.view';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'contests.entry';

DELETE FROM `{PREFIX}acos` WHERE `group`='contest';

DELETE FROM `{PREFIX}categories` WHERE `type`='Contest';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='Contest';

DELETE FROM `{PREFIX}comments` WHERE `type`='Contest_Contest';

DELETE FROM `{PREFIX}likes` WHERE `type`='Contest_Contest';

DELETE FROM `{PREFIX}comments` WHERE `type`='Contest_Contest_Entry';

DELETE FROM `{PREFIX}likes` WHERE `type`='Contest_Contest_Entry';

DELETE FROM `{PREFIX}tags` WHERE `type`='Contest_Contest';

DELETE FROM `{PREFIX}tasks` WHERE `plugin`='Contest';

DELETE FROM `{PREFIX}mailtemplates` WHERE `plugin`='Contest';

DELETE FROM `{PREFIX}notifications` WHERE `plugin`='Contest';
DELETE FROM `{PREFIX}reports` WHERE `type`='Contest_Contest';
DELETE FROM `{PREFIX}reports` WHERE `type`='Contest_Contest_Entry';