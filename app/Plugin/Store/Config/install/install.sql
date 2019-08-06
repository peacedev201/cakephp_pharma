CREATE TABLE IF NOT EXISTS `{PREFIX}store_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `lft` int(10) unsigned DEFAULT NULL,
  `rght` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `force_to_buy` tinyint(4) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `enable` tinyint(1) DEFAULT '0',
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_order_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `amount_credit` decimal(10,2) NULL,
  `promo_price` decimal(10,2) DEFAULT NULL,
  `attributes` text,
  `attribute_ids` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `FK_orders_detail` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_code` varchar(255) DEFAULT NULL,
  `billing_email` varchar(255) NOT NULL,
  `billing_first_name` varchar(255) NOT NULL,
  `billing_last_name` varchar(255) NOT NULL,
  `billing_company` varchar(255) DEFAULT NULL,
  `billing_phone` varchar(255) NOT NULL,
  `billing_address` varchar(255) NOT NULL,
  `billing_city` varchar(255) NOT NULL,
  `billing_postcode` varchar(255) DEFAULT NULL,
  `billing_country` varchar(255) DEFAULT NULL,
  `billing_country_id` int(11) DEFAULT NULL,
  `shipping_email` varchar(255) NOT NULL,
  `shipping_first_name` varchar(255) NOT NULL,
  `shipping_last_name` varchar(255) NOT NULL,
  `shipping_company` varchar(255) DEFAULT NULL,
  `shipping_phone` varchar(255) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `shipping_city` varchar(255) NOT NULL,
  `shipping_postcode` varchar(255) DEFAULT NULL,
  `shipping_country` varchar(255) DEFAULT NULL,
  `shipping_country_id` int(11) DEFAULT NULL,
  `order_comments` text,
  `store_payment_id` int(11) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `store_shipping_id` int(11) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT '0.00',
  `shipping_fee_credit` decimal(10,2) DEFAULT '0.00',
  `shipping_description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `amount_credit` decimal(10,2) NULL,
  `site_profit` decimal(10,2) NOT NULL,
  `site_profit_credit` decimal(10,2) NOT NULL,
  `order_status` enum('NEW','PENDING','CANCELLED','PROCESSING','REFUNDED','COMPLETED') DEFAULT 'NEW',
  `currency` varchar(10) DEFAULT NULL,
  `currency_symbol` varchar(10) DEFAULT NULL,
  `currency_position` varchar(10) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_producers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `enable` tinyint(1) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_product_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `force_to_buy` tinyint(1) DEFAULT '0',
  `plus` tinyint(1) DEFAULT '1',
  `attribute_price` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `FK_product_attribute` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_product_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `enable` int(1) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_product_wishlists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NULL,
  `store_category_id` int(11) NOT NULL,
  `producer_id` int(11) DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `enable` tinyint(1) DEFAULT '0',
  `approve` tinyint(1) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `promotion_price` decimal(10,2) DEFAULT '0.00',
  `include_tax` tinyint(1) DEFAULT '0',
  `allow_promotion` tinyint(1) DEFAULT '0',
  `promotion_start` varchar(255) DEFAULT NULL,
  `promotion_end` varchar(255) DEFAULT NULL,
  `is_limit_amount` tinyint(4) DEFAULT '0',
  `limit_buy_min` int(11) DEFAULT NULL,
  `limit_buy_max` int(11) DEFAULT NULL,
  `shipping_id` varchar(255) DEFAULT NULL,
  `member_only` tinyint(1) DEFAULT '0',
  `out_of_stock` tinyint(1) DEFAULT '0',
  `allow_share` tinyint(1) DEFAULT '1',
  `allow_contact_form` tinyint(1) DEFAULT '1',
  `is_social` tinyint(1) DEFAULT '1',
  `allow_comment` tinyint(1) DEFAULT '1',
  `allow_review` tinyint(1) DEFAULT '1',
  `is_related` tinyint(1) DEFAULT '1',
  `is_qr` tinyint(1) DEFAULT '1',
  `is_call` tinyint(1) DEFAULT '0',
  `is_contact` tinyint(1) DEFAULT '0',
  `is_hot` tinyint(1) DEFAULT '0',
  `is_new` tinyint(1) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `warranty` varchar(255) DEFAULT NULL,
  `brief` varchar(255) DEFAULT NULL,
  `article` longtext,
  `promotion_content` text,
  `rating` decimal(10,2) DEFAULT '0.00',
  `rating_count` int(11) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `dislike_count` int(11) DEFAULT '0',
  `comment_count` int(11) DEFAULT '0',
  `share_count` int(11) DEFAULT '0',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `show_quanity` tinyint(1) DEFAULT '1',
  `featured` tinyint(1) DEFAULT '0',
  `unlimited_feature` tinyint(1) DEFAULT '0',
  `weight` decimal(10,2) DEFAULT '0.00',
  `feature_expiration_date` datetime DEFAULT NULL,
  `sent_expiration_email` tinyint(1) DEFAULT '0',
  `product_type` enum('regular','digital','link') NOT NULL DEFAULT 'regular',
  `digital_file` varchar(255) DEFAULT NULL,
  `product_link` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}stores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `paypal_api_username` varchar(255) DEFAULT NULL,
  `paypal_api_password` varchar(255) DEFAULT NULL,
  `paypal_api_signature` varchar(255) DEFAULT NULL,
  `paypal_sandbox` tinyint(1) DEFAULT '0',
  `enable_paypal` tinyint(1) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `dislike_count` int(11) DEFAULT '0',
  `comment_count` int(11) DEFAULT '0',
  `share_count` int(11) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `privacy` int(1) NOT NULL,
  `enable` tinyint(1) DEFAULT '1',
  `store_price` varchar(255) DEFAULT NULL,
  `store_transaction_currency` varchar(10) DEFAULT NULL,
  `store_transaction_id` varchar(255) DEFAULT NULL,
  `paypal_email` varchar(255) DEFAULT NULL,
  `payments` varchar(255) DEFAULT NULL,
  `paypal_first_name` varchar(255) DEFAULT NULL,
  `paypal_last_name` varchar(255) DEFAULT NULL, 
  `featured` tinyint(1) DEFAULT '0',
  `unlimited_feature` tinyint(1) DEFAULT '0',
  `feature_expiration_date` datetime DEFAULT NULL,
  `sent_expiration_email` tinyint(1) DEFAULT '0',
  `policy` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `lft` int(10) unsigned DEFAULT NULL,
  `rght` int(10) unsigned DEFAULT NULL,
  `enable` tinyint(1) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_product_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` text,
  `rating` int(11) DEFAULT '0',
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `enable` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_product_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `content` text,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_payments` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `key_name` varchar(20) NOT NULL,
    `description` text,
    `information` text,
    `is_online` tinyint(1) DEFAULT '0',
    `enable` tinyint(1) DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_transactions` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `store_id` int(11) unsigned NOT NULL,
    `store_product_id` int(11) unsigned NOT NULL,
    `store_package_id` int(11) NOT NULL,
    `gateway_id` int(10) unsigned NOT NULL DEFAULT '0',
    `item_name` varchar(255) NOT NULL,
    `status` enum('initial','completed','pending','expired','refunded','failed','cancel','inactive') NOT NULL DEFAULT 'initial',
    `amount` decimal(16,2) NOT NULL DEFAULT '0.00',
    `currency` char(3) NOT NULL DEFAULT '',
    `currency_symbol` char(3) NOT NULL DEFAULT '',
    `period` int(11) DEFAULT NULL COMMENT 'days',
    `expiration_date` datetime DEFAULT NULL,
    `transaction_id` varbinary(255) DEFAULT NULL,
    `callback_params` text,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_packages` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `price` decimal(16,2) NOT NULL DEFAULT '0.00',
    `period` int(11) NOT NULL DEFAULT 1,
    `reminder` int(11) NOT NULL DEFAULT 1,
    `description` text,
    `enable` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_shipping_methods` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `key_name` varchar(255) NOT NULL,
    `enabled` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_shippings` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `store_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `store_shipping_zone_id` int(11) NOT NULL,
    `store_shipping_method_id` int(11) NOT NULL,
    `price` decimal(10,2) NOT NULL DEFAULT '0.00',
    `weight` decimal(10,2) DEFAULT '0.00',
    `enable` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_shipping_zones` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `store_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `enable` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_shipping_zone_locations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `store_id` int(11) NOT NULL,
    `store_shipping_zone_id` int(11) NOT NULL,
    `country_id` int(11) NOT NULL,
    `enable` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_shipping_details` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `store_id` int(11) NOT NULL,
    `store_shipping_method_id` int(11) NOT NULL,
    `enable` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_digital_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}store_product_videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `enable` int(1) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_review_usefuls` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `store_review_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}store_reviews` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL,
    `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
    `store_product_id` int(11) NOT NULL,
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

insert into `{PREFIX}acos` (`group`, `key`, `description`) values('store','create_store','Create/Edit Seller');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('store','view_product_detail','View Product Detail');
insert into `{PREFIX}acos` (`group`, `key`, `description`) values('store','buy_product','Buy Product');
insert  into `{PREFIX}store_packages`(`id`,`name`,`price`,`period`,`description`,`enable`) values (1,'Featured Product',0.00,0,'',1);
insert  into `{PREFIX}store_packages`(`id`,`name`,`price`,`period`,`description`,`enable`) values (2,'Featured Store',0.00,0,'',1);
insert  into `{PREFIX}store_payments`(`id`,`name`,`key_name`,`description`,`information`,`is_online`) values (1,'Cash on delivery','cheque','Customer pays when goods are delivered.',NULL,0),(2,'Pay in Store','cheque_store','Customer pay in store when collecting goods.',NULL,0),(3,'PayPal','paypal','Customer pays online and goods get delivered.','',1),(4,'PayPal Collect','paypal_store','Customer pays online and collects goods from store.','',1),(5,'Credit','credits','Customer pays online by using credits.','',0);
insert  into `{PREFIX}tasks`(`title`,`plugin`,`timeout`,`enable`,`class`) values ('Store Items Expiration','Store', '600', '1', 'Store_Task_Expiration');
insert  into `{PREFIX}store_shipping_methods`(`id`,`name`,`key_name`,`enabled`) values (1,'Free Shipping','free_shipping',1),(2,'Per item Shipping Rate','per_item_shipping',1),(3,'Pickup From Store','pickup_from_store',1),(4,'Flat Shipping Rate','flat_shipping_rate',1),(5,'Weight Based Shipping','weight_based_shipping',1);