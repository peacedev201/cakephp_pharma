<?php
App::uses('CreditAppModel', 'Credit.Model');

class CreditWithdraw extends CreditAppModel
{
    public $belongsTo = array('User');
    public $validate = array(
        'amount' => array(
            'notEmpty' => array(
                'rule' => 'notBlank'
            ),
            'naturalNumber' => array(
                'rule' => 'naturalNumber',
                'message' => "Amount id must be greater than 0",
            ),
            'checkBalances' => array(
                'rule' => array('checkBalances'),
                'message' => 'Current balance credits not enough'
            ),
            'num_withdraw' => array(
                'rule' => array('checkNumWithdraw'),
                'message' => "Exceed the limit in a Month"
            )
        )

    );


    public function checkNumWithdraw($data)
    {
        $creditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $uid = MooCore::getInstance()->getViewer();
        $result = $creditBalances->findById($uid['User']['id']);

        return (isset($result['CreditBalances']) && $result['CreditBalances']['num_withdraw'] < Configure::read('Credit.num_withdrawal')) ? true : false;

    }

    public function checkBalances(){
        $creditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $uid = MooCore::getInstance()->getViewer();
        $result = $creditBalances->findById($uid['User']['id']);

        return (isset($result['CreditBalances'])) ? true : false;
    }

}
