<?php
class StoreShippingZonesController extends StoreAppController{
	public $components = array('Paginator');
	
	public function beforeFilter() {
        parent::beforeFilter();	
        if(!Configure::read('Store.store_enable_shipping'))
        {
            if($this->request->is('ajax'))
            {
                $this->_jsonError(__d('store', 'Page not found'));
            }
            else 
            {
                $this->_redirectError(__d('store', 'Page not found'), STORE_MANAGER_URL);
            }
        }
        $this->url = STORE_MANAGER_URL.'shipping_zones/';
        $this->set('url', $this->url);
        $this->loadModel('Store.StoreShippingZone');
        $this->loadModel('Store.StoreShippingZoneLocation');
    }
	
	public function manager_create($id = null){
        if(!empty($id) && !$this->StoreShippingZone->isShippingZoneExist($id))
        {
            $this->_redirectError(__d('store', 'Shipping not found'), $this->url.'manage');
        }
        else 
        {
            //load shipping_zone
            if (!empty($id))
            {
                $shipping_zone = $this->StoreShippingZone->loadShippingZoneDetail($id);
            }
            else 
            {
                $shipping_zone = $this->StoreShippingZone->initFields();
            }
            
            $this->set(array(
                'shipping_zone' => $shipping_zone,
                'active_menu' => 'create_shipping_zone',
                'title_for_layout' => !empty($id) ? __d('store', "Edit Shipping Zone") : __d('store', "Create Shipping Zone")
            ));
        }
	}	
	
	public function manager_save(){
        $data = $this->request->data;
        $isEdit = false;
        if((int)$data['id'] > 0 && $this->StoreShippingZone->isShippingZoneExist($data['id']))
        {
            $this->StoreShippingZone->id = $data['id'];
            $isEdit = true;
        }

        $this->StoreShippingZone->set($data);
        $this->_validateData($this->StoreShippingZone);
        $this->checkValidShippingZoneLocation($data);
        if($this->StoreShippingZone->save())
        {
            //save zone location
            $this->saveShippingZoneLocation($data, $this->StoreShippingZone->id);
            
            $redirect = $this->url.'';
            if($this->request->data['save_type'] == 1)
            {
                $redirect = $this->url.'create/'.$this->StoreShippingZone->id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }	
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));						
	}
    
    private function checkValidShippingZoneLocation($data){
        if(empty($data['country_id']))
        {
            $this->_jsonError(__d('store', 'Please add at least one country'));
        }
        else if(count(array_unique($data['country_id'])) < count($data['country_id']))
        {
            $this->_jsonError(__d('store', 'Duplicate country'));
        }
        else
        {
            foreach($data['country_id'] as $key => $country_id)
            {
                $this->StoreShippingZoneLocation->create();
                $this->StoreShippingZoneLocation->set(array(
                    'country_id' => $country_id
                ));
                $this->_validateData($this->StoreShippingZoneLocation);
            }
        }
    }
    
    private function saveShippingZoneLocation($data, $store_shipping_zone_id){
        $shipping_locations = $this->StoreShippingZoneLocation->loadShippingZoneLocationList($store_shipping_zone_id);
        if(!empty($data['country_id']))
        {
            foreach($data['country_id'] as $key => $country_id)
            {
                if($shipping_locations == null || !in_array($country_id, $shipping_locations))
                {
                    $this->StoreShippingZoneLocation->create();
                    $this->StoreShippingZoneLocation->save(array(
                        'store_shipping_zone_id' => $store_shipping_zone_id,
                        'country_id' => $country_id,
                        'enable' => $data['enable_location'][$key]
                    ));
                }
                else if(in_array($country_id, $shipping_locations))
                {
                    $this->StoreShippingZoneLocation->updateAll(array(
                        'enable' => $data['enable_location'][$key]
                    ), array(
                        'store_shipping_zone_id' => $store_shipping_zone_id,
                        'country_id' => $country_id,
                    ));
                }
            }
        }
        
        //delete old location
        if($shipping_locations != null)
        {
            foreach($shipping_locations as $shippind_zone_location_id => $country_id)
            {
                if($data['country_id'] == null || !in_array($country_id, $data['country_id']))
                {
                    $this->StoreShippingZoneLocation->delete($shippind_zone_location_id);
                }
            }
        }
    }
	
	public function manager_index(){
        //search
        $search = !empty($this->request->query) ? $this->request->query : '';			

        $shipping_zones = $this->StoreShippingZone->loadManagerPaging($this, $search);			
        $this->set(array(
            'shipping_zones' => $shipping_zones,
            'search' => $search,
            'active_menu' => 'manage_shipping_zones',
            'title_for_layout' => __d('store', "Manage Shipping Zones")
        ));
	}
    
    public function manager_delete()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreShippingZone->checkUsed($id))
                {
                    $this->_redirectError(__d('store', 'Can not delete this zone. It is being used by other items.'), $this->referer());
                }
                else if($this->StoreShippingZone->isShippingZoneExist($id))
                {
                    $this->StoreShippingZone->delete($id);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully deleted'), $this->referer());
    }
	
	 public function manager_enable()
    {		
        $this->active($this->request->data, 1, 'enable');
    }
    
    public function manager_disable()
    {
        $this->active($this->request->data, 0, 'enable');
    }
	
	private function active($data, $value, $task)
    {
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreShippingZone->isShippingZoneExist($id))
                {
                    $this->StoreShippingZone->create();
                    $this->StoreShippingZone->activeField($id, $task, $value);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
}