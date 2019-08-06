DROP TABLE IF EXISTS `{PREFIX}faqs`;
DROP TABLE IF EXISTS `{PREFIX}faq_helpfuls`;
DROP TABLE IF EXISTS `{PREFIX}faq_help_categories`;
DROP TABLE IF EXISTS `{PREFIX}faq_results`;

DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Faq';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'faqs.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'faqs.view');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'faqs.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'faqs.view';

DELETE FROM `{PREFIX}acos` WHERE `group`='faq';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='Faq';

DELETE FROM `{PREFIX}comments` WHERE `type`='Faq_Faq';

DELETE FROM `{PREFIX}likes` WHERE `type`='Faq_Faq';

DELETE FROM `{PREFIX}tags` WHERE `type`='Faq_Faq';