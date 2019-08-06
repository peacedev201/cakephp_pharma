<?php
	if(Configure::read('Poll.poll_enabled')){
		App::uses('PollListener','Poll.Lib');
		CakeEventManager::instance()->attach(new PollListener());
		
		MooSeo::getInstance()->addSitemapEntity("Poll", array(
			'poll'
		));
	}
	
	define('POLL_MAX_ITEM_FEED', 3);
	define('POLL_MAX_USER', 0);
?>