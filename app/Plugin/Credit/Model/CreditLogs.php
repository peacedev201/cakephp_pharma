<?php
App::uses('CreditAppModel', 'Credit.Model');
class CreditLogs extends CreditAppModel {

    public function checkCredit($action_type, $user_id)
    {
        // if( $action_type['CreditActiontypes']['credit'] <= 0 ) {
        //     return false;
        // }
        $sCond = array(
            'user_id' => $user_id,
            'action_id' => $action_type['CreditActiontypes']['id'],
        );
        if( $action_type['CreditActiontypes']['rollover_period'] == 0 ) {
            return true;
        }
        array_push($sCond, 'DATEDIFF("'.date("Y-m-d").'", creation_date) < '.$action_type['CreditActiontypes']['rollover_period']);
        $credits = $this->find('all', array(
            'conditions' => array($sCond)
        ));
        $all_credits = 0;
        foreach($credits as $credit) {
            $all_credits += $credit['CreditLogs']['credit'];
        }

        return ($action_type['CreditActiontypes']['max_credit'] - $all_credits > 0) ? true : false;
        //return ($all_credits + $action_type['CreditActiontypes']['credit'] < $action_type['CreditActiontypes']['max_credit']) ? true : false;
    }

    public function checkDeleteItem($action_id,$user_id, $object_type, $object_id)
    {
        $sCond = array(
            'user_id' => $user_id,
            'object_type' => $object_type,
            'object_id' => $object_id,
            'deleted' => 0,
            'is_delete' => 0,
            'action_id' => $action_id
        );
        array_push($sCond, 'DATEDIFF("'.date("Y-m-d").'", creation_date) = 0');
        $item = $this->find('first', array(
            'conditions' => array($sCond)
        ));
        return $item;
    }

    public function addLog($action_id, $credit, $object_type, $user_id, $object_id,$is_delete = false)
    {
        $params_log = array(
            'user_id' => $user_id,
            'action_id' => $action_id,
            'object_type' => $object_type,
            'object_id' => $object_id,
            'credit' => $credit,
            'creation_date' => date("Y-m-d H:i:s"),
            'is_delete' => $is_delete
        );
        $this->set($params_log);
        $this->save();
        $this->clear();
        //return $this->CreditLogs->id;
    }

    public function getTransactions($user_id, $page)
    {
        $conditions = array(
            'user_id' => $user_id
        );
        $join = array();
        $join[] = array(
            "table" => $this->tablePrefix."credit_actiontypes",
            "alias" => "CreditActiontypes",
            "type" => "LEFT",
            "conditions" => array(
                "CreditLogs.action_id = CreditActiontypes.id"
            )
        );
        $order = 'CreditLogs.creation_date DESC';
        //$limit = 5;
        $items = $this->find('all', array(
            'joins' => $join,
            'conditions' => $conditions,
            //'limit' => $limit,
            'page' => $page,
            'fields' => array('*'),
            'order' => $order
        ));//debug($items);die();
        return $items;
    }

    public function addLogByType($type, $credit, $uid, $object_type, $object_id, $add_credit = 0)
    {
        $actionTypeModel = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
        $action_type = $actionTypeModel->getActionTypeFormModule($type);
        if(!empty($action_type)) {
            $action_id = $action_type['CreditActiontypes']['id'];
            $this->addLog($action_id, $credit, $object_type, $uid, $object_id);
            if($add_credit)
            {
                $creditBalancesModel = MooCore::getInstance()->getModel('Credit.CreditBalances');
                $creditBalancesModel->addCredit($uid, $credit);
            }
        }
    }

    // nhson219

    public function getCredit($action_type, $user_id){
        $sCond = array(
            'user_id' => $user_id,
            'action_id' => $action_type['CreditActiontypes']['id'],
        );
        if($action_type['CreditActiontypes']['rollover_period'] == 0)
        {
            return true;
        }
        array_push($sCond, 'DATEDIFF("'.date("Y-m-d").'", creation_date) < '.$action_type['CreditActiontypes']['rollover_period']);
        $credits = $this->find('all', array(
            'conditions' => array($sCond)
        ));
        $all_credits = 0;
        foreach($credits as $credit) {
            $all_credits += $credit['CreditLogs']['credit'];
        }

        return $all_credits;
    }

}
