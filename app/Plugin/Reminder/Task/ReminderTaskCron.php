<?php
App::import('Cron.Task','CronTaskAbstract');
class ReminderTaskCron extends CronTaskAbstract
{
    public function execute()
    {
    	if (!Configure::read('Reminder.reminder_enabled'))
    	{
    		return;
    	}
    	$helper = MooCore::getInstance()->getHelper("Reminder_Reminder");
    	$helper->runCron();
    }
}