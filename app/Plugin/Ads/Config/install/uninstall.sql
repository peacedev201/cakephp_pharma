DROP TABLE IF EXISTS `{PREFIX}ads_campaigns`;
DROP TABLE IF EXISTS `{PREFIX}ads_placements`;
DROP TABLE IF EXISTS `{PREFIX}ads_positions`;
DROP TABLE IF EXISTS `{PREFIX}ads_reports`;
DROP TABLE IF EXISTS `{PREFIX}ads_transactions`;
DROP TABLE IF EXISTS `{PREFIX}ads_placement_feeds`;
DELETE FROM `{PREFIX}core_blocks` WHERE `plugin`='Ads';
DELETE FROM `{PREFIX}core_contents` WHERE `plugin`='Ads';
DELETE FROM `{PREFIX}acos` WHERE `group` = 'ads';