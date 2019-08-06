<?php
if(Configure::read('Reminder.reminder_enabled')){
	App::uses('ReminderListener','Reminder.Lib');
	CakeEventManager::instance()->attach(new ReminderListener());
}
?>