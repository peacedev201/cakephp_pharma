<?php
	if(Configure::read('Activitylog.activitylog_enabled')) {
        App::uses('ActivitylogListener', 'Activitylog.Lib');
        CakeEventManager::instance()->attach(new ActivitylogListener());
    }
?>