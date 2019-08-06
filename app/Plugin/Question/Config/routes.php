<?php
if(Configure::read('Question.question_enabled')){
	Router::connect('/questions/:action/*', array(
	    'plugin' => 'Question',
	    'controller' => 'questions'
	));
	
	Router::connect('/questions/*', array(
	    'plugin' => 'Question',
	    'controller' => 'questions',
	    'action' => 'index'
	));
}