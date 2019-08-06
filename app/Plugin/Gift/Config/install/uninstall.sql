DROP TABLE IF EXISTS `{PREFIX}gift_categories`;
DROP TABLE IF EXISTS `{PREFIX}gift_sents`;
DROP TABLE IF EXISTS `{PREFIX}gifts`;
DROP TABLE IF EXISTS `{PREFIX}gift_settings`;
DELETE FROM `{PREFIX}acos` WHERE `group` = 'gift';
DELETE FROM `{PREFIX}i18n` WHERE content IN('Gift Navi', 'Gift Detail Statistic', 'Gift Create', 'Gift Page', 'Gift Detail Page');
DELETE FROM `{PREFIX}i18n` WHERE model IN('Gift', 'GiftCategory');