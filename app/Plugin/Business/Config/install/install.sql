CREATE TABLE `{PREFIX}business_addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(10) unsigned NOT NULL,
  `address` varchar(255) NOT NULL,
  `lat` varchar(20) NOT NULL,
  `lng` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` smallint(6) DEFAULT NULL,
  `rght` smallint(6) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `is_highlight` tinyint(1) NOT NULL DEFAULT '0',
  `business_count` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL,
  `feature_ordering` smallint(6) NOT NULL DEFAULT '0',
  `user_create` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_category_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(10) unsigned NOT NULL,
  `business_category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY(`business_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_category_searches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) NOT NULL,
  `values` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_checkins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_favourites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_follows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_location_searches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `lng` varchar(20) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` smallint(6) DEFAULT NULL,
  `rght` smallint(6) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `business_count` int(11) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `on_popup` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` smallint(6) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `city` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_nearby_cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `lat` varchar(20) NOT NULL,
  `lng` varchar(20) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_packages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `price` decimal(16,2) unsigned NOT NULL DEFAULT '0.00',
  `duration` int(11) unsigned NOT NULL DEFAULT '0',
  `duration_type` enum('day','week','month','year','forever') NOT NULL DEFAULT 'day',
  `expiration_reminder` int(11) unsigned NOT NULL DEFAULT '0',
  `expiration_reminder_type` enum('day','week','month','year') NOT NULL DEFAULT 'day',
  `billing_cycle` int(11) unsigned NOT NULL DEFAULT '0',
  `billing_cycle_type` enum('day','week','month','year') NOT NULL DEFAULT 'day',
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  `ordering` tinyint(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `photo_number` int(6) NOT NULL DEFAULT '0',
  `manage_admin` tinyint(1) NOT NULL DEFAULT '0',
  `response_review` tinyint(1) NOT NULL DEFAULT '0',
  `send_verification_request` tinyint(1) NOT NULL DEFAULT '0',
  `contact_form` tinyint(1) NOT NULL DEFAULT '0',
  `follow` tinyint(1) NOT NULL DEFAULT '0',
  `checkin` tinyint(1) NOT NULL DEFAULT '0',
  `favourite` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `most_popular` tinyint(1) NOT NULL DEFAULT '0',
  `trial` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_paids` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `business_id` int(11) unsigned NOT NULL DEFAULT '0',
  `business_package_id` int(11) unsigned NOT NULL DEFAULT '0',
  `pay_type` enum('unknown','featured_package','business_package') NOT NULL DEFAULT 'unknown',
  `feature_day` int(6) NOT NULL DEFAULT '0',
  `feature_price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `status` enum('initial','active','pending','expired','refunded','failed','cancel','process','inactive') NOT NULL DEFAULT 'initial',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `reminder_date` datetime DEFAULT NULL,
  `pay_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `gateway_id` int(10) DEFAULT NULL,
  `is_warning_email_sent` tinyint(1) DEFAULT '0',
  `currency_code` text NOT NULL,
  `business_transaction_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_payment_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_payment_id` int(11) unsigned NOT NULL,
  `business_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_review_usefuls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `business_id` int(11) NOT NULL,
  `lft` smallint(6) DEFAULT NULL,
  `rght` smallint(6) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` decimal(10,2) NOT NULL DEFAULT '0.00',
  `useful_count` int(11) NOT NULL DEFAULT '0',
  `report_count` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_times` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `time_open` time NOT NULL,
  `time_close` time NOT NULL,
  `next_day` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `business_id` int(11) NOT NULL DEFAULT '0',
  `business_paid_id` int(11) unsigned NOT NULL DEFAULT '0',
  `business_package_id` int(11) unsigned NOT NULL DEFAULT '0',
  `pay_type` enum('unknown','featured_package','business_package') NOT NULL DEFAULT 'unknown',
  `status` enum('initial','completed','pending','expired','refunded','failed','cancel','inactive') NOT NULL DEFAULT 'initial',
  `gateway_id` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `currency` char(3) NOT NULL DEFAULT '',
  `callback_params` text,
  `created` datetime DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `txn` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` smallint(6) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}business_verifies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone_number` varchar(225) DEFAULT NULL,
  `document` varchar(225) DEFAULT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}businesses` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `parent_id` int(11) NOT NULL DEFAULT '0',
    `user_id` int(11) unsigned NOT NULL,
    `creator_id` int(11) unsigned NOT NULL,
    `album_id` int(11) DEFAULT NULL,
    `business_location_id` int(11) NOT NULL,
    `business_type_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `company_number` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `logo` varchar(255) NOT NULL,
    `cover` varchar(255) DEFAULT NULL,
    `address` varchar(255) NOT NULL,
    `lat` varchar(20) NOT NULL,
    `lng` varchar(20) NOT NULL,
    `postal_code` varchar(15) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(255) NOT NULL,
    `fax` varchar(255) NOT NULL,
    `website` varchar(255) NOT NULL,
    `facebook` varchar(255) DEFAULT NULL,
    `twitter` varchar(255) DEFAULT NULL,
    `linkedin` varchar(255) DEFAULT NULL,
    `youtube` varchar(255) DEFAULT NULL,
    `instagram` varchar(255) DEFAULT NULL,
    `always_open` tinyint(1) NOT NULL DEFAULT '0',
    `timezone` varchar(50) DEFAULT NULL,
    `total_score` decimal(10,2) NOT NULL DEFAULT '0.00',
    `featured` tinyint(1) NOT NULL DEFAULT '0',
    `review_count` int(11) NOT NULL DEFAULT '0',
    `follow_count` int(11) NOT NULL DEFAULT '0',
    `photo_count` int(11) NOT NULL DEFAULT '0',
    `branch_count` int(11) NOT NULL DEFAULT '0',
    `checkin_count` int(11) NOT NULL DEFAULT '0',
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `verify` tinyint(1) NOT NULL DEFAULT '0',
    `is_free` tinyint(1) NOT NULL DEFAULT '1',
    `business_package_id` int(11) DEFAULT NULL,
    `claim_id` int(11) NOT NULL DEFAULT '0',
    `is_claim` tinyint(2) NOT NULL DEFAULT '0',
    `permissions` text,
    `created` datetime NOT NULL,
    `updated` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

insert into `{PREFIX}business_packages` (`id`, `type`, `name`, `price`, `duration`, `duration_type`, `expiration_reminder`, `expiration_reminder_type`, `billing_cycle`, `billing_cycle_type`, `deleted`, `ordering`, `created`, `updated`, `photo_number`, `manage_admin`, `response_review`, `send_verification_request`, `contact_form`, `follow`, `checkin`, `favourite`, `enable`, `is_default`, `most_popular`, `trial`) values('1','1','Free Package','0.00','1','forever','1','day','0','day','0','1','2016-03-21 02:57:39','2016-07-01 08:59:01','4','1','1','1','1','1','1','1','1','1','0','0');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('1','Visa','visa.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('2','Cash','cash.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('3','Mastercard','mastercard.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('4','Delta','delta.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('5','Maestro','maestro.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('6','Direct Debit','direct_debit.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('7','American Express','amarican_express.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('8','Visa Electron','visa_electron.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('9','Solo','solo.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('10','Credit Cards','credit_cards.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('11','Cheque','cheque.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('12','Diners Club Int','diners_club_int.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('13','BACS','bacs.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}business_payments` (`id`, `name`, `icon`, `enable`, `created`, `updated`) values('14','Paypal','paypal.png','1','0000-00-00 00:00:00','0000-00-00 00:00:00');
insert into `{PREFIX}tasks`(`title`,`plugin`,`timeout`,`enable`,`class`) values ('Business Task Payment','Business', '600', '1', 'Business_Task_Payment');
