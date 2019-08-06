<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('ContestAppModel', 'Contest.Model');

class ContestEntry extends ContestAppModel {

    public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumbnail' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}contest_entries{DS}{field}{DS}',
            )
        ),
        'Hashtag' => array(
            'field_created_get_hashtag' => 'description',
            'field_updated_get_hashtag' => 'description',
        ),
        'Storage.Storage' => array(
            'type'=>array('events'=>'photo'),
        ),
        'Storage.Storage' => array(
            'type'=>array('contest_entries'=>'thumbnail','contest_musics'=>'thumbnail')
        )
    );
    public $mooFields = array('title', 'href', 'plugin', 'type', 'url', 'thumb');
    public $belongsTo = array(
        'Contest' => array(
            'className' => 'Contest.Contest',
            'counterCache' => true,
            'counterScope' => array('(ContestEntry.entry_status = "published" OR ContestEntry.entry_status = "win")')
        ),
        'User'
    );

    public function getThumb($row) {
        return 'thumbnail';
    }

    public function getHref($row) {
        $request = Router::getRequest();
        if (isset($row['id']))
            return $request->base . '/contests/entry/' . $row['id'];
        return false;
    }

    public function getType($row) {
        return 'Contest_Contest_Entry';
    }

    public function getTitle(&$row) {
        if (isset($row['caption'])) {
            if(!empty($row['caption'])) {
                return $row['caption'];
            }else{
                return 'your entry';
            }
        }
        return false;
    }

    public function setWinEntries($entry_ids, $contest_id) {
        $ids = array_values($entry_ids);
        if (!empty($ids)) {
            $this->updateAll(array('entry_status' => '"win"', 'approved_date' => "'" . date('Y-m-d H:i:s') . "'"), array('ContestEntry.id' => $ids));
            $mContest = MooCore::getInstance()->getModel('Contest.Contest');
            $mContest->updateContestStatus($contest_id, 'closed');
        }
    }

    public function updateWinStatus($entries) {
        if (!empty($entries)) {
            foreach ($entries as $entry) {
                $this->updateStatus($entry['ContestEntry']['id'], 'win');
            }
        }
    }

    public function isContestEntryExist($id, $user_id = null) {
        $cond = array(
            'ContestEntry.id' => $id
        );
        if ($user_id > 0) {
            $cond['ContestEntry.user_id'] = $user_id;
        }
        return $this->hasAny($cond);
    }

    public function getTopVotes($contest, $limit = 5) {
        if(!$contest ) {
            $cond =  array( 'ContestEntry.contest_vote_count >' => 0, 
                            'ContestEntry.entry_status' => array('published', 'win'));
        }else{
            $cond =  array( 'ContestEntry.contest_vote_count >' => 0, 
                            'ContestEntry.entry_status' => array('published', 'win'),
                            'ContestEntry.contest_id' => $contest['Contest']['id']);
        }
        $cond = $this->addBlockCondition($cond);

        return $this->find('all', array('conditions' => $cond, 
                                        'order' => 'ContestEntry.contest_vote_count DESC, ContestEntry.like_count DESC', 
                                        'limit' => $limit));
    }

    public function getMostViewEntries($contest, $limit) {
        if(!$contest ) {
            $cond =  array( 'ContestEntry.view_count >' => 0, 
                            'ContestEntry.entry_status' => array('published', 'win'));
        }else{
           $cond =  array( 'ContestEntry.view_count >' => 0, 
                            'ContestEntry.entry_status' => array('published', 'win'), 
                            'ContestEntry.contest_id' => $contest['Contest']['id']);
        }
        $cond = $this->addBlockCondition($cond);
        return $this->find('all', array( 'conditions' => $cond,
                                         'order' => 'ContestEntry.view_count DESC, ContestEntry.contest_vote_count DESC', 
                                         'limit' => $limit));
    }

    public function getWinningEntries($contest) {
        $cond = array('ContestEntry.entry_status' => array('win'), 'ContestEntry.contest_id' => $contest['Contest']['id']);
        // $cond = $this->addBlockCondition($cond);
        return $this->find('all', array('conditions' => $cond, 'order' => 'ContestEntry.contest_vote_count DESC, ContestEntry.like_count DESC'));
    }

    public function getPhotoSelect($uid) {
        $mPhoto = MooCore::getInstance()->getModel('Photo.Photo');
        return $mPhoto->find('all', array('conditions' => array('Photo.user_id' => $uid)));
    }
    public function getVideoSelect($uid) {
        $mVideo = MooCore::getInstance()->getModel('Video.Video');
        return $mVideo->find('all', array('conditions' => array('Video.user_id' => $uid)));
    }
    public function getEntryCountByUserId($contest_id, $user_id) {
        $cond = array('ContestEntry.user_id' => $user_id, 'ContestEntry.contest_id' => $contest_id);
        $cond = $this->addBlockCondition($cond);
        return $this->find('count', array('conditions' => $cond));
    }

    public function getContestEntriesCount($type, $params) {
        $cond = $this->getConditions($type, $params);
        return $this->find('count', array('conditions' => $cond));
    }

    public function getConditions($type, $params) {
        $viewer = MooCore::getInstance()->getViewer();
        switch ($type) {
            case 'approved' :
                $cond = array('ContestEntry.entry_status' => array('published', 'win'), 'ContestEntry.contest_id' => $params['contest_id']);
                break;
            case 'my_approved':
                $cond = array('ContestEntry.entry_status' => array('published', 'win'), 'ContestEntry.contest_id' => $params['contest_id'], 'ContestEntry.user_id' => $viewer['User']['id']);

                break;
            case 'pending':
                if ($viewer['Role']['is_admin']) {
                    $cond = array('ContestEntry.entry_status' => array('pending'), 'ContestEntry.contest_id' => $params['contest_id']);
                } else {
                    $cond = array('ContestEntry.entry_status' => array('pending'), 'ContestEntry.contest_id' => $params['contest_id']);
                }
                break;
            case 'my_pending':
                $cond = array('ContestEntry.entry_status' => array('pending'), 'ContestEntry.contest_id' => $params['contest_id'], 'ContestEntry.user_id' => $viewer['User']['id']);
                break;
            default:
                $cond = array('ContestEntry.entry_status' => array('published', 'win'), 'ContestEntry.contest_id' => $params['contest_id']);
        }
        $cond = $this->addBlockCondition($cond);
        return $cond;
    }

    public function getContestEntries($type, $params, $page = 1, $limit = 12) {
        $cond = $this->getConditions($type, $params);
        return $this->find('all', array('conditions' => $cond, 'limit' => $limit, 'page' => $page, 'order' => array('ContestEntry.contest_vote_count' => 'DESC', 'ContestEntry.created DESC')));
    }

    public function getPendingEntries($contest_id, $uid) {
        $cond = array('ContestEntry.entry_status' => array('pending'), 'ContestEntry.contest_id' => $contest_id, 'ContestEntry.user_id' => $uid);
        $cond = $this->addBlockCondition($cond);
        return $this->find('all', array('conditions' => $cond, 'order' => array('ContestEntry.contest_vote_count' => 'DESC', 'ContestEntry.created DESC')));
    }

    public function updateStatus($id, $status) {
        $entry = $this->findById($id);
        if (!empty($entry)) {
            $this->id = $id;
            $this->save(array('entry_status' => $status, 'approved_date' => date('Y-m-d H:i:s')));
            switch ($status) {
                case 'pending':
                    if ($entry['Contest']['contest_status'] == 'published') {
                        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
                        if ($helper->integrate_credit() 
                                && $entry['Contest']['submit_entry_fee'] > 0 
                                && !$entry['ContestEntry']['is_pay']) {
                            $this->_processCredit($entry);
                        }
                    }
                    $notify_type = 'request_approve_entry';
                    $sender_id = $entry['User']['id'];
                    $user_id = $entry['Contest']['user_id'];
                    $params = htmlspecialchars($entry['ContestEntry']['id']);
                    break;
                case 'published':
                    if ($entry['Contest']['contest_status'] == 'published') {
                        $this->_processPublish($entry);
                    }
                    if ($entry['Contest']['auto_approve']) {
                        $notify_type = 'submit_entry';
                        $sender_id = $entry['User']['id'];
                        $user_id = $entry['Contest']['user_id'];
                    }else{
                        $notify_type = 'entry_approved';
                        $sender_id = $entry['Contest']['user_id'];
                        $user_id = $entry['User']['id'];
                    }
                    $params = htmlspecialchars($entry['ContestEntry']['id']);
                    break;
                case 'win':
                    $notify_type = 'entry_win';
                    $sender_id = $entry['Contest']['user_id'];
                    $user_id = $entry['User']['id'];
                    $params = htmlspecialchars($entry['Contest']['id']);
                    $mContest = MooCore::getInstance()->getModel('Contest.Contest');
                    $mContest->updateContestStatus($entry['ContestEntry']['contest_id'], 'closed');
                    break;
                default:
                    break;
            }
            //var_dump($notify_type, $sender_id, $user_id);
            if (!empty($notify_type) && $sender_id != $user_id) {
                $notificationModel = MooCore::getInstance()->getModel('Notification');
                $notification = $notificationModel->find('first', array('conditions' => array(
                        'user_id' => $user_id,
                        'sender_id' => $sender_id,
                        'action' => $notify_type,
                        'url' => '/contests/entry/' . $entry['ContestEntry']['id'],
                        'plugin' => 'Contest'
                )));
                $helper = MooCore::getInstance()->getHelper('Contest_Contest'); 
                $is_block = $helper->areUserBlocks($sender_id, $user_id);
                //var_dump($notification);
                if (empty($notification) && $sender_id != $user_id && !$is_block) {
                    $notificationModel->record(array(
                        'recipients' => $user_id,
                        'sender_id' => $sender_id,
                        'action' => $notify_type,
                        'url' => '/contests/entry/' . $entry['ContestEntry']['id'],
                        'params' => $params,
                        'plugin' => 'Contest'
                    ));
                }
            }
        }
    }

    private function _processPublish($item) {
//add activity
        $this->_addActivity($item);
//process credit
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        if ($helper->integrate_credit() 
                && $item['Contest']['submit_entry_fee'] > 0 
                && !$item['ContestEntry']['is_pay']) {
            $this->_processCredit($item);
        }
    }

    private function _addActivity($item) {
        $activityModel = MooCore::getInstance()->getModel('Activity');
        $activity = $activityModel->find('first', array('conditions' => array(
                'action' => 'contest_entry_create',
                'Activity.item_type' => 'Contest_Contest_Entry',
                'Activity.item_id' => $item['ContestEntry']['id'],
        )));

        if (empty($activity)) {
            $share = false;
// only enable share feature for public event
            if ($item['Contest']['privacy'] == PRIVACY_EVERYONE || $item['Contest']['privacy'] == PRIVACY_FRIENDS) {
                $share = true;
            }
            $activityModel->save(array(
                'type' => 'user',
                'target_id' => 0,
                'action' => 'contest_entry_create',
                'user_id' => $item['User']['id'],
                'item_type' => 'Contest_Contest_Entry',
                'item_id' => $item['ContestEntry']['id'],
                'query' => 1,
                'privacy' => $item['Contest']['privacy'],
                'params' => 'item',
                'share' => $share,
                'plugin' => 'Contest'
            ));
        }
    }

    private function _processCredit($entry) {
        $submit_entry_fee = $entry['Contest']['submit_entry_fee'];
        if ($submit_entry_fee > 0) {
// send credit to admin
            $mCreditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
            $mCreditLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
            $helper = MooCore::getInstance()->getHelper('Contest_Contest');
// Check credit
            $flag = $helper->spendBalanceCredit($submit_entry_fee, $entry['User']['id']);
            if ($flag) {
                $mCreditBalances->addCredit(CONTEST_ADMIN_ID, floatval($submit_entry_fee));
// write log
                $mCreditLogs->addLogByType('transfer_submit_entry_from', floatval($submit_entry_fee), CONTEST_ADMIN_ID, 'core_user', $entry['User']['id'], 0);
                $mCreditLogs->addLogByType('transfer_submit_entry_to', floatval('-' . $submit_entry_fee), $entry['User']['id'], 'core_user', CONTEST_ADMIN_ID, 0);
// increase credit column
                $mContest = MooCore::getInstance()->getModel('Contest.Contest');
                $mContest->updateCredit($entry['Contest']['id'], $submit_entry_fee);
                // update is_pay
                $this->updateAll(array('is_pay' => 1), array('ContestEntry.id' => $entry['ContestEntry']['id']));
            }
        }
    }

    public function deleteEntry($id) {
        $entry = $this->findById($id);
        $this->delete($id);
        $viewer = MooCore::getInstance()->getViewer();
        if($viewer['User']['id'] != $entry['User']['id']) {
            $notificationModel = MooCore::getInstance()->getModel('Notification');
            $notification = $notificationModel->find('first', array('conditions' => array(
                    'user_id' => $entry['User']['id'],
                    'sender_id' => $viewer['User']['id'],
                    'action' => 'delete_entry',
                    'url' => $entry['Contest']['moo_url'],
                    'plugin' => 'Contest'
            )));
            $helper = MooCore::getInstance()->getHelper('Contest_Contest'); 
            $is_block = $helper->areUserBlocks($viewer['User']['id'], $entry['User']['id']);
            if (empty($notification) && $viewer['User']['id'] != $entry['User']['id'] && !$is_block) {
                $notificationModel->record(array(
                    'recipients' => $entry['User']['id'],
                    'sender_id' => $viewer['User']['id'],
                    'action' => 'delete_entry',
                    'url' => $entry['Contest']['moo_url'],
                    'params' => $entry['Contest']['id'],
                    'plugin' => 'Contest'
                ));
            }
        }
    }

    public function deleteEntries($uid) {
        $entries = $this->find('all', array('conditions' => array('ContestEntry.user_id' => $uid)));
        if (!empty($entries)) {
            foreach ($entries as $entry) {
                $this->deleteEntry($entry['ContestEntry']['id']);
            }
        }
    }

    public function beforeDelete($cascade = true) {
        $item = $this->findById($this->id);
        if ($item) {
            $activityModel = MooCore::getInstance()->getModel('Activity');
            $parentActivity = $activityModel->find('list', array('fields' => array('Activity.id'), 'conditions' =>
                array('Activity.item_type' => 'Contest_Contest_Entry', 'Activity.item_id' => $item['ContestEntry']['id'])));
            $activityModel->deleteAll(array('Activity.item_type' => 'Contest_Contest_Entry', 'Activity.parent_id' => $parentActivity));
            $activityModel->deleteAll(array('Activity.item_type' => 'Contest_Contest_Entry', 'Activity.parent_id' => $this->id));
            $notifyModel = MooCore::getInstance()->getModel('Notification');
            $notifyModel->deleteAll(array('Notification.action' => array('request_approve_entry', 'submit_entry'), 'Notification.url' => '/contests/entry/'.$this->id));
            $notifyModel->deleteAll(array('Notification.url' => '/contests/entry/'.$this->id));

        }
        parent::beforeDelete($cascade);
    }
    
    public function updateCounter($id, $field = 'comment_count', $conditions = '', $model = 'Comment') {
        if (empty($conditions)) {
            $conditions = array('Comment.type' => 'Contest_Contest_Entry', 'Comment.target_id' => $id);
        }
        parent::updateCounter($id, $field, $conditions, $model);
    }

    public function afterDeleteComment($comment) {
        if ($comment['Comment']['target_id']) {
            $this->decreaseCounter($comment['Comment']['target_id']);
        }
    }
 
    public function afterComment($data, $uid) {
        $this->increaseCounter($data['target_id']);
    }

}
