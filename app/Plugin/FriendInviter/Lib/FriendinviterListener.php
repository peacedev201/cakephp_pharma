<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('CakeEventListener', 'Event');

class FriendinviterListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'UserController.doSaveUser' => 'doSaveUser',
            'MooView.beforeRender' => 'beforeRender',
            'Model.beforeValidate' => 'beforeValidate'
        );
    }

    public function beforeValidate($event) {
        $enable_referral_code_field = Configure::read('FriendInviter.friendinviter_enable_referral_code_field');
        if ($enable_referral_code_field) {
            $model_obj = $event->subject();
            if ($model_obj->name == 'User' && !$model_obj->id) {
                $model_obj->validator()->add('suggest_code', 'required', array(
                    'rule' => 'notBlank',
                    'message' => __d('friend_inviter','Referral Code is required')
                ));
                
                if(!$this->validateSuggestCode($model_obj->data['User'])){
                    $model_obj->validator()->add('suggest_code', 'exist', array(
                        'rule' => array('equalTo', time()),
                        'message' => __d('friend_inviter','Referral Code does not exist')
                    ));
                }
            }
        }
    }
    
    public function validateSuggestCode($check){
         $usserSuggestCodeModel = MooCore::getInstance()->getModel('FriendInviter.UserSuggestCode');
                        $suggest_code = $usserSuggestCodeModel->find('count', array(
                            'conditions' => array('suggest_code' => $check['suggest_code']),
                        ));
                        return $suggest_code;
    }

    public function doSaveUser($event) {
        $controller = $event->subject();
        $data = isset($event->data['data']) ? $event->data['data'] : '';
        $inviteModel = MooCore::getInstance()->getModel('FriendInviter.Invite');
        $uid = $controller->User->id;
        if (isset($data['suggest_code']) && !empty($data['suggest_code'])) {
            $user_suugest_code_mod = MooCore::getInstance()->getModel('FriendInviter.UserSuggestCode');
            $suggest_row = $user_suugest_code_mod->findBySuggestCode($data['suggest_code']);           
            if ($suggest_row) {
                $user_id = $suggest_row['UserSuggestCode']['user_id'];
                $invite_record = $inviteModel->find('first', array('conditions' => array('Invite.user_id' => $user_id, 'Invite.recipient' => $data['email'])));
                if (empty($invite_record)) {
                    $inviteModel->clear();
                    $inviteModel->save(array(
                        'user_id' => $user_id,
                        'recipient' => $data['email'],
                        'code' => $data['suggest_code'],
                        'message' => '',
                        'timestamp' => date('Y-m-d h:m:s'),
                        'service' => 'link',
                        'social_profileid' => 0
                    ));
                }

                if (Configure::read('Credit.credit_enabled')) {
                    $credit_action_type = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
                    $action_type = $credit_action_type->getActionTypeFormModule('friend_inviter');
                    if (!$action_type) {
                        $credit_action_type->create();
                        $saved_data = array(
                            'action_type' => 'friend_inviter',
                            'action_type_name' => 'Friend Inviter',
                            'action_module' => 'Friend Inviter',
                            'credit' => 10,
                            'max_credit' => 1000,
                            'rollover_period' => 0,
                            'type' => 'none',
                            'plugin' => 'Credit',
                            'show' => 1
                        );
                        $credit_action_type->save($saved_data);
                        $action_type = $credit_action_type->read();
                    }


                    if (!empty($action_type)) {
                        $creditLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
                        // check credit max
                        if ($creditLogs->checkCredit($action_type, $user_id)) {

                            $action_id = $action_type['CreditActiontypes']['id'];

                            $all_credits = $creditLogs->getCredit($action_type, $user_id);

                            if ($action_type['CreditActiontypes']['max_credit'] - $all_credits >= $action_type['CreditActiontypes']['credit'])
                                $credit = $action_type['CreditActiontypes']['credit'];
                            else
                                $credit = $action_type['CreditActiontypes']['max_credit'] - $all_credits;

                            $creditLogs->addLog($action_id, $credit, 'core_user', $user_id, $uid);
                            $creditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
                            $creditBalances->addCredit($user_id, $credit);
                        }
                    }
                }
            }
        }

        $inviteModel->clear();
        $inviteModel->updateAll(
                array('Invite.new_user_id' => $uid), array('Invite.recipient' => $data['email'])
        );

        $auto_friend = Configure::read('FriendInviter.friendinviter_automatic_addfriend');

        if (!empty($auto_friend)) {
            $cond = array('Invite.recipient' => $data['email']);
            $invites = $inviteModel->find('list', array(
                'conditions' => $cond, 
                'joins' => array(
                            array(
                                'table' => 'users',
                                'alias' => 'User',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'User.id = Invite.user_id'
                                )
                            )
                ),
                'fields' => array('Invite.user_id'), 'group' => 'Invite.user_id')
            );

            $friendModel = MooCore::getInstance()->getModel('Friend');
            $friendModel->autoFriends($controller->User->id, $invites);

            $activityModel = MooCore::getInstance()->getModel('Activity');
            $activityModel->clear();
        }
    }

    public function beforeRender($event) {
        $e = $event->subject();
        $e->addPhraseJs(array(
            'sending_request' => __d('friend_inviter', 'Sending Request'),
            'please_select_at_least_one_friend_to_invite' => __d('friend_inviter', 'Please select at least one friend to invite'),
            'drag_or_click_here_to_upload_file' => __d('friend_inviter', 'Drag or click here to upload a CSV file'),
            'You have exceeded the maximum number of people you can invite' => __d('friend_inviter', 'You have exceeded the maximum number of people you can invite')
        ));
        $params = $e->request->params;
        if (($params['controller'] == 'users' && $params['action'] == 'register') || ($params['controller'] == 'home' && $params['action'] == 'index')) {
            $enable_referral_code_field = Configure::read('FriendInviter.friendinviter_enable_referral_code_field');
            $sc = '';
            if (isset($params['named']['suggest_code'])) {
                $sc = $params['named']['suggest_code'];
            }
            if ($enable_referral_code_field) {
                if ($params['action'] == 'register') {
                    $referer_code_el = '<div class="form-group required"><label class="col-md-3 control-label" for="name">' . __d('friend_inviter', 'Referral Code') . '(<a href="javascript:void(0)" class="tip" title="' . __d('friend_inviter', 'Enter the referral code you got from your friend in this community to sign up') . '">?</a>)</label><div class="col-md-9"><input type="text" id="suggest_code" class="form-control" name="data[suggest_code]" value="' . $sc . '" /></div></div>';
                } else {
                    $referer_code_el = '<div class="form-group required"><input type="text" id="suggest_code" class="form-control" placeholder="' . __d('friend_inviter', 'Referral Code') . '" name="data[suggest_code]" value="' . $sc . '" /></div>';
                }
                $e->Helpers->Html->scriptBlock(
                        "require(['jquery'], function($) {\$(document).ready(function(){ \$('#regFields').append('" . $referer_code_el . "');$(\".tip\").tipsy({ html: true, gravity: 's' }); });});", array(
                    'inline' => false,
                        )
                );
                // $userModel->afterValidate();
            } else {
                $e->Helpers->Html->scriptBlock(
                        "require(['jquery'], function($) {\$(document).ready(function(){ \$('#regFields').append('<input type=\'hidden\' name=\'data[suggest_code]\' value=\'$sc\' />'); });});", array(
                    'inline' => false,
                        )
                );
            }
        }

        $checkcredit = Cache::read('friendinviter.checkcredit');
        if (!$checkcredit) {
            $pluginModel = MooCore::getInstance()->getModel('Plugin');
            if ($pluginModel->isKeyExist('Credit')) {
                $credit_action_type = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
                $action_type = $credit_action_type->getActionTypeFormModule('friend_inviter');
                if (!$action_type) {
                    $credit_action_type->create();
                    $saved_data = array(
                        'action_type' => 'friend_inviter',
                        'action_type_name' => 'Friend Inviter',
                        'action_module' => 'Friend Inviter',
                        'credit' => 10,
                        'max_credit' => 1000,
                        'rollover_period' => 0,
                        'type' => 'none',
                        'plugin' => 'Credit',
                        'show' => 1
                    );
                    $credit_action_type->save($saved_data);
                }
            }
            Cache::write('friendinviter.checkcredit', true);
        }
        /*
          $angel_invitation = Configure::read('FriendInviter.invites_greate_angel');
          if(!empty($angel_invitation) && is_numeric($angel_invitation) && $params['controller'] == 'users' && $params['action'] == 'view'){
          $subject = MooCore::getInstance()->getSubject();

          if(isset($subject['User']['id']) && !empty($subject['User']['id'])){
          $user_id = $subject['User']['id'];
          $invite_model = MooCore::getInstance()->getModel('FriendInviter.Invite');
          $total_successful_invite =  $invite_model->getTotalSignupInvite($user_id);
          if($total_successful_invite >= $angel_invitation){
          $e->Helpers->Html->css( array(
          'FriendInviter.fi'
          ),
          array('block' => 'css')
          );

          }
          }
          }
         */
        if (Configure::read('FriendInviter.friendinviter_enabled') && !isset($params['admin'])) {
            $e->Helpers->Html->scriptBlock(
                    "require(['jquery'], function($) {\$(document).ready(function(){ $('a[href$=\"invite-friends\"]').unbind('click');$('a[href$=\"invite-friends\"]').addClass('no-ajax');$('a[href$=\"invite-friends\"]').removeAttr('data-url');$('a[href*=\"friends/ajax_invite?mode=model\"]').removeAttr('data-toggle'); });});", array(
                'inline' => false,
                    )
            );
        }
    }

}
