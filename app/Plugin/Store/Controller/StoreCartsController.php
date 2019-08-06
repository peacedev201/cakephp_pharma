<?php 
class StoreCartsController extends StoreAppController{
    public $components = array('Store.MyCart');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.Cart');
        $this->loadModel('Store.StoreSetting');
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.Store');
    }
    
    function index()
    {
        if(!$this->Store->storePermission(STORE_PERMISSION_BUY_PRODUCT))
        {
            
            $this->_redirectError(__d('store', 'You don\'t have permission to view this page.'), STORE_URL);
        }
        $cart = $this->MyCart->show();
        $cart = $this->Cart->loadCart($cart);
        $warning_store = !empty($this->request->query['warning_store']) ? urldecode($this->request->query['warning_store']) : '';
        $store_list = array();
        if(!empty($cart['items']))
        {
            foreach($cart['items'] as $cart_item)
            {
                $store_list[$cart_item['Store']['id']] = $cart_item['Store']['name'];
            }
        }
        
        //for credits
        $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
        $setting_show_money_type = Configure::read('Store.store_show_money_type');
        $allow_credit = 0;
        if($mStoreCredit->isAllowCredit() && ($setting_show_money_type == STORE_SHOW_MONEY_TYPE_CREDIT || $setting_show_money_type == STORE_SHOW_MONEY_TYPE_ALL))
        {
            $allow_credit = 1;
        }

        $this->set(array(
            'cart' => $cart,
            'store_id' => "",
            'store_list' => $store_list,
            'warning_store' => $warning_store,
            'public_header' => true,
            'no_footer' => true,
            'title_for_layout' => __d('store', 'Carts'),
            'allow_credit' => $allow_credit,
            'setting_show_money_type' => $setting_show_money_type,
        ));
    }
    
    function load_cart_by_store()
    {
        $data = $this->request->data;
        $cart = $this->MyCart->show($data['store_id']);
        $cart = $this->Cart->loadCart($cart);

        $this->set(array(
            'cart' => $cart,
            'store_id' => $data['store_id'],
            'warning_store' => !empty($data['warning_store']) ? explode(',', $data['warning_store']) : array(),
        ));
        $this->render('Store.Elements/load_cart_by_store');
    }
    
    function add_to_cart()
    {
        $cuser = $this->_getUser();
        /*if (!$cuser) {
            $this->autoRender = false;
            echo json_encode(array(
                'result' => 'login',
                'message' => __d('store', 'Please login or signup to buy products.')
            ));
            exit;
        }*/
        
        if(!$this->Store->storePermission(STORE_PERMISSION_BUY_PRODUCT))
        {
            $this->_jsonError(__d('store', 'You don\'t have permission to buy product.'), null, array(
                'redirect' => STORE_URL
            ));
        }
        $product_id = $this->request->data['id'];
        $quantity = (int)$this->request->data['quantity'] > 0 ? $this->request->data['quantity'] : 1;
        $product = $this->StoreProduct->loadProductDetail($product_id, false, false, '', 1, 1);
        $attribute_id = !empty($this->request->data['attribute_id']) ? $this->request->data['attribute_id'] : null;

        if($product == null)
        {
            $this->_jsonError(__d('store', 'Product does not exist'));
        }
        else if($product['StoreProduct']['out_of_stock'])
        {
            $this->_jsonError(__d('store', 'This product is not available to buy'));
        }
        else if($product['StoreProduct']['attribute_to_buy'] && 
                (($attribute_name = $this->Cart->validCartAttribute($product_id, $attribute_id)) !== true))
        {
            $this->_jsonError(__d('store', 'Please select ').$attribute_name);
        }
        else 
        {
            $this->MyCart->add($product['StoreProduct']['store_id'], $product_id, $quantity, $attribute_id);

            $this->set(array(
                'product' => $product
            ));  
            if(isset($this->request->data['quickview']))
            {
                $this->_jsonSuccess(__d('store', 'Product was added to cart'));
            }
            $this->render('Store.Elements/add_to_cart');
        }
    }
    
    function update_cart()
    {
        $data = $this->request->data;
        if(!empty($data['cart']))
        {
            foreach($data['cart'] as $cart_id => $quantity)
            {
                $this->MyCart->updateQuantity($cart_id, $quantity);
            }
        }
        
        $this->_jsonSuccess(__d('store', 'Cart has been updated'));
    }
    
    function clear_cart()
    {
        $this->MyCart->clearAll();
        
        $this->_jsonSuccess(__d('store', 'Cart has been cleared'));
    }
    
    function remove_cart_item()
    {
        $store_id = $this->request->data['store_id'];
        $cart_id = $this->request->data['id'];
        $this->MyCart->clear($store_id, $cart_id);
        $this->_jsonSuccess(__d('store', 'Item has been removed'));
    }
}