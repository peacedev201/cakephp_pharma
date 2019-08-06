<?php
if(Configure::read('ProfileCompletion.profile_completion_enabled')){
	App::uses('ProfileCompletionListener', 'ProfileCompletion.Lib');
	CakeEventManager::instance()->attach(new ProfileCompletionListener(),null,array('priority'=>200));
}
?>
