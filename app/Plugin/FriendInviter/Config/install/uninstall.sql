DROP TABLE IF EXISTS `{PREFIX}user_suggest_codes`;
DROP TABLE IF EXISTS `{PREFIX}invites`;
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='FriendInviter';
DELETE FROM `{PREFIX}acos` WHERE `group`='friendinviter';
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'friend_inviters.index');
DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'friend_inviters.pending');
DELETE FROM `{PREFIX}core_contents` WHERE `name` = 'friend_inviters.badge';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'friend_inviters.index';
DELETE FROM `{PREFIX}pages` WHERE `uri` = 'friend_inviters.pending';