DROP TABLE IF EXISTS `{PREFIX}questions`;
DROP TABLE IF EXISTS `{PREFIX}question_answers`;
DROP TABLE IF EXISTS `{PREFIX}question_attachments`;
DROP TABLE IF EXISTS `{PREFIX}question_badges`;
DROP TABLE IF EXISTS `{PREFIX}question_comments`;
DROP TABLE IF EXISTS `{PREFIX}question_content_histories`;
DROP TABLE IF EXISTS `{PREFIX}question_favorites`;
DROP TABLE IF EXISTS `{PREFIX}question_point_histories`;
DROP TABLE IF EXISTS `{PREFIX}question_tags`;
DROP TABLE IF EXISTS `{PREFIX}question_tag_maps`;
DROP TABLE IF EXISTS `{PREFIX}question_users`;
DROP TABLE IF EXISTS `{PREFIX}question_votes`;

DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Question';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'questions.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'questions.badges');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'questions.view');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'questions.ratings');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'question_tags.index');
DELETE FROM `{PREFIX}core_contents` WHERE `plugin` = 'Question';

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'questions.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'questions.badges';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'questions.view';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'questions.ratings';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'question_tags.index';

DELETE FROM `{PREFIX}acos` WHERE `group`='question';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='Question';

DELETE FROM `{PREFIX}tags` WHERE `type`='Question_Question';