<?php

App::uses('FaqListener', 'Faq.Lib');
CakeEventManager::instance()->attach(new FaqListener());
MooSeo::getInstance()->addSitemapEntity("Faq", array('faq'));

define('FAQ_REASON_1', 1);
define('FAQ_REASON_2', 2);
define('FAQ_REASON_3', 3);
define('FAQ_REASON_4', 4);
?>