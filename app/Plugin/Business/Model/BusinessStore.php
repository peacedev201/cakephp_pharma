<?php
class BusinessStore extends BusinessAppModel 
{
    public $validationDomain = 'business';
    
    public function isIntegrateStore()
    {
        if(CakePlugin::loaded("Store") && Configure::read('Store.store_integrate_business') && Configure::read('Store.store_enabled'))
        {
            return true;
        }
        return false;
    }
    
    public function hasStore($business_id)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        return $mStore->hasAny(array(
            'Store.business_id' => $business_id
        ));
    }

    public function getStoreFromBusinessId($business_id)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $store = $mStore->findByBusinessId($business_id);
        if ($store)
        {
            $store_id = $store['Store']['id'];
            $store = $mStore->loadStoreDetail($store_id);
            return $store;
        }
        return array();
    }
    
    public function removeBusinessFromStore($business_id)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->updateAll(array(
            "Store.business_id" => 0
        ), array(
            "Store.business_id" => $business_id
        ));
    }
}