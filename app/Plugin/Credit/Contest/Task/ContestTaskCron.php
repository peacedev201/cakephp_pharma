<?php

App::import('Cron.Task', 'CronTaskAbstract');

class ContestTaskCron extends CronTaskAbstract {

    public function execute() {
        if (Configure::read('Contest.contest_enabled')) {
            $contestModel = MooCore::getInstance()->getModel('Contest.Contest');
            $helper = MooCore::getInstance()->getHelper('Contest_Contest');
            // expire
            $items = $contestModel->find('all', array(
                'conditions' => array(
                    'Contest.contest_status' => 'published',
                    'Contest.approve_status' => 'approved',
                    'Contest.duration_end <=' => date("Y-m-d H:i:s")
                ),
                'limit' => 10
                )
            );
            foreach ($items as $item) {
                $contestModel->updateContestStatus($item['Contest']['id'], 'closed');
            }
        }
    }

}
