<?php
App::uses('CreditAppModel', 'Credit.Model');
class CreditRanks extends CreditAppModel {
    public $validationDomain = 'credit';
    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'message' => 'Name is required'
        ),
        'credit' => array(
            'rule' => 'notBlank',
            'message' => 'Credit is required'
        ),
        'description' => array(
            'rule' => 'notBlank',
            'message' => 'Description is required'
        ),
        'credit' => array(
            'rule' => 'numeric',
            'message' => 'Credit is required',
        )
    );
    public $actsAs = array(
        'MooUpload.Upload' => array(
            'photo' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}credit_ranks{DS}{field}{DS}',
            )
        ),
        'Storage.Storage' => array(
            'type'=>array('credit_ranks'=>'photo'),
        ),
    );

    public function getRanks($page)
    {
        $ranks = $this->find( 'all',array(
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'page' => $page,
        ) );
        return $ranks;
    }

    public function getNextRank($credit)
    {
        $rank = $this->find( 'first',array(
            'conditions' => array('credit > ' => $credit),
            'order' => 'credit ASC'
        ) );
        return $rank;
    }

    public function getNowRank($credit)
    {
        $rank = $this->find( 'first',array(
            'conditions' => array('credit <= ' => $credit),
            'order' => 'credit DESC'
        ) );
        return $rank;
    }

    public function getRankUser($credit, $rank_id)
    {
        $rank = $this->find( 'first',array(
            'conditions' => array('credit <= ' => $credit, 'id > ' => $rank_id)
        ) );
//        if(!empty($rank) && $rank['CreditRanks']['notify'] == 1)
//        {
//            $data = array(
//                'user_id' => MooCore::getInstance()->getViewer(true),
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
        return $rank;
    }

    public function getRankUserAndNoti($credit, $rank_id = 0,$user_id = 0,$is_send_noti = true)
    {
        $creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');
        $rank = $creditHelper->doUpdateRankUser($credit,$user_id);

        //get user admin to alert rank
        App::import('Model', 'User');
        $this->User = new User();

        $user_admin = $this->User->findByRoleId(1);

        if($is_send_noti == true) {
            if (!empty($rank) && $rank['CreditRanks']['notify'] == 1 && $rank['CreditRanks']['id'] >= $rank_id) {
                $data = array(
                    'user_id' => $user_id,
                    'sender_id' => $user_admin['User']['id'],
                    'action' => 'got_badge',
                    'url' => '/credits/index/rank',
                    'params' => json_encode(array('name' => $rank['CreditRanks']['name'])),
                    'plugin' => 'Credit',
                );

                App::import('Model', 'Notification');
                $mNotification = new Notification();
                $mNotification->clear();
                $mNotification->create();
                $mNotification->save($data);
            }
        }
        return $rank;
    }
}
