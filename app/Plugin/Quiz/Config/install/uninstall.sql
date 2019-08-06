-- delete table
DROP TABLE IF EXISTS `{PREFIX}quizzes`;
DROP TABLE IF EXISTS `{PREFIX}quiz_answers`;
DROP TABLE IF EXISTS `{PREFIX}quiz_questions`;
DROP TABLE IF EXISTS `{PREFIX}quiz_results`;
DROP TABLE IF EXISTS `{PREFIX}quiz_takes`;

-- delete content
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Quiz';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'quizzes.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'quizzes.view');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'quizzes.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'quizzes.view';

DELETE FROM `{PREFIX}acos` WHERE `group`='quiz';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='Quiz';

DELETE FROM `{PREFIX}categories` WHERE `type`='Quiz_Quiz';

DELETE FROM `{PREFIX}comments` WHERE `type`='Quiz_Quiz';

DELETE FROM `{PREFIX}likes` WHERE `type`='Quiz_Quiz';

DELETE FROM `{PREFIX}mailtemplates` WHERE `plugin`='Quiz';

DELETE FROM `{PREFIX}hashtags` WHERE `item_table`='quizzes';

DELETE FROM `{PREFIX}tags` WHERE `type`='Quiz_Quiz';