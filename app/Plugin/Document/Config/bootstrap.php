<?php
	if(Configure::read('Document.document_enabled')){
		App::uses('DocumentListener','Document.Lib');
		CakeEventManager::instance()->attach(new DocumentListener());
		
		MooSeo::getInstance()->addSitemapEntity("Document", array(
    		'document'
   	 	));
	}
	define('DOCUMENT_STATUS_PROCESSING', 'PROCESSING');
	define('DOCUMENT_STATUS_ERROR', 'ERROR');
	define('DOCUMENT_STATUS_DONE', 'DONE'); 
	
?>