<?php 
class ReminderCronController extends ReminderAppController{  
	public $check_subscription = false;
	public $check_force_login = false;
    public function cron()
    {
    	if (!Configure::read('Reminder.reminder_enabled'))
    	{
    		return;
    	}
    	$helper = MooCore::getInstance()->getHelper("Reminder_Reminder");
    	$helper->runCron();
    	die('done');
    }
}