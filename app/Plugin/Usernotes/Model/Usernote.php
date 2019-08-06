<?php

App::uses('UsernotesAppModel', 'Usernotes.Model');

class Usernote extends UsernotesAppModel {

//    public $hasOne = array(
//        'User'=>array(
//            'className'=>'User',
//            'foreignKey'=>false,
//            'conditions'=>array('User.id = Usernote.user_id')
//        )
//    );
     public $validationDomain = 'usernotes';
    public $validate = array(
        'content' => array(
            'rule' => 'notBlank',
            'message' => 'Note can not empty'
        )
     );
 
    public function loadUsernotesPaging($obj, $keyword = '') {
        $muser = MooCore::getInstance()->getModel('User');
        $muser->bindModel(array(
            'hasOne' => array(
                'Usernote' => array(
                    'className' => 'Usernote',
                    'foreignKey' => false,
                    'conditions' => array('User.id = Usernote.target_id')
                )
            )
        ));
        $user_id = AuthComponent::user('id');
        $conds = array();
        if (!empty($keyword)) {
            $keyword = str_replace("'", "\'", $keyword);
            $conds[] = "(User.name LIKE '%$keyword%' OR User.email LIKE '%$keyword%')";
        }
        $conds[] = "User.id != $user_id";

        $muser->recursive = 0;
        $obj->Paginator->settings = array(
            'conditions' => $conds,
            'order' => array('User.id' => 'DESC'),
            'limit' => 10        );
        return $obj->paginate('User');
    }

    public function loadNoteByUserIDAndTargetID($user_id, $target_id) {
        $conds = array('Usernote.user_id' => $user_id, 'target_id' => $target_id);
        return $this->find('first', array('conditions' => $conds, 'recursive' => -1));
    }

    public function saveNote($data) {
        return $this->save($data);
    }

    public function checkNoteExist($user_id, $target_id) {
        $conds = array(
            'user_id' => $user_id,
            'target_id' => $target_id
        );
        $note = $this->find('first', array('conditions' => $conds));
        if ($note) {
            return $note['Usernote']['id'];
        }
        return false;
    }

    public function deleteNote($note_id) {
        $this->delete($note_id);
    }

    public function getNoteDetail($user_id, $target_id) {
        $conds = array(
            'user_id' => $user_id,
            'target_id' => $target_id
        );
        return $this->find('first', array('conditions' => $conds));
    }

    public function getUsers($page = 1, $conditions = null, $limit = UNOTE_LIMIT_NOTES,$keywords="") {
        $mUser = MooCore::getInstance()->getModel('User');
        $conditions = array();
        if(!empty($keywords)){
            $conditions = array(
                 'OR' => array(
            'User.name LIKE' => "%".$keywords."%",
            'Usernote.content LIKE' => "%".$keywords."%"
        )
            );
        }
        
        $u_info = MooCore::getInstance()->getViewer();
        $u_id = $u_info['User']['id'];
        $mUser->bindModel(array(
            'hasOne' => array(
                'Usernote' => array(
                    'type' => 'INNER',
                    'className' => 'Usernote',
                    'foreignKey' => 'target_id',
                    'conditions' => array('Usernote.user_id=' . $u_id)
                )
            )
        ));
        $users = $mUser->find('all', array('conditions' => $conditions,
            'limit' => $limit,
            'page' => $page,
            'order' => 'Usernote.updated_date desc',
        ));
        return $users;
    }

    public function getUserFriends($uid, $page = 1, $limit=UNOTE_LIMIT_NOTES) {
        $mFriend = MooCore::getInstance()->getModel('Friend');
        $mFriend->unbindModel(
                array('belongsTo' => array('User'))
        );

        $mFriend->bindModel(
                array('belongsTo' => array(
                        'User' => array(
                            'className' => 'User',
                            'foreignKey' => 'friend_id',
                        )
                    ),
                    'hasOne'=>array(
                        'Usernote'=>array(
                            'className'=>'Usernote',
                            'type'=>'INNER',
                            'foreignKey'=>false,
                            'conditions'=>array('User.id = Usernote.target_id')
                        )
                    )
                )
        );
        $userBlockModal = MooCore::getInstance()->getModel('UserBlock');
        $blockedUsers = $userBlockModal->getBlockedUsers();
        $friends = $mFriend->find('all', array(
            'conditions' => array('Friend.user_id'=>$uid,'Usernote.user_id'=>$uid,'User.active' => 1, 'NOT' => array('Friend.friend_id' => $blockedUsers)
            ),
            'order' => 'Usernote.updated_date desc',
            'limit' => $limit,
            'page' => $page)
        );	
        $dbo = $this->getDatasource();
	return $friends;

    }
    public function noteCheckUserRoles($aco) {
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer) && $viewer['Role']['is_admin']) {
            return true;
        }
        $params = $this->_getUserRoleParams();
        if (in_array($aco, $params)) {
            return true;
        }
        return false;
    }
    
    public function countAllNoteByUser($user_id){
        return $this->find('count',array('conditions'=>array('Usernote.user_id'=>$user_id)));
    }
    public function _getUser() {
     
        $uid = AuthComponent::user('id');
        $cuser = array();
        if (!empty($uid)) { // logged in users
            $userModal =  MooCore::getInstance()->getModel('User');
            $user = $userModal->findById($uid);

            $cuser = $user['User'];
            $cuser['Role'] = $user['Role'];
        }

        return $cuser;
    }

    public function _getUserRoleParams() {
        $cuser = $this->_getUser();

        if (!empty($cuser)) {
            $params = explode(',', $cuser['Role']['params']);
        } else {
            $params = Cache::read('guest_role');

            if (empty($params)) {
                $this->loadModel('Role');
                $guest_role = $this->Role->findById(ROLE_GUEST);
                $params = explode(',', $guest_role['Role']['params']);
            }
        }

        return $params;
    }

}
