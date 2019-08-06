<?php

App::uses('AppHelper', 'View/Helper');

class ContestHelper extends AppHelper {

    public $friend_list = array();

    public $helpers = array('Storage.Storage');
    public function getFriends($id) {
        if (!isset($this->friend_list[$id])) {
            $friendModel = MooCore::getInstance()->getModel('Friend');
            $this->friend_list[$id] = $friendModel->getFriends($id);
        }
        return $this->friend_list[$id];
    }

    public function integrate_credit() {
        $mSetting = MooCore::getInstance()->getModel('Contest.ContestSetting');
        $contest_integrate_credit = $mSetting->getValueSetting('contest_integrate_credit');
        $credit_enable = Configure::read('Credit.credit_enabled');
        if ($credit_enable && $contest_integrate_credit) {
            return true;
        }
        return false;
    }

    public function getImage($item, $options = array()) {
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'] . '_';
        }

        $base_url = '';
        if (isset($options['no_full_url']) && $options['no_full_url']) {
            
        } else {
            $base_url = FULL_BASE_URL;
        }
        return $this->Storage->getUrl($item[key($item)]['id'], $prefix, $item[key($item)]['thumbnail'], "contests");
    }

    public function getEntryImage($item, $options = array()) {
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'] . '_';
        }

        $base_url = '';
        if (isset($options['no_full_url']) && $options['no_full_url']) {
            
        } else {
            $base_url = FULL_BASE_URL;
        }
        return $this->Storage->getUrl($item[key($item)]['id'], $prefix, $item[key($item)]['thumbnail'], "contest_entries");
    }
    public function getSourceUrl($item){
        $request = Router::getRequest();
        $url = '';
        if ($item['ContestEntry']['source_id']) {
          //  $url = FULL_BASE_URL . $request->webroot . 'uploads/contest_entries/music/'. $item['ContestEntry']['source_id'];
            $url = $this->Storage->getUrl($item['ContestEntry']['id'], '', $item['ContestEntry']['source_id'], "contest_musics");
        }
        return $url;
    }

    public function checkSeeComment($contest, $uid) {
        if ($contest['Contest']['privacy'] == PRIVACY_EVERYONE) {
            return true;
        }

        return $this->checkPostStatus($contest, $uid);
    }

    public function checkPostStatus($contest, $uid) {
        if (!$uid)
            return false;
        $friendModel = MooCore::getInstance()->getModel('Friend');
        if ($uid == $contest['Contest']['user_id'])
            return true;

        if ($contest['Contest']['privacy'] == PRIVACY_EVERYONE) {
            return true;
        }

        if ($contest['Contest']['privacy'] == PRIVACY_FRIENDS) {
            $areFriends = $friendModel->areFriends($uid, $contest['Contest']['user_id']);
            if ($areFriends)
                return true;
        }
        return false;
    }

    public function checkSeeActivity($contest, $uid) {
        return $this->checkPostStatus($contest, $uid);
    }

    public function checkAutoApproved() {
        return Configure::read('Contest.contest_auto_approved');
    }

    public function canEdit($item, $viewer) {
        if (!$viewer) {
            return false;
        }
        $bAllow = false;
        if ($item['Contest']['contest_status'] == 'draft' || $item['Contest']['contest_status'] == 'denied') {
            if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['Contest']['user_id']) {
                $bAllow = true;
            }
        }
        return $bAllow;
    }

    public function canDelete($item, $viewer) {
        if (!$viewer) {
            return false;
        }
        if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['Contest']['user_id']) {
            return true;
        }
    }
    public function canPublish($item, $viewer) {
        if($item['Contest']['contest_status'] == 'draft') {
            if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['Contest']['user_id']) {
                return true;
            }
        }
        return false;
    }
    public function checkOwner($item, $viewer) {
        if (!$viewer) {
            return false;
        }
        if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['Contest']['user_id']) {
            return true;
        }
    }

    public function checkCandidate($contest_id, $user_id) {
        $isCandidate = false;
        $mContestCandidate = MooCore::getInstance()->getModel('Contest.ContestCandidate');
        $candidate = $mContestCandidate->find('first', array('conditions' => array('ContestCandidate.user_id' => $user_id, 'ContestCandidate.contest_id' => $contest_id)));
        if (!empty($candidate)) {
            $isCandidate = true;
        }
        return $isCandidate;
    }

    public function canView($contest) {
        $viewer = MooCore::getInstance()->getViewer();
        $bAllow = false;
        if ($viewer['Role']['is_admin'] || ($viewer['User']['id'] == $contest['Contest']['user_id']) || ($contest['Contest']['approve_status'] == 'approved' && ( $contest['Contest']['contest_status'] == 'closed' || $contest['Contest']['contest_status'] == 'published'))) {
            $bAllow = true;
        }
        return $bAllow;
    }

    public function canVote($entry, $user) {
        if (!$user) {
            return false;
        }
        if($entry['User']['id'] == $user['User']['id']) {
            return false;
        }
        if ($entry['ContestEntry']['entry_status'] != 'published') {
            return false;
        }
        if ($entry['Contest']['approve_status'] != 'approved' || $entry['Contest']['contest_status'] != 'published') {
            return false;
        }
        $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';
        // user timezone
        if (!empty($user['timezone'])) {
            $utz = $user['timezone'];
        }
        $s_from_time = $entry['Contest']['voting_start'];
        $s_to_time = $entry['Contest']['voting_end'];
        if ($s_from_time > date('Y-m-d H:i:s') || $s_to_time < date('Y-m-d H:i:s')) {
            return false;
        }
        if ($entry['Contest']['vote_without_join']) {
            return true;
        } else {
            return $this->checkCandidate($entry['Contest']['id'], $user['User']['id']);
        }
    }

    public function isVote($entry, $viewer) {
        $mVote = MooCore::getInstance()->getModel('Contest.ContestVote');
        return $mVote->isVote($entry['ContestEntry']['id'], $viewer['User']['id']);
    }

    public function canSubmitEntry($contest, $user, $my_entry = false) {
        $canSubmit = false;
        if ($contest['Contest']['approve_status'] != 'approved'
         || $contest['Contest']['contest_status'] != 'published'
         || $contest['Contest']['user_id'] == $user['User']['id']) {
            return false;
        }
        if ($this->checkCandidate($contest['Contest']['id'], $user['User']['id'])) {
            if ($contest['Contest']['maximum_entry'] == 0) {
                $canSubmit = true;
            } else {
                if (!$my_entry) {
                    $mContestEntry = MooCore::getInstance()->getModel('Contest.ContestEntry');
                    $entry_count = $mContestEntry->getEntryCountByUserId($contest['Contest']['id'], $user['User']['id']);
                    if ($entry_count < $contest['Contest']['maximum_entry']) {
                        $canSubmit = true;
                    }
                } else {
                    $canSubmit = true;
                }
            }
        }
        // check range time to submit 
        if ($canSubmit) {
            $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';
            // user timezone
            if (!empty($user['timezone'])) {
                $utz = $user['timezone'];
            }
            $s_from_time = $contest['Contest']['submission_start'];
            $s_to_time = $contest['Contest']['submission_end'];
            if ($s_from_time > date('Y-m-d H:i:s') || $s_to_time < date('Y-m-d H:i:s')) {
                $canSubmit = false;
            }
        }
        return $canSubmit;
    }

    // for candidate 
    public function canEditEntry($contest, $user) {
        $canEdit = false;
        if ($contest['User']['id'] == $user['User']['id'] || $user['Role']['is_admin']) {
            $canEdit = true;
        }
        if ($contest['Contest']['approve_status'] == 'approved' && $contest['Contest']['contest_status'] == 'published') {
            if ($this->checkCandidate($contest['Contest']['id'], $user['User']['id'])) {
                $canEdit = true;
            }
        }
        if ($canEdit) {
            $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';
            // user timezone
            if (!empty($user['timezone'])) {
                $utz = $user['timezone'];
            }
            $s_from_time = $contest['Contest']['submission_start'];
            $s_to_time = $contest['Contest']['submission_end'];
            if ($s_from_time > date('Y-m-d H:i:s') || $s_to_time < date('Y-m-d H:i:s')) {
                $canEdit = false;
            }
        }
        return $canEdit;
    }

    public function canDeleteEntryDetail($entry, $user) {
        $canDel = false;
        if ($entry['Contest']['user_id'] == $user['User']['id'] || $user['Role']['is_admin']) {
            return true;
        }
        if ($entry['User']['id'] == $user['User']['id']) {
            if ($entry['ContestEntry']['entry_status'] != 'win') {
                $canDel = true;
            }
        }
        return $canDel;
    }

    public function canApproveEntryDetail($entry, $user) {
        $canApprove = false;
        if ($entry['Contest']['user_id'] == $user['User']['id'] || $user['Role']['is_admin']) {
            if ($entry['ContestEntry']['entry_status'] == 'pending') {
                $canApprove = true;
            }
        }
        return $canApprove;
    }

    public function canSetwinEntryDetail($entry, $user) {
        $canWin = false;
        if ($entry['Contest']['user_id'] == $user['User']['id'] || $user['Role']['is_admin']) {
            if ($entry['Contest']['contest_status'] == 'published' && $entry['ContestEntry']['entry_status'] == 'published') {
                $canWin = true;
            }
        }
        return $canWin;
    }

    // for all entries contestowner or admin
    public function canManageEntries($contest, $user) {
        $canEdit = false;
        if (($contest['User']['id'] == $user['User']['id'] || $user['Role']['is_admin']) && $contest['Contest']['contest_status'] != 'closed') {
            $canEdit = true;
        }
        return $canEdit;
    }

    public function getEnable() {
        return Configure::read('Contest.contest_enabled');
    }

    public function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    public function getContestStatus($item) {
        $txt = '';
        if ($item['Contest']['approve_status'] == 'approved') {
            if ($item['Contest']['contest_status'] == 'published') {
                
            }
            if ($item['Contest']['contest_status'] == 'draft') {
                $txt = __d('contest', 'Draft');
            }
            if ($item['Contest']['contest_status'] == 'closed') {
                $txt = __d('contest', 'Closed');
            }
        } elseif ($item['Contest']['approve_status'] == 'pending') {
            $txt = __d('contest', 'Pending approval');
        } elseif ($item['Contest']['approve_status'] == 'denied') {
            $txt = __d('contest', 'Denied');
        }
        return $txt;
    }

    public function getAprroveTextStatus($item) {
        switch ($item['Contest']['approve_status']) {
            case 'approved' : return __d('contest', 'Approved');
            case 'denied': return __d('contest', 'Denied');
            case 'pending': return __d('contest', 'Pending approval');
        }
    }

    public function getContestTextStatus($item) {
        switch ($item['Contest']['contest_status']) {
            case 'closed' : return __d('contest', 'Closed');
            case 'draft': return __d('contest', 'Draft');
            case 'published': return __d('contest', 'Published');
        }
    }

    public function getEntryStatus($entry) {
        switch ($entry['ContestEntry']['entry_status']) {
           // case 'denied' : return __d('contest', 'Denied');
           // case 'draft': return __d('contest', 'Draft');
            case 'pending': return __d('contest', 'Pending');
            case 'published': return __d('contest', 'Published');
            case 'win': return __d('contest', 'Win');
        }
    }

    public function getEntryStatusSelect() {
        return array( 'pending' => __d('contest', 'Pending'), 'published' => __d('contest', 'Published'), 'win' => __d('contest', 'Win'));
    }

    public function getPrevNextId($entry) {
        $mEntry = MooCore::getInstance()->getModel('Contest.ContestEntry');
        $viewer = MooCore::getInstance()->getViewer();
        if ($entry['Contest']['user_id'] == $viewer['User']['id'] || $viewer['Role']['is_admin']) {
            $prev_cond = array('ContestEntry.id <' => $entry['ContestEntry']['id'], 'ContestEntry.contest_id' => $entry['ContestEntry']['contest_id']);
            $next_cond = array('ContestEntry.id >' => $entry['ContestEntry']['id'], 'ContestEntry.contest_id' => $entry['ContestEntry']['contest_id']);
        } else {
            $prev_cond = array('ContestEntry.id <' => $entry['ContestEntry']['id'], 'ContestEntry.contest_id' => $entry['ContestEntry']['contest_id'], 'ContestEntry.entry_status' => array('published', 'win'));
            $next_cond = array('ContestEntry.id >' => $entry['ContestEntry']['id'], 'ContestEntry.contest_id' => $entry['ContestEntry']['contest_id'], 'ContestEntry.entry_status' => array('published', 'win'));
        }
        $prev_entry = $mEntry->find('first', array('conditions' => $prev_cond, 'limit' => 1, 'order' => 'ContestEntry.id DESC'));
        $next_entry = $mEntry->find('first', array('conditions' => $next_cond, 'limit' => 1, 'order' => 'ContestEntry.id ASC'));
        return array($prev_entry, $next_entry);
    }

    public function getEntryCount($contest) {
        $viewer = MooCore::getInstance()->getViewer();
        if ($contest['Contest']['user_id'] == $viewer['User']['id'] || $viewer['Role']['is_admin']) {
            $cond = array('ContestEntry.contest_id' => $contest['Contest']['id']);
        } else {
            $cond = array('ContestEntry.contest_id' => $contest['Contest']['id'], 'ContestEntry.entry_status' => array('published', 'win'));
        }
        $mEntry = MooCore::getInstance()->getModel('Contest.ContestEntry');
        return $mEntry->find('count', array('conditions' => $cond));
    }

    public function getCandidateCount($contest) {
        $cond = array('ContestCandidate.contest_id' => $contest['Contest']['id']);
        $mCandidate = MooCore::getInstance()->getModel('Contest.ContestCandidate');
        return $mCandidate->find('count', array('conditions' => $cond));
    }

    public function onClose($contest) {
        $mEntry = MooCore::getInstance()->getModel('Contest.ContestEntry');
        $win_entries = $mEntry->getWinningEntries($contest);
        if (!empty($win_entries)) {
            $this->afterWinnerEntries($win_entries, $contest);
        } else {
            $this->updateWinContest($contest);
        }
    }

    public function updateWinContest($contest) {
        $mEntry = MooCore::getInstance()->getModel('Contest.ContestEntry');
        $max_vote = $mEntry->find('first', array('conditions' => array(
            'ContestEntry.contest_id' => $contest['Contest']['id'],
            'ContestEntry.contest_vote_count >' => 0),
            'order' => 'ContestEntry.contest_vote_count DESC', 'limit' => 1));
        
        if (!empty($max_vote)) {
            $win_entries = $mEntry->find('all', array('conditions' => array(
                'ContestEntry.contest_id' => $contest['Contest']['id'],
                'ContestEntry.contest_vote_count' => $max_vote['ContestEntry']['contest_vote_count'])));
            if (!empty($win_entries)) {
                $mEntry->updateWinStatus($win_entries);
                $this->afterWinnerEntries($win_entries, $contest);
            }
        }
    }

    public function afterWinnerEntries($win_entries, $contest) {
        if ($this->integrate_credit() && $contest['Contest']['win_percent'] > 0 && $contest['Contest']['credit']) {
            $this->_processCredit($win_entries, $contest);
        }
        $this->_notifyAfterClose($win_entries, $contest);
    }

    public function getCreditToWin($contest) {
        $credit = 0;
        if ($contest['Contest']['credit'] > 0 && $contest['Contest']['win_percent'] > 0) {
            $credit = $contest['Contest']['credit'] * $contest['Contest']['win_percent'] / 100;
        }
        return $credit;
    }

    public function spendBalanceCredit($spend = 0, $user_id = 0) {
        $mCreditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $balance = $mCreditBalances->getBalancesUser($user_id);
        if (empty($balance)) {
            return false;
        } else {
            if (floatval($balance['CreditBalances']['current_credit']) < floatval($spend)) {
                return false;
            } else {
                $mCreditBalances->id = $user_id;
                $params_balance = array(
                    'current_credit' => floatval($balance['CreditBalances']['current_credit']) - floatval($spend),
                    'spent_credit' => floatval($balance['CreditBalances']['spent_credit']) + floatval($spend),
                );
                $mCreditBalances->set($params_balance);
                $mCreditBalances->save();
                return true;
            }
        }
    }

    private function _processCredit($win_entries, $contest) {
        $mCreditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $mCreditLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
        $mCreditRanks = MooCore::getInstance()->getModel('Credit.CreditRanks');
        $balance = $mCreditBalances->getBalancesUser(CONTEST_ADMIN_ID);
        if (floatval($balance['CreditBalances']['current_credit']) < floatval($contest['Contest']['credit'])) {
            // set credit for admin
            $mCreditBalances->addCredit(CONTEST_ADMIN_ID, floatval($contest['Contest']['credit']));
            // write log
            $mCreditLogs->addLogByType('give_credits', floatval($contest['Contest']['credit']), CONTEST_ADMIN_ID, 'user', CONTEST_ADMIN_ID);

            $balances_user = $mCreditBalances->getBalancesUser(CONTEST_ADMIN_ID);
            if ($balances_user) {
                $mCreditRanks->getRankUserAndNoti($balances_user['CreditBalances']['current_credit'], $balances_user['CreditBalances']['rank_id'], CONTEST_ADMIN_ID, false);
            }
        }
        $commission = Configure::read('Contest.contest_credit_commission');
        $win_credits = ($this->getCreditToWin($contest)) / count($win_entries);
        $owner_credit = ($contest['Contest']['credit'] - $this->getCreditToWin($contest) ) * (100 - floatval($commission)) / 100;
        
        // send credit to win 
        if($win_credits > 0) {
            $winner = array();
            foreach ($win_entries as $win_entry) {
                if(array_key_exists($win_entry['User']['id'], $winner)) {
                    $winner[$win_entry['User']['id']] += $win_credits;
                }else{
                    $winner[$win_entry['User']['id']] = $win_credits;
                }
            }
            if(!empty($winner)) {
                foreach($winner as $winner_id => $winner_credits) {
                    $flag = $this->spendBalanceCredit($winner_credits, CONTEST_ADMIN_ID);
                    if ($flag) {
                        $mCreditBalances->addCredit($winner_id, floatval($winner_credits));
                        // write log
                        $mCreditLogs->addLogByType('transfer_contest_from', floatval($winner_credits), $winner_id, 'core_user', CONTEST_ADMIN_ID, 0);
                        $mCreditLogs->addLogByType('transfer_contest_to', floatval('-' . $winner_credits), CONTEST_ADMIN_ID, 'core_user', $winner_id, 0);
                    }
                }
            }
        }
        // send credit to owner
        if ($owner_credit > 0) {
            // send credit to admin
            $mCreditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
            $mCreditLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
            // Check credit
            $flag = $this->spendBalanceCredit($owner_credit, CONTEST_ADMIN_ID);
            if ($flag) {
                $mCreditBalances->addCredit($contest['User']['id'], floatval($owner_credit));
                // write log
                $mCreditLogs->addLogByType('transfer_contest_from', floatval($owner_credit), $contest['User']['id'], 'core_user', CONTEST_ADMIN_ID, 0);
                $mCreditLogs->addLogByType('transfer_contest_to', floatval('-' . $owner_credit), CONTEST_ADMIN_ID, 'core_user', $contest['User']['id'], 0);
            }
        }
    }

    private function _notifyAfterClose($win_entries, $contest) {
        $mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
        $notificationModel = MooCore::getInstance()->getModel('Notification');
        // send mail to winner
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        $current_language = Configure::read('Config.language');
        $winner = array();
        // send notify to winner and email
        foreach ($win_entries as $win_entry) {
            $winner[] = '<a href="' . $http . '://' . $_SERVER['SERVER_NAME'] . $win_entry['User']['moo_href'] . '">' . $win_entry['User']['moo_title'] . '</a>';
            // send notification
            $notification = $notificationModel->find('first', array('conditions' => array(
                    'user_id' => $win_entry['ContestEntry']['user_id'],
                    'sender_id' => $win_entry['Contest']['user_id'],
                    'action' => '',
                    'url' => '/contests/entry/' . $win_entry['ContestEntry']['id'],
                    'plugin' => 'Contest'
            )));
            // $is_block = $this->areUserBlocks($win_entry['ContestEntry']['user_id'], $win_entry['Contest']['user_id']);
            $is_block = false;
            if (empty($notification) && $win_entry['ContestEntry']['user_id'] != $win_entry['Contest']['user_id'] && !$is_block) {
                $notificationModel->record(array(
                    'recipients' => $win_entry['ContestEntry']['user_id'],
                    'sender_id' => $win_entry['Contest']['user_id'],
                    'action' => 'entry_win',
                    'url' => '/contests/entry/' . $win_entry['ContestEntry']['id'],
                    'params' => $win_entry['Contest']['id'],
                    'plugin' => 'Contest'
                ));
            }
            //send mail to winner
            if ($win_entry['User']['lang']) {
                Configure::write('Config.language', $win_entry['User']['lang']);
            }
            $params = array(
                'contest_name' => $win_entry['Contest']['moo_title'],
                'contest_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $win_entry['Contest']['moo_href'],
                'contest_owner_name' => $win_entry['Contest']['moo_title'],
                'contest_owner_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $win_entry['Contest']['moo_href']
            );
            if ($win_entry['User']['lang']) {
                Configure::write('Config.language', $current_language);
            }
            $mailComponent->send($win_entry['User']['email'], 'contest_winner_email', $params);
        }
        // send notify to contest owner
        $notification = $notificationModel->find('first', array('conditions' => array(
                'user_id' => $contest['Contest']['user_id'],
                'sender_id' => 1,
                'action' => 'owner_entry_win',
                'url' => $contest['Contest']['moo_url'],
                'plugin' => 'Contest'
        )));

        // $is_block = $this->areUserBlocks(CONTEST_ADMIN_ID, $contest['Contest']['user_id']);
        $is_block = false;
        if (empty($notification) && CONTEST_ADMIN_ID != $contest['Contest']['user_id'] && !$is_block) {
            $notificationModel->record(array(
                'recipients' => $contest['Contest']['user_id'],
                'sender_id' => CONTEST_ADMIN_ID,
                'action' => 'owner_entry_win',
                'url' => $contest['Contest']['moo_url'],
                'params' => $contest['Contest']['id'],
                'plugin' => 'Contest'
            ));
        }
        // send mail to contest owner
        if ($contest['User']['lang']) {
            Configure::write('Config.language', $contest['User']['lang']);
        }
        $o_params = array(
            'winner' => implode(', ', $winner),
            'contest_name' => $contest['Contest']['moo_title'],
            'contest_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $contest['Contest']['moo_href']
        );
        if ($contest['User']['lang']) {
            Configure::write('Config.language', $current_language);
        }
        $mailComponent->send($contest['User']['email'], 'contest_owner_winner_email', $o_params);
    }

    public function getContestDurationText($contest) {
        // comming, on going, close
        $txt = '';
        $viewer = MooCore::getInstance()->getViewer();
        // comming, on going, close
        $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';
        // user timezone
        if (!empty($viewer['timezone'])) {
            $utz = $viewer['timezone'];
        }
        $s_from_time = $contest['Contest']['duration_start'];
        $s_to_time = $contest['Contest']['duration_end'];
        if (date('Y-m-d H:i:s') < $s_from_time) {
            $txt = __d('contest', 'Coming');
        }
        if (date('Y-m-d H:i:s') >= $s_from_time && date('Y-m-d H:i:s') <= $s_to_time) {
            $txt = __d('contest', 'On Going');
        }
        if (date('Y-m-d H:i:s') > $s_to_time) {
            $txt = __d('contest', 'End');
        }
        return $txt;
    }

    public function getContestSubmitText($contest) {
        // comming, on going, close
        $txt = '';
        $viewer = MooCore::getInstance()->getViewer();
        // comming, on going, close
        $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';
        // user timezone
        if (!empty($viewer['timezone'])) {
            $utz = $viewer['timezone'];
        }
        $s_from_time = $contest['Contest']['submission_start'];
        $s_to_time = $contest['Contest']['submission_end'];
        if (date('Y-m-d H:i:s') < $s_from_time) {
            $txt = __d('contest', 'Coming');
        }
        if (date('Y-m-d H:i:s') >= $s_from_time && date('Y-m-d H:i:s') <= $s_to_time) {
            $txt = __d('contest', 'On Going');
        }
        if (date('Y-m-d H:i:s') > $s_to_time) {
            $txt = __d('contest', 'End');
        }
        return $txt;
    }

    public function getContestVoteText($contest) {
        $txt = '';
        $viewer = MooCore::getInstance()->getViewer();
        // comming, on going, close
        $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';
        // user timezone
        if (!empty($viewer['timezone'])) {
            $utz = $viewer['timezone'];
        }
        $s_from_time = $contest['Contest']['voting_start'];
        $s_to_time = $contest['Contest']['voting_end'];

        if (date('Y-m-d H:i:s') < $s_from_time) {
            $txt = __d('contest', 'Coming');
        }
        if (date('Y-m-d H:i:s') >= $s_from_time && date('Y-m-d H:i:s') <= $s_to_time) {
            $txt = __d('contest', 'On Going');
        }
        if (date('Y-m-d H:i:s') > $s_to_time) {
            $txt = __d('contest', 'End');
        }
        return $txt;
    }

    public function getTimeCountdown($dateTime, $utc) {
        return $this->getTimeOnUTC($dateTime, $utc, 'Y/m/d H:i:s');
    }

    public function getTimeLeft($dateTime, $utc) {
        $dateTime = $this->getTimeOnUTC($dateTime, $utc, 'Y-m-d H:i:s');
        //Calculate difference
        $seconds = strtotime($dateTime) - time();

        $years = floor($seconds / (86400 * 365));
     //   $seconds %= (86400 * 365);

        $months = floor($seconds / (86400 * 30));
      //  $seconds %= (86400 * 30);

        $days = floor($seconds / 86400);
     //   $seconds %= 86400;

        $hours = floor($seconds / 3600);
     //   $seconds %= 3600;

        $minutes = floor($seconds / 60);
        $seconds %= 60;

        if ($years > 1) {
            return __d('contest', '%s years left', $years);
        }
        if ($months > 1) {
            return __d('contest', '%s months left', $months);
        }
        if ($days > 1) {
            return __d('contest', '%s days left', $days);
        }
        if ($hours > 1) {
            return __d('contest', '%s hours left', $hours);
        }
        if ($minutes > 1) {
            return __d('contest', '%s minutes left', $minutes);
        }
        if ($seconds > 1) {
            return __d('contest', '%s seconds left', $seconds);
        }
    }

    public function getTimeOnUTC($dateTime, $utc, $format = 'Y-m-d H:i:s') {
        if (!empty($utc)) {
            if (strpos($dateTime, 'AM') !== false || strpos($dateTime, 'PM') !== false) {
                $dateTime = date($format, strtotime($dateTime . ' ' . $utc));
            } else {
                $dateTime = date($format, strtotime($dateTime . ':00 ' . $utc));
            }
        }
        return $dateTime;
    }

    public function getTime($dateTime, $format = '', $utc = 'UTC', $c_utc = null) {
        if (!empty($c_utc)) {
            if (strpos($dateTime, 'AM') !== false || strpos($dateTime, 'PM') !== false) {
                $dateTime = date('Y-m-d H:i:s', strtotime($dateTime . ' ' . $c_utc));
            } else {
                $dateTime = date('Y-m-d H:i:s', strtotime($dateTime . ':00 ' . $c_utc));
            }
        }
        $date = new DateTime($dateTime);
        $date->setTimezone(new DateTimeZone($utc));
        return $date->format($format);
    }
    public function getItemSitemMap($name,$limit,$offset)
    {
        if (!MooCore::getInstance()->checkPermission(null, 'contest_view'))
            return null;

        $model = MooCore::getInstance()->getModel("Contest.Contest");
        $items = $model->find('all',array(
            'conditions' => array('Contest.privacy'=>PRIVACY_PUBLIC),
            'limit' => $limit,
            'offset' => $offset
        ));

        $urls = array();
        foreach ($items as $item)
        {
            $urls[] = FULL_BASE_URL.$item['Contest']['moo_href'];
        }

        return $urls;
    }
    public function areUserBlocks( $uid1, $uid2 )
    {
        $is_block = false;
        $mUserBlock = MooCore::getInstance()->getModel('UserBlock');
        $is_block_1 = $mUserBlock->areUserBlocks($uid1, $uid2);
        $is_block_2 = $mUserBlock->areUserBlocks($uid2, $uid1);
        if($is_block_1 || $is_block_2) {
            $is_block = true;
        }
        return $is_block;
    }
}
