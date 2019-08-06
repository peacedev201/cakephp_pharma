<?php
class StoreShippingsController extends StoreAppController{
	public $components = array('Paginator', 'Store.MyCart');
	
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
        $this->url = STORE_MANAGER_URL.'shippings/';
        $this->set('url', $this->url);
        $this->loadModel('Store.StoreShipping');
        $this->loadModel('Store.StoreShippingMethod');
        $this->loadModel('Store.StoreShippingZone');
        $this->loadModel('Store.Cart');
    }
    
    public function manager_index(){
        $shipping_methods = $this->StoreShippingMethod->loadManagerPaging($this);

        $this->set(array(
            'shipping_methods' => $shipping_methods,
            'active_menu' => 'manage_shippings',
            'title_for_layout' => __d('store', "Manage Shippings")
        ));
	}
	
	public function manager_create($id = null){
        if(!$this->StoreShippingMethod->isShippingMethodExist($id))
        {
            $this->_redirectError(__d('store', 'Shipping method not found'), $this->url);
        }
        else 
        {
            //load shipping
            $shipping_method = $this->StoreShippingMethod->loadShippingMethodDetail($id);
            $shippings = $this->StoreShipping->loadShippings($id);
            
            $this->set(array(
                'shipping_method' => $shipping_method,
                'shippings' => $shippings,
                'active_menu' => 'manage_shippings',
                'title_for_layout' => !empty($id) ? __d('store', "Edit Shipping") : __d('store', "Create Shipping")
            ));
        }
	}	
	
	public function manager_save(){
        $data = $this->request->data;

        //check vlaid
        $this->checkValidShipping($data);
        
        //save shipping detail
        $this->StoreShipping->activeField($data['id'], 'enable', $data['enable']);
        
        $shippings = $this->StoreShipping->loadShippingList($data['id']);
        if(!empty($data['store_shipping_zone_id']))
        {
            foreach($data['store_shipping_zone_id'] as $key => $store_shipping_zone_id)
            {
                if($shippings == null || !in_array($store_shipping_zone_id, $shippings))
                {
                    $this->StoreShipping->create();
                    $this->StoreShipping->save(array(
                        'store_shipping_method_id' => $data['id'],
                        'store_shipping_zone_id' => $store_shipping_zone_id,
                        'price' => isset($data['price'][$key]) ? $data['price'][$key] : 0,
                        'weight' => isset($data['weight'][$key]) ? $data['weight'][$key] : 0,
                        'enable' => $data['enable_zone'][$key]
                    ));
                }
                else if(in_array($store_shipping_zone_id, $shippings))
                {
                    $this->StoreShipping->updateAll(array(
                        'price' => isset($data['price'][$key]) ? $data['price'][$key] : 0,
                        'weight' => isset($data['weight'][$key]) ? $data['weight'][$key] : 0,
                        'enable' => $data['enable_zone'][$key]
                    ), array(
                        'store_shipping_method_id' => $data['id'],
                        'store_shipping_zone_id' => $store_shipping_zone_id,
                    ));
                }
            }
        }
        
        //delete old zone
        if($shippings != null)
        {
            foreach($shippings as $shipping_id => $store_shipping_zone_id)
            {
                if(empty($data['store_shipping_zone_id']) || !in_array($store_shipping_zone_id, $data['store_shipping_zone_id']))
                {
                    $this->StoreShipping->delete($shipping_id);
                }
            }
        }
        
        $redirect = $this->url.'';
        if($this->request->data['save_type'] == 1)
        {
            $redirect = $this->url.'create/'.$data['id'];
        }
        $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
            'location' => $redirect
        ));
	}
    
    public function manager_load_order_shippings()
    {
        $data = $this->request->data;
        $shippings = $this->StoreShipping->loadShippingByLocation(Configure::read('store.store_id'), $data['country_id']);
        $this->set(array(
            'select_id' => $data['select_id'],
            'shippings' => $shippings
        ));
        $this->render('Store.Elements/manager_order_shipping');
    }
    
    private function checkValidShipping($data){
        if(empty($data['store_shipping_zone_id']))
        {
            $data['store_shipping_zone_id'] = array();
        }
        if(!$this->StoreShippingMethod->isShippingMethodExist($data['id']))
        {
            $this->_jsonError(__d('store', 'Shipping method not found'));
        }
        /*else if(empty($data['store_shipping_zone_id']))
        {
            $this->_jsonError(__d('store', 'Please add at least one zone'));
        }*/
        else if(count(array_unique($data['store_shipping_zone_id'])) < count($data['store_shipping_zone_id']))
        {
            $this->_jsonError(__d('store', 'Duplicate Zone'));
        }
        else
        {
            foreach($data['store_shipping_zone_id'] as $key => $store_shipping_zone_id)
            {
                $this->StoreShipping->create();
                $this->StoreShipping->set(array(
                    'store_shipping_method_id' => $data['id'],
                    'store_shipping_zone_id' => $store_shipping_zone_id,
                    'price' => isset($data['price'][$key]) ? $data['price'][$key] : 0,
                ));
                $this->_validateData($this->StoreShipping);
            }
        }
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
                if($this->StoreShippingMethod->isShippingMethodExist($id))
                {
                    $this->StoreShipping->activeField($id, $task, $value);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function load_order_shippings()
    {
        $this->autoRender = false;
        $data = $this->request->data;
        
        $cart = $this->MyCart->show(null);
        $cart = $this->Cart->loadCart($cart);
        if(!empty($cart['items']) && $data['country_id'] > 0)
        {
            foreach($cart['items'] as $cart_item)
            {
                $store = $cart_item['Store'];
                $products = $cart_item['Products'];
                $result[$store['id']] = $this->StoreShipping->loadShippingByLocation($store['id'], $data['country_id'], null, $store['total_weight']);
            }
        }
        echo json_encode($result);
        exit;
    }
}