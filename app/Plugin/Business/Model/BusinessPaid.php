<?php
/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('BusinessAppModel','Business.Model');
class BusinessPaid extends BusinessAppModel 
{
    public $validationDomain = 'business';
    public $mooFields = array('plugin','type');
	
    public $validate = array(   
        'price' => array(
            'rule' => array('comparison', '>=', 0),
            'message'  => 'Price is not valid'
        ),
        'recurring_price' => array(
            'rule' => array('comparison', '>=', 0),
            'message'  => 'Recurring price is not valid'
        ),
        'recurring' => array(
            'rule' => array('comparison', '>', 0),
            'message'  => 'Recurring must be a valid interger greater than 0'
        )
    );
    public $belongsTo = array(
        'Business' => array(
            'className'=> 'Business.Business',
        ),
        'BusinessPackage' => array(
            'className'=> 'Business.BusinessPackage',
        ),
        'Gateway' => array(
        	'className'=> 'PaymentGateway.Gateway',
                'foreignKey' => 'gateway_id'
        ),
        'BusinessTransaction' => array(
           'className'=> 'Business.BusinessTransaction',
        ),
        'User');


    public function isIdExist($id)
    {
        return $this->hasAny(array('id' => (int)$id));
    }

    public function getFeaturedExpiredActivePaid($business_id){
        $paid = $this->find('all', array('conditions' => array('BusinessPaid.business_id' => $business_id, 'BusinessPaid.pay_type' => "featured_package",'BusinessPaid.status' => 'active', 'BusinessPaid.active' => 1), 'limit' => 1));
        if(!empty($paid[0])){
            return  array($paid[0]['BusinessPaid']['expiration_date'], $paid[0]['BusinessPaid']['end_date']);
        }
        return array(0,0);
    }

    public function getExpiredActivePaid($business_id){
        $paid = $this->find('all', array('conditions' => array('BusinessPaid.business_id' => $business_id, 'BusinessPaid.status' => 'active', 'BusinessPaid.active' => 1), 'limit' => 1));
        if(!empty($paid[0])){
            return  array($paid[0]['BusinessPaid']['expiration_date'], $paid[0]['BusinessPaid']['end_date']);
        }
        return array(0,0);
    }
    public function isBelongToPackage($package_id)
    {
        return $this->hasAny(array('business_package_id' => (int)$package_id));
    }
    public function afterSave($created, $options = array())
    {
    	Cache::clearGroup('business');
    	parent::afterSave($created, $options);
    }
    
    public function resetBusinessStatus($status, $business_id, $pay_type)
    {
        $this->updateAll(array(
            'BusinessPaid.status' => "'".$status."'",
            'BusinessPaid.active' => 0
        ), array(
            'BusinessPaid.business_id' => $business_id,
            'BusinessPaid.pay_type' => $pay_type
        ));
    }
    
    public function isUsedPackage($business_package_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $count_paid = $this->find('count', array(
            'conditions' => array(
                'BusinessPaid.business_package_id' => $business_package_id
            )
        ));
        $count_business = $mBusiness->find('count', array(
            'conditions' => array(
                'Business.business_package_id' => $business_package_id
            )
        ));
        if($count_paid > 0 || $count_business > 0)
        {
            return true;
        }
        return false;
    }
    public function deletePaid($business_id, $user_id = null)
    {
         $cond = array(
            'BusinessPaid.business_id' => $business_id,
        );
        if($user_id > 0)
        {
            $cond['BusinessPaid.user_id'] = $user_id;
        }
        return $this->deleteAll($cond);
    }
}
