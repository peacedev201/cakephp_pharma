-- delete table
DROP TABLE IF EXISTS `{PREFIX}verify_profiles`;
DROP TABLE IF EXISTS `{PREFIX}verify_reasons`;

-- delete content
DELETE FROM `{PREFIX}acos` WHERE `group`='verify_profile';

DELETE FROM `{PREFIX}activities` WHERE `plugin`='VerifyProfile';