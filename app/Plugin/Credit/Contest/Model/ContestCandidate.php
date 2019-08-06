<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('ContestAppModel', 'Contest.Model');
class ContestCandidate extends ContestAppModel {

    public $mooFields = array('plugin', 'type');
    public $belongsTo = array(
        'Contest' => array(
            'counterCache' => true,
            'className' => 'Contest.Contest',
        ),
        'User'
    );
    
    public function join($contest, $user_id) {
        $this->clear();
        $this->save(array('contest_id' => $contest['Contest']['id'], 'user_id' => $user_id));
        $notificationModel = MooCore::getInstance()->getModel('Notification');
        $notification = $notificationModel->find('first', array('conditions' => array(
                'user_id' => $contest['Contest']['user_id'],
                'sender_id' => $user_id,
                'action' => 'contest_join',
                'params' => $contest['Contest']['id'],
                'plugin' => 'Contest'
        )));
        $helper = MooCore::getInstance()->getHelper('Contest_Contest'); 
        $is_block = $helper->areUserBlocks($user_id, $contest['Contest']['user_id']);
        if (empty($notification) && $user_id != $contest['Contest']['user_id'] && !$is_block) {
            $notificationModel->record(array(
                'recipients' => $contest['Contest']['user_id'],
                'sender_id' => $user_id,
                'action' => 'contest_join',
                'url' =>  '/contests/view/' .  $contest['Contest']['id'].'/'. seoUrl($contest['Contest']['name']) .'/tab:candidate',
                'params' => $contest['Contest']['id'],
                'plugin' => 'Contest'
            ));
        }
        return true;
    }
    
    public function leave($contest_id, $user_id) {
        $record = $this->find('first', array('conditions' => array('ContestCandidate.contest_id' => $contest_id, 'ContestCandidate.user_id' => $user_id), 'limit' => 1));
        if($record['ContestCandidate']['id']) {
            $this->deleteCandidate($record['ContestCandidate']['id']);
        }
    }
    public function getContestCandidate($contest_id, $page = 1, $limit = 12) {
        $conditions =  array('ContestCandidate.contest_id' => $contest_id);
        $conditions = $this->addBlockCondition($conditions);
        return $this->find('all', array('conditions' => $conditions,'limit' => $limit, 'page' => $page));
    }
    public function getContestCandidateCount($contest_id) {
        $conditions =  array('ContestCandidate.contest_id' => $contest_id);
        $conditions = $this->addBlockCondition($conditions);
        return $this->find('count', array('conditions' => $conditions));
    }
    public function getJoinedContest($user_id) {
        $conditions = array('ContestCandidate.user_id' => $user_id);
        $conditions = $this->addBlockCondition($conditions);
        return $this->find('list', array(
                 'conditions' => $conditions,
                'fields' => array('ContestCandidate.contest_id')
            ));
    }
    public function isCandidateEmail($email) {
        $cond = array(
            'User.email' => $email
        );
        return $this->find('count', array('conditions' => $cond));
    }
    public function deleteCandidate($id) {
        $this->delete($id);
    }
     public function deleteCandidates($uid) {
        $candidates = $this->find('all', array('conditions' => array('ContestCandidate.user_id' => $uid)));
        if (!empty($candidates)) {
            foreach ($candidates as $candidate) {
                $this->deleteCandidate($candidate['ContestCandidate']['id']);
            }
        }
    }

}
