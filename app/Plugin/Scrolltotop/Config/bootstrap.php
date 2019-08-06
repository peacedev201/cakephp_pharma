<?php	
	App::uses('ScrolltotopListener','Scrolltotop.Lib');
	CakeEventManager::instance()->attach(new ScrolltotopListener()); 
?>