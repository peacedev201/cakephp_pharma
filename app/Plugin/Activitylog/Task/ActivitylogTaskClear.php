<?php

App::import('Cron.Task', 'CronTaskAbstract');

class ActivitylogTaskClear extends CronTaskAbstract {

    public function execute() {
        if(Configure::read('Activitylog.activitylog_enabled')){
            $mModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
            $mModel->deleteAll(array('DATEDIFF(CURDATE(), Activitylog.created) > 125'));
        }
    }
}
