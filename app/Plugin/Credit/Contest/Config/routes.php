<?php
if(Configure::read('Contest.contest_enabled')){
	Router::connect('/contests/:action/*', array(
	    'plugin' => 'Contest',
	    'controller' => 'contests'
	));

	Router::connect('/contests/*', array(
	    'plugin' => 'Contest',
	    'controller' => 'contests',
	    'action' => 'index'
	));
}