<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreShipping extends StoreAppModel
{
    public $validationDomain = 'store';
    public $recursive = 1; 
		
	public $validate = array(     
        'store_shipping_method_id' =>   array(   
            'rule' => array('checkShippingMethodExist'),
            'message' => 'Please select method'
        ),
        'store_shipping_zone_id' =>   array(   
            'rule' => array('checkShippingZoneExist'),
            'message' => 'Please select zone'
        ),
        'price' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please enter price'
        ),
    );
    
    function checkShippingZoneExist($id)
    {
        $mStoreShippingZone = MooCore::getInstance()->getModel('Store.StoreShippingZone');
        return $mStoreShippingZone->hasAny(array(
            'StoreShippingZone.id' => $this->data['StoreShipping']['store_shipping_zone_id']
        ));
    }
    
    function checkShippingMethodExist($id)
    {
        $mShippingMethod = MooCore::getInstance()->getModel('Store.StoreShippingMethod');
        return $mShippingMethod->hasAny(array(
            'StoreShippingMethod.id' => $this->data['StoreShipping']['store_shipping_method_id']
        ));
    }
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreShipping']['store_id'] = Configure::read('store.store_id');
    }
	
    public function loadShippingDetail($id, $store_id = 0)
    {
        $mShippingMethod = MooCore::getInstance()->getModel('Store.StoreShippingMethod');
        $cond = array(
            'StoreShipping.store_id' => $store_id == 0 ? Configure::read('store.store_id') : $store_id,
            'StoreShipping.id' => $id
        );

        $data = $this->find('first', array(
            'conditions' => $cond
        ));
        if($data != null)
        {
            $shipping_method = $mShippingMethod->findById($data['StoreShipping']['store_shipping_method_id']);
            $data['StoreShippingMethod'] = $shipping_method['StoreShippingMethod'];
        }
        return $data;
    }
    
	public function isShippingExist($id)
    {
        return $this->hasAny(array(
            'store_id' => Configure::read('store.store_id'),
            'id' => $id
        ));
    }
	
	public function activeField($id, $task, $value)
    {
        $mStoreShippingDetail = MooCore::getInstance()->getModel('Store.StoreShippingDetail');
        $detail = $mStoreShippingDetail->find('first', array(
            'conditions' => array(
                'StoreShippingDetail.store_id' => Configure::read('store.store_id'),
                'StoreShippingDetail.store_shipping_method_id' => $id,
            )
        ));
        $mStoreShippingDetail->create();
        $mStoreShippingDetail->id = $detail != null ? $detail['StoreShippingDetail']['id'] : '';
        $mStoreShippingDetail->save(array(
            "store_id" => Configure::read('store.store_id'),
            "store_shipping_method_id" => $id,
            "$task" => $value
        ));
    }
    
    public function getCountryList()
    {
        $mCountry = MooCore::getInstance()->getModel('Country');
        return $mCountry->find('list', array(
            'fields' => array('Country.id', 'Country.name')
        ));
    }
    
    public function loadShippingList($store_shipping_method_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'StoreShipping.store_id' => Configure::read('store.store_id'),
                'StoreShipping.store_shipping_method_id' => $store_shipping_method_id
            ),
            'fields' => array('StoreShipping.id', 'StoreShipping.store_shipping_zone_id')
        ));
    }
    
    public function loadShippings($store_shipping_method_id)
    {
        return $this->find('all', array(
            'conditions' => array(
                'StoreShipping.store_id' => Configure::read('store.store_id'),
                'StoreShipping.store_shipping_method_id' => $store_shipping_method_id
            )
        ));
    }
    
    public function loadShippingByLocation($store_id, $location_id, $store_shipping_id = null, $limit_weight = null)
    {
        $mStoreShippingZone = MooCore::getInstance()->getModel('Store.StoreShippingZone');
        $mStoreShippingZoneLocation = MooCore::getInstance()->getModel('Store.StoreShippingZoneLocation');
        $mStoreShippingMethod = MooCore::getInstance()->getModel('Store.StoreShippingMethod');
        $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
        $locations = $mStoreShippingZoneLocation->find('list', array(
            'conditions' => array(
                'StoreShippingZoneLocation.store_id' => $store_id,
                'StoreShippingZoneLocation.country_id' => $location_id,
                'StoreShippingZoneLocation.enable' => 1,
            ),
            'fields' => array('StoreShippingZoneLocation.id', 'StoreShippingZoneLocation.store_shipping_zone_id')
        ));
        if($locations == null)
        {
            return array();
        }
        
        //load zones
        $zones = $mStoreShippingZone->find('list', array(
            'conditions' => array(
                'StoreShippingZone.store_id' => $store_id,
                'StoreShippingZone.id' => $locations,
                'StoreShippingZone.enable' => 1,
            ),
            'fields' => array('StoreShippingZone.id')
        ));
        if($zones == null)
        {
            return array();
        }
        
        //load shippinds
        /*$this->bindModel(array(
            'belongsTo' => array('StoreShippingMethod' => array(
                'className' => 'Store.StoreShippingMethod',
                'foreignKey' => 'store_shipping_method_id',
                'dependent' => true
            ))
        ));*/
        $cond = array(
            'StoreShipping.store_id' => $store_id,
            'StoreShipping.store_shipping_zone_id' => $zones,
            'StoreShipping.enable' => 1,
        );
        $shippings = $this->find('all', array(
            'conditions' => $cond,
            'joins' => array(
                array(
                    'table' => 'store_shipping_details',
                    'alias' => 'StoreShippingDetail',
                    'type' => 'INNER',
                    'conditions' => array(
                        'StoreShippingDetail.store_id = StoreShipping.store_id',
                        'StoreShippingDetail.store_shipping_method_id = StoreShipping.store_shipping_method_id',
                        'StoreShippingDetail.enable = 1',
                    )
                )
            )
        ));
        if($shippings == null)
        {
            return array();
        }
        
        $data = array();
        foreach($shippings as $k => $shipping)
        {
            $shipping_method = $mStoreShippingMethod->findById($shipping['StoreShipping']['store_shipping_method_id']);
			if($shipping_method['StoreShippingMethod']['key_name'] == STORE_SHIPPING_WEIGHT && $limit_weight !== null && $shipping['StoreShipping']['weight'] < $limit_weight)
			{
				unset($shippings[$k]);
				continue;
			}
            $name = $shipping_method['StoreShippingMethod']['name'];
            $price_format = $storeHelper->formatMoney($shipping['StoreShipping']['price']);
            if($shipping_method['StoreShippingMethod']['key_name'] == STORE_SHIPPING_PER_ITEM)
            {
                $name .= ' ('.sprintf(__d('store', '%s/product'), $price_format).')';
            }
            if($shipping_method['StoreShippingMethod']['key_name'] == STORE_SHIPPING_WEIGHT)
            {
                $name .= ' ('.sprintf(__d('store', '%s from %s kg'), $price_format, $shipping['StoreShipping']['weight']).')';
            }
            $value = array(
                'id' => $shipping['StoreShipping']['id'],
                'name' => $name,
                'key_name' => $shipping_method['StoreShippingMethod']['key_name'],
                'price' => $shipping['StoreShipping']['price'],
                'weight' => $shipping['StoreShipping']['weight'],
            );
            if((int)$store_shipping_id > 0 && $store_shipping_id == $shipping['StoreShipping']['id'])
            {
                return $value;
            }
            else if($store_shipping_id == null)
            {
                $data[] = $value;
            }
        }
        return $data;
    }
    
    public function parseShippingCredit($data)
    {
        $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
        if($data == null)
        {
            return $data;
        }
        foreach($data as $k => $item)
        {
            $data[$k]['price'] = $storeHelper->exchangeToCredit($item['price']);
        }
        return $data;
    }

    public function calculateShippingPrice($store_id, $products, $key_name, $price, $weight)
    {
        if($key_name == STORE_SHIPPING_PER_ITEM)
        {
            $total_quantity = 0;
            foreach($products as $product)
            {
                $product = $product['StoreProduct'];
                $total_quantity += $product['quantity'];
            }
            $price = $price * $total_quantity;
        }
        if($key_name == STORE_SHIPPING_WEIGHT)
        {
            $total_weight = 0;
            foreach($products as $product)
            {
                $product = $product['StoreProduct'];
                $total_quantity += $product['quantity'];
                $total_weight += $product['weight'] * $product['quantity'];
            }
            if($weight == 0/* || $weight > 0 && $total_weight < $weight*/)
            {
                $price = 0;
            }
        }
        return $price;
    }
}