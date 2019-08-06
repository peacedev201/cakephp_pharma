CREATE TABLE IF NOT EXISTS `{PREFIX}ads_campaigns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ads_placement_id` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `note` text,
  `name` varchar(255) NOT NULL,
  `set_date` datetime DEFAULT NULL,
  `set_end_date` datetime DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `payment_status` tinyint(1) DEFAULT '0',
  `ads_title` varchar(25) DEFAULT NULL,
  `ads_image` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `description` varchar(90) DEFAULT NULL,
  `role_id` varchar(255) DEFAULT NULL,
  `age_from` varchar(255) DEFAULT NULL,
  `age_to` varchar(255) DEFAULT NULL,
  `gender` enum('male','famale') DEFAULT NULL,
  `item_status` enum('pending','active','disable') DEFAULT 'pending',
  `view_count` int(11) DEFAULT '0',
  `click_count` int(11) DEFAULT '0',
  `last_date_report` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `is_hide` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}ads_placements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `placement_type` enum('html','image','feed') NOT NULL,
  `ads_position_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `dimension_width` decimal(10,0) NOT NULL,
  `dimension_height` decimal(10,0) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `period` int(11) NOT NULL,
  `view_limit` int(11) DEFAULT '0',
  `click_limit` int(11) DEFAULT '0',
  `number_of_ads` int(11) NOT NULL,
  `total_ads` int(11) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}ads_positions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into `{PREFIX}ads_positions`(`id`,`name`,`image`,`ordering`) values (1,'bottom','bottom.png',2),(2,'bottom left','bottom-left.png',4),(3,'bottom left 2c','bottom-left-2c.png',8),(4,'bottom middle','bottom-middle.png',12),(5,'bottom right','bottom-right.png',6),(6,'bottom right 2c','bottom-right-2c.png',10),(7,'top','top.png',1),(8,'top left','top-left.png',3),(9,'top left 2c','top-left-2c.png',7),(10,'top middle','top-middle.png',11),(11,'top right','top-right.png',5),(12,'top right 2c','top-right-2c.png',9),(13,'feed','feed.png',13);

CREATE TABLE IF NOT EXISTS `{PREFIX}ads_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ads_campaign_id` int(11) NOT NULL,
  `type` enum('view','click') NOT NULL,
  `male` tinyint(1) DEFAULT '0',
  `famale` tinyint(1) DEFAULT '0',
  `role_id` int(11) DEFAULT '0',
  `under_20` tinyint(1) DEFAULT '0',
  `20_to_50` tinyint(1) DEFAULT '0',
  `above_50` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}ads_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ads_campaign_id` int(11) NOT NULL,
  `ads_placement_id` int(11) NOT NULL,
  `ads_campaign_name` varchar(255) NOT NULL,
  `ads_placement_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `currency_symbol` varchar(5) NOT NULL,
  `status` enum('pending','failed','canceled','completed') NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `verification_code` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}ads_placement_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_placement_id` int(11) NOT NULL,
   `feed_position` int(11) NOT NULL, 
 PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{PREFIX}core_blocks` (`name`, `path_view`, `params`, `is_active`, `plugin_id`, `group`, `restricted`, `plugin`) VALUES
('Ads','ads.advertisement','[\r\n   {\r\n      \"label\":\"Title\",\r\n      \"input\":\"text\",\r\n      \"value\":\"Advertising\",\r\n      \"name\":\"title\"\r\n   },\r\n   {\r\n      \"label\":\"Select Placement\",\r\n      \"input\":\"select\",\r\n      \"value\":{\r\n       },\r\n      \"name\":\"placement\"\r\n   },\r\n   {\r\n      \"label\":\"Title\",\r\n      \"input\":\"checkbox\",\r\n      \"value\":\"Enable Title\",\r\n      \"name\":\"title_enable\"\r\n   },\r\n   {\r\n      \"label\":\"Background Block\",\r\n      \"input\":\"checkbox\",\r\n      \"value\":\"1\",\r\n      \"name\":\"background_block\"\r\n   },\r\n   {\r\n      \"label\":\"Show see your ad here\",\r\n      \"input\":\"checkbox\",\r\n      \"value\":\"1\",\r\n      \"name\":\"see_your_ad_here\"\r\n   },\r\n   {\r\n      \"label\":\"plugin\",\r\n      \"input\":\"hidden\",\r\n      \"value\":\"Ads\",\r\n      \"name\":\"plugin\"\r\n   }\r\n]','1','0','','','Ads');
INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES('ads', 'can_add_ads', 'Can add ads');
INSERT INTO `{PREFIX}acos` (`group`, `key`, `description`) VALUES('ads', 'hide_all_ads', 'Hide all ads');
