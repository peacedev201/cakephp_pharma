<?php
    require_once(APP . DS . 'Plugin' . DS . 'Ads' . DS .'Config' . DS . 'constants.php');
	App::uses('AdsListener','Ads.Lib');
	CakeEventManager::instance()->attach(new AdsListener());
       
?>