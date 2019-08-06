<?php
App::uses('CreditAppModel', 'Credit.Model');
class CreditSells extends CreditAppModel {
    public $validationDomain = 'credit';
    public $validate = array(

        'credit' => array(
            'notEmpty' => array(
                'rule' => 'notBlank',
                'message' => 'Credit is required'
            ),
            'notNumber' => array(
                'rule' => 'numeric',
                'message' => 'Only numbers allowed'
            )
        ),
        'price' => array(
            'notEmpty' => array(
                'rule' => 'notBlank',
                'message' => 'Price is required'
            ),
            'notNumber' => array(
                'rule' => 'numeric',
                'message' => 'Only numbers allowed'
            )
        )

    );

    public function getAllSellCredit(){
        $sells = $this->find('all', array(
            'order' => array('credit' => 'ASC')
        ));
        return $sells;
    }
}
