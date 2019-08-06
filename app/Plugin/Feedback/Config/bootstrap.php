<?php
    require_once(APP . DS . 'Plugin' . DS . 'Feedback' . DS .'Config' . DS . 'constants.php');
    App::uses('FeedbackListener','Feedback.Lib');
	CakeEventManager::instance()->attach(new FeedbackListener());	
?>