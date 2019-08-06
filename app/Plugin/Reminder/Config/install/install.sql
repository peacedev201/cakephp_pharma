CREATE TABLE `{PREFIX}reminder_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `verify_date` datetime DEFAULT NULL,
  `verify_time` int(11) NOT NULL DEFAULT '0',
  `verify_sms_date` datetime DEFAULT NULL,
  `verify_sms_time` int(11) NOT NULL DEFAULT '0',
  `share_date` datetime DEFAULT NULL,
  `share_time` int(11) NOT NULL DEFAULT '0',
  `login_date` datetime DEFAULT NULL,
  `login_time` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO {PREFIX}reminder_users (user_id,verify_date,verify_sms_date, share_date,login_date)
SELECT id , now(),now(),now(),now()
FROM {PREFIX}users;

INSERT INTO `{PREFIX}tasks` (`title`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`, `enable`, `class`) VALUES
('Reminder Cron', 'Reminder', '120', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', 'Reminder_Task_Cron');