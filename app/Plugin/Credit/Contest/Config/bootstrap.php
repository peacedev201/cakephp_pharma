<?php
    define('CONTEST_ADMIN_ID', 1);
    if(Configure::read('Contest.contest_enabled')){
        App::uses('ContestListener','Contest.Lib');
        CakeEventManager::instance()->attach(new ContestListener());
        MooSeo::getInstance()->addSitemapEntity("Contest", array(
            'contest'
        ));
    }
?>