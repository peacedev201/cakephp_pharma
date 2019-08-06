<?php
class StoreShippingMethodsController extends StoreAppController{
	public $components = array('Paginator', 'Store.MyCart');
	
	public function beforeFilter() {
        parent::beforeFilter();	
        $this->admin_url = $this->request->base.'/admin/store/store_shipping_methods/';
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.StoreShippingMethod');
    }
    
    public function admin_index()
    {
        $store_shipping_methods = $this->StoreShippingMethod->loadStoreShippingMethod();

        $this->set(array(
            'store_shipping_methods' => $store_shipping_methods,
            'title_for_layout' => __d('store', 'Store Shipping Methods')
        ));
    }
    
    public function admin_create($id)
    {
        if(!$this->StoreShippingMethod->isShippingMethodExist($id))
        {
            $this->_redirectError(__d('store', 'Payment not found'), '/admin/store/store_shipping_method/');
        }
        else
        {
            $store_shipping_method = $this->StoreShippingMethod->findById($id);
            $translate = $this->StoreShippingMethod->loadListTranslate($id);
            $this->set(array(
                'store_shipping_method' => $store_shipping_method,
                'translate' => $translate,
                'title_for_layout' => __d('store', 'Store Shipping Methods')
            ));
        }
    }
    
    public function admin_save()
    {
		$this->autoRender = false;
        $data = $this->request->data;
        if(!$this->StoreShippingMethod->isShippingMethodExist($data['id']))
        {
            $this->_jsonError(__d('store', 'Payment not found'));
        }
        else 
        {
            $this->StoreShippingMethod->id = $data['id'];
        }
        
        $this->StoreShippingMethod->set($data);
        $this->_validateData($this->StoreShippingMethod);

        if($this->StoreShippingMethod->save())
        {
            //show message
            $redirect = $this->admin_url;
            if($this->request->data['save_type'] == 1)
            {
                $redirect = $this->admin_url.'create/'.$this->StoreShippingMethod->id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }	
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));
	}
}