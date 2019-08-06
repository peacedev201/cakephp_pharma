<?php
    require_once(APP . DS . 'Plugin' . DS . 'Gift' . DS .'Config' . DS . 'constants.php');
	App::uses('GiftListener','Gift.Lib');
	CakeEventManager::instance()->attach(new GiftListener());
?>