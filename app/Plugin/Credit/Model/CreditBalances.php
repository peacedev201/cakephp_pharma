<?php
App::uses('CreditAppModel', 'Credit.Model');

class CreditBalances extends CreditAppModel
{
    public $validationDomain = 'credit';
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'id',
        )
    );

    public $validate = array(
        'credit' => array(
            'rule' => 'notBlank',
            'message' => 'Credit is required'
        ),
        'credit' => array(
            'rule' => 'numeric',
            'message' => 'Only numbers allowed',
        )
    );

    public function getBalancesUser($userId)
    {
        $item = $this->find('first', array(
            'conditions' => array('CreditBalances.id' => $userId)
        ));
        return $item;
    }

    public function addCredit($uid, $credit, $type = '')
    {
        $balance = $this->getBalancesUser($uid);
        if (empty($balance)) {
            $current_credit = $credit;
            $params_balance = array(
                'id' => $uid,
                'current_credit' => $credit,
                'earned_credit' => $credit,
                'spent_credit' => 0,
            );
            $rank_id = 0;
        } else {
            $this->id = $uid;
            $earned_credit = $balance['CreditBalances']['earned_credit'];
            $spent_credit = $balance['CreditBalances']['spent_credit'];
            $current_credit = $balance['CreditBalances']['current_credit'] + $credit;
            if ($credit > 0) {
                $earned_credit = $balance['CreditBalances']['earned_credit'] + $credit;
            } else {
                $spent_credit = $balance['CreditBalances']['spent_credit'] - $credit;
            }

            if($type == ''){
                $params_balance = array(
                    'current_credit' => $current_credit,
                    'spent_credit' => $spent_credit,
                    'earned_credit' => $earned_credit
                );
            }else{
                $params_balance = array(
                    'current_credit' => $current_credit
                );
            }

            $rank_id = $balance['CreditBalances']['rank_id'];
        }
        App::import('Model', 'Credit.CreditRanks');
        $this->CreditRanks = new CreditRanks();

        // nhson219 remove beacause update rank is working bottom

//        $rank = $this->CreditRanks->getRankUser($params_balance['current_credit'], $rank_id);
//        if (!empty($rank)) {
//            $params_balance['rank_id'] = $rank['CreditRanks']['id'];
//        }

        $creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');

//        if(!empty($rank) && $rank['CreditRanks']['notify'] == 1)
//        {
//            $data = array(
//                'user_id' => $uid,
//                'sender_id' => MooCore::getInstance()->getViewer(true),
//                'action' => 'got_badge',
//                'url' => '/credits/index/rank',
//                'params' => json_encode(array('name' => $rank['CreditRanks']['name'])),
//                'plugin' => 'Credit',
//            );
//            App::import('Model', 'Notification');
//            $mNotification = new Notification();
//            $mNotification->create();
//            $mNotification->save($data);
//        }

        $this->set($params_balance);
        $this->save();
        $this->clear();
        $rank = $this->CreditRanks->getRankUserAndNoti($current_credit,$rank_id,$uid);
        return $this->id;
    }

    public function addCreditFrozen($uid, $credit,$is_withdraw_completed = false)
    {
        $balance = $this->getBalancesUser($uid);
        if (empty($balance)) {
            $params_balance = array(
                'id' => $uid,
                'current_credit' => $credit,
                'earned_credit' => $credit,
                'spent_credit' => 0,
            );
            $rank_id = 0;
        } else {

            $this->id = $uid;
            $earned_credit = $balance['CreditBalances']['earned_credit'];
            $spent_credit = $balance['CreditBalances']['spent_credit'];
            $current_credit = $balance['CreditBalances']['current_credit'];
            if($is_withdraw_completed == false) {
                $spent_credit +=  $credit;
                $current_credit -=  $credit;
            }
            $frozen_credit = $balance['CreditBalances']['frozen_credit'] + $credit;


            $params_balance = array(
                'current_credit' => $current_credit,
                'earned_credit' => $earned_credit,
                'spent_credit' => $spent_credit,
                'frozen_credit' => $frozen_credit
            );
            $rank_id = $balance['CreditBalances']['rank_id'];
        }
        App::import('Model', 'Credit.CreditRanks');
        $this->CreditRanks = new CreditRanks();
        $rank = $this->CreditRanks->getRankUser($params_balance['current_credit'], $rank_id);
        if (!empty($rank)) {
            $params_balance['rank_id'] = $rank['CreditRanks']['id'];
        }
        $this->set($params_balance);
        $this->save();
        $this->clear();
        return $this->id;
    }

    public function spendBalanceCredit($spend = 0)
    {
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $balance = $this->getBalancesUser($viewer_id);
        if (empty($balance)) {
            return false;
        } else {
            if ($balance['CreditBalances']['current_credit'] < $spend) {
                return false;
            } else {
                $this->id = $viewer_id;
                $params_balance = array(
                    'current_credit' => $balance['CreditBalances']['current_credit'] - $spend,
                    'spent_credit' => $balance['CreditBalances']['spent_credit'] + $spend,
                );
                $this->set($params_balance);
                $this->save();
                return true;
            }
        }
    }

    public function getMembers($page)
    {
        $limit = Configure::read('Credit.credit_item_per_pages');
        $sort = Configure::read('Credit.credit_default_sort_by');
        $order = $sort . ' DESC';
        $conditions = $this->addBlockCondition();//debug($conditions);die();
        $items = $this->find('all', array(
            'conditions' => $conditions,
            'limit' => $limit,
            'page' => $page,
            'fields' => array('*'),
            'order' => $order
        ));
        return $items;
    }

    public function pluginUseCredit($credit, $action_type, $object_name, $uId, $object_id)
    {
        if ($this->spendBalanceCredit($credit)) {
            // write log
            $actionTypeModel = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
            $action_type = $actionTypeModel->getActionTypeFormModule($action_type);
            if (!empty($action_type)) {
                $action_id = $action_type['CreditActiontypes']['id'];
                $mLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
                $credit = intval('-' . $credit);
                $mLogs->addLog($action_id, $credit, $object_name, $uId, $object_id);
            }
            return true;
        }
        return false;
    }

    public function checkBalance($amount = "")
    {
        if ($amount == "")
            return false;
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $balance = $this->getBalancesUser($viewer_id);
        if (empty($balance)) {
            return false;
        } elseif (intval($balance['CreditBalances']['current_credit']) < intval($amount))
                return false;

        return true;
    }

    public function addBlockCondition($cond = array(), $modal_name = null) {
        $userBlockModal = MooCore::getInstance()->getModel('UserBlock');
        $blockedUsers = $userBlockModal->getBlockedUsers();
        if(!empty($blockedUsers)){
            $str_blocked_users = implode(',', $blockedUsers);
            $field_name = 'User.id';
            if(empty($modal_name)){
                $modal_name = $this->name;
            }
            if($modal_name != 'User'){
                $field_name = $modal_name . '.id';
            }
            $cond[] = "$field_name NOT IN ($str_blocked_users)";
        }

        return $cond;
    }
}
