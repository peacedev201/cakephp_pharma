<?php
if(Configure::read('Activitylog.activitylog_enabled')){
	Router::connect('/activity_log/:action/*', array(
	    'plugin' => 'Activitylog',
	    'controller' => 'activitylogs'
	));
    Router::connect("/activity_log/*",array('plugin'=>'Activitylog','controller'=>'activitylogs','action'=>'index'));
}