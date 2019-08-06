ALTER TABLE `{PREFIX}users` ADD `sms_verify` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_social`;
ALTER TABLE `{PREFIX}users` ADD `sms_verify_phone` VARCHAR(128) NULL DEFAULT NULL AFTER `is_social`;
ALTER TABLE `{PREFIX}users` ADD `sms_verify_checked` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_social`;

CREATE TABLE `{PREFIX}sms_verify_gateways` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `element` varchar(256) NOT NULL,
  `params` text NOT NULL,
  `enable` tinyint(1) NOT NULL,
  `class` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `{PREFIX}sms_verify_gateways` (`id`, `name`, `element`, `params`, `enable`, `class`) VALUES
(1, 'Twilio', 'twilio', '', 1, 'SmsTwilio'),
(2, 'Clickatell', 'clickatell', '', 0, 'SmsClickatell');