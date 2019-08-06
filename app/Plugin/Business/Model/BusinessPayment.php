<?php
class BusinessPayment extends BusinessAppModel 
{
    public $hasAndBelongsToMany = array(
        'Business' => array(
            'className' => 'Business.Business',
            'joinTable' => 'business_payment_types',
            'foreignKey' => 'business_payment_id',
            'associationForeignKey' => 'business_id',
        )
    );
    
    public function getBusinessPayment()
    {
        return $this->find('all', array(
            'conditions' => array(
                'BusinessPayment.enable' => 1
            )
        ));
    }
}