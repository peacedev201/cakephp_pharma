<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreShippingZone extends StoreAppModel
{
    public $validationDomain = 'store';
    public $recursive = 1; 
    public $hasMany = array(
        'StoreShippingZoneLocation'=> array(
            'className' => 'StoreShippingZoneLocation',
            'foreignKey' => 'store_shipping_zone_id',
            'dependent' => true
    ));
	public $validate = array(           
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide shipping zone name'
        )
    );
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreShippingZone']['store_id'] = Configure::read('store.store_id');
    }
	
    public function loadShippingZoneDetail($id)
    {
        $cond = array(
            'StoreShippingZone.store_id' => Configure::read('store.store_id'),
            'StoreShippingZone.id' => $id
        );
        
        return $this->find('first', array(
            'conditions' => $cond
        ));
    }
	
	public function loadManagerPaging($obj, $search = array())
    {       
        //pagination
        $cond = array(
            'StoreShippingZone.store_id' => Configure::read('store.store_id'),
        );
        
		if(!empty($search['keyword']))
        {
            $cond[] = "StoreShippingZone.name LIKE '%".$search['keyword']."%'";
        }
		
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => array('StoreShippingZone.id' => 'DESC'),
        );
        $data = $obj->paginate('StoreShippingZone');      
        
        return $data;
    }
	
	public function isShippingZoneExist($id)
    {
        return $this->hasAny(array(
            'store_id' => Configure::read('store.store_id'),
            'id' => $id
        ));
    }
    
    public function checkUsed($id)
    {
        $mStoreShipping = MooCore::getInstance()->getModel('Store.StoreShipping');
        $data = $mStoreShipping->find('count', array(
            'conditions' => array(
                'StoreShipping.store_shipping_zone_id' => $id
            )
        ));
        if($data > 0)
        {
            return true;
        }
        return false;
    }
	
	public function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StoreShippingZone.'.$task => $value
        ), array(
            'StoreShippingZone.store_id' => Configure::read('store.store_id'),
            'StoreShippingZone.id' => $id,
        ));
    }
    
    public function getShippingZoneList($store_id = 0)
    {
        $this->recursive = -1; 
        $cond = array();
        if($store_id > 0)
        {
            $cond['StoreShippingZone.store_id'] = $store_id;
        }
        return $this->find('list', array(
            'conditions' => $cond,
            'fields' => array('StoreShippingZone.id', 'StoreShippingZone.name')
        ));
    }
}