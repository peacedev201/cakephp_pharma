<?php
	MooCache::getInstance()->setCache('question', array('groups' => array('question')));	
	if(Configure::read('Question.question_enabled')){
		App::uses('QuestionListener','Question.Lib');
		CakeEventManager::instance()->attach(new QuestionListener());
		
		MooSeo::getInstance()->addSitemapEntity("Question", array(
			'question'
		));
	}
	
	define('QUESTION_CAN_ERROR_LOGIN', '1');
	define('QUESTION_CAN_ERROR_POINT', '2');
	define('QUESTION_CAN_ERROR_NONE', '0');
?>