<?php

App::uses('FaqAppModel', 'Faq.Model');

class FaqResult extends FaqAppModel {

    public $belongsTo = array('User');
    public $order = 'FaqResult.id desc';

//    public $validate = array(
//        'faq_id' => array(
//            'rule' => 'notBlank',
//            'message' => 'faq id is required',
//        ),
//        'user_id' => array(
//            'rule' => 'notBlank',
//            'message' => 'user id is required',
//        ),
//        'vote' => array(
//            'rule' => 'notBlank',
//            'message' => 'vote is required',
//        ),
//        'helpful_id' => array(
//            'rule' => 'notBlank',
//            'message' => 'Privacy is required',
//        ),
//    );
//    
//    public function getFaq_id($row) {
//        if (isset($row['faq_id'])) {
//            return $row['faq_id'];
//        }
//        return false;
//    }
//
//    public function getUser_id($row) {
//        if (isset($row['user_id'])) {
//            return $row['user_id'];
//        }
//        return false;
//    }
//
//  
    public function getLastUpdate($id_faq = NULL) {
        $cond = array();
        $cond['FaqResult.faq_id'] = $id_faq;
        $order = array('FaqResult.modified desc');

        return $this->find('first', array(
                    'conditions' => $cond,
                    'limit' => 1,
                    'page' => 1,
                    'order' => $order
        ));
    }

    public function getResults($id_faq = null, $user_id = NULL) {
        $cond = array();
        if($id_faq)
        $cond['FaqResult.faq_id'] = $id_faq;
        if($user_id)
        $cond['FaqResult.user_id'] = $user_id;
        return $this->find('all', array('conditions' => $cond));
    }
    public function getTotalByFaqId($id_faq = null,$yes = FALSE, $no= FALSE) {
        $cond = array();
        $cond['FaqResult.faq_id'] = $id_faq;
        if($yes)
            $cond['FaqResult.vote'] = 1;
        if($no)
            $cond['FaqResult.vote'] = 0;
        return $this->find('count', array('conditions' => $cond));
    }

      public function deleteResults($id_faq = null, $user_id = NULL) {
        $datas = $this->getResults($id_faq,$user_id);
        foreach ($datas as $data){
            $this->delete($data['FaqResult']['id']);
        }
        return true;
    }
}
