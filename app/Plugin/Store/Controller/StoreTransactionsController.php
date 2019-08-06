<?php 
class StoreTransactionsController extends StoreAppController{
    public $components = array('Paginator');
    public $check_force_login = false;
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->url = STORE_MANAGER_URL.'transactions/';
        $this->set('url', $this->url);
        $this->admin_url = $this->request->base.'/admin/store/store_transactions/';
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.Store');
        $this->loadModel('Store.StoreTransaction');
    }
    
    ////////////////////////////////////////////////////////admin////////////////////////////////////////////////////////
    public function admin_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : null;
        
        //load transactions
        $store_transactions = $this->StoreTransaction->loadAdminPaging($this, $search);

        $this->set(array(
            'search' => $search,
            'store_transactions' => $store_transactions,
            'currency' => $this->Store->loadDefaultGlobalCurrency(),
            'title_for_layout' => __d('store', 'Store Transactions')
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
                if($this->StoreTransaction->isStoreTransactionExist($id))
                {
                    $this->StoreTransaction->activeField($id, $task, $value);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function admin_delete($id)
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                $this->StoreTransaction->delete($id);
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully deleted'), '/admin/store/store_transactions/');
    }
    
    ////////////////////////////////////////////////////////backend////////////////////////////////////////////////////////
    public function manager_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : null;
        
        //load transactions
        $store_transactions = $this->StoreTransaction->loadManagerPaging($this, $search);

        $this->set(array(
            'search' => $search,
            'store_transactions' => $store_transactions,
            'active_menu' => 'transactions',
            'title_for_layout' => __d('store', "Manage Transactions")
        ));
    }
}