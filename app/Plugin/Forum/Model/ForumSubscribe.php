<?php
class ForumSubscribe extends ForumAppModel
{
    public function getUsersList($target_id, $type = 'Topic') {
        $cond = array('ForumSubscribe.target_id' => $target_id, 'type' => $type);

        $cond = $this->addBlockCondition($cond);
        $users = $this->find('list', array('conditions' => $cond,
            'fields' => array('ForumSubscribe.user_id')
        ));

        return $users;
    }

    public function getUserItemList($uid, $type = 'Topic') {
        $cond = array('ForumSubscribe.user_id' => $uid, 'type' => $type);

        $cond = $this->addBlockCondition($cond);
        $users = $this->find('list', array('conditions' => $cond,
            'fields' => array('ForumSubscribe.target_id')
        ));

        return $users;
    }

    public function isSubscribe($user_id, $target_id, $type = 'Topic'){
        $result = $this->find('first', array(
            'conditions' => array('ForumSubscribe.user_id' => $user_id, 'ForumSubscribe.target_id' => $target_id, 'ForumSubscribe.type' => $type)
        ));

        if(!empty($result)){
            return $result['ForumSubscribe']['id'];
        }
        return false;
    }
}