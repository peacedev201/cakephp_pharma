<?php
require_once(APP . DS . 'Plugin' . DS . 'Usernotes' . DS .'Config' . DS . 'constants.php');
	App::uses('UsernotesListener','Usernotes.Lib');
	CakeEventManager::instance()->attach(new UsernotesListener());	
?>