<?php

class ChatApiIntegrationController extends ChatAppController
{
    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Chat.ChatRoom');
        $this->loadModel('Chat.ChatMessage');
        $this->loadModel('Chat.ChatStatusMessages');
        $this->loadModel('Chat.ChatRoomsMember');
        $this->loadModel('User');
    }
    public function api_me(){
        $cuser = $this->_getUser();
        $this->set(array(
            'count_notification' => isset($cuser['notification_count']) ? $cuser['notification_count'] : "0",
            'count_conversation' => isset($cuser['chat_count']) ? $cuser['chat_count'] : "0",
            '_serialize' => array('count_notification', 'count_conversation')
        ));
    }
    private function _getLatestMessageId($uId,$page=0,$limit=RESULTS_LIMIT){
        $roomIdS = $this->ChatRoomsMember->find("all",array(
            'fields' => array('ChatRoomsMember.room_id'),
            'conditions'=>array("ChatRoomsMember.user_id"=>$uId),
        ));

        $messageIdS = $this->ChatRoom->find("all",array(
            'fields' => array('ChatRoom.latest_mesasge_id'),
            'conditions'=>array("ChatRoom.id"=>Hash::extract($roomIdS,'{n}.ChatRoomsMember.room_id')),
            'page'=>$page,
            'order' => array('ChatRoom.latest_mesasge_id DESC'),
            'limit' => $limit,
        ));
        return Hash::extract($messageIdS,'{n}.ChatRoom.latest_mesasge_id');
    }
    public function show() {

        $this->_checkPermission();

        $condRoomIds = array();
        $viewerId = $this->Auth->user('id');
        $this->ChatStatusMessages->query('SET NAMES utf8mb4');
        /*
        $messageIdS = $this->ChatStatusMessages->find("all",array(
            'fields' => array('MAX(ChatStatusMessages.message_id) AS message_id'),
            'conditions'=>array("ChatStatusMessages.user_id"=>$viewerId),
            'group'=>'ChatStatusMessages.room_id',
            'limit' => 50,
        ));
        $condMessageIds = Hash::extract($messageIdS,'{n}.{n}.message_id');
        */
        $condMessageIds = $this->_getLatestMessageId($viewerId,0,1000);
        $messageStatusS = $this->ChatStatusMessages->find("all",array(
            'fields' => array(' ChatStatusMessages.message_id,ChatStatusMessages.unseen'),
            'conditions'=>array("ChatStatusMessages.message_id"=>$condMessageIds,"ChatStatusMessages.user_id"=>$viewerId),
        ));
        $messageStatusS = Hash::combine($messageStatusS,'{n}.ChatStatusMessages.message_id','{n}.ChatStatusMessages.unseen');

        $messages = $this->ChatMessage->find("all",array(
            'conditions'=>array("ChatMessage.id"=>$condMessageIds),
            'order'=> array('ChatMessage.id DESC'),
        ));
        $condRoomIds = Hash::extract($messages,'{n}.ChatMessage.room_id');
        $rooms = $this->ChatRoom->find("all",array(
            'conditions'=>array("ChatRoom.id"=>$condRoomIds)
        ));

        $users = $this->User->find('all', array(
            'conditions' => array('User.id' => Hash::extract($rooms,'{n}.ChatRoomsMember.{n}.user_id'))
        ));
        $users = Hash::combine($users, '{n}.User.id', '{n}.User');
        //var_dump($messages,$rooms);die();

        $this->set("status",$messageStatusS);
        $this->set("rooms",Hash::combine($rooms,'{n}.ChatRoom.id','{n}'));
        $this->set("users",$users);
        $this->set('viewerId',$viewerId);

        $conversations = $messages;
        $filter = "first";
        if ((!empty($this->request->query['filter']) || !empty($this->request->data['filter']))) {
            $filter = !empty($this->request->query['filter'])?$this->request->query['filter']:$this->request->data['filter'];
        }
        switch ($filter) {
            case "first":
                $this->set('notifications', array_slice($conversations, 0, 10));
                break;
            case "more":
                $this->set('notifications', array_slice($conversations, 9, count($conversations) - 1));
                break;
            case "all":
                $this->set('conversations', $conversations);
                break;
            default:

        }
    }
    private function _api_validate(){
        $this->OAuth2 = $this->Components->load('OAuth2');
        $this->OAuth2->verifyResourceRequest();

    }
    private function _create_chat_token(){
        $cuser = $this->_getUser();
        $chatToken = uniqid('moo_');
        $Model = MooCore::getInstance()->getModel('MooToken');
        $Model->create();
        $Model->save(array(
            'user_id' => $cuser['id'],
            'session_id' => $this->Session->id(),
            'token' => $chatToken,
        ));
        return $chatToken;
    }
    public function token(){
        $this->_api_validate();
        $this->set(array(
            'token' => $this->_create_chat_token(),
            '_serialize' => array('token')
        ));
    }
    public function config(){
        $this->_api_validate();
        // Hacking for passing the mooTokens validate
        $this->Session->write('mooTokens',$this->_create_chat_token());
        $this->viewClass="Moo";
        header('Content-Type: application/json');
        $response = $this->_getViewObject()->fetch("config",'',true);
        echo json_encode($response);
        die();
    }
}