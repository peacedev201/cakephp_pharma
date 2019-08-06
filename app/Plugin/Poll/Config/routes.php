<?php
if(Configure::read('Poll.poll_enabled')){
	Router::connect('/polls/:action/*', array(
	    'plugin' => 'Poll',
	    'controller' => 'polls'
	));
	
	Router::connect('/polls/*', array(
	    'plugin' => 'Poll',
	    'controller' => 'polls',
	    'action' => 'index'
	));
}