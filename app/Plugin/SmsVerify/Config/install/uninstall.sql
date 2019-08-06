ALTER TABLE `{PREFIX}users` DROP `sms_verify`;
ALTER TABLE `{PREFIX}users` DROP `sms_verify_phone`;
ALTER TABLE `{PREFIX}users` DROP `sms_verify_checked`;

DROP TABLE IF EXISTS `{PREFIX}sms_verify_gateways`;