<?php

class ContestsController extends ContestAppController {

    public $components = array('Paginator');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Contest.Contest');
        $this->loadModel('Contest.ContestSetting');
        $this->loadModel('Category');
        $this->loadModel('Contest.ContestEntry');
        $this->loadModel('Contest.ContestCandidate');
        $this->loadModel('Contest.ContestVote');
        $this->loadModel('Contest.ContestTransacion');
        $viewer = MooCore::getInstance()->getViewer();
        $this->set('viewer', $viewer);
        $this->contest_integrate_credit = $this->ContestSetting->getValueSetting('contest_integrate_credit');
        $this->set('contest_integrate_credit', $this->contest_integrate_credit);
    }

    public function admin_index() {
        $this->Paginator->settings = array(
            'limit' => Configure::read('Contest.contest_item_per_pages'),
            'order' => array(
                'Contest.id' => 'DESC'
            )
        );

        $cond = array();
        $passedArgs = array();
        $named = $this->request->params['named'];
        if ($named) {
            foreach ($named as $key => $value) {
                $this->request->data[$key] = $value;
            }
        }

        if (!empty($this->request->data['category_id'])) {
            $cond['Contest.category_id'] = $this->request->data['category_id'];
            $this->set('category_id', $this->request->data['category_id']);
            $passedArgs['category_id'] = $this->request->data['category_id'];
        }

        if (isset($this->request->data['featured']) && $this->request->data['featured'] != '') {
            $cond['Contest.featured'] = $this->request->data['featured'];
            $this->set('featured', $this->request->data['featured']);
            $passedArgs['featured'] = $this->request->data['featured'];
        }

        if (!empty($this->request->data['name'])) {
            $cond['Contest.name LIKE'] = '%' . $this->request->data['name'] . '%';
            $this->set('name', $this->request->data['name']);
            $passedArgs['name'] = $this->request->data['name'];
        }

        $this->loadModel('Category');
        $categories = $this->Category->getCategoriesList('Contest');
        $this->set('categories', $categories);
        $contests = $this->Paginator->paginate('Contest', $cond);
        $this->set('contests', $contests);
        $this->set('passedArgs', $passedArgs);
        $this->set('title_for_layout', __d('contest', 'Contests'));
    }

    public function admin_entry() {
        $this->Paginator->settings = array(
            'limit' => Configure::read('Contest.contest_item_per_pages'),
            'order' => array(
                'ContestEntry.created' => 'DESC',
                'Contest.id' => 'DESC',
            )
        );

        $cond = array();
        $passedArgs = array();
        $named = $this->request->params['named'];
        if ($named) {
            foreach ($named as $key => $value) {
                $this->request->data[$key] = $value;
            }
        }

        if (!empty($this->request->data['entry_status'])) {
            $cond['ContestEntry.entry_status'] = $this->request->data['entry_status'];
            $this->set('entry_status', $this->request->data['entry_status']);
            $passedArgs['entry_status'] = $this->request->data['entry_status'];
        }
        if (!empty($this->request->data['name'])) {
            $cond['Contest.name LIKE'] = '%' . $this->request->data['name'] . '%';
            $this->set('name', $this->request->data['name']);
            $passedArgs['name'] = $this->request->data['name'];
        }

        $entry_status_select = MooCore::getInstance()->getHelper('Contest_Contest')->getEntryStatusSelect();
        $this->set('entry_status_select', $entry_status_select);
        $entries = $this->Paginator->paginate('ContestEntry', $cond);
        $this->set('entries', $entries);
        $this->set('passedArgs', $passedArgs);
        $this->set('title_for_layout', __d('contest', 'Entries'));
    }

    public function admin_contest_entry_status($entry_status, $id) {
        if (!$this->ContestEntry->isContestEntryExist($id)) {
            $this->_redirectError(__d('contest', 'Entry not found'), '/admin/contest/contests/entry');
        }
        else {
            $this->ContestEntry->updateStatus($id, $entry_status);
            return $this->_redirectSuccess(__d('contest', 'Successfully update entry status'), '/admin/contest/contests/entry');
        }
    }

    public function admin_delete_entry($id = null) {
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $this->_checkExistence($entry);
        $this->_checkPermission(array('admins' => array($entry['User']['id'])));
        $this->ContestEntry->deleteEntry($entry['ContestEntry']['id']);
        $cakeEvent = new CakeEvent('Plugin.Controller.Contest.afterDeleteContestEntry', $this, array('item' => $entry));
        $this->getEventManager()->dispatch($cakeEvent);
        return $this->_redirectSuccess(__d('contest', 'Entry has been deleted'), $this->referer());
    }

    public function admin_mul_delete() {
        $this->_checkPermission(array('super_admin' => 1));
        //var_dump($_POST['contest_entries']);die;
        if (!empty($_POST['contest_entries'])) {
            $entries = $this->ContestEntry->findAllById($_POST['contest_entries']);

            foreach ($entries as $entry) {
                $this->ContestEntry->deleteEntry($entry['ContestEntry']['id']);
            }
        }
        return $this->_redirectSuccess(__d('contest', 'Entries has been deleted'), $this->referer());
    }

    public function admin_approve_status($status, $id) {
        if (!$this->Contest->isContestExist($id)) {
            $this->_redirectError(__d('contest', 'Contest not found'), $this->referer());
        }
        else {
            $this->Contest->updateApproveStatus($id, $status);
            $this->_redirectSuccess(__d('contest', 'Successfully change approve status'), $this->referer());
        }
    }

    public function admin_contest_status($status, $id) {
        if (!$this->Contest->isContestExist($id)) {
            $this->_redirectError(__d('contest', 'Contest not found'), $this->referer());
        }
        else {
            $this->Contest->updateContestStatus($id, $status);
            $this->_redirectSuccess(__d('contest', 'Successfully change contest status'), $this->referer());
        }
    }

    public function admin_contest_category($category_id, $id) {
        if (!$this->Contest->isContestExist($id)) {
            $this->_redirectError(__d('contest', 'Contest not found'), '/admin/contest/contests');
        }
        else {
            if ($this->Contest->changeCategory($category_id, $id)) {
                return $this->_redirectSuccess(__d('contest', 'Successfully change category'), '/admin/contest/contests');
            }
            // $this->_redirectError(__d('contest', 'Can not change category! Please try again'), '/admin/contest/contests');
        }
    }

    public function admin_contest_feature($id) {
        if (!$this->Contest->isContestExist($id)) {
            $this->_redirectError(__d('contest', 'Contest not found'), '/admin/contest/contests');
        }
        else {
            if ($this->Contest->changeFeature($id)) {
                $contest = $this->Contest->findById($id);
                if ($contest['Contest']['featured']) {
                    $this->_redirectSuccess(__d('contest', 'Contest has been featured'), $this->referer());
                }
                else {
                    $this->_redirectSuccess(__d('contest', 'Contest has been un-featured'), $this->referer());
                }
            }
            $this->_redirectError(__d('contest', 'Can not set feature contest! Please try again'), $this->referer());
        }
    }

    public function ajax_delete() {
        $this->autoRender = false;
        $id = intval($this->request->data['id']);
        $contest = $this->Contest->findById($id);
        $this->_checkExistence($contest);
        $this->_checkPermission(array('admins' => array($contest['User']['id'])));
        $this->Contest->deleteContest($contest);
        $cakeEvent = new CakeEvent('Plugin.Controller.Contest.afterDeleteContest', $this, array('item' => $contest));
        $this->getEventManager()->dispatch($cakeEvent);
        $response['result'] = 1;
        echo json_encode($response);
    }

    public function delete($id = null) {
        $id = intval($id);
        $contest = $this->Contest->findById($id);
        $this->_checkExistence($contest);
        $this->_checkPermission(array('admins' => array($contest['User']['id'])));
        $this->Contest->deleteContest($contest);
        $cakeEvent = new CakeEvent('Plugin.Controller.Contest.afterDeleteContest', $this, array('item' => $contest));
        $this->getEventManager()->dispatch($cakeEvent);
        $this->Session->setFlash(__d('contest', 'Contest has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if (!$this->isApp())
    	{
    		$this->redirect( '/contests' );
    	}
    }

    public function request_delete($id) {
        $id = intval($id);
        $contest = $this->Contest->findById($id);
        $this->_checkExistence($contest);
        $this->_checkPermission(array('admins' => array($contest['User']['id'])));
        // add notification and request_delete column
        $this->Contest->id = $id;
        $this->Contest->save(array('request_delete' => 1));
        $notificationModel = MooCore::getInstance()->getModel('Notification');
        $notification = $notificationModel->find('first', array('conditions' => array(
                'user_id' => CONTEST_ADMIN_ID,
                'sender_id' => $contest['User']['id'],
                'action' => 'request_delete',
                'url' => $contest['Contest']['moo_url'],
                'plugin' => 'Contest'
        )));
        $this->loadModel("UserBlock");
        $helper = MooCore::getInstance()->getHelper('Contest_Contest'); 
        $is_block = $helper->areUserBlocks(CONTEST_ADMIN_ID, $contest['User']['id']);
        if (empty($notification) && CONTEST_ADMIN_ID != $contest['User']['id'] && !$is_block) {
            $notificationModel->record(array(
                'recipients' => CONTEST_ADMIN_ID,
                'sender_id' => $contest['User']['id'],
                'action' => 'request_delete',
                'url' => $contest['Contest']['moo_url'],
                'params' => $contest['Contest']['id'],
                'plugin' => 'Contest'
            ));
        }
        // end
        $this->Session->setFlash(__d('contest', 'Request delete has been sent. Admin will review and delete this contest afterward'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if (!$this->isApp())
    	{
            $this->redirect($contest['Contest']['moo_url']);
    	}
    }

    public function delete_entry($id = null) {
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $this->_checkExistence($entry);
        $this->_checkPermission(array('admins' => array($entry['User']['id'], $entry['Contest']['user_id'])));
        $this->ContestEntry->deleteEntry($entry['ContestEntry']['id']);
        $cakeEvent = new CakeEvent('Plugin.Controller.Contest.afterDeleteContestEntry', $this, array('item' => $entry));
        $this->getEventManager()->dispatch($cakeEvent);
        $this->Session->setFlash(__d('contest', 'Entry has been deleted successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if (!$this->isApp())
    	{
            return $this->redirect($entry['Contest']['moo_url']);
        }
    }

    public function approve_entry($id = null) {
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $this->_checkExistence($entry);
        $this->_checkPermission(array('admins' => array($entry['Contest']['user_id'])));
        $this->ContestEntry->updateStatus($entry['ContestEntry']['id'], 'published');
        $this->Session->setFlash(__d('contest', 'Entry has been approved successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if (!$this->isApp())
    	{
            $this->redirect($entry['ContestEntry']['moo_url']);
        }
    }

    public function win_entry($id = null) {
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $this->_checkExistence($entry);
        $this->_checkPermission(array('admins' => array($entry['Contest']['user_id'])));
        $this->ContestEntry->updateStatus($entry['ContestEntry']['id'], 'win');
        $this->Session->setFlash(__d('contest', 'Entry has been set to win successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if (!$this->isApp())
    	{
            $this->redirect($entry['ContestEntry']['moo_url']);
        }
    }

    public function admin_delete($id = null) {
        $id = intval($id);
        $contest = $this->Contest->findById($id);
        $this->_checkExistence($contest);
        $this->_checkPermission(array('admins' => array($contest['User']['id'])));
        $this->Contest->deleteContest($contest);
        $cakeEvent = new CakeEvent('Plugin.Controller.Contest.afterDeleteContest', $this, array('item' => $contest));
        $this->getEventManager()->dispatch($cakeEvent);
        $this->Session->setFlash(__d('contest', 'Contest has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect('/admin/contest/contests');
    }

    public function index() {
        
        $params = $this->request->params['named'];
        if (isset($params['category']) && $params['category']) {
            $this->loadModel('Catagory');
            $category = $this->Category->findById($params['category']);
            if ($category && !$category['Category']['active']) {
                $this->redirect('/contests');
            }
        }
        if ($this->isApp()) {
            App::uses('browseContestWidget', 'Contest.Controller' . DS . 'Widgets' . DS . 'contest');
            $browseContestWidget = new browseContestWidget(new ComponentCollection(), null);
            $browseContestWidget->beforeRender($this);
        }
        $this->set('title_for_layout', '');
    }

    public function browse($type = null, $param = null) {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = (!empty($param) ) ? $type . '/' . $param : $type;
        $uid = MooCore::getInstance()->getViewer(true);
        $contests = array();
        $total = 0;

        switch ($type) {
            case 'all':
            case 'my':
            case 'friend':
            case 'home':
            case 'active':
            case 'upcoming':
            case 'close':
            case 'join':
                $contests = $this->Contest->getContests(array('type' => $type, 'user_id' => $uid, 'page' => $page));
                $total = $this->Contest->getTotalContests(array('type' => $type, 'user_id' => $uid, 'page' => $page));
                break;
            case 'profile':
                $contests = $this->Contest->getContests(array('type' => $type, 'owner_id' => $param, 'user_id' => $uid, 'page' => $page));
                $total = $this->Contest->getTotalContests(array('type' => $type, 'owner_id' => $param, 'user_id' => $uid, 'page' => $page));
                break;
            case 'category':
                $contests = $this->Contest->getContests(array('category' => $param, 'user_id' => $uid, 'page' => $page));
                $total = $this->Contest->getTotalContests(array('category' => $param, 'user_id' => $uid, 'page' => $page));
                break;
            case 'search':
                $contests = $this->Contest->getContests(array('search' => $param, 'user_id' => $uid, 'page' => $page));
                $total = $this->Contest->getTotalContests(array('search' => $param, 'user_id' => $uid, 'page' => $page));
                break;
        }

        $limit = Configure::read('Contest.contest_item_per_pages');
        $is_view_more = (($page - 1) * $limit + count($contests)) < $total;

        $this->set('is_view_more', $is_view_more);
        $this->set('contests', $contests);
        $this->set('type', $type);
        $this->set('page', $page);
        $this->set('param', $param);
        $this->set('url_more', '/contests/browse/' . htmlspecialchars($url) . '/page:' . ( $page + 1 ));
        if($type != "profile"){
            $this->set('title_for_layout', '');
        }else{
            $this->set('title_for_layout', __d('contest', 'Contests'));
        }
    
    }

    public function publish($id = null) {
        $viewer = MooCore::getInstance()->getViewer();
        $contest = $this->Contest->findById($id);
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        if ($helper->canPublish($contest, $viewer)) {
            $this->Contest->updateContestStatus($id, 'published');
            if (!$this->isApp())
            {
                return $this->_redirectSuccess(__d('contest', 'Contest has been published successfully'), '/contests/view/' . $contest['Contest']['id']);
            }else{
                $this->Session->setFlash(__d('contest', 'Contest has been published successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

            }
        }
        else {
            if (!$this->isApp())
            {
                return $this->_redirectError(__d('contest', 'Can not publish contest! Please check all information try again'), '/contests/create/' . $contest['Contest']['id']);
            }else{
                $this->Session->setFlash(__d('contest', 'Can not publish contest! Please check all information try again'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));

            }
            
        }
    }
    public function publish_app($id = null) {
        $this->autoRender = false;
        $viewer = MooCore::getInstance()->getViewer();
        $contest = $this->Contest->findById($id);
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        if ($helper->canPublish($contest, $viewer)) {
            $this->Contest->updateContestStatus($id, 'published');
            $this->Session->setFlash(__d('contest', 'Contest has been published successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }
        else {
            $this->Session->setFlash(__d('contest', 'Can not publish contest! Please check all information try again'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
        }
        $response['result'] = 1;
        $response['id'] = $contest['Contest']['id'];
        echo json_encode($response);
        exit;
    }

    public function create($id = null) {
        $id = intval($id);
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'contest_create'));
        $this->loadModel('Category');
        $role_id = $this->_getUserRoleId();
        $categories = $this->Category->getCategoriesList('Contest', $role_id);
        $this->loadModel('Tag');
        $is_edit = false;
        if ($id) {
            $contest = $this->Contest->findById($id);
            if ($contest) {
                $viewer = MooCore::getInstance()->getViewer();
                $this->_checkAllowEdit($contest, $viewer);
                $this->_checkPermission(array('admins' => array($contest['User']['id'])));
                $this->Contest->id = $id;
                $is_edit = true;
                $tags = $this->Tag->getContentTags($id, 'Contest_Contest');
                $contest['Contest']['tags'] = $tags;
                $this->set('title_for_layout', __d('contest', 'Edit Contest'));
            }
        }
        else {
            $contest = $this->Contest->initFields();
            $contest['Contest']['tags'] = '';
            $this->set('title_for_layout', __d('contest', 'Create new contest'));
        }
        $this->set('categories', $categories);
        $this->set('contest', $contest);
        $this->set('is_edit', $is_edit);
    }

    public function upload_avatar() {
        $this->autoRender = false;
        $uid = MooCore::getInstance()->getViewer(true);

        if (!$uid)
            return;

        // save this picture to album
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit();
    }
    public function upload_file() {
        $this->autoRender = false;
    	$this->_checkPermission(array('confirm' => true));
    	$allowedExtensions = array('mp3','mp4','ogg','webm','wav');
    
    	App::import('Vendor', 'qqFileUploader');
    	$uploader = new qqFileUploader($allowedExtensions);
    
    	// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
    	$path = 'uploads' . DS . 'contest_entries'.DS.'music';
        $this->_prepareDir($path);
    	$original_filename = $this->request->query['qqfile'];
    	$result = $uploader->handleUpload($path);
    
    	if (!empty($result['success'])) {
    		$result['file'] = $result['filename'];
    		$result['original_filename'] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $original_filename);
    	}
    
    	// to pass data through iframe you will need to encode all html tags
    	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit();
    }
    public function save() {
        $this->loadModel('Tag');
        $this->autoRender = false;
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $this->_checkPermission(array('aco' => 'contest_create'));
        $this->_checkPermission(array('confirm' => true));

        $uid = MooCore::getInstance()->getViewer(true);
        $id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
        $is_edit = false;

        if ($id) {
            $contest = $this->Contest->findById($id);
            if ($contest) {
                $this->_checkPermission(array('admins' => array($contest['User']['id'])));
                $this->Contest->id = $id;
                $is_edit = true;
            }
        }
        $this->request->data['description'] = str_replace('../', '/', $this->request->data['description']);
        $this->request->data['award'] = str_replace('../', '/', $this->request->data['award']);
        $this->request->data['term_and_condition'] = str_replace('../', '/', $this->request->data['term_and_condition']);
        if ($this->request->data['description'] == '<div>&nbsp;</div>') {
            $this->request->data['description'] = '';
        }
        if ($this->request->data['award'] == '<div>&nbsp;</div>') {
            $this->request->data['award'] = '';
        }
        if ($this->request->data['term_and_condition'] == '<div>&nbsp;</div>') {
            $this->request->data['term_and_condition'] = '';
        }
        if (!empty($this->request->data['from']) && !empty($this->request->data['from_time']) && !empty($this->request->data['timezone'])) {
            $this->request->data['duration_start'] = $helper->getTimeOnUTC($this->request->data['from'] . ' ' . $this->request->data['from_time'], $this->request->data['timezone'], 'Y-m-d H:i:s');
        }
        else {
            $this->request->data['duration_start'] = '';
        }
        if (!empty($this->request->data['to']) && !empty($this->request->data['to_time']) && !empty($this->request->data['timezone'])) {
            $this->request->data['duration_end'] = $helper->getTimeOnUTC($this->request->data['to'] . ' ' . $this->request->data['to_time'], $this->request->data['timezone'], 'Y-m-d H:i:s');
        }
        else {
            $this->request->data['duration_end'] = '';
        }
        if (!empty($this->request->data['s_from']) && !empty($this->request->data['s_from_time']) && !empty($this->request->data['timezone'])) {
            $this->request->data['submission_start'] = $helper->getTimeOnUTC($this->request->data['s_from'] . ' ' . $this->request->data['s_from_time'], $this->request->data['timezone'], 'Y-m-d H:i:s');
        }
        else {
            $this->request->data['submission_start'] = '';
        }
        if (!empty($this->request->data['s_to']) && !empty($this->request->data['s_to_time']) && !empty($this->request->data['timezone'])) {
            $this->request->data['submission_end'] = $helper->getTimeOnUTC($this->request->data['s_to'] . ' ' . $this->request->data['s_to_time'], $this->request->data['timezone'], 'Y-m-d H:i:s');
        }
        else {
            $this->request->data['submission_end'] = '';
        }
        if (!empty($this->request->data['v_from']) && !empty($this->request->data['v_from_time']) && !empty($this->request->data['timezone'])) {
            $this->request->data['voting_start'] = $helper->getTimeOnUTC($this->request->data['v_from'] . ' ' . $this->request->data['v_from_time'], $this->request->data['timezone'], 'Y-m-d H:i:s');
        }
        else {
            $this->request->data['voting_start'] = '';
        }
        if (!empty($this->request->data['v_to']) && !empty($this->request->data['v_to_time']) && !empty($this->request->data['timezone'])) {
            $this->request->data['voting_end'] = $helper->getTimeOnUTC($this->request->data['v_to'] . ' ' . $this->request->data['v_to_time'], $this->request->data['timezone'], 'Y-m-d H:i:s');
        }
        else {
            $this->request->data['voting_end'] = '';
        }
        $this->Contest->set($this->request->data);
        $this->_validateData($this->Contest);
        $data = $this->request->data;

        if (!$is_edit) {
            $data['user_id'] = $uid;
        }
        if ($data['contest_status'] == 'published' && !$data['publish_confirm']) {
            $response['result'] = 'publish_confirm';
            echo json_encode($response);
            exit;
        }
        else {
            if ($this->Contest->save($data)) {
                //save tag
                $this->Tag->saveTags($this->request->data['tags'], $this->Contest->id, 'Contest_Contest');
                //auto approve
                $auto_approved = $helper->checkAutoApproved();
                if ($auto_approved) {
                    $this->Contest->updateApproveStatus($this->Contest->id, 'approved');
                }
                if (!$is_edit) {
                    // add candidate for owner
                    $data_candidate = array('user_id' => $uid, 'contest_id' => $this->Contest->id);
                    $this->ContestCandidate->save($data_candidate);

                    $this->Session->setFlash(__d('contest', 'Contest has been successfully added'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                }
                else {
                    $this->Session->setFlash(__d('contest', 'Contest has been successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                }
                $event = new CakeEvent('Plugin.Controller.Contest.afterSaveContest', $this, array(
                    'tags' => $this->request->data['tags'],
                    'id' => $this->Contest->id,
                    'privacy' => $this->request->data['privacy']
                ));
                $this->getEventManager()->dispatch($event);
                $contest = $this->Contest->read();
                $response['result'] = 1;
                $response['id'] = $this->Contest->id;
    		    $response['href'] = $contest['Contest']['moo_href'];
                echo json_encode($response);
                exit;
            }
        }
    }

    public function view($id = null) {
        $contest_id = intval($id);
        $contest = $this->Contest->findById($contest_id);
        //debug($contest);die;
        $this->_checkExistence($contest);
        $this->_checkPermission(array('aco' => 'contest_view'));
        // $this->_checkPermission( array('user_block' => $contest['Contest']['user_id']) );
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $contest_allow_view = $helper->canView($contest);
        if (!$contest_allow_view) {
            return $this->_redirectError(__d('contest', 'You do not have permission to view this contest'), '/contests');
        }
        $uid = $this->Auth->user('id');
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($this->request->named['tab'])) { // open a specific tab
            $this->set('tab', $this->request->named['tab']);
        }
        if (!empty($uid) && $uid != $contest['User']['id']) {
            $this->Contest->updateAll(array('view_count' => $contest['Contest']['view_count'] + 1), array('Contest.id' => $contest_id));
        }
        $event = new CakeEvent('Plugin.Controller.Contest.beforeView', $this, array('id' => $contest_id, 'uid' => $uid, 'contest' => $contest));
        $this->getEventManager()->dispatch($event);
        MooCore::getInstance()->setSubject($contest);
        $this->loadModel('Like');
        $likes = $this->Like->getLikes($contest['Contest']['id'], 'Contest_Contest');
        $this->set('likes', $likes);
        // set og:image
        if ($contest['Contest']['thumbnail']) {
            $this->set('og_image', $helper->getImage($contest, array('prefix' => '850')));
        }
        $og = array('type' => 'contest');
        $this->set('og', $og);
        $this->set('contest', $contest);
        $this->set('viewer', $viewer);

        $this->set('title_for_layout', htmlspecialchars($contest['Contest']['name']));
        $description = $this->getDescriptionForMeta($contest['Contest']['description']);
        if ($description) {
            $this->set('description_for_layout', $description);
            $tags = $this->viewVars['tags'];
            if (count($tags))
            {
                $tags = implode(",", $tags).' ';
            }
            else
            {
                $tags = '';
            }
            $this->set('mooPageKeyword', $this->getKeywordsForMeta($tags.$description));
        }
        $this->set('admins', array($contest['Contest']['user_id']));

        $type = 'approved';
        $page = 1;
        $limit = Configure::read('Contest.contest_item_per_pages');
        $params = array('contest_id' => $contest_id);
        $no_entries = false;
        $all_entries = $this->ContestEntry->findByContestId($id);
        if (empty($all_entries)) {
            $no_entries = true;
        }
        $entries = $this->ContestEntry->getContestEntries($type, $params, $page, $limit);
        $entries_count = $this->ContestEntry->getContestEntriesCount($type, $params);
        $is_more_url = true;
        if ($entries_count <= $limit * $page) {
            $is_more_url = false;
        }
        $more_url = '/contests/browse_entries/' . htmlspecialchars($type) . '/' . $contest_id . '/page:' . ($page + 1);
        $this->set('entries', $entries);
        $this->set('is_more_url', $is_more_url);
        $this->set('more_url', $more_url);
        $this->set('params', $params);
        $this->set('type', $type);
        $this->set('no_entries', $no_entries);
        $this->set('entry_action', array('approve' => __d('contest', 'Approve'), 'delete' => __d('contest', 'Delete'), 'win' => __d('contest', 'Win')));
    }

    public function contest_join($contest_id) {
        if ($this->isApp()){
            $this->autoRender = false;
        }   
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $contest = $this->Contest->findById($contest_id);
        if (!empty($contest)) {
            $this->ContestCandidate->join($contest, $viewer_id);
            $this->Session->setFlash(__d('contest', 'You have joined this contest successfully.'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }
        else {
            $this->Session->setFlash(__d('contest', 'Contest does not exist.'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
        }
        if (!$this->isApp())
    	{
            return $this->redirect('/contests/view/' . $contest['Contest']['id'] . '/' . seoUrl($contest['Contest']['moo_title']));
        }else{
        $response['result'] = 1;
        $response['href'] = $contest['Contest']['moo_href'];
        echo json_encode($response);
        exit;
        }
    }

    public function contest_leave($contest_id) {
        
        if ($this->isApp()){
            $this->autoRender = false;
        }   
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $contest = $this->Contest->findById($contest_id);
        if (!empty($contest)) {
            $this->ContestCandidate->leave($contest_id, $viewer_id);
            $this->ContestEntry->deleteEntries($viewer_id);
            if (!$contest['Contest']['vote_without_join']) {
                $votes = $this->ContestVote->getAllVoteContest($contest_id, $viewer_id);
                if (!empty($votes)) {
                    foreach ($votes as $vote) {
                        $this->ContestVote->deleteVote($vote['ContestVote']['id']);
                    }
                }
            }
            $this->Session->setFlash(__d('contest', 'You have left this contest.'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }
        else {
            $this->Session->setFlash(__d('contest', 'Contest does not exist.'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
        }
        if (!$this->isApp())
    	{
            return $this->redirect('/contests/view/' . $contest['Contest']['id'] . '/' . seoUrl($contest['Contest']['moo_title']));
        }else{
        $response['result'] = 1;
        $response['href'] = $contest['Contest']['moo_href'];
        echo json_encode($response);
        exit;
        }
    }

    public function contest_candidate($contest_id) {
        $contest = $this->Contest->findById($contest_id);
        $page = 1;
        $limit = Configure::read('Contest.contest_item_per_pages');
        $params = array('contest_id' => $contest_id);
        $candidates = $this->ContestCandidate->getContestCandidate($params, $page, $limit);
        $is_more_url = true;
        if (count($candidates) < $limit) {
            $is_more_url = false;
        }
        $more_url = '/contests/browse_candidate/' . $contest_id . '/page:' . ($page + 1);
        $this->set('candidates', $candidates);
        $this->set('contest', $contest);
        $this->set('is_more_url', $is_more_url);
        $this->set('more_url', $more_url);
        $this->set('params', $params);
        $uid = MooCore::getInstance()->getViewer(true);
        if (!empty($uid)) {
            $this->loadmodel('Friend');
            $this->loadModel('FriendRequest');
            $friends = $this->Friend->getFriends($uid);
            $friends_request = $this->FriendRequest->getRequestsList($uid);
            $respond = $this->FriendRequest->getRequests($uid);
            $request_id = Hash::combine($respond, '{n}.FriendRequest.sender_id', '{n}.FriendRequest.id');
            $respond = Hash::extract($respond, '{n}.FriendRequest.sender_id');
            $friends_requests = array_merge($friends, $friends_request);
            $this->set(compact('friends', 'respond', 'request_id', 'friends_request'));
        }
        $this->render('/Elements/ajax/contest_candidate');
    }

    public function browse_candidate($contest_id) {
        $contest = $this->Contest->findById($contest_id);
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $limit = Configure::read('Contest.contest_item_per_pages');
        $params = array('contest_id' => $contest_id);
        $candidates = $this->ContestCandidate->getContestCandidate($params, $page, $limit);
        $is_more_url = true;
        if (count($candidates) < $limit) {
            $is_more_url = false;
        }
        $more_url = '/contests/browse_candidate/' . $contest_id . '/page:' . ($page + 1);
        $this->set('candidates', $candidates);
        $this->set('contest', $contest);
        $this->set('is_more_url', $is_more_url);
        $this->set('more_url', $more_url);
        $this->set('params', $params);
        $uid = MooCore::getInstance()->getViewer(true);
        if (!empty($uid)) {
            $this->loadmodel('Friend');
            $this->loadModel('FriendRequest');
            $friends = $this->Friend->getFriends($uid);
            $friends_request = $this->FriendRequest->getRequestsList($uid);
            $respond = $this->FriendRequest->getRequests($uid);
            $request_id = Hash::combine($respond, '{n}.FriendRequest.sender_id', '{n}.FriendRequest.id');
            $respond = Hash::extract($respond, '{n}.FriendRequest.sender_id');
            $friends_requests = array_merge($friends, $friends_request);
            $this->set(compact('friends', 'respond', 'request_id', 'friends_request'));
        }
        $this->render('/Elements/lists/candidate_list');
    }

    public function contest_award($contest_id) {
        $contest = $this->Contest->findById($contest_id);
        $this->set('contest', $contest);
        $this->render('/Elements/ajax/contest_award');
    }

    public function contest_policy($contest_id) {
        $contest = $this->Contest->findById($contest_id);
        $this->set('contest', $contest);
        $this->render('/Elements/ajax/contest_policy');
    }

    public function save_info() {
        $this->autoRender = false;
        $this->_checkPermission(array('aco' => 'contest_create'));
        $this->_checkPermission(array('confirm' => true));
        $data = $this->request->data;
        $id = isset($data['id']) ? $data['id'] : '';
        if ($id) {
            $contest = $this->Contest->findById($id);
            if ($contest) {
                $this->_checkPermission(array('admins' => array($contest['User']['id'])));
                $this->Contest->id = $id;
            }
        }
        if (isset($data['award'])) {
            $data['award'] = str_replace('../', '/', $data['award']);
            if ($data['award'] == '<div>&nbsp;</div>') {
                $response['result'] = 0;
                $response['message'] = __d('contest', 'Award is required.');
                echo json_encode($response);
                exit();
            }
        }
        if (isset($data['term_and_condition'])) {
            $data['term_and_condition'] = str_replace('../', '/', $data['term_and_condition']);
            if ($data['term_and_condition'] == '<div>&nbsp;</div>') {
                $response['result'] = 0;
                $response['message'] = __d('contest', 'Terms & Conditions is required.');
                echo json_encode($response);
                exit();
            }
        }
        //debug($data);die;
        if ($this->Contest->save($data)) {
            $response['result'] = 1;
            echo json_encode($response);
            exit;
        }
    }

    public function contest_detail($contest_id) {
        $contest_id = intval($contest_id);
        $contest = $this->Contest->findById($contest_id);
        if ($contest['Category']['id'])
        {
            $cat = $this->Category->findById($contest['Category']['id']);
            if(!empty($cat['nameTranslation'])){
                foreach ($cat['nameTranslation'] as $translate)
                {
                    if ($translate['locale'] == Configure::read('Config.language'))
                    {
                        $contest['Category']['name'] = $translate['content'];
                        break;
                    }
                }
            }
        }
        $this->_checkExistence($contest);
        $this->_checkPermission(array('aco' => 'contest_view'));
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $contest_allow_view = $helper->canView($contest);
        if (!$contest_allow_view) {
            return $this->_redirectError(__d('contest', 'You do not have permission to view this contest'), '/contests');
        }
        $uid = $this->Auth->user('id');
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($this->request->named['tab'])) { // open a specific tab
            $this->set('tab', $this->request->named['tab']);
        }
        $event = new CakeEvent('Plugin.Controller.Contest.beforeView', $this, array('id' => $contest_id, 'uid' => $uid, 'contest' => $contest));
        $this->getEventManager()->dispatch($event);
        MooCore::getInstance()->setSubject($contest);
        $this->loadModel('Like');
        $likes = $this->Like->getLikes($contest['Contest']['id'], 'Contest_Contest');
        $this->set('likes', $likes);
        // set og:image
        if ($contest['Contest']['thumbnail']) {
            $this->set('og_image', $helper->getImage($contest, array('prefix' => '850')));
        }
        $og = array('type' => 'contest');
        $this->set('og', $og);
        $this->set('contest', $contest);
        $this->set('viewer', $viewer);
        $this->set('title_for_layout', htmlspecialchars($contest['Contest']['name']));
        
        $description = $this->getDescriptionForMeta($contest['Contest']['description']);
        $this->set('description_for_layout', $description);
        $this->set('admins', array($contest['Contest']['user_id']));
        $this->render('/Elements/ajax/contest_detail');
    }

    public function my_entries($contest_id) {
        $contest = $this->Contest->findById($contest_id);
        $viewer = MooCore::getInstance()->getViewer();
        $type = 'my_approved';
        $page = 1;
        $limit = Configure::read('Contest.contest_item_per_pages');
        $params = array('contest_id' => $contest_id);
        $entries = $this->ContestEntry->getContestEntries($type, $params, $page, $limit);
        $entries_count = $this->ContestEntry->getContestEntriesCount($type, $params);
        $is_more_url = true;
        if ($entries_count <= $limit * $page) {
            $is_more_url = false;
        }
        $no_entries = false;
        $my_entries = $this->ContestEntry->findByUserId($viewer['User']['id']);
        if (empty($my_entries)) {
            $no_entries = true;
        }
        $this->set('no_entries', $no_entries);
        $more_url = '/contests/browse_entries/' . htmlspecialchars($type) . '/' . $contest_id . '/page:' . ($page + 1);
        $this->set('entries', $entries);
        $this->set('contest', $contest);
        $this->set('viewer', $viewer);
        $this->set('is_more_url', $is_more_url);
        $this->set('more_url', $more_url);
        $this->set('params', $params);
        $this->set('type', $type);
        $this->set('entry_action', array('delete' => __d('contest', 'Delete')));
        $this->render('/Elements/ajax/my_entries');
    }

    public function browse_entries($type, $contest_id) {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $limit = Configure::read('Contest.contest_item_per_pages');
        $params = array('contest_id' => $contest_id);
        $entries = $this->ContestEntry->getContestEntries($type, $params, $page, $limit);
        $entries_count = $this->ContestEntry->getContestEntriesCount($type, $params);
        $is_more_url = true;
        if ($entries_count <= $limit * $page) {
            $is_more_url = false;
        }
        $contest = $this->Contest->findById($contest_id);
        $viewer = MooCore::getInstance()->getViewer();
        $this->set('viewer', $viewer);
        $this->set('entries', $entries);
        $this->set('more_url', '/contests/browse_entries/' . htmlspecialchars($type) . '/' . $contest_id . '/page:' . ($page + 1));
        $this->set('is_more_url', $is_more_url);
        $this->set('page', $page);
        $this->set('type', $type);
        $this->set('contest', $contest);
        $this->set('params', $params);
        $this->render('Contest.Elements/lists/entries_list');
    }

    public function winning_entries($contest_id) {
        $this->_checkPermission(array('confirm' => true));
        $contest = $this->Contest->findById($contest_id);
        $entries = $this->ContestEntry->getWinningEntries($contest);
        $this->set('entries', $entries);
        $this->set('contest', $contest);
        $this->render('/Elements/ajax/winning_entries');
    }

    public function submit_entry($contest_id) {
        $this->_checkPermission(array('confirm' => true));
        $contest = $this->Contest->findById($contest_id);
        $uid = MooCore::getInstance()->getViewer(true);
        $photos = $this->ContestEntry->getPhotoSelect($uid);
        $videos = $this->ContestEntry->getVideoSelect($uid);
        $this->set('contest', $contest);
        $this->set('photo_count', count($photos));
        $this->set('video_count', count($videos));
    }

    public function select_photos($contest_id) {
        $this->_checkPermission(array('confirm' => true));
        $contest = $this->Contest->findById($contest_id);
        $uid = MooCore::getInstance()->getViewer(true);
        $viewer = MooCore::getInstance()->getViewer();
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        if (!$helper->canSubmitEntry($contest, $viewer)) {
            $this->Session->setFlash(__d('contest', 'Access denied'), 'default', array('class' => 'error-message'));
            return $this->redirect('/pages/no-permission');
        }
        $photos = $this->ContestEntry->getPhotoSelect($uid);
        $this->set('photos', $photos);
        $this->set('contest', $contest);
    }
    public function select_videos($contest_id) {
        $this->_checkPermission(array('confirm' => true));
        $contest = $this->Contest->findById($contest_id);
        $uid = MooCore::getInstance()->getViewer(true);
        $viewer = MooCore::getInstance()->getViewer();
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        if (!$helper->canSubmitEntry($contest, $viewer)) {
            $this->Session->setFlash(__d('contest', 'Access denied'), 'default', array('class' => 'error-message'));
            return $this->redirect('/pages/no-permission');
        }
        $videos = $this->ContestEntry->getVideoSelect($uid);
        $this->set('videos', $videos);
        $this->set('contest', $contest);
    }

    public function entry_upload() {
        $this->autoRender = false;
        $this->_checkPermission();
        $uid = $this->Auth->user('id');
        $data = $this->request->data;
        //var_dump($data);
        $contest = $this->Contest->findById($data['contest_id']);
        $data['user_id'] = $uid;
        // check case videos
        if(isset($data['source']) 
                && in_array($data['source'], array('youtube','vimeo'))) {
            $n_path = 'uploads' . DS . 'tmp' . DS . 'imgtmp.jpg';
            $newfile = WWW_ROOT . $n_path;
            copy($data['thumbnail'], $newfile);
            $data['thumbnail'] = $n_path;
        }
        // check case select photos
        if (isset($data['select_photo']) && !empty($data['select_photo'])) {
            $n_path = 'uploads' . DS . 'tmp' . DS . $data['thumbnail_name'];
            $c_file = WWW_ROOT . $data['thumbnail'];
            $newfile = WWW_ROOT . $n_path;
            copy($c_file, $newfile);
            $data['thumbnail'] = $n_path;
        }
        // case select video
        if(isset($data['select_video']) && !empty($data['select_video'])) {
            $n_path = 'uploads' . DS . 'tmp' . DS . $data['thumbnail_name'];
            $c_file = $data['thumbnail'];
            $newfile = WWW_ROOT . $n_path;
            copy($c_file, $newfile);
            $data['thumbnail'] = $n_path;
            $item_id = $data['item_id'];
            $this->loadModel('Video.Video');
            $video = $this->Video->findById($item_id);
            if(!empty($video)) {
                if($video['Video']['pc_upload']) {
                    $data['source'] = 'upload';
                    $data['source_id'] = $video['Video']['id'];
                }else{
                    $data['source'] = $video['Video']['source'];
                    $data['source_id'] = $video['Video']['source_id'];
                }
            }
            
        }
        $this->ContestEntry->set($data);
        $this->_validateData($this->ContestEntry);
        if ($this->ContestEntry->save($data)) {
            if ($contest['Contest']['auto_approve']) {
                $this->ContestEntry->updateStatus($this->ContestEntry->id, 'published');
            }
            else {
                $this->ContestEntry->updateStatus($this->ContestEntry->id, 'pending');
            }
            $event = new CakeEvent('Plugin.Controller.Contest.afterSaveEntry', $this, array(
                'id' => $this->ContestEntry->id
            ));
            $this->getEventManager()->dispatch($event);
        //    $this->Session->setFlash(__d('contest', 'Entry has been submitted successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            $this->redirect($contest['Contest']['moo_url'] . '/tab:my-entries');
            
        }
    }
    public function entry_upload_onapp() {
        $this->autoRender = false;
        $this->_checkPermission();
        $uid = $this->Auth->user('id');
        $data = $this->request->data;
        //var_dump($data);
        $contest = $this->Contest->findById($data['contest_id']);
        $data['user_id'] = $uid;
        // check case videos
        if(isset($data['source']) 
                && in_array($data['source'], array('youtube','vimeo'))) {
            $n_path = 'uploads' . DS . 'tmp' . DS . 'imgtmp.jpg';
            $newfile = WWW_ROOT . $n_path;
            copy($data['thumbnail'], $newfile);
            $data['thumbnail'] = $n_path;
        }
        // check case select photos
        if (isset($data['select_photo']) && !empty($data['select_photo'])) {
            $n_path = 'uploads' . DS . 'tmp' . DS . $data['thumbnail_name'];
            $c_file = WWW_ROOT . $data['thumbnail'];
            $newfile = WWW_ROOT . $n_path;
            copy($c_file, $newfile);
            $data['thumbnail'] = $n_path;
        }
        // case select video
        if(isset($data['select_video']) && !empty($data['select_video'])) {
            $n_path = 'uploads' . DS . 'tmp' . DS . $data['thumbnail_name'];
            $c_file = $data['thumbnail'];
            $newfile = WWW_ROOT . $n_path;
            copy($c_file, $newfile);
            $data['thumbnail'] = $n_path;
            $item_id = $data['item_id'];
            $this->loadModel('Video.Video');
            $video = $this->Video->findById($item_id);
            if(!empty($video)) {
                if($video['Video']['pc_upload']) {
                    $data['source'] = 'upload';
                    $data['source_id'] = $video['Video']['id'];
                }else{
                    $data['source'] = $video['Video']['source'];
                    $data['source_id'] = $video['Video']['source_id'];
                }
            }
            
        }
        $this->ContestEntry->set($data);
        $this->_validateData($this->ContestEntry);
        if ($this->ContestEntry->save($data)) {
            if ($contest['Contest']['auto_approve']) {
                $this->ContestEntry->updateStatus($this->ContestEntry->id, 'published');
            }
            else {
                $this->ContestEntry->updateStatus($this->ContestEntry->id, 'pending');
            }
            $event = new CakeEvent('Plugin.Controller.Contest.afterSaveEntry', $this, array(
                'id' => $this->ContestEntry->id
            ));
            $this->getEventManager()->dispatch($event);
            $this->Session->setFlash(__d('contest', 'Entry has been submitted successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));    
            
        }
        
        $response['result'] = 1;
        $response['id'] = $contest['Contest']['id'];
        $response['url'] = '/contests/entry_upload_onapp_finish';
        echo json_encode($response);
        exit;
    }
    public function entry_upload_onapp_finish() { 

    }
    public function entry_upload_app() {
        $this->autoRender = false;
        $this->_checkPermission();
        $uid = $this->Auth->user('id');
        $data = $this->request->data;
        $contest = $this->Contest->findById($data['contest_id']);
        $data['user_id'] = $uid;
        // check case videos
        if(isset($data['source']) 
                && in_array($data['source'], array('youtube','vimeo'))) {
            $n_path = 'uploads' . DS . 'tmp' . DS . 'imgtmp.jpg';
            $newfile = WWW_ROOT . $n_path;
            copy($data['thumbnail'], $newfile);
            $data['thumbnail'] = $n_path;
        }
        // check case select photos
        if (isset($data['select_photo']) && !empty($data['select_photo'])) {
            $n_path = 'uploads' . DS . 'tmp' . DS . $data['thumbnail_name'];
            $c_file = WWW_ROOT . $data['thumbnail'];
            $newfile = WWW_ROOT . $n_path;
        //   var_dump($c_file, $newfile);
            copy($c_file, $newfile);
            $data['thumbnail'] = $n_path;
        }
        // case select video
        if(isset($data['select_video']) && !empty($data['select_video'])) {
            $n_path = 'uploads' . DS . 'tmp' . DS . $data['thumbnail_name'];
            $c_file = $data['thumbnail'];
            $newfile = WWW_ROOT . $n_path;
            copy($c_file, $newfile);
            $data['thumbnail'] = $n_path;
            $item_id = $data['item_id'];
            $this->loadModel('Video.Video');
            $video = $this->Video->findById($item_id);
            if(!empty($video)) {
                if($video['Video']['pc_upload']) {
                    $data['source'] = 'upload';
                    $data['source_id'] = $video['Video']['id'];
                }else{
                    $data['source'] = $video['Video']['source'];
                    $data['source_id'] = $video['Video']['source_id'];
                }
            }
            
        }
        $this->ContestEntry->set($data);
        $this->_validateData($this->ContestEntry);
        if ($this->ContestEntry->save($data)) {
            if ($contest['Contest']['auto_approve']) {
                $this->ContestEntry->updateStatus($this->ContestEntry->id, 'published');
            }
            else {
                $this->ContestEntry->updateStatus($this->ContestEntry->id, 'pending');
            }
            $event = new CakeEvent('Plugin.Controller.Contest.afterSaveEntry', $this, array(
                'id' => $this->ContestEntry->id
            ));
            $this->getEventManager()->dispatch($event);
            $this->Session->setFlash(__d('contest', 'Entry has been submitted successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));    
            $response['result'] = 1;
            $response['id'] = $contest['Contest']['id'];
            $response['url'] = $contest['Contest']['moo_href'] . '/tab:my-entries';
            echo json_encode($response);
            exit;
        }
    }
    public function edit_entry($id = null) {
        $entry = $this->ContestEntry->findById($id);
        $contest = $this->Contest->findById($entry['ContestEntry']['contest_id']);
        $viewer = MooCore::getInstance()->getViewer();
        if ($entry['User']['id'] != $viewer['User']['id']) {
            $this->Session->setFlash(__d('contest', 'Access denied'), 'default', array('class' => 'error-message'));
            return $this->redirect('/pages/no-permission');
        }
        $this->set('contest', $contest);
        $this->set('entry', $entry);
        if ($this->request->is('post')) { // handle form submission
            $data = $this->request->data;
            if (isset($data['caption_' . $entry['ContestEntry']['id']])) {
                // update caption
                $this->ContestEntry->id = $entry['ContestEntry']['id'];
                $this->ContestEntry->save(array('caption' => $data['caption_' . $entry['ContestEntry']['id']]));
            }
            return $this->redirect($entry['ContestEntry']['moo_url']);
        }
    }
    public function edit_entry_save() {
        $this->autoRender = false;
        $data = $this->request->data;
        $entry = $this->ContestEntry->findById($data['entry_id']);
        $contest = $this->Contest->findById($entry['ContestEntry']['contest_id']);
        if (isset($data['caption_' . $entry['ContestEntry']['id']])) {
            // update caption
            $this->ContestEntry->id = $entry['ContestEntry']['id'];
            $this->ContestEntry->save(array('caption' => $data['caption_' . $entry['ContestEntry']['id']]));
        }
        $response['result'] = 1;
        $response['id'] = $entry['ContestEntry']['id'];
        $response['url'] = $entry['ContestEntry']['moo_href'];
        echo json_encode($response);
        exit;
    }

    public function manage_entries() {
        $data = $this->request->data;
        if (!empty($data['entry_id_list'])) {
            $entry_ids = explode(',', $data['entry_id_list']);
            if (!empty($entry_ids)) {
                switch ($data['entry_action']) {
                    case 'approve':
                        foreach ($entry_ids as $id) {
                            $this->ContestEntry->updateStatus($id, 'published');
                        }
                        $this->Session->setFlash(__d('contest', 'Entries has been approved successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    case 'deny':
                        foreach ($entry_ids as $id) {
                            $this->ContestEntry->updateStatus($id, 'denied');
                        }
                        $this->Session->setFlash(__d('contest', 'Entries has been denied successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    case 'delete':
                        foreach ($entry_ids as $id) {
                            $this->ContestEntry->deleteEntry($id);
                        }
                        $this->Session->setFlash(__d('contest', 'Entries has been deleted successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    case 'win':
                        $this->ContestEntry->setWinEntries($entry_ids, $data['contest_id']);
                        $this->Session->setFlash(__d('contest', 'Entries has been set to win successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    default:
                        break;
                }
            }
        }
        if (!$this->isApp())
    	{
            return $this->redirect($this->referer());
        }
    }
    public function manage_entries_app() {
        $this->autoRender = false;
        $data = $this->request->data;
        if (!empty($data['entry_id_list'])) {
            $entry_ids = explode(',', $data['entry_id_list']);
            if (!empty($entry_ids)) {
                switch ($data['entry_action']) {
                    case 'approve':
                        foreach ($entry_ids as $id) {
                            $this->ContestEntry->updateStatus($id, 'published');
                        }
                        $this->Session->setFlash(__d('contest', 'Entries has been approved successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    case 'deny':
                        foreach ($entry_ids as $id) {
                            $this->ContestEntry->updateStatus($id, 'denied');
                        }
                        $this->Session->setFlash(__d('contest', 'Entries has been denied successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    case 'delete':
                        foreach ($entry_ids as $id) {
                            $this->ContestEntry->deleteEntry($id);
                        }
                        $this->Session->setFlash(__d('contest', 'Entries has been deleted successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    case 'win':
                        $this->ContestEntry->setWinEntries($entry_ids, $data['contest_id']);
                        $this->Session->setFlash(__d('contest', 'Entries has been set to win successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    default:
                        break;
                }
            }
        }
        $response['result'] = 1;
        $response['url'] = $this->referer();
        echo json_encode($response);
        exit;
    }

    public function entry($id) {
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $this->_checkExistence($entry);
        $contest = $this->Contest->findById($entry['ContestEntry']['contest_id']);
        $this->_checkExistence($contest);
        $this->_checkPermission(array('aco' => 'contest_view'));
        $this->_checkPermission( array('user_block' => $entry['ContestEntry']['user_id']) );
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $contest_allow_view = $helper->canView($contest);
        if (!$contest_allow_view) {
            return $this->_redirectError(__d('contest', 'You do not have permission to view this entry'), '/contests');
        }
        $uid = $this->Auth->user('id');
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($uid) &&  $uid != $entry['User']['id']) {
            $this->ContestEntry->updateAll(array('view_count' => $entry['ContestEntry']['view_count'] + 1), array('ContestEntry.id' => $id));
        }
        $event = new CakeEvent('Plugin.Controller.Contest.beforeViewEntry', $this, array('id' => $id, 'uid' => $uid, 'contest' => $contest, 'entry' => $entry));
        $this->getEventManager()->dispatch($event);
        MooCore::getInstance()->setSubject($entry);
        $this->loadModel('Like');
        $likes = $this->Like->getLikes($entry['ContestEntry']['id'], 'Contest_Contest_Entry');
        $this->set('likes', $likes);
        // set og:image
        if ($entry['ContestEntry']['thumbnail']) {
            $this->set('og_image', $helper->getEntryImage($entry, array('prefix' => '850')));
        }
        list($prev_entry, $next_entry) = $helper->getPrevNextId($entry);
        $og = array('type' => 'contest_entry');
        $this->set('og', $og);
        $this->set('prev_entry', $prev_entry);
        $this->set('next_entry', $next_entry);
        $this->set('contest', $contest);
        $this->set('entry', $entry);
        $this->set('viewer', $viewer);

        $this->set('title_for_layout', htmlspecialchars($entry['ContestEntry']['caption']));
        $description = $this->getDescriptionForMeta($entry['ContestEntry']['caption']);
        if ($description) {
            $this->set('description_for_layout', $description);
            $this->set('mooPageKeyword', $this->getKeywordsForMeta($description));
        }
        $this->set('admins', array($entry['Contest']['user_id'], $entry['ContestEntry']['user_id']));
    }

    public function vote($id) {
        $this->autoRender = false;
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $viewer = MooCore::getInstance()->getViewer();
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $this->_checkExistence($entry);
        $this->_checkPermission(array('aco' => 'contest_view'));
        if (!$helper->canVote($entry, $viewer)) {
            $this->Session->setFlash(__d('contest', 'Can not vote this entry'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
        }
        else {
            if (!empty($entry) && $viewer['User']['id']) {
                $this->ContestVote->vote($id, $viewer['User']['id']);
                $this->Session->setFlash(__d('contest', 'Voted Successfully.'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            }
            else {
                $this->Session->setFlash(__d('contest', 'Can not vote this entry'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
            }
       
        }
        if (!$this->isApp())
    	{
            return $this->redirect('/contests/entry/' . $entry['ContestEntry']['id']);
        }else{
            $response['result'] = 1;
            $response['href'] = $entry['ContestEntry']['moo_href'];
            echo json_encode($response);
            exit;
        }
    }

    public function un_vote($id) {
        $this->autoRender = false;
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $viewer = MooCore::getInstance()->getViewer();
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $this->_checkExistence($entry);
        $this->_checkPermission(array('aco' => 'contest_view'));
        if (!$helper->canVote($entry, $viewer)) {
            $this->Session->setFlash(__d('contest', 'Can not un-vote this entry'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
        }
        else {
            if (!empty($entry) && $viewer['User']['id']) {
                $this->ContestVote->un_vote($id, $viewer['User']['id']);
                $this->Session->setFlash(__d('contest', 'Un-voted Successfully.'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            }
            else {
                $this->Session->setFlash(__d('contest', 'Can not un-vote this entry'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
            }
        }
        if (!$this->isApp())
    	{
            return $this->redirect('/contests/entry/' . $entry['ContestEntry']['id']);
        }else{
            $response['result'] = 1;
            $response['href'] = $entry['ContestEntry']['moo_href'];
            echo json_encode($response);
            exit;
        }
    }

    public function ajax_vote($id) {
        $this->autoRender = false;
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $viewer = MooCore::getInstance()->getViewer();
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $this->_checkExistence($entry);
        $this->_checkPermission(array('aco' => 'contest_view'));
        if (!$helper->canVote($entry, $viewer)) {
            $response['result'] = 0;
            $response['message'] = __d('contest', 'Can not vote this entry');
            echo json_encode($response);
            exit;
        }
        else {
            if (!empty($entry) && $viewer['User']['id']) {
                $this->ContestVote->vote($id, $viewer['User']['id']);
                $response['result'] = 1;
                echo json_encode($response);
                exit;
            }
            else {
                $response['result'] = 0;
                $response['message'] = __d('contest', 'Can not vote this entry');
                echo json_encode($response);
                exit;
            }
        }
    }

    public function ajax_un_vote($id) {
        $this->autoRender = false;
        $id = intval($id);
        $entry = $this->ContestEntry->findById($id);
        $viewer = MooCore::getInstance()->getViewer();
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $this->_checkExistence($entry);
        $this->_checkPermission(array('aco' => 'contest_view'));
        if (!$helper->canVote($entry, $viewer)) {
            $response['result'] = 0;
            $response['message'] = __d('contest', 'Can not vote this entry');
            echo json_encode($response);
            exit;
        }
        else {
            if (!empty($entry) && $viewer['User']['id']) {
                $this->ContestVote->un_vote($id, $viewer['User']['id']);
                $response['result'] = 1;
                echo json_encode($response);
                exit;
            }
            else {
                $response['result'] = 0;
                $response['message'] = __d('contest', 'Can not vote this entry');
                echo json_encode($response);
                exit;
            }
        }
    }

    public function invite($contest_id) {
        $this->_checkPermission(array('confirm' => true));
        $this->set('contest_id', $contest_id);
    }

    public function ajax_doSend() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));
        $postData = $this->request->data;
        if (empty($postData['contest_id'])) {
            $response['result'] = 0;
            $response['message'] = __d('contest', 'Contest is required');
            echo json_encode($response);
        }
        elseif (empty($postData['emails'])) {
            $response['result'] = 0;
            $response['message'] = __d('contest', 'Emails is required');
            echo json_encode($response);
        }
        elseif (empty($postData['subject'])) {
            $response['result'] = 0;
            $response['message'] = __d('contest', 'Subject is required');
            echo json_encode($response);
        }
        elseif (empty($postData['message'])) {
            $response['result'] = 0;
            $response['message'] = __d('contest', 'Message is required');
            echo json_encode($response);
        }
        else {
            $tmp_emails = explode(',', $postData['emails']);
            if (!empty($tmp_emails)) {
                $emails = array();
                foreach ($tmp_emails as $tmp_email) {
                    if (!empty($tmp_email)) {
                        if (!filter_var(trim($tmp_email), FILTER_VALIDATE_EMAIL)) {
                            $response['result'] = 0;
                            $response['message'] = __d('contest', 'Emails is invalid');
                            echo json_encode($response);
                            exit();
                        }
                        else {
                            if (!$this->ContestCandidate->isCandidateEmail(trim($tmp_email))) {
                                $emails[] = trim($tmp_email);
                            }
                        }
                    }
                }
                if (!empty($emails)) {
                    $contest = $this->Contest->findById($postData['contest_id']);
                    $viewer = MooCore::getInstance()->getViewer();
                    $request = Router::getRequest();
                    $ssl_mode = Configure::read('core.ssl_mode');
                    $http = (!empty($ssl_mode)) ? 'https' : 'http';
                    $mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
                    $params = array(
                        'sender_name' => $viewer['User']['name'],
                        'sender_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $request->base . $viewer['User']['moo_href'],
                        'contest_name' => $contest['Contest']['moo_title'],
                        'contest_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $request->base . $contest['Contest']['moo_url'],
                        'subject' => $postData['subject'],
                        'message' => $postData['message'],
                    );
                    $i = 1;
                    foreach ($emails as $email) {
                        if ($i <= 10) {
                            if (Validation::email(trim($email))) {
                                $mailComponent->send(trim($email), 'contest_invite_email', $params);
                            }
                        }
                        $i++;
                    }
                }

                $response['result'] = 1;
                echo json_encode($response);
            }
            else {
                $response['result'] = 0;
                $response['message'] = __d('contest', 'Emails is required');
                echo json_encode($response);
            }
        }
    }

    public function ajax_show_voted($id = null) {
        $id = intval($id);
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $limit = Configure::read('Contest.contest_item_per_pages');
        $users = $this->ContestVote->getVotes($id, $limit, $page);
        $user_count = $this->ContestVote->getVoteCount($id);
        $this->set('users', $users);
        $this->set('user_count', $user_count);
        $this->set('page', $page);
        $this->set('more_url', '/contests/ajax_show_voted/' . $id . '/page:' . ( $page + 1 ));
        $this->render('/Elements/ajax/user_votes');
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

    protected function _checkExistence($item = null) {
        if (empty($item)) {
            $this->_showError(__d('contest', 'Contest does not exist'));
            return;
        }
    }

    public function _checkContestPrivacy($privacy, $owner, $arefriends) {
        return parent::_checkPrivacy($privacy, $owner, $arefriends);
    }

    public function _checkAllowEdit($contest, $viewer) {
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $contest_allow_edit = $helper->canEdit($contest, $viewer);
        if (!$contest_allow_edit) {
            return $this->_redirectError(__d('contest', 'Can not edit published or closed contest'), '/contests');
        }
    }

    public function fetch() {
        $this->_checkPermission(array('confirm' => true));      
        $this->loadModel('Video.Video');
        $video = $this->Video->fetchVideo($this->request->data['source'], $this->request->data['url']);
        $contest_id = $this->request->data['contest_id'];
        if (!empty($video)) {
            $this->set('video', $video);
            $this->set('contest_id', $contest_id);
            $this->render('/Elements/ajax/aj_fetch');
        }
        else {
            $this->autoRender = false;
            echo '<span style="color:red">' . __d('contest', 'Invalid URL. Please try again') . '</span>';
        }
    }

    public function aj_validate() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));
        $this->loadModel('Video.Video');
        $video = $this->Video->fetchVideo($this->request->data['source'], $this->request->data['url']);
        if (isset($video['errorMsg']) && $video['errorMsg']) {
            echo json_encode(array('error' => '<span style="color:red">' . $video['errorMsg'] . '</span>'));
        }
        if (empty($video)) {
            echo json_encode(array('error' => '<span style="color:red">' . __d('contest', 'Invalid URL. Please try again') . '</span>'));
        }
    }
    public function categories_list(){
        if ($this->request->is('requested')){
            $this->loadModel('Category');
            $role_id = $this->_getUserRoleId();
            $categories = $this->Category->getCategories('Contest', $role_id);
            return $categories;
        }
    }
}
