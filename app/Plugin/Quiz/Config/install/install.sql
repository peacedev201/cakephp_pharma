CREATE TABLE IF NOT EXISTS `{PREFIX}quizzes` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL,
    `category_id` int(11) unsigned NOT NULL DEFAULT '0',
    `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `description` text COLLATE utf8_unicode_ci,
    `thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `comment_count` smallint(5) unsigned NOT NULL DEFAULT '0',
    `share_count` smallint(5) unsigned NOT NULL DEFAULT '0',
    `like_count` smallint(5) unsigned NOT NULL DEFAULT '0',
    `dislike_count` smallint(5) unsigned NOT NULL DEFAULT '0',
    `take_count` smallint(5) unsigned NOT NULL DEFAULT '0',
    `published` tinyint(2) unsigned NOT NULL DEFAULT '0',
    `approved` tinyint(2) unsigned NOT NULL DEFAULT '1',
    `privacy` tinyint(2) unsigned NOT NULL DEFAULT '1',
    `timer` smallint(5) unsigned NOT NULL DEFAULT '0',
    `pass_score` tinyint(3) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}quiz_answers` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `quiz_question_id` int(11) unsigned NOT NULL,
    `title` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
    `correct` tinyint(2) unsigned NOT NULL DEFAULT '0',
    `weight` smallint(5) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}quiz_questions` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `quiz_id` int(11) unsigned NOT NULL,
    `title` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
    `weight` smallint(5) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}quiz_results` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `quiz_take_id` int(11) unsigned NOT NULL,
    `question_id` int(11) unsigned NOT NULL,
    `answer_id` int(11) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{PREFIX}quiz_takes` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `quiz_id` int(11) unsigned NOT NULL,
    `user_id` int(11) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `privacy` tinyint(2) unsigned NOT NULL DEFAULT '1',
    `privacy_hash` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
    `correct_answer` smallint(5) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;