<?php
class ForumFavorite extends ForumAppModel
{
    public function getUsersList($target_id) {
        $cond = array('target_id' => $target_id);

        $cond = $this->addBlockCondition($cond);
        $users = $this->find('list', array('conditions' => $cond,
            'fields' => array('ForumFavorite.user_id')
        ));

        return $users;
    }

    public function getTopicList($uid) {
        $cond = array('user_id' => $uid);

        $cond = $this->addBlockCondition($cond);
        $ids = $this->find('list', array('conditions' => $cond,
            'fields' => array('ForumFavorite.target_id')
        ));

        return $ids;
    }

    public function isFavorite($user_id, $topic_id){
        $result = $this->find('first', array(
            'conditions' => array('ForumFavorite.user_id' => $user_id, 'ForumFavorite.target_id' => $topic_id)
        ));

        if(!empty($result)){
            return $result['ForumFavorite']['id'];
        }
        return false;
    }
}