<?php 
class StorePopupsController extends StoreAppController
{
    public $check_subscription = false;
	public $check_force_login = false;
    public $components = array('Store.MyCart');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Store.Store');
    }
    
    public function load_shortcut()
    {
        if(!MooCore::getInstance()->isMobile(null))
        {
            $this->autoRender = false;
            $store = $this->Store->findByUserId(Configure::read('store.uid'));
            $this->set(array(
                'total_quantity' => $this->MyCart->totalQuantity(),
                'store' => $store
            ));
            $this->render('Store.Elements/menustore');
        }
    }
    
    public function load_cart_balloon()
    {
        echo $this->MyCart->totalQuantity();
        exit;
    }
}