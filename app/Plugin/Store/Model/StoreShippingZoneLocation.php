<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreShippingZoneLocation extends StoreAppModel
{
    public $validationDomain = 'store';
	public $validate = array(           
        'country_id' =>   array(   
            'rule' => array('checkCountryExist'),
            'message' => 'Please select country'
        ),
    );
    
    function checkCountryExist($id)
    {
        $mCountry = MooCore::getInstance()->getModel('Country');
        return $mCountry->hasAny(array(
            'Country.id' => $this->data['StoreShippingZoneLocation']['country_id']
        ));
    }
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreShippingZoneLocation']['store_id'] = Configure::read('store.store_id');
    }
	
    public function loadShippingZoneLocationList($store_shipping_zone_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'StoreShippingZoneLocation.store_id' => Configure::read('store.store_id'),
                'StoreShippingZoneLocation.store_shipping_zone_id' => $store_shipping_zone_id
            ),
            'fields' => array('StoreShippingZoneLocation.id', 'StoreShippingZoneLocation.country_id')
        ));
    }
}