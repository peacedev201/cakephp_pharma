<?php

App::uses('ContestAppModel', 'Contest.Model');

class Contest extends ContestAppModel {

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
    }

    public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumbnail' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}contests{DS}{field}{DS}',
            )
        ),
        'Hashtag' => array(
            'field_created_get_hashtag' => 'description',
            'field_updated_get_hashtag' => 'description',
        ),
        'Storage.Storage' => array(
            'type'=>array('contests'=>'thumbnail')
        )
    );
    public $validationDomain = 'contest';
    public $belongsTo = array(
        'User' => array('counterCache' => true),
        'Category' => array(
            'counterCache' => 'item_count',
            'counterScope' => array('Category.type' => 'Contest')
        )    );
    public $mooFields = array('title', 'href', 'plugin', 'type', 'url', 'thumb', 'privacy');
    public $hasMany = array('Comment' => array(
            'className' => 'Comment',
            'foreignKey' => 'target_id',
            'conditions' => array('Comment.type' => 'Contest_Contest'),
            'dependent' => true
        ),
        'Like' => array(
            'className' => 'Like',
            'foreignKey' => 'target_id',
            'conditions' => array('Like.type' => 'Contest_Contest'),
            'dependent' => true
        ),
        'Tag' => array(
            'className' => 'Tag',
            'foreignKey' => 'target_id',
            'conditions' => array('Tag.type' => 'Contest_Contest'),
            'dependent' => true
        )
    );
    public $order = 'Contest.id desc';
    public $validate = array(
        'type' => array(
            'rule' => 'notBlank',
            'message' => 'Contest Type is required',
        ),
        'category_id' => array(
            'rule' => 'notBlank',
            'message' => 'Category is required',
        ),
        'name' => array(
            'rule' => 'notBlank',
            'message' => 'Contest Name is required',
        ),
        'description' => array(
            'rule' => 'notBlank',
            'message' => 'Description is required',
        ),
        'award' => array(
            'rule' => 'notBlank',
            'message' => 'Award is required',
        ),
        'term_and_condition' => array(
            'rule' => 'notBlank',
            'message' => 'Terms & Conditions is required',
        ),
        'from' => array(
            'rule' => array('date', 'ymd'),
            'message' => 'Contest Duration: Start is required',
            'allowEmpty' => false
        ),
        'from_time' => array(
            'rule' => 'notBlank',
            'message' => 'Contest Duration Start: Please select time',
            'allowEmpty' => false
        ),
        'duration_start' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Contest Duration is required, Please select date and time.'
            ),
            'check_duration_s' => array(
                'rule' => 'check_duration_s',
                'message' => 'Contest Duration: Start date must be greater than current date'
            )
        ),
        'to' => array(
            'rule' => array('date', 'ymd'),
            'message' => 'Contest Duration: End is required',
            'allowEmpty' => false
        ),
        'to_time' => array(
            'rule' => 'notBlank',
            'message' => 'Contest Duration End: Please select time',
            'allowEmpty' => false
        ),
        'duration_end' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Contest Duration is required, Please select date and time.'
            ),
            'check_duration_e' => array(
                'rule' => 'check_duration_e',
                'message' => 'Contest Duration: End date must be greater than current date'
            ),
            'check_duration' => array(
                'rule' => 'check_duration',
                'message' => 'Contest Duration: End date must be greater than Start date'
            )
        ),
        's_from' => array(
            'rule' => array('date', 'ymd'),
            'message' => 'Submit Entries Duration: Start is required',
            'allowEmpty' => false
        ),
        's_from_time' => array(
            'rule' => 'notBlank',
            'message' => 'Submit Entries Duration Start: Please select time',
            'allowEmpty' => false
        ),
        'submission_start' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Submit Entries Duration is required, Please select date and time.'
            ),
            'check_submit_s' => array(
                'rule' => 'check_submit_s',
                'message' => 'Submit Entries Duration: Start date must be greater than current date'
            ),
            'check_submission_s' => array(
                'rule' => 'check_submission_s',
                'message' => 'Start of Submit Entries Duration must be greater than or equal to the Start of Contest Duration.'
            )
        ),
        's_to' => array(
            'rule' => array('date', 'ymd'),
            'message' => 'Submit Entries Duration: End is required',
            'allowEmpty' => false
        ),
        's_to_time' => array(
            'rule' => 'notBlank',
            'message' => 'Submit Entries Duration End: Please select time',
            'allowEmpty' => false
        ),
        'submission_end' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Submit Entries Duration: End is required, Please select date and time.'
            ),
            'check_submit_e' => array(
                'rule' => 'check_submit_e',
                'message' => 'Submit Entries Duration: End date must be greater than current date'
            ),
            'check_submit' => array(
                'rule' => 'check_submit',
                'message' => 'Submit Entries Duration: End date must be greater than Start date'
            ),
            'check_submission_e' => array(
                'rule' => 'check_submission_e',
                'message' => 'End of Contest Duration must be greater than or equal to the End of Submit Entries Duration'
            )
        ),
        'v_from' => array(
            'rule' => array('date', 'ymd'),
            'message' => 'Voting Duration: Start is required',
            'allowEmpty' => false
        ),
        'v_from_time' => array(
            'rule' => 'notBlank',
            'message' => 'Voting Duration Start: Please select time',
            'allowEmpty' => false
        ),
        'voting_start' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Voting Duration: Start is required, Please select date and time.'
            ),
            'check_vote_s' => array(
                'rule' => 'check_vote_s',
                'message' => 'Voting Duration: Start date must be greater than current date'
            ),
            'check_voting_s' => array(
                'rule' => 'check_voting_s',
                'message' => 'Start of Voting Duration must be greater than or equal to the Start of Submit Entries Duration.'
            )
        ),
        'v_to' => array(
            'rule' => array('date', 'ymd'),
            'message' => 'Voting Duration: End is required',
            'allowEmpty' => false
        ),
        'v_to_time' => array(
            'rule' => 'notBlank',
            'message' => 'Voting Duration End: Please select time',
            'allowEmpty' => false
        ),
        'voting_end' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Voting Duration: End is required, Please select date and time.'
            ),
            'check_vote_e' => array(
                'rule' => 'check_vote_e',
                'message' => 'Voting Duration: End date must be greater than current date'
            ),
            'check_vote' => array(
                'rule' => 'check_vote',
                'message' => 'Voting Duration: End date must be greater than Start date'
            ),
            'check_voting_e' => array(
                'rule' => 'check_voting_e',
                'message' => 'End of Voting Duration must be greater than or equal to the End of Submit Entries Duration'
            )
        ),
        'maximum_entry' => array(
            'rule' => 'notBlank',
            'message' => 'Maximum entries a participant can submit is required',
        ),
        'submit_entry_fee' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Submit Entry Fee is required, Please enter decimal number.'
            ),
            'num_check' => array(
                'rule' => array('decimal'),
                'message' => 'Submit Entry Fee is required'
            )
        ),
        'win_percent' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => '% credit for winner is required, Please enter decimal number.'
            ),
            'num_check' => array(
                'rule' => array('decimal'),
                'message' => '% credit for winner is required'
            )
        ),
        'tags' => array(
            'validateTag' => array(
                'rule' => array('validateTag'),
                'message' => 'No special characters ( /,?,#,%,...) allowed in Tags',
            )
        )
    );

    public function check_duration_s() {
        $duration_start = $this->data['Contest']['duration_start'];
        if (!empty($duration_start) && $duration_start < date('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function check_duration_e() {
        $duration_end = $this->data['Contest']['duration_end'];
        if (!empty($duration_end) && $duration_end < date('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function check_submit_s() {
        $submission_start = $this->data['Contest']['submission_start'];
        if (!empty($submission_start) && $submission_start < date('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function check_submit_e() {
        $submission_end = $this->data['Contest']['submission_end'];
        if (!empty($submission_end) && $submission_end < date('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function check_vote_s() {
        $voting_start = $this->data['Contest']['voting_start'];
        if (!empty($voting_start) && $voting_start < date('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function check_vote_e() {
        $voting_end = $this->data['Contest']['voting_end'];
        if (!empty($voting_end) && $voting_end < date('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function check_duration() {
        $start = $this->data['Contest']['duration_start'];
        $end = $this->data['Contest']['duration_end'];
        if ($start >= $end) {
            return false;
        }
        return true;
    }

    public function check_submit() {
        $start = $this->data['Contest']['submission_start'];
        $end = $this->data['Contest']['submission_end'];
        if ($start >= $end) {
            return false;
        }
        return true;
    }

    public function check_vote() {
        $start = $this->data['Contest']['voting_start'];
        $end = $this->data['Contest']['voting_end'];
        if ($start >= $end) {
            return false;
        }
        return true;
    }

    public function check_submission_s() {
        $duration_start = $this->data['Contest']['duration_start'];
        $submission_start = $this->data['Contest']['submission_start'];
        if ($duration_start > $submission_start) {
            return false;
        }

        return true;
    }

    public function check_submission_e() {
        $submission_end = $this->data['Contest']['submission_end'];
        $duration_end = $this->data['Contest']['duration_end'];
        if ($submission_end > $duration_end) {
            return false;
        }
        return true;
    }

    public function check_voting_s() {
        $voting_start = $this->data['Contest']['voting_start'];
        $submission_start = $this->data['Contest']['submission_start'];
        if ($submission_start > $voting_start) {
            return false;
        }
        return true;
    }

    public function check_voting_e() {
        $voting_end = $this->data['Contest']['voting_end'];
        $submission_end = $this->data['Contest']['submission_end'];

        if ($submission_end > $voting_end) {
            return false;
        }
        return true;
    }

    public function getPrivacy($row) {
        if (isset($row['privacy'])) {
            return $row['privacy'];
        }
        return false;
    }

    public function getThumb($row) {
        return 'thumbnail';
    }

    public function getHref($row) {
        $request = Router::getRequest();
        if (isset($row['id']))
            return $request->base . '/contests/view/' . $row['id'] . '/' . seoUrl($row['moo_title']);

        return false;
    }

    public function getType($row) {
        return 'Contest_Contest';
    }

    public function getTitle(&$row) {
        if (isset($row['name'])) {
            return $row['name'];
        }
        return false;
    }

    public function isContestExist($id, $user_id = null) {
        $cond = array(
            'Contest.id' => $id
        );
        if ($user_id > 0) {
            $cond['Contest.user_id'] = $user_id;
        }
        return $this->hasAny($cond);
    }

    public function changeCategory($category_id, $id) {
        return $this->updateAll(array('Contest.category_id' => $category_id), array('Contest.id' => $id));
    }

    public function changeFeature($id) {
        $contest = $this->findById($id);
        return $this->updateAll(array('Contest.featured' => !$contest['Contest']['featured']), array('Contest.id' => $id));
    }

    public function updateApproveStatus($id, $status) {
        $contest = $this->findById($id);
        if (!empty($contest)) {
            $this->updateAll(array('Contest.approve_status' => "'" . $status . "'"), array('Contest.id' => $contest['Contest']['id']));
            if ($status == 'approved') {
                if ($contest['Contest']['contest_status'] == 'published') {
                    $this->_processPublish($contest);
                }
                $notify_type = 'approved';
                $sender_id = CONTEST_ADMIN_ID;
                $user_id = $contest['User']['id'];
            }
            if ($status == 'pending') {
                $notify_type = 'pending';
                $sender_id = CONTEST_ADMIN_ID;
                $user_id = $contest['User']['id'];
            }

            if ($status == 'denied') {
                $notify_type = 'denied';
                $sender_id = CONTEST_ADMIN_ID;
                $user_id = $contest['User']['id'];
            }
            if (!empty($notify_type)) {
                $notificationModel = MooCore::getInstance()->getModel('Notification');
                $notification = $notificationModel->find('first', array('conditions' => array(
                        'user_id' => $user_id,
                        'sender_id' => $sender_id,
                        'action' => $notify_type,
                        'url' => $contest['Contest']['moo_url'],
                        'plugin' => 'Contest'
                )));
                $helper = MooCore::getInstance()->getHelper('Contest_Contest'); 
                $is_block = $helper->areUserBlocks($user_id, $sender_id);
                if (empty($notification) && !$is_block && $user_id != $sender_id) {
                    $notificationModel->record(array(
                        'recipients' => $user_id,
                        'sender_id' => $sender_id,
                        'action' => $notify_type,
                        'url' => $contest['Contest']['moo_url'],
                        'params' => htmlspecialchars($contest['Contest']['name']),
                        'plugin' => 'Contest'
                    ));
                }
            }
        }
    }

    public function updateContestStatus($id, $status) {
        $this->id = $id;
        $this->save(array('contest_status' => $status));
        $contest = $this->findById($id);
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        if ($status == 'published') {
            if ($contest['Contest']['approve_status'] == 'approved') {
                $this->_processPublish($contest);
            }
        }
        if ($status == 'closed') {
            $helper->onClose($contest);
        }
    }

    private function _processPublish($contest) {
        // create activity
        $this->_addActivity($contest);
        //process credit
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        if ($helper->integrate_credit()) {
            $this->_processCredit($contest);
        }
    }

    private function _addActivity($item) {
        $activityModel = MooCore::getInstance()->getModel('Activity');
        $activity = $activityModel->find('first', array('conditions' => array(
                'action' => 'contest_create',
                'Activity.item_type' => 'Contest_Contest',
                'Activity.item_id' => $item['Contest']['id'],
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
                'action' => 'contest_create',
                'user_id' => $item['User']['id'],
                'item_type' => 'Contest_Contest',
                'item_id' => $item['Contest']['id'],
                'query' => 1,
                'privacy' => $item['Contest']['privacy'],
                'params' => 'item',
                'share' => $share,
                'plugin' => 'Contest'
            ));
        }
    }

    private function _processCredit($contest) {
        $mCreditActiontypes = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
        $action_type = $mCreditActiontypes->getActionTypeFormModule('contest', false);
        if (!empty($action_type)) {
            $mCreditLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
            // check credit max
            $uid = $contest['User']['id'];
            if ($mCreditLogs->checkCredit($action_type, $uid)) {
                $action_id = $action_type['CreditActiontypes']['id'];

                $all_credits = $mCreditLogs->getCredit($action_type, $uid);
                if (($action_type['CreditActiontypes']['max_credit'] - $all_credits) >= $action_type['CreditActiontypes']['credit']) {
                    $credit = $action_type['CreditActiontypes']['credit'];
                } else {
                    $credit = $action_type['CreditActiontypes']['max_credit'] - $all_credits;
                }
                $object_type = 'contest_contest';
                $object_id = $contest['Contest']['id'];
                $mCreditLogs->addLog($action_id, $credit, $object_type, $uid, $object_id);
                $mCreditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
                $mCreditBalances->addCredit($uid, $credit);
            }
        }
    }
    public function updateCredit($id, $credit) {
        return $this->updateAll(
                array('Contest.credit' => "Contest.credit + $credit"),                    
                array('Contest.id' => $id)
            );
    }
    public function deleteContest($contest) {
        $this->delete($contest['Contest']['id']);
    }

    public function beforeDelete($cascade = true) {
        $item = $this->findById($this->id);
        if ($item) {
            // delete entries
            $entryModel = MooCore::getInstance()->getModel('Contest.ContestEntry');
            $entries = $entryModel->find('all', array('conditions' => array('ContestEntry.contest_id' => $item['Contest']['id'])));
            if (!empty($entries)) {
                foreach ($entries as $entry) {
                    $entryModel->deleteEntry($entry['ContestEntry']['id']);
                }
            }
            // delete activity
            $activityModel = MooCore::getInstance()->getModel('Activity');
            $parentActivity = $activityModel->find('list', array('fields' => array('Activity.id'), 'conditions' =>
                array('Activity.item_type' => 'Contest_Contest', 'Activity.item_id' => $item['Contest']['id'])));
            $activityModel->deleteAll(array('Activity.item_type' => 'Contest_Contest', 'Activity.parent_id' => $parentActivity));
            $activityModel->deleteAll(array('Activity.item_type' => 'Contest_Contest', 'Activity.parent_id' => $this->id));
        }
        parent::beforeDelete($cascade);
    }

    public function updateCounter($id, $field = 'comment_count', $conditions = '', $model = 'Comment') {
        if (empty($conditions)) {
            $conditions = array('Comment.type' => 'Contest_Contest', 'Comment.target_id' => $id);
        }
        parent::updateCounter($id, $field, $conditions, $model);
    }

    public function afterDeleteComment($comment) {
        if ($comment['Comment']['target_id']) {
            $this->decreaseCounter($comment['Comment']['target_id']);
        }
    }

    public function getFriends($id) {
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        return $helper->getFriends($id);
    }

    public function afterComment($data, $uid) {
        $this->increaseCounter($data['target_id']);
    }

    public function countContestByCategory($category_id, $params = array()) {
        if (count($params)) {
            $params['category'] = $category_id;
            $conditions = $this->getConditionsContests($params);
        } else {
            $conditions = array(
                'Contest.category_id' => $category_id,
            );
        }

        $num = $this->find('count', array(
            'conditions' => $conditions
        ));

        return $num;
    }

    public function getCategories($params = array()) {
        $categoryModel = MooCore::getInstance()->getModel('Category');
        $cond = array('Category.type' => 'Contest', 'Category.header' => 0, 'Category.active' => 1);
        $categories = $categoryModel->find('all', array('conditions' => $cond, 'order' => 'Category.type asc, Category.weight asc'));
        foreach ($categories as &$category) {
            $category['Category']['item_count'] = $this->countContestByCategory($category['Category']['id'], $params);
        }

        return $categories;
    }

    public function getTotalContests($params = array()) {
        $conditions = $this->getConditionsContests($params);
        //debug($conditions);die;
        return $this->find('count', array('conditions' => $conditions));
    }

    public function getContests($params = array()) {
        $conditions = $this->getConditionsContests($params);
        //debug($conditions);die;
        $page = 1;
        if (isset($params['page']) && $params['page']) {
            $page = $params['page'];
        }
        $limit = Configure::read('Contest.contest_item_per_pages');
        if (isset($params['limit']) && $params['limit']) {
            $limit = $params['limit'];
        }
        $order = array('Contest.id desc');
        if (isset($params['order'])) {
            $order = array($params['order']);
        }
        return $this->find('all', array(
                    'conditions' => $conditions,
                    'limit' => $limit,
                    'page' => $page,
                    'order' => $order
        ));
    }

    public function getConditionsContests($params = array()) {
        $viewer = MooCore::getInstance()->getViewer();
        $friend_ids = array();
        $conditions = array('Contest.approve_status' => 'approved', 'Contest.contest_status <>' => 'draft');
        if (!$viewer || !$viewer['Role']['is_admin'] && (isset($params['owner_id']) && isset($params['user_id']) && $params['owner_id'] != $params['user_id'])) {

            if (isset($params['user_id']) && $params['user_id']) {
                $friend_ids = $this->getFriends($params['user_id']);
                $conditions['OR'] = array(
                    array('Contest.user_id' => $params['user_id']),
                    array('Contest.privacy' => PRIVACY_EVERYONE)
                );
                if (count($friend_ids)) {
                    $conditions['OR'][] = array('Contest.user_id' => $friend_ids, 'Contest.privacy' => PRIVACY_FRIENDS);
                }
            } else {
                $conditions['Contest.privacy'] = PRIVACY_EVERYONE;
            }
        }

        if ($viewer && $viewer['Role']['is_admin']) {
            $friend_ids = $this->getFriends($viewer['User']['id']);
        }

        if (isset($params['type']) && $params['type']) {
            switch ($params['type']) {
                case 'all':
                    break;
                case 'upcoming':
                    $conditions['Contest.contest_status'] = 'published';
                    $conditions['Contest.duration_start >'] = date('Y-m-d H:i:s');
                    break;
                case 'active':
                    $conditions['Contest.contest_status'] = 'published';
                    $conditions['Contest.duration_start <'] = date('Y-m-d H:i:s');
                    $conditions['Contest.duration_end >'] = date('Y-m-d H:i:s');
                    break;
                case 'close':
                    $conditions['Contest.contest_status'] = 'closed';
                    break;
                case 'my':
                case 'home':
                    $conditions = array();
                    $conditions['Contest.user_id'] = $params['user_id'];
                    break;
                case 'profile':
                    if ($viewer['Role']['is_admin'] || $params['user_id'] == $params['owner_id']) {
                        unset($conditions['Contest.approve_status']);
                        unset($conditions['Contest.contest_status <>']);
                    }
                    break;
                case 'join':
                    $mCandidate = MooCore::getInstance()->getModel('Contest.ContestCandidate');
                    $contest_ids = $mCandidate->getJoinedContest($params['user_id']);
                    $conditions['Contest.id'] = $contest_ids;
                    break;
                case 'friend':
                    if (!count($friend_ids))
                        $friend_ids = array(0);
                    $conditions['Contest.user_id'] = $friend_ids;
                    break;
            }
        }
        if (isset($params['category']) && $params['category']) {
            $conditions['Contest.category_id'] = $params['category'];
        }

        if (isset($params['owner_id']) && $params['owner_id']) {
            $conditions['Contest.user_id'] = $params['owner_id'];
        }

        if (isset($params['feature']) && $params['feature']) {
            $conditions['Contest.featured'] = 1;
        }
        if (isset($params['ids'])) {
            $conditions['Contest.id'] = $params['ids'];
        }
        if (isset($params['search']) && $params['search']) {
            $conditions['AND'] = array(
                'OR' => array('Contest.name LIKE ' => '%' . $params['search'] . '%')
            );
        }
        // $conditions = $this->addBlockCondition($conditions);
        //debug($conditions);die;
        return $conditions;
    }

    public function getFeaturedContests($limit) {
        $viewer = MooCore::getInstance()->getViewer();
        if (isset($viewer['Role']['is_admin']) && $viewer['Role']['is_admin']) {
            $cond = array('Contest.featured' => 1, 'Contest.approve_status' => 'approved', 'Contest.contest_status <>' => 'draft');
        } else {
            if ($viewer['User']['id']) {
                $friendModel = MooCore::getInstance()->getModel('Friend');
                $friend_list = $friendModel->getFriendsList($viewer['User']['id']);
                $cond = array(
                    'Contest.featured' => 1,
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'OR' => array(
                        array(
                            'Contest.privacy' => PRIVACY_EVERYONE,
                        ),
                        array(
                            'Contest.user_id' => $viewer['User']['id']
                        ),
                        array(
                            'Contest.user_id' => array_keys($friend_list),
                            'Contest.privacy' => PRIVACY_FRIENDS
                        )
                    ),
                );
            } else {
                $cond = array(
                    'Contest.featured' => 1,
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'Contest.privacy' => PRIVACY_EVERYONE,
                );
            }
        }
        // $cond = $this->addBlockCondition($cond);

        //debug($cond);die;
        return $this->find('all', array('conditions' => $cond, 'order' => array('Contest.featured' => 'DESC', 'Contest.like_count' => 'DESC'), 'limit' => $limit));
    }

    public function getPopularContests($limit) {
        $viewer = MooCore::getInstance()->getViewer();
        if (isset($viewer['Role']['is_admin']) && $viewer['Role']['is_admin']) {
            $cond = array('Contest.approve_status' => 'approved', 'Contest.contest_status <>' => 'draft');
        } else {
            if ($viewer['User']['id']) {
                $friendModel = MooCore::getInstance()->getModel('Friend');
                $friend_list = $friendModel->getFriendsList($viewer['User']['id']);
                $cond = array(
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'OR' => array(
                        array(
                            'Contest.privacy' => PRIVACY_EVERYONE,
                        ),
                        array(
                            'Contest.user_id' => $viewer['User']['id']
                        ),
                        array(
                            'Contest.user_id' => array_keys($friend_list),
                            'Contest.privacy' => PRIVACY_FRIENDS
                        )
                    ),
                );
            } else {
                $cond = array(
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'Contest.privacy' => PRIVACY_EVERYONE,
                );
            }
        }
        // $cond = $this->addBlockCondition($cond);
        //debug($cond);die;
        return $this->find('all', array('conditions' => $cond, 'order' => array('Contest.contest_candidate_count' => 'DESC', 'Contest.created' => 'DESC'), 'limit' => $limit));
    }

    public function getRecentContests($limit) {
        $viewer = MooCore::getInstance()->getViewer();
        if (isset($viewer['Role']['is_admin']) && $viewer['Role']['is_admin']) {
            $cond = array('Contest.approve_status' => 'approved', 'Contest.contest_status <>' => 'draft');
        } else {
            if ($viewer['User']['id']) {
                $friendModel = MooCore::getInstance()->getModel('Friend');
                $friend_list = $friendModel->getFriendsList($viewer['User']['id']);
                $cond = array(
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'OR' => array(
                        array(
                            'Contest.privacy' => PRIVACY_EVERYONE,
                        ),
                        array(
                            'Contest.user_id' => $viewer['User']['id']
                        ),
                        array(
                            'Contest.user_id' => array_keys($friend_list),
                            'Contest.privacy' => PRIVACY_FRIENDS
                        )
                    ),
                );
            } else {
                $cond = array(
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'Contest.privacy' => PRIVACY_EVERYONE,
                );
            }
        }
        // $cond = $this->addBlockCondition($cond);
        //debug($cond);die;
        return $this->find('all', array('conditions' => $cond, 'order' => array('Contest.featured' => 'DESC', 'Contest.created' => 'DESC'), 'limit' => $limit));
    }

    public function getTopContests($limit) {
        $viewer = MooCore::getInstance()->getViewer();
        if (isset($viewer['Role']['is_admin']) && $viewer['Role']['is_admin']) {
            $cond = array('Contest.approve_status' => 'approved', 'Contest.contest_status <>' => 'draft');
        } else {
            if ($viewer['User']['id']) {
                $friendModel = MooCore::getInstance()->getModel('Friend');
                $friend_list = $friendModel->getFriendsList($viewer['User']['id']);
                $cond = array(
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'OR' => array(
                        array(
                            'Contest.privacy' => PRIVACY_EVERYONE,
                        ),
                        array(
                            'Contest.user_id' => $viewer['User']['id']
                        ),
                        array(
                            'Contest.user_id' => array_keys($friend_list),
                            'Contest.privacy' => PRIVACY_FRIENDS
                        )
                    ),
                );
            } else {
                $cond = array(
                    'Contest.approve_status' => 'approved',
                    'Contest.contest_status <>' => 'draft',
                    'Contest.privacy' => PRIVACY_EVERYONE,
                );
            }
        }
        // $cond = $this->addBlockCondition($cond);
        //debug($cond);die;
        return $this->find('all', array('conditions' => $cond, 'order' => array('Contest.contest_entry_count' => 'DESC', 'Contest.created' => 'DESC'), 'limit' => $limit));
    }
    public function getContestHashtags($qid, $limit = RESULTS_LIMIT,$page = 1){
        $cond = array(
            'Contest.id' => $qid,
            'Contest.approve_status' => 'approved',
            'Contest.contest_status' => array('published', 'win'),
            'User.active' => 1
        );
        // $cond = $this->addBlockCondition($cond);
        return $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
    }


}
