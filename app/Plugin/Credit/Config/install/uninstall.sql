
DROP TABLE IF EXISTS `{PREFIX}credit_ranks`;
DROP TABLE IF EXISTS `{PREFIX}credit_sells`;
DROP TABLE IF EXISTS `{PREFIX}credit_balances`;
DROP TABLE IF EXISTS `{PREFIX}credit_orders`;
DROP TABLE IF EXISTS `{PREFIX}credit_logs`;
DROP TABLE IF EXISTS `{PREFIX}credit_faqs`;
DROP TABLE IF EXISTS `{PREFIX}credit_actiontypes`;
DROP TABLE IF EXISTS `{PREFIX}credit_withdraws`;

DELETE FROM `{PREFIX}core_blocks` WHERE path_view = 'credits.buy';
DELETE FROM `{PREFIX}core_contents` WHERE name = 'credits.buy';

DELETE FROM `{PREFIX}core_blocks` WHERE path_view = 'credits.send';
DELETE FROM `{PREFIX}core_contents` WHERE name = 'credits.send';

DELETE FROM `{PREFIX}core_blocks` WHERE path_view = 'credits.options';
DELETE FROM `{PREFIX}core_contents` WHERE name = 'credits.options';

DELETE FROM `{PREFIX}core_blocks` WHERE path_view = 'credits.rank';
DELETE FROM `{PREFIX}core_contents` WHERE name = 'credits.rank';

DELETE FROM `{PREFIX}core_blocks` WHERE path_view = 'credits.badge';
DELETE FROM `{PREFIX}core_contents` WHERE name = 'credits.badge';

DELETE FROM `{PREFIX}core_contents` WHERE `page_id` = (SELECT id FROM `{PREFIX}pages` WHERE `uri` = 'credits.index');

DELETE FROM `{PREFIX}pages` WHERE `uri` = 'credits.index';

DELETE FROM `{PREFIX}notifications` WHERE `plugin` = 'Credit';

DELETE FROM `{PREFIX}gateways` WHERE `plugin` = 'Credit';

DELETE FROM `{PREFIX}acos` WHERE `group` = 'credit';