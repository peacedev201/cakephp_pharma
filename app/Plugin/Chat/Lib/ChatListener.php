<?php
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('CakeEventListener', 'Event');

class ChatListener implements CakeEventListener
{

    public function implementedEvents()
    {
        return array(
            'MooView.afterLoadMooCore' => 'afterLoadMooCore',
            'MooView.beforeRenderRequreJsConfig' => 'beforeRenderRequreJsConfig',
            //'Auth.afterIdentify' => 'Auth_afterIdentify',
            'MooView.beforeMooConfigJSRender' => 'mooView_beforeMooConfigJSRender',
            'Controller.User.afterLogout' => 'doControllerUserAfterLogout',
            'NotificationsController.refresh' => 'notificationRefresh',
            'AppController.doViewerProcess' => 'doViewerProcess',
            'MooPoupHelper.tag' => 'tagPopuHelperIntegration',
            'Model.Friend.afterSave' => 'afterSaveFriendModel',
            'Model.Friend.beforeDelete' => 'beforeDeleteFriendModel',
            'Model.UserBlock.afterSave' => 'afterSaveUserBlockModel',
            'Model.UserBlock.beforeDelete' => 'beforeDeleteUserBlockModel',
            'Controller.User.afterEdit' => 'afterEditUserProfile',
            //'User.beforeMemberLogin' => 'beforeMemberLogin'
            'AppController.doBeforeFilter' => 'doBeforeFilterAppController',
            'MooView.beforeMooAppOptimizedCssRender' => 'beforeMooAppOptimizedCssRender',
            'MooView.beforeMooAppOptimizedScriptRender' => 'beforeMooAppOptimizedScriptRender',
        );
    }

    public function afterLoadMooCore($event)
    {

        $v = $event->subject();

        $arrayCss = array();
        if ($this->isMobile()) { // For dev it will has ! , the correct is not !
            array_push($arrayCss,'Chat.chat-mobile.css');
        } else {
            array_push($arrayCss,'Chat.chat.css');
        }
        if ($this->isApp($v)) {
            array_push($arrayCss,'Chat.chat-app.css');
        }
        $v->Helpers->Html->css($arrayCss, array('block' => 'css'));
    }

    public function beforeRenderRequreJsConfig($event)
    {
        if (!$this->checkAllowChat($event)) {
            return;
        }
        $v = $event->subject();
        //check subscription
        if ($v->request->params['plugin'] == 'subscription') {
            $helper = MooCore::getInstance()->getHelper('Subscription_Subscription');
            if ($helper->checkEnableSubscription()) {
                $cuser = MooCore::getInstance()->getViewer();

                if ($cuser) {
                    $tmp = $cuser['User'];
                    $tmp['Role'] = $cuser['Role'];

                    $cuser = $tmp;
                }

                $subscribe = $helper->getSubscribeActive($cuser, false);

                if (!$subscribe) {
                    return;
                }
                if ($subscribe['Subscribe']['status'] == 'process') {
                    return;
                }
            }
        }

        if (Configure::read('debug') == 0) {
            $min = "min.";
        } else {
            $min = "";
        }

        $chatUri = $v->Helpers->MooRequirejs->assetUrlJS("/chat/js/client/mooChat.js");
        $chatMobiUri = $v->Helpers->MooRequirejs->assetUrlJS("/chat/js/client/mooChat-mobile.js");
        // Hacking for debug
        //$chatUri = $v->request->base .'/chat/js/client/mooChat';
        //$chatMobiUri = $v->request->base ."/chat/js/client/mooChat-mobile";
        // End hacking
        $isEnableGzip = false;
        if (is_null(Configure::read('Storage.storage_current_type'))) {
            $isEnableGzip = true; // Support moo 2.5.0
        }
        if (Configure::read('debug') == 0 && Configure::read('Storage.storage_current_type') == "local") {
            $isEnableGzip = true;
        }
        if ($isEnableGzip) {
            $chatUri = $v->request->base . "/chats/gzip/moochat?desktop";
            $chatMobiUri = $v->request->base . "/chats/gzip/moochat-mobi?mobi";
        }
        if ($this->isMobile()) {
            $chatJS = $chatMobiUri;
        } else {
            $chatJS = $chatUri;
        }
        $v->Helpers->MooRequirejs->addPath(array(
            "mooChat" => $chatJS,
            "chat" => $v->Helpers->MooRequirejs->assetUrl('/chat'),
            "webChat" => $v->Helpers->MooRequirejs->assetUrlJS("Chat.js/webChat.{$min}js")
        ));

        $v->Helpers->MooRequirejs->addShim(array(
            "mooAjax" => array("deps" => array("mooChat")),
        ));


    }

    //public function beforeMemberLogin($event){
    public function doBeforeFilterAppController($event)
    {
        $sub = $event->subject();
        if ($sub->isAllowedPermissions("chat_allow_chat")) {
            if (!$sub->Session->check("enable_mooTokens")) {
                $sub->Session->write('enable_mooTokens', true);
            }
        }

    }

    /*
    public function Auth_afterIdentify($event)
    {
        $user = $event->data['user'];
        $sub = $event->subject();
        //$chatModel = MooCore::getInstance()->getModel('Chat.ChatToken');
        $chatModel = MooCore::getInstance()->getModel('MooToken');
        $chatToken = uniqid('chat_');
        if ($sub->_Collection->getController()->isAllowedPermissions("chat_allow_chat")) {

            if (!$sub->Session->check("mooTokens")){
                $sub->Session->write('mooTokens', $chatToken);
                $chatModel->create();
                $chatModel->save(array(
                    'user_id' => $user['id'],
                    'session_id' => $sub->Session->id(),
                    'token' => $chatToken,
                ));
            }

        }


    }
    */

    public function isApp($v)
    {
        return $v->request->is('androidApp') || $v->request->is('iosApp');
    }

    public function getAccessToken($v)
    {
        $access_token = null;
        if ($v->request->header('moo-access-token') !== false) {
            $access_token = $v->request->header('moo-access-token');
        }
        if ($v->request->is('post') || $v->request->is('put')) {
            if (!empty($v->request->data['access_token'])) {
                $access_token = $v->request->data['access_token'];
            }
        }

        if (is_null($access_token)) {
            $access_token = $v->request->query('access_token');
        }
        if (is_array($access_token) && isset($access_token["access_token"]) ) return $access_token["access_token"];
        return $access_token;
    }

    public function mooView_beforeMooConfigJSRender($event)
    {
        $v = $event->subject();
        if (!$this->checkAllowChat($event)) {
            return;
        }
        $url = array(
            'server' => Configure::read('Chat.chat_chat_server_url'),
            'web' => $v->request->base,
            'storage' => $v->request->base,
        );
        if (!is_null(Configure::read('Storage.storage_current_type'))) {
            if (Configure::read('Storage.storage_current_type') == 'amazon') {
                $url['storage'] = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap")->getAwsBaseURl();
                if (Configure::read('Storage.storage_amazon_use_cname') == '1') {
                    $url['storage'] = Configure::read('Storage.storage_amazon_url_cname');
                }
                if (Configure::read('Storage.storage_cloudfront_enable') == "1") {
                    $url['storage'] = rtrim(Configure::read('Storage.storage_cloudfront_cdn_mapping'), "/");
                }
            }
        }
        
        $language = Configure::read('Config.language');
        $language_rtl = MooCore::getInstance()->getModel('Language')->getRtlOption();
        $site_rtl = '';
        if (!empty($language_rtl)) {
            foreach ($language_rtl as $rtl) {
                if ($rtl['Language']['key'] == $language) {
                    $site_rtl = $rtl['Language']['rtl']; 
                }
            }
        }

        $chat_setting = array(
            'disable' => Configure::read('Chat.chat_disable'),
            'url' => $url,
            'hide_offline' => Configure::read('Chat.chat_hide_offline_users_in_who_online'),
            'open_chatbox_when_a_new_message_arrives' => Configure::read('Chat.chat_open_chatbox_when_a_new_message_arrives'),
            'chat_beep_on_arrival_of_all_messages' => Configure::read('Chat.chat_beep_on_arrival_of_all_messages'),
            //'hidden_in_mobile' => Configure::read('Chat.chat_hidden_in_mobile'),
            'load_bar_in_offline_mode_for_all_first_time_users' => Configure::read('Chat.chat_load_bar_in_offline_mode_for_all_first_time_users'),
            'number_of_messages_fetched_when_load_earlier_messeges' => Configure::read('Chat.chat_number_of_messages_fetched_when_load_earlier_messeges'),
            'number_of_messages_fetched_window_first_time' => Configure::read('Chat.number_of_messages_fetched_window_first_time'),
            'send_message_to_non_friend' => Configure::read('core.send_message_to_non_friend'),
            'chat_turn_on_notification' => Configure::read('Chat.chat_turn_on_notification'),
            'chat_waiting_video_call_time_out' => Configure::read('Chat.chat_waiting_video_call_time_out'),
            'isRTL' => $site_rtl,
            'isApp' => $this->isApp($v) ? true : false ,
            'isAndroid' => $v->request->is('androidApp') ? true : false ,
            'isIOS' => $v->request->is('iosApp') ? true : false ,
        );
        /*
        if (Configure::read('Chat.chat_hidden_in_mobile') == '1') {
            if ($this->isMobile()) {
                $chat_setting["disable"] = "1";
            }
        }*/

        // Hacking for native app
        //if (is_null($token) && $this->isApp($v)){
        if ($this->isApp($v)) {
            $access_token = $this->getAccessToken($v);
            $token = "app_" . $access_token;
            $chat_setting['access_token'] = $access_token;
        }else{
            $token = $v->Helpers->Session->read('mooTokens');
        }
        if (!is_null($token)) {
            $chat_setting['token'] = $token;
            $settings = $v->get("ChatUserSettings");
            if (isset($settings["ChatUsersSetting"])) {
                unset($settings["ChatUsersSetting"]['id'], $settings["ChatUsersSetting"]["user_id"]);
                $chat_setting['settings'] = $settings["ChatUsersSetting"];
            }
            $chat_setting['permissions'] = $v->get("ChatUserPermissions");
            // Fixing for 2.4.1
            if (isset($event->data['mooConfig'])) {
                $config = $event->data['mooConfig'];
            } else {
                $config = $v->mooConfig;
            }

            if (!isset($config['language'])) {
                $config['language'] = Configure::read('Config.language');

            }
            if (!isset($config['language_2letter'])) {
                App::uses('MooLangISOConvert', 'Lib');
                $config['language_2letter'] = MooLangISOConvert::getInstance()->lang_iso639_2t_to_1(Configure::read('Config.language'));

            }

            // Fixing for sharing action

            if (isset($v->params['controller']) && isset($v->params['action'])) {
                if ($v->params['controller'] == 'share' && $v->params['action'] == 'ajax_share') {
                    $chat_setting["disable"] = "1";
                }
            }
            if (Configure::read('core.site_offline')){
                $chat_setting["disable"] = "1";
            }
            $event->result['mooConfig'] = $config + array("mooChat" => $chat_setting);
            $v->mooConfig = $event->result['mooConfig'];
        }


    }

    public function doControllerUserAfterLogout($event)
    {
        $v = $event->subject();
        $v->CakeSession->delete("mooTokens");
    }

    private function isMobile()
    {
        //return true;
        return MooCore::getInstance()->isMobile(null);
    }

    public function notificationRefresh($event)
    {

        if (Configure::read('Chat.chat_turn_on_notification') == 1) {
            $v = $event->subject();
            $data = $event->data['data'];
            $user = $event->data['user'];
            if (isset($user['chat_count'])) {
                $data['conversation_count'] = $user['chat_count'];
                $event->result['data'] = $data;
            }

        }

    }

    public function doViewerProcess($event)
    {

        if (Configure::read('Chat.chat_turn_on_notification') == 1) {
            $v = $event->subject();
            $user = $event->data['cuser'];
            if (isset($user['chat_count'])) {
                $user['conversation_user_count'] = $user['chat_count'];
                $event->result['cuser'] = $user;
            }

        }
        $appController = $event->subject();
        $appController->Auth->authenticate["Chat.Chat"] = array();
        $appController->loadModel('Chat.ChatUsersSetting');
        $settings = $appController->ChatUsersSetting->find('first', array(
            'conditions' => array('ChatUsersSetting.user_id' => $appController->Auth->user('id'))
        ));

        if (empty($settings)) {
            $settings = array(
                'ChatUsersSetting' => array(
                    'user_id' => $appController->Auth->user('id'),
                )
            );
            $appController->ChatUsersSetting->save($settings);
            $settings = $appController->ChatUsersSetting->find('first', array(
                'conditions' => array('ChatUsersSetting.user_id' => $appController->Auth->user('id'))
            ));
        }
        $isAllowedVideoCalling = false;
        if($appController->isAllowedPermissions(array('chat_allow_chat', 'chat_allow_video_calling')) && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
        {
            $isAllowedVideoCalling = true;
        }
        $appController->set('ChatUserSettings', $settings);
        $appController->set('ChatUserPermissions', array(
            'isAllowedChat' => $appController->isAllowedPermissions('chat_allow_chat'),
            'isAllowedSendPicture' => $appController->isAllowedPermissions(array('chat_allow_chat', 'chat_allow_send_picture')),
            'isAllowedSendFiles' => $appController->isAllowedPermissions(array('chat_allow_chat', 'chat_allow_send_files')),
            'isAllowedEmotion' => $appController->isAllowedPermissions(array('chat_allow_chat', 'chat_allow_user_emotion')),
            'isAllowedChatGroup' => $appController->isAllowedPermissions(array('chat_allow_chat', 'chat_allow_chat_group')),
            'isAllowedVideoCalling' => /*$isAllowedVideoCalling*/ false
        ));
    }

    public function tagPopuHelperIntegration($event)
    {
        if (Configure::read('Chat.chat_turn_on_notification') == 1) {
            $v = $event->subject();
            $params = $event->data['params'];

            if (isset($params['title']) && $params['title'] == __('Send New Message')) {
                if (!$this->checkAllowChat($event)) {
                    return;
                }
                $link = Router::url(array("controller" => "conversations",
                    "action" => "ajax_send",
                    "plugin" => false
                ), false);

                $profileId = str_replace($link . "/", "", $params["href"]);

                if (ctype_digit($profileId)) {
                    $replace = array(
                        'id' => $params['id'],

                        'class' => $params['class'],
                        'title' => $params['title'],
                        'innerHtml' => $params['innerHtml'],
                        'style' => $params['style'],

                    );
                    $search = array('#id', '#class', '#title', '#innerHtml', '#style');
                    $a = "<a id='#id' href='#' onclick='require([\"mooChat\"],function(chat){chat.openChatWithOneUser($profileId)});'  class='#class' title='#title' style='#style' >#innerHtml</a>";
                    $a = str_replace($search, $replace, $a);
                    $event->result['a'] = $a;

                    if (!Configure::read('core.send_message_to_non_friend')) {
                        $friendModel = MooCore::getInstance()->getModel('Friend');
                        $viewer_id = MooCore::getInstance()->getViewer(true);

                        if (!$friendModel->areFriends($viewer_id, $profileId)) {
                            $event->result['a'] = '';
                        }

                    }
                }

            }

        }
    }

    public function afterSaveFriendModel($event)
    {
        $v = $event->subject();

        if (isset($v->data["Friend"]["user_id"])) {
            $chatCachedModel = MooCore::getInstance()->getModel("Chat.ChatCachedQueryUserStat");
            $record = $chatCachedModel->findByUserId($v->data["Friend"]["user_id"]);

            if ($record) {
                $chatCachedModel->read(null, $record['ChatCachedQueryUserStat']['id']);
            }
            $chatCachedModel->save(array(
                'user_id' => $v->data["Friend"]["user_id"],
                'new_friend' => true,
            ));
        }
    }

    public function beforeDeleteFriendModel($event)
    {
        $v = $event->subject();


        $chatCachedModel = MooCore::getInstance()->getModel("Chat.ChatCachedQueryUserStat");
        $record = $chatCachedModel->findByUserId($v->field('user_id'));

        if ($record) {
            $chatCachedModel->read(null, $record['ChatCachedQueryUserStat']['id']);
        }
        $chatCachedModel->save(array(
            'user_id' => $v->field('user_id'),
            'new_friend' => true,
        ));
    }

    public function afterSaveUserBlockModel($event)
    {
        $v = $event->subject();

        if (isset($v->data['UserBlock']['user_id'])) {
            $chatCachedModel = MooCore::getInstance()->getModel("Chat.ChatCachedQueryUserStat");
            $record = $chatCachedModel->findByUserId($v->data['UserBlock']['user_id']);

            if ($record) {
                $chatCachedModel->read(null, $record['ChatCachedQueryUserStat']['id']);
            }
            $chatCachedModel->save(array(
                'user_id' => $v->data['UserBlock']['user_id'],
                'new_block' => true,
            ));
            $rId = $this->_blockUser($v->data['UserBlock']['user_id'], $v->data['UserBlock']['object_id']);

        }

    }

    public function beforeDeleteUserBlockModel($event)
    {
        $v = $event->subject();
        $chatCachedModel = MooCore::getInstance()->getModel("Chat.ChatCachedQueryUserStat");
        $record = $chatCachedModel->findByUserId($v->field('user_id'));

        if ($record) {
            $chatCachedModel->read(null, $record['ChatCachedQueryUserStat']['id']);
        }
        $chatCachedModel->save(array(
            'user_id' => $v->field('user_id'),
            'new_block' => true,
        ));
        $rId = $this->_unblockuser($v->field('user_id'), $v->field('object_id'));

    }

    public function _parseRoomCode($userIds = array())
    {
        sort($userIds);
        return implode(".", $userIds);
    }

    public function _blockUser($ownerId, $blockerId)
    {
        $roomModel = MooCore::getInstance()->getModel("Chat.ChatRoom");
        $code = $this->_parseRoomCode(array($ownerId, $blockerId));
        $room = $roomModel->findByCode($code);

        if ($room) {
            if ($room["ChatRoom"]["first_blocked"] == 0 && $room["ChatRoom"]["second_blocked"] != $ownerId) {
                $room["ChatRoom"]["first_blocked"] = $ownerId;
            } elseif ($room["ChatRoom"]["second_blocked"] == 0 && $room["ChatRoom"]["first_blocked"] != $ownerId) {
                $room["ChatRoom"]["second_blocked"] = $ownerId;
            }
            $roomModel->save($room["ChatRoom"]);
        } else {
            $roomModel->save(
                array(
                    'code' => $code,
                    'name' => $code,
                    'first_blocked' => $ownerId
                )
            );
            $roomMemberModel = MooCore::getInstance()->getModel("Chat.ChatRoomsMember");
            $roomMemberModel->saveMany(array(
                array("room_id" => $roomModel->id, "user_id" => $ownerId, "joined" => date("Y-m-d H:i:s")),
                array("room_id" => $roomModel->id, "user_id" => $blockerId, "joined" => date("Y-m-d H:i:s")),
            ));

        }
    }

    public function _unblockuser($ownerId, $blockerId)
    {
        $roomModel = MooCore::getInstance()->getModel("Chat.ChatRoom");
        $code = $this->_parseRoomCode(array($ownerId, $blockerId));
        $room = $roomModel->findByCode($code);
        if ($room) {
            if ($room["ChatRoom"]["second_blocked"] != $ownerId) {
                $room["ChatRoom"]["first_blocked"] = 0;
            } elseif ($room["ChatRoom"]["first_blocked"] != $ownerId) {
                $room["ChatRoom"]["second_blocked"] = 0;
            }
            $roomModel->save($room["ChatRoom"]);

        }
    }

    public function afterEditUserProfile($event)
    {
        $v = $event->subject();
        $chatCachedModel = MooCore::getInstance()->getModel("Chat.ChatCachedQueryUserStat");
        $record = $chatCachedModel->findByUserId($v->Auth->user('id'));

        if ($record) {
            $chatCachedModel->read(null, $record['ChatCachedQueryUserStat']['id']);
        }
        $chatCachedModel->save(array(
            'user_id' => $v->Auth->user('id'),
            'new_profile' => true,
        ));
    }

    private function checkAllowChat($event)
    {
        $v = $event->subject();
        if(in_array($v->params["controller"],array("subscribes","paypal_expresss","paypal_adaptives","gateway"))) return false;
        //"PaypalExpress.paypal_expresss.process" string(15) "paypal_expresss"
        // PaypalAdaptive.paypal_adaptives.process" string(16) "paypal_adaptives"
        // "credit.gateway.process" string(7) "gateway"
        //"Stripe.stripes.process" string(7) "stripes"
        // subscription.subscribes.gateway subscribes
        $viewer = MooCore::getInstance()->getViewer();
        if (!$viewer['User']['confirmed'] && Configure::read('core.email_validation')) {
            return false;
        }
        return true;
    }

    public function beforeMooAppOptimizedCssRender($event)
    {
        $v = $event->subject();
        $v->Html->css(array(
            'Chat.chat-mobile.css',
            'Chat.chat-app.css'
        ),
            array('block' => 'mooAppOptimizedCss', 'minify' => false));
    }

    public function beforeMooAppOptimizedScriptRender($event)
    {
        $v = $event->subject();
        $v->Html->script('moocore/require.js',
            array('block' => 'mooAppOptimizedScript')//
        );
        if (Configure::read('debug') == 0) {
            $min = "min.";
        } else {
            $min = "";
        }
        $v->MooRequirejs->addPath(array(

            // moo amd js
            'jquery' => $v->MooRequirejs->assetUrlJS('js/global/jquery-1.11.1.min.js'),
            "mooAjax" => $v->MooRequirejs->assetUrlJS("js/moocore/ajax.{$min}js"),

        ));
        $v->MooRequirejs->addPath(array(
            "mooChat" => $v->MooRequirejs->assetUrlJS("/chat/js/client/mooChat-mobile.js"),
        ));
        $v->MooRequirejs->addShim(array(
            'mooAjax' => array("deps" => array('mooChat')),
        ));
        $v->MooRequirejs->addToFirst(array('jquery', 'mooAjax'));
        $v->Html->scriptBlock("requirejs.config({$v->MooRequirejs->config()});require({$v->MooRequirejs->first()});", array(
            'inline' => false, 'block' => 'mooAppOptimizedScript'
        ));
        //Add action declare on mooApp.
        if ($this->isApp($v)) {
                $v->Html->script('../moo_app/js/mobile.action.bundle.js',
                    array('block' => 'mooAppOptimizedScript')//
                );
        }
        
    }
}
