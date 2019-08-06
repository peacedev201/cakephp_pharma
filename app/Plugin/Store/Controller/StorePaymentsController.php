<?php 
class StorePaymentsController extends StoreAppController{
    public $components = array('Paginator','Session', 'Store.MyCart');
    public $check_force_login = false;
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->admin_url = $this->request->base.'/admin/store/store_payments/';
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.StorePayment');
        $this->loadModel('Store.Store');
    }
    
    public function admin_index()
    {
        $store_payments = $this->StorePayment->loadStorePayment();
        
        $this->set(array(
            'store_payments' => $store_payments,
            'currency' => $this->Store->loadDefaultGlobalCurrency(),
            'title_for_layout' => __d('store', 'Store Payments')
        ));
    }
    
    public function admin_create($id)
    {
        if(!$this->StorePayment->isStorePaymentExist($id))
        {
            $this->_redirectError(__d('store', 'Payment not found'), '/admin/store/store_payments/');
        }
        else
        {
            $store_payment = $this->StorePayment->findById($id);
            $translate = $this->StorePayment->loadListTranslate($id);
            $this->set(array(
                'store_payment' => $store_payment,
                'translate' => $translate,
                'currency' => $this->Store->loadDefaultGlobalCurrency(),
                'title_for_layout' => __d('store', 'Store Payments')
            ));
        }
    }
    
    public function admin_save()
    {
		$this->autoRender = false;
        $data = $this->request->data;
        if(!$this->StorePayment->isStorePaymentExist($data['id']))
        {
            $this->_jsonError(__d('store', 'Payment not found'));
        }
        else 
        {
            $this->StorePayment->id = $data['id'];
        }
        
        $this->StorePayment->set($data);
        $this->_validateData($this->StorePayment);

        if($this->StorePayment->save())
        {
            //show message
            $redirect = $this->admin_url;
            if($this->request->data['save_type'] == 1)
            {
                $redirect = $this->admin_url.'create/'.$this->StorePayment->id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }	
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));
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
                if($this->StorePayment->isStorePaymentExist($id))
                {
                    if($value == 0 && $this->StorePayment->checkRequireEnable())
                    {
                        $this->_redirectSuccess(__d('store', 'You must enable at least a payment'), $this->referer());
                    }
                    $this->StorePayment->activeField($id, $task, $value);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
}