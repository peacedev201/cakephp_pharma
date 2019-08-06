<?php 
App::uses('StoreAppModel', 'Store.Model');
class StorePayment extends StoreAppModel
{
    public $validationDomain = 'store';
    public $actsAs = array(
        'Translate' => array('name' => 'nameTranslation', 'description' => 'descriptionTranslation')
    );
    public $validate = array( 
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide name'
        ),
        'description' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide description'
        ),
    );
	
	private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }
    
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }
    
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        foreach($this->actsAs['Translate'] as $field => $item)
        {
            $this->data['StorePayment']['trans_'.$field] = $this->data['StorePayment'][$field];
            $this->data['StorePayment'][$field] = reset($this->data['StorePayment'][$field]);
        }
    }

    public function afterSave($created, $options = array()) {
        parent::afterSave($created, $options);
        
        //save multi language
        foreach($this->actsAs['Translate'] as $field => $item)
        {
            $data = !empty($this->data['StorePayment']['trans_'.$field]) ? $this->data['StorePayment']['trans_'.$field] : null;
            $this->saveMultiLanguage($data, $field, $this->data['StorePayment']['id']);
        }
    }
    
    function beforeFind($queryData) {
        $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
        if(!$mStoreCredit->isAllowCredit())
        {
            $queryData['conditions']['StorePayment.key_name !='] = ORDER_GATEWAY_CREDIT;
        }
        return $queryData;
    }
    
    public function loadStorePayment($id = null)
    {
        if($id > 0)
        {
            return $this->findById($id);
        }
        return $this->find('all');
    }
    
    public function isStorePaymentExist($id, $enable = null)
    {
        $cond = array(
            'StorePayment.id' => $id
        );
        if(is_bool($enable))
        {
            $cond['StorePayment.enable'] = $enable;
        }
        return $this->hasAny($cond);
    }
    
    public function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StorePayment.'.$task => $value
        ), array(
            'StorePayment.id' => $id,
        ));
    }
    
    public function checkRequireEnable()
    {
        $count = $this->find('count', array(
            'conditions' => array(
                'StorePayment.enable' => 1
            )
        ));
        if($count == 1)
        {
            return true;
        }
        return false;
    }
	
    public function checkStorePaymentExist($id = null, $key_name = null)
    {
        $cond = array();
        if($id > 0)
        {
            $cond = array(
                'StorePayment.id' => $id
            );
        }
        if($key_name != null)
        {
            $cond = array(
                'StorePayment.key_name' => $key_name
            );
        }
        if($cond != null)
        {
            return $this->hasAny($cond);
        }
        return false;
    }
    
    public function getList($id = null)
    {
        if($id > 0)
        {
            return $this->findByIdAndEnable($id, 1);
        }
        else
        {
            return $this->find('all', array(
                'conditions' => array(
                    'StorePayment.enable' => 1
                )
            ));
        }
    }
    
    public function getListName($ids = null, $key = 'id')
    {
        $cond = array('StorePayment.enable' => 1);
        if($ids != null)
        {
            $cond['StorePayment.id'] = $ids;
        }
        return $this->find('list', array(
            'conditions' => $cond,
            'fields' => array('StorePayment.'.$key, 'StorePayment.name')
        ));
    }
    
    public function getPaymentNameByKey($key)
    {
        $data = $this->findByKeyName($key);
        if($data != null)
        {
            return $data['StorePayment']['name'];
        }
        return null;
    }
    
    public function getStorePayments($store_id = null)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $payments = array();
        if(is_array($store_id))
        {
            $stores = $mStore->find('list', array(
                'conditions' => array(
                    'Store.id' => $store_id
                ),
                'fields' => array('Store.id', 'Store.payments')
            ));
            if($stores != null)
            {
                foreach($stores as $id => $store_payments)
                {
                    $payments[] = $store_payments;
                }
                $payments = implode(',', $payments);
                $payments = explode(',', $payments);
                $payments = array_unique($payments);
            }
        }
        else
        {
            $store = $mStore->findById($store_id);
            $payments = !empty($store['Store']['payments']) ? explode(',', $store['Store']['payments']) : array();
        }
        $cond = array(
            'StorePayment.enable' => 1
        );
        if($payments != null)
        {
            $cond['StorePayment.id'] = $payments;
            
        }
        return $this->find('all', array(
            'conditions' => $cond
        ));
    }
    
    public function checkStoreSupportPayment($store_id, $store_payment_id)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $store = $mStore->findById($store_id);
        if($store != null)
        {
            $payment_id = !empty($store['Store']['payments']) ? explode(',', $store['Store']['payments']) : array();
            if(in_array($store_payment_id, $payment_id))
            {
                return true;
            }
        }
        return false;
    }
    
    public function getPaymentByKey($key)
    {
        return $this->findByKeyName($key);
    }
}