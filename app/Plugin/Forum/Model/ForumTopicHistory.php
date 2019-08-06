<?php
class ForumTopicHistory extends ForumAppModel
{
    public $belongsTo = array( 'User' );

    public $order = 'ForumTopicHistory.created ASC';

    public function getHistory($target_id,$page)
    {
        $cond = array(
            'target_id' => $target_id
        );
        $activities = $this->find('all', array( 'conditions' => $cond,
            'limit' => RESULTS_LIMIT,
            'page' => $page
        )	);
        return $activities;
    }

    public function getHistoryCount($target_id)
    {
        $cond = array(
            'target_id' => $target_id
        );

        $count = $this->find('count', array('conditions' => $cond));

        return $count;
    }
}