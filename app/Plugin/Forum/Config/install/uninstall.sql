DROP TABLE IF EXISTS `{PREFIX}forums`;
DROP TABLE IF EXISTS `{PREFIX}forum_categories`;
DROP TABLE IF EXISTS `{PREFIX}forum_topics`;
DROP TABLE IF EXISTS `{PREFIX}forum_favorites`;
DROP TABLE IF EXISTS `{PREFIX}forum_subscribes`;
DROP TABLE IF EXISTS `{PREFIX}forum_thanks`;
DROP TABLE IF EXISTS `{PREFIX}forum_topic_histories`;
DROP TABLE IF EXISTS `{PREFIX}forum_files`;
DROP TABLE IF EXISTS `{PREFIX}forum_pins`;
DROP TABLE IF EXISTS `{PREFIX}forum_reports`;

DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Forum';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'forum_topics.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'forum_topics.view');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'forum_topics.search');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'forums.view');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'forums.index');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'forum_topics.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'forum_topics.view';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'forum_topics.search';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'forums.view';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'forums.index';

DELETE FROM `{PREFIX}tags` WHERE `type`='Forum_ForumTopic';

ALTER TABLE `{PREFIX}users` DROP `signature`;
ALTER TABLE `{PREFIX}users` DROP `show_signature`;

DELETE FROM `{PREFIX}acos` WHERE `group`='forum';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='forum';

DELETE FROM `{PREFIX}hashtags` WHERE `item_table`='forum_topics';

DELETE FROM `{PREFIX}tasks` WHERE `plugin`='Forum';