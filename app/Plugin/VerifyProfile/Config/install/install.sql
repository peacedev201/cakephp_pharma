CREATE TABLE IF NOT EXISTS `{PREFIX}verify_profiles` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `status` enum('verified','unverified','pending') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
    `images` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `created` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}verify_reasons` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `description` text COLLATE utf8_unicode_ci NOT NULL,
    `created` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;