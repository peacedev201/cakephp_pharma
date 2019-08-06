<?php

class UsernotessController extends UsernotesAppController {

    public $components = array('Paginator');

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->loadModel('Usernotes.Usernote');
    }

//    public function admin_index() {
//        
//        $keyword = isset($this->request->data['keyword']) ? $this->request->data['keyword'] : null;
//        $aUsers = $this->Usernote->loadUsernotesPaging($this, $keyword);
//
//        $this->set(array(
//            'aUsers' => $aUsers,
//            'keyword' => $keyword,
//            'title_for_layout' => __d('usernotes', 'User notes')
//        ));
//    }

    public function index($type = 'home') {
        $this->_checkPermission(array('aco'=>ROLE_UNOTES_CAN_WRITE_NOTE));
        $uid = $this->Auth->user('id');
        
        if(!$uid || !Configure::read('Usernotes.usernotes_enabled')){
            $this->redirect('/pages/no-permission');
        }
        $data = '';
        if ($this->request->is('post')) {
            $data = $this->request->data;
        }



        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        switch ($type) {
            case 'home':
            default:
                $users =  $this->Usernote->getUsers($page, null, UNOTE_LIMIT_NOTES);
                $more_users =  $this->Usernote->getUsers($page + 1, null, UNOTE_LIMIT_NOTES);
                if (!empty($more_users))
                    $more_result = 1;
        }
        $this->set(compact( 'users', 'more_result'));
        
        $this->set('title_for_layout', '');
    }

    public function friends($type = 'home'){

        $this->_checkPermission(array('aco'=>ROLE_UNOTES_CAN_WRITE_NOTE));
        $uid = $this->Auth->user('id');
        
        if(!$uid || !Configure::read('Usernotes.usernotes_enabled')){
            $this->redirect('/pages/no-permission');
        }
        $data = '';
        if ($this->request->is('post')) {
            $data = $this->request->data;
        }



        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        switch ($type) {
            case 'home':
            default:
                $users = $this->Usernote->getUserFriends($uid, $page);
                $more_users = $this->Usernote->getUserFriends($uid, $page + 1);
                if (!empty($more_users))
                    $more_result = 1;
        }
        $this->set(compact( 'users', 'more_result'));
        
        $this->set('title_for_layout', '');
    }

    public function ajax_leave_note() {
        $this->autoRender = false;
        $target_id = $this->request->query['target_id'];
        $viewer = MooCore::getInstance()->getViewer();
        if (!$viewer) {
            return false;
        }
        $user_id = $viewer['User']['id'];
        $note = $this->Usernote->loadNoteByUserIDAndTargetID($user_id, $target_id);

        $content = '';
        $note_id = '';
        if ($note) {
            $content = $note['Usernote']['content'];
            $note_id = $note['Usernote']['id'];
        }
        $this->set(compact('content', 'target_id', 'note_id'));
        $this->render('Usernotes.Elements/note');
    }

    public function admin_delete_note($id = '') {
        $this->autoRender = false;
        if ($this->request->data) {
            $data = $this->request->data;
            if (!empty($data['cid'])) {
                foreach ($data['cid'] as $note_id) {
                    if ($note_id) {
                        $this->Usernote->deleteNote($note_id);
                    }
                }
            }
        }
        $this->_redirectSuccess(__d('usernotes', 'Delete successfully'), '/admin/usernotes/usernotess');
    }

    public function admin_save_note() {
        $this->autoRender = false;
        if ($this->request->data) {
            $viewer = MooCore::getInstance()->getViewer();
            if (!$viewer) {
                return false;
            }
            $user_id = $viewer['User']['id'];
            $data = $this->request->data;
            $this->Usernote->set($data);
            $this->_validateData($this->Usernote);
            $target_id = $data['target_id'];
            $note_id = $this->Usernote->checkNoteExist($user_id, $target_id);
            if (!$note_id) {
                $data['created_date'] = date('Y-m-d H:i:s');
            } else {
                $data['id'] = $note_id;
            }
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['user_id'] = $user_id;
            $this->Usernote->save($data);
            $this->_jsonSuccess(__d('usernotes', 'Leaved note successfully'), true);
        }
    }

    public function save_note() {
        $this->autoRender = false;
        $viewer = MooCore::getInstance()->getViewer();
        if ($this->request->is('ajax') && $viewer && Configure::read('Usernotes.usernotes_enabled')) {
            $data = $this->request->data;
            $this->Usernote->set($data);
            $this->_validateData($this->Usernote);
            $target_id = $data['target_id'];
            $user_id = $viewer['User']['id'];
            $note_id = $this->Usernote->checkNoteExist($user_id, $target_id);
            if (!$note_id) {
                $data['created_date'] = date('Y-m-d H:i:s');
            } else {
                $data['id'] = $note_id;
            }
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['user_id'] = $user_id;
            $data['result'] = true;
            $data['content'] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $data['content']);
            $this->Usernote->save($data);
            $this->_jsonSuccess("", false, $data);
        }
    }

    public function browser($type = 'all') {
        $this->_checkPermission(array('aco'=>ROLE_UNOTES_CAN_WRITE_NOTE));
        $uid = $this->Auth->user('id');
        
        if(!$uid || !Configure::read('Usernotes.usernotes_enabled')){
            $this->redirect('/pages/no-permission');
        }
        $data = '';
        if ($this->request->is('post')) {
            $data = $this->request->data;
        }

        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        switch ($type) {
            case 'home':
            default:
                $users =  $this->Usernote->getUsers($page, null, UNOTE_LIMIT_NOTES);
                $more_users =  $this->Usernote->getUsers($page + 1, null, UNOTE_LIMIT_NOTES);
                if (!empty($more_users))
                    $more_result = 1;
        }
        $this->set(compact( 'users', 'more_result'));
        
        $this->set('title_for_layout', __d('usernotes','My Notes'));
    }

    public function ajax_browse($type = null, $param = null) {
        $this->autoLayout = false;
        $uid = $this->Auth->user('id');
        $this->loadModel('Friend');
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $users = array();
        $more_result = 0;
        $keywords = "";
        switch ($type) {
            case 'home':
            case 'friends': 
                $this->_checkPermission();
                $users = $this->Usernote->getUserFriends($uid, $page);
                $more_users = $this->Usernote->getUserFriends($uid, $page + 1);
                if (!empty($more_users))
                    $more_result = 1;
                break;
            case 'all':
                  $users =  $this->Usernote->getUsers($page, null, UNOTE_LIMIT_NOTES);
                  $more_users =  $this->Usernote->getUsers($page + 1, null, UNOTE_LIMIT_NOTES);
                if (!empty($more_users))
                    $more_result = 1;
                break;
            default: // search
               if($page == 1){
                    $keywords = $this->request->data['name'];
               }elseif($page > 1){
                   $keywords = $this->request->named['name'];
               }
                $users =  $this->Usernote->getUsers($page, null, UNOTE_LIMIT_NOTES,$keywords);
                $more_users =  $this->Usernote->getUsers($page + 1, null, UNOTE_LIMIT_NOTES,$keywords);
                if (!empty($more_users))
                    $more_result = 1;
        }
        // get current user friends and requests
        $this->set('users', $users);
        $this->set('more_result', $more_result);
        $this->set('type', $type);
        $this->set('more_url', '/usernotess/ajax_browse/' . h($type) . '/page:' . ( $page + 1 ).'/name:'.$keywords);
        $this->render('Usernotes.Elements/lists/users_list');
    }
    
    public function ajax_remove_note(){
        $this->autoRender = false;
        $user_id = AuthComponent::user('id');
        if($this->request->is('ajax') && $user_id){
            $target_id = $this->request->data('target_id');
            if($target_id){
                $note_id = $this->Usernote->checkNoteExist($user_id, $target_id);
                if($note_id){
                    $this->Usernote->delete($note_id);
                }
            }
        }
    }

    public function remove_note($target_id = null, $action = null){
        // $this->autoRender = false;
        $user_id = AuthComponent::user('id');
        if($user_id && $target_id){
            $note_id = $this->Usernote->checkNoteExist($user_id, $target_id);
            if($note_id){
                $this->Usernote->delete($note_id);
            }
        }

        $this->Session->setFlash(__('Delete successfully.'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));

        // if($action == 'index'){
        //     $this->redirect('/usernotess');
        // }else{
        //     $this->redirect('/usernotess/'.$action);
        // }
    }

    public function profile_user_add_note($uid){
        $this->loadModel('Usernotes.Usernote');
        $this->loadModel('User');
        $is_enable = false;
        $note_content = "";
        $role_can_write_note = $this->Usernote->noteCheckUserRoles(ROLE_UNOTES_CAN_WRITE_NOTE);
        if ($uid && Configure::read('Usernotes.usernotes_enabled') && $role_can_write_note) {
            $is_enable = true;
            $data = $this->params;
            $user_id = $this->Auth->user('id');

            $target_id = $uid;

            $note = $this->Usernote->getNoteDetail($user_id,$target_id);

            $note_id = 0;
            if($note){
                $note_content = $note['Usernote']['content'];
                $note_id = $note['Usernote']['id'];
            }
            if($target_id == $user_id){
                $is_enable = false;
            }
            $this->set('note_id', $note_id);
            $this->set('target_id', $target_id);
            $this->set('note_content', $note_content);
            $this->set('note_title', $data['title']);
        }
      
        $this->set('is_enable', $is_enable);
    }

}
