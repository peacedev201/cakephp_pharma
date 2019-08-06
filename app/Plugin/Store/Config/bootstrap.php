<?php
    require_once(APP . DS . 'Plugin' . DS . 'Store' . DS .'Config' . DS . 'constants.php');
	App::uses('StoreListener','Store.Lib');
	CakeEventManager::instance()->attach(new StoreListener());	
?>