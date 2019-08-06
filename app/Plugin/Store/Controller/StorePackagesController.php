<?php 
class StorePackagesController extends StoreAppController{
    public $components = array('Paginator','Session', 'Store.MyCart');
    public $check_force_login = false;
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->admin_url = $this->request->base.'/admin/store/store_packages/';
        $this->set('admin_url', $this->admin_url);
        $this->feature_product_package_id = 1;
        $this->feature_store_package_id = 2;
        $this->loadModel('Store.Store');
        $this->loadModel('Store.StorePackage');
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Gateway');
        $this->loadModel('Store.StoreTransaction');
    }
    
    public function admin_index()
    {
        $store_packages = $this->StorePackage->loadStorePackage();
        
        $this->set(array(
            'store_packages' => $store_packages,
            'currency' => $this->Store->loadDefaultGlobalCurrency(),
            'title_for_layout' => __d('store', 'Store Packages')
        ));
    }
    
    public function admin_enable()
    {
        $this->active($this->request->data, 1, 'enable');
    }
    
    public function admin_disable()
    {
        $this->active($this->request->data, 0, 'enable');
    }

	private function active($data, $value, $task)
    {
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StorePackage->isStorePackageExist($id))
                {
                    $this->StorePackage->activeField($id, $task, $value);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function admin_create($id)
    {
        if(!$this->StorePackage->isStorePackageExist($id))
        {
            $this->_redirectError(__d('store', 'Package not found'), '/admin/store/store_package/');
        }
        else
        {
            $store_package = $this->StorePackage->findById($id);
            $this->set(array(
                'store_package' => $store_package,
                'currency' => $this->Store->loadDefaultGlobalCurrency(),
                'title_for_layout' => __d('store', 'Store Packages')
            ));
        }
    }
    
    public function admin_save()
    {
		$this->autoRender = false;
        $data = $this->request->data;
        if(!$this->StorePackage->isStorePackageExist($data['id']))
        {
            $this->_jsonError(__d('store', 'Package not found'));
        }
        else 
        {
            $this->StorePackage->id = $data['id'];
        }
        
        $this->StorePackage->set($data);
        $this->_validateData($this->StorePackage);

        if($this->StorePackage->save())
        {
            //show message
            $redirect = $this->admin_url;
            if($this->request->data['save_type'] == 1)
            {
                $redirect = $this->admin_url.'create/'.$this->StorePackage->id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }	
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));
	}
    
    ///////////////////////////////////////////featured product///////////////////////////////////////////
    public function manager_buy_featured_product($product_id) 
    {
        if(!Configure::read('Store.store_buy_featured_product'))
        {
            $this->_jsonError(__d('store', 'This feature does not exist'));
        }
        $paypal_type = Configure::read('Store.store_paypal_type');
        if($paypal_type == STORE_PAYPAL_TYPE_EXPRESS)
        {
            $gateway = $this->Gateway->findByPlugin('PaypalExpress');
        }
        else
        {
            $gateway = $this->Gateway->findByPlugin('PaypalAdaptive');
        }
        
        //check product
        if(!$this->StoreProduct->checkProductExist($product_id))
        {
            $this->_jsonError(__d('store', 'Product not found'));
        }
        else if (!$gateway) 
        {
            $this->_jsonError(__d('store', 'Payment gateway not found'));
        }
        
        //check pacakge
        $package = $this->StorePackage->loadStorePackage($this->feature_product_package_id);
        if($package == null || $package['StorePackage']['enable'] == 0) 
        {
            $this->_jsonError(__d('store', 'This package is not available to buy'));
        }
		else if($package['StorePackage']['price'] == 0)
		{
			$this->_jsonError(__d('store', 'Package price must be greater than zero. Please contact with site admin for more info.'));
		}
		else if($package['StorePackage']['period'] == 0)
		{
			$this->_jsonError(__d('store', 'Package period must be greater than zero. Please contact with site admin for more info.'));
		}
        
        $currency = $this->Store->loadDefaultGlobalCurrency();
        $product = $this->StoreProduct->loadOnlyProduct($product_id);
        $expiration_date = date('Y-m-d h:i:s', strtotime(date('Y-m-d H:i:s').' + '.$package['StorePackage']['period'].' day'));
        if($product['StoreProduct']['featured'] == 1 && strtotime($product['StoreProduct']['feature_expiration_date']) != null)
        {
            $expiration_date = date('Y-m-d h:i:s', strtotime($product['StoreProduct']['feature_expiration_date'].' + '.$package['StorePackage']['period'].' day'));
        }
        
        $data = array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'store_id' => $product['StoreProduct']['store_id'],
            'store_product_id' => $product['StoreProduct']['id'],                    
            'store_package_id' => $package['StorePackage']['id'],
            'gateway_id' => $gateway['Gateway']['id'],
            'item_name' => $product['StoreProduct']['name'],
            'amount' => $package['StorePackage']['price'],
            'period' => $package['StorePackage']['period'],
            'expiration_date' =>  $expiration_date,
            'currency' => $currency['Currency']['currency_code'],
            'currency_symbol' => $currency['Currency']['symbol']
        );
        if($this->StoreTransaction->save($data))
        {
            $plugin = $gateway['Gateway']['plugin'];
            $helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
            $this->_jsonSuccess("", false, array(
                "redirect" => $helperGateway->getUrlProcess() . '/Store_Store_Transaction/' . $this->StoreTransaction->id
            ));
        }
        else
        {
            $this->_jsonError(__d('store', 'Something went wrong, please try again'));
        }
    }
	
	public function cancel_featured_product($store_transaction_id)
	{
        $this->StoreTransaction->updateStatus($store_transaction_id, TRANSACTION_STATUS_CANCEL);
		$this->_redirectError(__d('store', 'Your featured product transaction has been cancelled'), '/stores/manager/products/');
	}
	
	public function success_featured_product()
	{
		$this->_redirectSuccess(__d('store', 'Transaction has been completed. Your product will be set featured or expiration date will be expanded in minutes.'), '/stores/manager/products/');
	}
    
    ///////////////////////////////////////////featured store///////////////////////////////////////////
    public function manager_buy_featured_store() 
    {
        $store_id = Configure::read('store.store_id');
        $paypal_type = Configure::read('Store.store_paypal_type');
        if($paypal_type == STORE_PAYPAL_TYPE_EXPRESS)
        {
            $gateway = $this->Gateway->findByPlugin('PaypalExpress');
        }
        else
        {
            $gateway = $this->Gateway->findByPlugin('PaypalAdaptive');
        }

        //check store
        if(!Configure::read('Store.store_buy_featured_store'))
        {
            $this->_jsonError(__d('store', 'This feature does not exist'));
        }
        else if(!$this->Store->checkStoreExist($store_id))
        {
            $this->_jsonError(__d('store', 'Store not found'));
        }
        else if (!$gateway)
        {
            $this->_jsonError(__d('store', 'Payment gateway not found'));
        }
        
        //check pacakge
        $package = $this->StorePackage->loadStorePackage($this->feature_store_package_id);
        if($package == null || $package['StorePackage']['enable'] == 0) 
        {
            $this->_jsonError(__d('store', 'This package is not available to buy'));
        }
		else if($package['StorePackage']['price'] == 0)
		{
			$this->_jsonError(__d('store', 'Package price must be greater than zero. Please contact with site admin for more info.'));
		}
		else if($package['StorePackage']['period'] == 0)
		{
			$this->_jsonError(__d('store', 'Package period must be greater than zero. Please contact with site admin for more info.'));
		}
        
        $currency = $this->Store->loadDefaultGlobalCurrency();
        $store = $this->Store->loadStoreDetail($store_id);
        $expiration_date = date('Y-m-d h:i:s', strtotime(date('Y-m-d H:i:s').' + '.$package['StorePackage']['period'].' day'));
        if($store['Store']['featured'] == 1 && strtotime($store['Store']['feature_expiration_date']) != null)
        {
            $expiration_date = date('Y-m-d h:i:s', strtotime($store['Store']['feature_expiration_date'].' + '.$package['StorePackage']['period'].' day'));
        }
        
        $data = array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'store_id' => $store['Store']['id'],
            'store_product_id' => '',                    
            'store_package_id' => $package['StorePackage']['id'],
            'gateway_id' => $gateway['Gateway']['id'],
            'item_name' => $store['Store']['name'],
            'amount' => $package['StorePackage']['price'],
            'period' => $package['StorePackage']['period'],
            'expiration_date' =>  $expiration_date,
            'currency' => $currency['Currency']['currency_code'],
            'currency_symbol' => $currency['Currency']['symbol']
        );
        if($this->StoreTransaction->save($data))
        {
            $plugin = $gateway['Gateway']['plugin'];
            $helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
            $this->_jsonSuccess("", false, array(
                "redirect" => $helperGateway->getUrlProcess() . '/Store_Store_Transaction/' . $this->StoreTransaction->id
            ));
        }
        else
        {
            $this->_jsonError(__d('store', 'Something went wrong, please try again'));
        }
    }
	
	public function cancel_featured_store($store_transaction_id)
	{
        $this->StoreTransaction->updateStatus($store_transaction_id, TRANSACTION_STATUS_CANCEL);
		$this->_redirectError(__d('store', 'Your featured store transaction has been cancelled'), '/stores/manager/');
	}
	
	public function success_featured_store()
	{
		$this->_redirectSuccess(__d('store', 'Transaction has been completed. Your store will be set featured or expiration date will be expanded in minutes.'), '/stores/manager/');
	}
}