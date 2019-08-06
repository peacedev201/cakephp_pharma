<?php
if(Configure::read('Document.document_enabled')){
	Router::connect('/documents/:action/*', array(
	    'plugin' => 'Document',
	    'controller' => 'documents'
	));
	
	Router::connect('/documents/*', array(
	    'plugin' => 'Document',
	    'controller' => 'documents',
	    'action' => 'index'
	));
	
	Router::connect('/document/:controller/:action/*', array(
	    'plugin' => 'Document',
	    'controller' => 'documents',
	    'action' => 'index'
	));
}