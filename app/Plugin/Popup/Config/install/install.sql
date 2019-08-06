--
-- Table structure for table `popups`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}popups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL DEFAULT '0',
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `onetime` tinyint(1) NOT NULL DEFAULT '1',
  `popup_option` tinyint(1) NOT NULL DEFAULT '1',
  `permission` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


CREATE TABLE IF NOT EXISTS `{PREFIX}popup_saves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `popup_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

--
-- Dumping data for table `popups`
--

INSERT INTO `{PREFIX}popups` (`id`, `page_id`, `title`, `body`, `onetime`, `popup_option`, `permission`, `enable`) VALUES
(1, 1, 'Welcome', 'Welcome to website!', 0, 1, '', 1);


