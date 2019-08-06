<?php

App::import('Cron.Task', 'CronTaskAbstract');

class ForumTaskExpire extends CronTaskAbstract {

    public function execute() {
        if(Configure::read('Forum.forum_enabled')){
            $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
            $helper = MooCore::getInstance()->getHelper('Forum_Forum');
            //Do expire
            $items = $topicModel->find('all',
                array(
                    'conditions' => array(
                        'ForumTopic.ping' => 1,
                        'ForumTopic.ping_expire <' => date("Y-m-d H:i:s"),
                        'ForumTopic.ping_expire <>' => 'NULL',
                    ),
                    'limit'=>20
                )
            );
            foreach ($items as $topic)
            {
                $helper->onExpire($topic);
            }
        }
    }
}
