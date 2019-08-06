<?php

class GivesController extends CreditAppController
{
    public function admin_index()
    {
        $this->loadModel('Role');
        $roles = $this->Role->find('all');

        $this->set('roles', $roles);
        $this->set('title_for_layout', __d('credit', 'Give mass credits'));
    }

    public function admin_do_get_json()
    {
        $this->_checkPermission();
        $uid = $this->Auth->user('id');
        $this->loadModel('User');
        $q = $this->request->query['q'];
        $ids = isset($this->request->query['ids']) ? explode(',',$this->request->query['ids']) : array();
        $ids = array_filter($ids);
        $conditions = array('User.active' => 1, 'User.confirmed' => 1, 'User.name LIKE ' => '%' . $q . '%');
        if (count($ids))
        {
            $conditions['NOT'] = array('User.id' => $ids);
        }
        $users = $this->User->find('all', array('conditions' => $conditions));
        // have to do this because find(list) does not work with bindModel
        $user_options = array();


        $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
        foreach ($users as $user) {
            $avatar = $mooHelper->getImage(array('User' => $user['User']), array('prefix' => '50_square', 'align' => 'absmiddle', 'style' => 'width: 40px'));
            $user_options[] = array('id' => $user['User']['id'], 'name' => $user['User']['name'], 'avatar' => $avatar);
        }
        return json_encode($user_options);
    }

    public function admin_ajax_do_send()
    {
        $this->autoRender = false;

        if (empty($this->request->data['friends']) && $this->request->data['group_type'] != 'user_group') {
            $this->_jsonError(__d('credit', 'Select member is required'));
        } elseif (empty($this->request->data['credit'])) {
            $this->_jsonError(__d('credit', 'Credit is required'));
        } else {
            if (floatval($this->request->data['credit']) == 0
                //|| strlen($this->request->data['credit']) != strlen(intval($this->request->data['credit']))
            ) {
                $this->_jsonError(__d('credit', 'Credit is invalid, please enter number'));
            } else {

                $recipients = array();
                if($this->request->data['friends'] != ''){
                    $recipients = explode(',', $this->request->data['friends']);
                }else{
                    $this->loadModel('User');
                    $list_all_user_by_role = $this->User->find('all', array('conditions' => array('User.role_id' => $this->request->data['role'])));
                    if($list_all_user_by_role != '')
                        $recipients = array_map(function($val){ return $val['User']['id'];}, $list_all_user_by_role);
                }
                
                // Add credit to friend select
                $this->loadModel('Credit.CreditBalances');
                $this->loadModel('Notification');
                $this->loadModel('Credit.CreditRanks');
                if(count($recipients) > 0){
                    foreach ($recipients as $k => $v) {
                        $this->CreditBalances->addCredit($v, floatval($this->request->data['credit']));
                        // write log
                        $this->loadModel('Credit.CreditLogs');
                        $uid = $this->Auth->user('id');
                        $this->CreditLogs->addLogByType('give_credits', floatval($this->request->data['credit']), $v, 'user', $uid);

                        $balances_user = $this->CreditBalances->getBalancesUser($v);
                        if($balances_user) {
                            $this->CreditRanks->getRankUserAndNoti($balances_user['CreditBalances']['current_credit'], $balances_user['CreditBalances']['rank_id'], $v,false);
                        }

                        // Send notify to user if enable
                        if ($this->request->data['select'] == 1) {
                            $this->Notification->clear();
                            $this->Notification->save(array('user_id' => $v,
                                'sender_id' => MooCore::getInstance()->getViewer(true),
                                'action' => 'buy_credit',
                                'params' => json_encode(array('credit' => floatval($this->request->data['credit']))),
                                'url' => '/credits/index/my_credits',
                                'plugin' => 'Credit'
                            ));
                        }
                    }
                }
                
                $response['result'] = 1;
                $response['message'] = __d('credit', 'Credits have been successfully sent.');
                echo json_encode($response);
            }
        }
    }
}
