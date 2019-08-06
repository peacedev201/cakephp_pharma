DROP TABLE IF EXISTS `{PREFIX}documents`;
DROP TABLE IF EXISTS `{PREFIX}document_licenses`;
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Document';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'documents.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'documents.view');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'documents.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'documents.view';

DELETE FROM `{PREFIX}acos` WHERE `group`='document';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='Document';

DELETE FROM `{PREFIX}comments` WHERE `type`='Document_Document';

DELETE FROM `{PREFIX}likes` WHERE `type`='Document_Document';

DELETE FROM `{PREFIX}hashtags` WHERE `item_table`='documents';

DELETE FROM `{PREFIX}tags` WHERE `type`='Document_Document';