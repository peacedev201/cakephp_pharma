<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('ContestAppModel', 'Contest.Model');

class ContestVote extends ContestAppModel {

    public $mooFields = array('plugin', 'type');
    public $belongsTo = array(
        'ContestEntry' => array(
            'counterCache' => true,
            'className' => 'Contest.ContestEntry',
        ),
        'User'
    );

    public function isVote($entry_id, $user_id) {
        $cond = array(
            'ContestVote.contest_entry_id' => $entry_id,
            'ContestVote.user_id' => $user_id
        );
        return $this->hasAny($cond);
    }

    public function vote($entry_id, $user_id) {
        if (!$this->isVote($entry_id, $user_id)) {
            $mEntry = MooCore::getInstance()->getModel('Contest.ContestEntry');
            $entry = $mEntry->findById($entry_id);
            $this->clear();
            return $this->save(array('contest_entry_id' => $entry_id, 'contest_id' => $entry['ContestEntry']['contest_id'], 'user_id' => $user_id));
        }
    }

    public function un_vote($entry_id, $user_id) {
        $record = $this->find('first', array('conditions' => array('ContestVote.contest_entry_id' => $entry_id, 'ContestVote.user_id' => $user_id), 'limit' => 1));
        if ($record['ContestVote']['id']) {
            $this->delete($record['ContestVote']['id']);
        }
    }

    public function getVotes($id, $limit = 12, $page = 1) {
        $cond = array('ContestVote.contest_entry_id' => $id) ;
        $cond = $this->addBlockCondition($cond);
        return $this->find('all', array('conditions' => $cond,
            'limit' => $limit,
            'page' => $page
        ));
    }
    public function getVoteCount($id) {
        $cond =  array(
            'ContestVote.contest_entry_id' => $id);
       // $cond = $this->addBlockCondition($cond);
        return $this->find('count', array('conditions' => $cond
        ));
    }
    public function getAllVoteContest($contest_id, $user_id) {
        $cond =  array(
            'ContestVote.contest_id' => $contest_id,
            'ContestVote.user_id' => $user_id
        );
        $cond = $this->addBlockCondition($cond);
        return $this->find('all', array('conditions' => $cond
        ));
    }
    public function deleteVote($id) {
        $this->delete($id);
    }
     public function deleteVotes($uid) {
        $votes = $this->find('all', array('conditions' => array('ContestVote.user_id' => $uid)));
        if (!empty($votes)) {
            foreach ($votes as $vote) {
                $this->deleteVote($vote['ContestVote']['id']);
            }
        }
    }
    

}
