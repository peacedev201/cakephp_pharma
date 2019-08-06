<?php

class StoreOrdersController extends StoreAppController
{

    public $components = array('Paginator', 'Session', 'Store.MyCart');
    public $check_force_login = false;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->url = STORE_MANAGER_URL . 'orders/';
        $this->set('url', $this->url);
        $this->admin_url = $this->request->base.'/admin/store/store_orders/';
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.Store');
        $this->loadModel('Store.Cart');
        $this->loadModel('Store.StoreOrder');
        $this->loadModel('Store.StoreOrderDetail');
        $this->loadModel('Store.StoreSetting');
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.StorePayment');
        $this->loadModel('Store.StoreShipping');
        $this->loadModel('Store.StoreDigitalProduct');
        $this->loadModel('PaymentGateway.Gateway');
    }

    ////////////////////////////////////////////////////////admin////////////////////////////////////////////////////////
    public function admin_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : array();

        $orders = $this->StoreOrder->loadManagerPaging($this, $search, true);
        list($site_profit, $site_profit_credit) = $this->StoreOrder->totalSiteProfit();
        
        $this->set(array(
            'orders' => $orders,
            'search' => $search,
            'site_profit' => $site_profit,
            'site_profit_credit' => $site_profit_credit,
            'order_statuses' => $this->StoreOrder->loadOrderStatusList(),
            'title_for_layout' => __d('store', "Orders")
        ));
    }

    ////////////////////////////////////////////////////////backend////////////////////////////////////////////////////////
    public function manager_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : '';

        //$producers = $this->StoreProducer->getProducer($cond);			
        $orders = $this->StoreOrder->loadManagerPaging($this, $search);
        $this->set(array(
            'orders' => $orders,
            'search' => $search,
            'active_menu' => 'manage_orders',
            'order_statuses' => $this->StoreOrder->loadOrderStatusList(),
            'title_for_layout' => __d('store', "Manage Orders")
        ));
    }

    public function manager_create($id = null)
    {
        $except_product_ids = '';
        if ($this->StoreOrder->checkOrderExist($id))//editing		
        {
            $order = $this->StoreOrder->getOrderDetai($id, Configure::read('store.store_id'));
            $this->set('title_for_layout', __d('store', 'Edit Order'));
            $except_product_ids = $this->StoreOrderDetail->loadProductIdList($id);
            $except_product_ids = !empty($except_product_ids) ? implode(',', $except_product_ids) : '';
        }
        else //create new
        {
            $order = $this->StoreOrder->initFields();
            $this->set('title_for_layout', __d('store', 'Create Order'));
        }

        $this->set(array(
            'order' => $order,
            'order_status' => $this->StoreOrder->loadOrderStatusList(),
            'payments' => $this->StorePayment->getList(),
            'active_menu' => 'create_order',
            'except_product_ids' => $except_product_ids,
            'title_for_layout' => !empty($id) ? __d('store', "Edit Order") : __d('store', "Create Order")
        ));
    }

    public function manager_add_order_details()
    {
        $this->autoRender = false;
        if ($this->request->is('post'))
        {
            $this->StoreOrderDetail->set($this->request->data);
            $this->_validateData($this->StoreOrderDetail);
            if ($this->StoreOrderDetail->save())
            {
                //update amount of order
                $order = $this->StoreOrder->findById($this->request->data['order_id']);
                $order_amount = $order['StoreOrder']['amount'] + $this->request->data['amount'];
                $this->StoreOrder->updateAll(array('StoreOrder.amount' => $order_amount), array('StoreOrder.id' => $this->request->data['order_id']));
                $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                    'location' => $this->referer()
                ));
            }
        }
    }

    public function manager_save()
    {
        $this->autoRender = false;
        $data = $this->request->data;
        $data['currency'] = Configure::read('store.currency_code');
        $data['currency_symbol'] = Configure::read('store.currency_symbol');
        $data['currency_position'] = Configure::read('store.currency_position');
        $data['store_id'] = Configure::read('store.store_id');
        if ((int) $data['id'] > 0 && $this->StoreOrder->checkOrderExist($data['id']))
        {
            $this->StoreOrder->id = $data['id'];
        }
        else
        {
            $data['user_id'] = MooCore::getInstance()->getViewer(true);
        }
        /* if($data['payment'] != ORDER_GATEWAY_PAYPAL)
          {
          $data['transaction_id'] = '';
          } */

        //shipping
        $shipping_fee = 0;
        $store_shipping_id = isset($data['store_shipping_id']) ? $data['store_shipping_id'] : '';
        if (!$this->checkValidShipping($data['store_id'], $data['shipping_country_id'], $store_shipping_id))
        {
            $this->_jsonError(__d('store', 'Please select shipping'));
        }
        if ((int) $store_shipping_id > 0)
        {
            $shipping = $this->StoreShipping->loadShippingByLocation($data['store_id'], $data['shipping_country_id'], $store_shipping_id);
            if ($shipping != null)
            {
                $data['store_shipping_id'] = $shipping['id'];
                $data['shipping_fee'] = $shipping['price'];
                $data['shipping_description'] = $shipping['name'];
                $shipping_fee = $shipping['price'];
            }
        }

        //valid product
        if (empty($data['product_id']))
        {
            $this->_jsonError(__d('store', 'Please add at least a product'));
        }

        $this->StoreOrder->set($data);
        $this->_validateData($this->StoreOrder);
        if ($this->StoreOrder->save($data))
        {
            $order_id = $this->StoreOrder->id;

            //save order code
            $this->StoreOrder->updateOrderCode($order_id);

            //save detail
            $total_amount = 0;
            $products = $this->StoreProduct->loadProductByListId($data['product_id']);
            if ($products != null)
            {
                foreach ($products as $k => $product)
                {
                    $product = $product['StoreProduct'];
                    $quantity = $data['quantity'][$product['id']];
                    $products[$k]['StoreProduct']['quantity'] = $quantity;
                    $total_price = $product['price'] * $quantity;
                    $total_amount += $total_price;
                    $products[$k]['StoreProduct']['total_price'] = $total_price;
                }
                $total_amount += $shipping_fee;
            }

            $this->StoreOrderDetail->deleteByOrderId($order_id);
            $this->save_order_detail($order_id, $products);

            //update amount
            $this->StoreOrder->updateOrderAmount($order_id, $total_amount);

            //add or remove digital product to list for user
            $order = $this->StoreOrder->getOrderDetai($order_id);
            if ($order['StoreOrder']['order_status'] == ORDER_STATUS_PROCESSING || $order['StoreOrder']['order_status'] == ORDER_STATUS_COMPLETED)
            {
                $this->addDigitalProduct($order);
            }
            else
            {
                $this->removeDigitalProduct($order);
            }

            //show message
            $redirect = STORE_MANAGER_URL . 'orders';
            if ($this->request->data['save_type'] == 1)
            {
                $redirect = STORE_MANAGER_URL . 'orders/create/' . $this->StoreOrder->id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));
    }

    public function manager_delete()
    {
        $data = $this->request->data;
        if (!empty($data['cid']))
        {
            foreach ($data['cid'] as $id)
            {
                if ($this->StoreOrder->checkOrderExist($id))
                {
                    $this->StoreOrder->delete($id);
                }
            }
        }
        $this->Session->setFlash(__d('store', 'Successfully deleted'));
        $this->redirect($this->referer());
    }

    public function manager_change_order_status()
    {
        $data = $this->request->data;
        if (!$this->StoreOrder->checkOrderExist($data['order_id']))
        {
            $this->_jsonError(__d('store', 'Order not found'));
        }
        else
        {
            $this->StoreOrder->id = $data['order_id'];
            if ($this->StoreOrder->save(array(
                        'order_status' => $data['status']
                    )))
            {
                //add or remove digital product to list for user
                $order = $this->StoreOrder->getOrderDetai($data['order_id']);
                if ($order['StoreOrder']['order_status'] == ORDER_STATUS_PROCESSING || $order['StoreOrder']['order_status'] == ORDER_STATUS_COMPLETED)
                {
                    $this->addDigitalProduct($order);
                }
                else
                {
                    $this->removeDigitalProduct($order);
                }
                $this->_jsonSuccess(__d('store', 'Status has been changed.'));
            }
            $this->_jsonError(__d('store', 'Cannot change status, please try again.'));
        }
    }

    public function manager_order_detail($order_id)
    {
		$user = $this->_getUser();
		$all = false;
		if(!empty($user['Role']['is_admin']) && $user['Role']['is_admin'] == 1)
		{
			$all = true;
		}
        if (!$this->StoreOrder->checkOrderExist($order_id, null, null, $all))
        {
            $this->_jsonError(__d('store', 'Order not found'));
        }
        else
        {
			$sport_id = Configure::read('store.store_id');
			if($all)
			{
				$store_id = null;
			}
            $order = $this->StoreOrder->getOrderDetai($order_id, $store_id);

            $this->set(array(
                'order' => $order
            ));
            $this->render('Store.Elements/order_detail_dialog');
        }
    }

    public function print_order($order_id)
    {
        $this->autoLayout = false;
        $this->autoRender = false;
        $order = $this->StoreOrder->getOrderDetai($order_id, Configure::read('store.store_id'));
        header("Content-Type: text/html; charset=UTF-8;");
        echo $this->StoreOrder->getPrintOrderTemplate($order);
        echo '<script type="text/javascript">window.print()</script>';
        exit;
    }

    ////////////////////////////////////////////////////////frontend////////////////////////////////////////////////////////
    /* function index()
      {
      $noPermission = false;
      if(!$this->Store->storePermission(STORE_PERMISSION_BUY_PRODUCT))
      {
      $noPermission = true;
      }
      $this->set(array(
      'noPermission' => $noPermission
      ));
      } */

    public function order_detail($order_id)
    {
        if (!$this->StoreOrder->checkOrderExist($order_id, null, MooCore::getInstance()->getViewer(true)))
        {
            $this->_jsonError(__d('store', 'Order not found'));
        }
        else
        {
            $order = $this->StoreOrder->getOrderDetai($order_id, null, MooCore::getInstance()->getViewer(true));

            $this->set(array(
                'order' => $order
            ));
            $this->render('Store.Elements/order_detail_dialog');
        }
    }

    function my_order_list()
    {
        $orders = $this->StoreOrder->getCurrentUserOrders($this);
        $this->set(array(
            'orders' => $orders,
        ));
        $this->render('Store.Elements/list/my_order_list');
    }

    function checkout($store_id = null)
    {
        $cart = $this->MyCart->show($store_id);
        if ($cart == null)
        {
            $this->_redirectError(__d('store', 'No products to checkout'), STORE_URL . 'carts/');
        }
        else
        {
            $cart = $this->Cart->loadCart($cart);
            $store_ids = array();
            if (!empty($cart['items']))
            {
                foreach ($cart['items'] as $store)
                {
                    if (empty($store['Products']))
                    {
                        $this->_redirectError(__d('store', 'No products to checkout for ') . $store['Store']['name'], STORE_URL . 'carts/');
                    }
                    else if (($valid_product = $this->Cart->validProductCart($store['Products'])) != null)
                    {
                        $this->_redirectError(__d('store', 'Some products are not available or out of stock, please remove out of cart: ') . implode(', ', $valid_product), STORE_URL . 'carts/');
                    }
                    $store_ids[] = $store['Store']['id'];
                }
            }

            //for credits
            $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
            $setting_show_money_type = Configure::read('Store.store_show_money_type');
            $allow_credit = 0;
            if ($mStoreCredit->isAllowCredit() && ($setting_show_money_type == STORE_SHOW_MONEY_TYPE_CREDIT || $setting_show_money_type == STORE_SHOW_MONEY_TYPE_ALL))
            {
                $allow_credit = 1;
            }

            $this->set(array(
                'cart' => $cart,
                'store_id' => $store_id,
                'store_payments' => $this->StorePayment->getStorePayments($store_ids),
                'title_for_layout' => __d('store', 'Billing & Payment'),
                'allow_credit' => $allow_credit,
                'setting_show_money_type' => $setting_show_money_type,
            ));
        }
    }

    function do_checkout()
    {
        $data = $this->request->data;

        //check store exist
        if (!$this->Store->storePermission(STORE_PERMISSION_BUY_PRODUCT))
        {
            $this->_jsonError(__d('store', 'You don\'t have permission to buy product.'));
        }

        $cart = $this->MyCart->show($data['store_id']);
        $store_shipping_id = !empty($data['store_shipping_id']) ? $data['store_shipping_id'] : array();
        $stores = $this->Cart->loadCart($cart, $store_shipping_id);
        $store_payment = $this->StorePayment->getList($data['store_payment_id']);
        $paypal_data = $list_order_id = array();
        if (empty($stores['items']))
        {
            $this->_jsonError(__d('store', 'No products to checkout, please try again!'));
        }
        else if ($store_payment == null)
        {
            $this->_jsonError(__d('store', 'Payment method not found! Please try again.'));
        }
        else if (in_array($store_payment['StorePayment']['key_name'], array(ORDER_GATEWAY_PAYPAL, ORDER_GATEWAY_PAYPAL_STORE)) && !$this->Store->checkPaypalGatewayInfo())
        {
            $this->_jsonError(__d('store', 'Paypal config is empty, please contact site admin for more details.'));
        }
        else if (in_array($store_payment['StorePayment']['key_name'], array(ORDER_GATEWAY_PAYPAL, ORDER_GATEWAY_PAYPAL_STORE)) && !$stores['amount_zero_online_transaction'])
        {
            $this->_jsonError(__d('store', 'To make an online transaction, order total for each store must be greater than 0'));
        }
        else
        {
            //valid credit
            if ($store_payment['StorePayment']['key_name'] == ORDER_GATEWAY_CREDIT)
            {
                $this->validCreditCheckout($stores);
            }

            $data['store_payment_id'] = $store_payment['StorePayment']['id'];
            if ($data['store_id'] > 0)
            {
                if (!$this->StorePayment->checkStoreSupportPayment($data['store_id'], $data['store_payment_id']))
                {
                    $this->_jsonError(__d('store', 'This store does not support this payment. Please try another one.'));
                }
            }
            else
            {
                $no_support_normal_payment = $no_support_online_payment = array();
                $cPaypalParallel = MooCore::getInstance()->getComponent('Store.PaypalParallel');
                foreach ($stores['items'] as $store)
                {
                    if (!$this->StorePayment->checkStoreSupportPayment($store['Store']['id'], $data['store_payment_id']))
                    {
                        if ($store_payment['StorePayment']['is_online'] == 1 && (empty($store['Store']['paypal_email']) ||
                                (!empty($store['Store']['paypal_email']) && !$cPaypalParallel->checkAccountExist($store['Store']['paypal_email']))))
                        {
                            $no_support_online_payment[$store['Store']['id']] = $store['Store']['name'];
                        }
                        else
                        {
                            $no_support_normal_payment[$store['Store']['id']] = $store['Store']['name'];
                        }
                    }
                }
                $warning_message = $warning_name = $warning_store = '';
                if ($no_support_normal_payment != null)
                {
                    $warning_name = implode(', ', $no_support_normal_payment);
                    $warning_store = implode(',', $no_support_normal_payment);
                    $warning_message = __d('store', 'The following stores do not accept %s: %s. Please select the correct payment option or remove items to continue. Click %s to get back to cart.');
                }
                else if ($no_support_online_payment != null)
                {
                    $warning_name = implode(', ', $no_support_online_payment);
                    $warning_store = implode(',', $no_support_online_payment);
                    $warning_message = __d('store', 'The following stores do not accept %s yet: %s. Please select the correct payment option or remove items to continue. Click %s to get back to cart.');
                }
                if ($warning_message != '')
                {
                    $warning_store = '?warning_store=' . urlencode($warning_store);
                    $this->_jsonError(sprintf($warning_message, $store_payment['StorePayment']['name'], $warning_name, '<a href="' . $this->request->base . '/stores/carts/' . $warning_store . '">' . __d('store', 'here') . '</a>'));
                }
            }

            //save order
            foreach ($stores['items'] as $k => $store)
            {
                list($order_id, $total_amount) = $this->each_store_checkout($data, $store);
                if ($order_id > 0)
                {
                    $list_order_id[] = $order_id;
                    $paypal_data['receivers'][] = array(
                        'request_id' => $order_id,
                        'amount' => $total_amount,
                        'email' => $store['Store']['paypal_email'],
                        'description' => $store['Store']['name'],
                    );
                }
            }
        }

        //payment
        switch ($store_payment['StorePayment']['key_name'])
        {
            case ORDER_GATEWAY_PAYPAL:
            case ORDER_GATEWAY_PAYPAL_STORE:
                $paypal_type = Configure::read('Store.store_paypal_type');
                if ($paypal_type == STORE_PAYPAL_TYPE_EXPRESS)
                {
                    $url = $this->paypal_express_checkout($paypal_data['receivers'], $data['store_id']);
                }
                else
                {
                    $url = $this->paypal_checkout($paypal_data, $list_order_id, $data['store_id']);
                }
                if ($url !== false)
                {
                    $this->_jsonSuccess(__d('store', 'Valid paypal.'), false, array(
                        'url' => $url
                    ));
                }
                $this->_jsonError(__d('store', 'Something went wrong, please try again.'));
                break;
            case ORDER_GATEWAY_CREDIT:
                $this->doCreditCheckout($list_order_id);
                break;
            default :
                $this->Session->write('checkout_complete', 1);
                if ($list_order_id != null)
                {
                    foreach ($list_order_id as $order_id)
                    {
                        $order = $this->StoreOrder->getOrderDetai($order_id);

                        //send notification
                        if ($order['Store'] != null)
                        {
                            $store = $order['Store'];
                            $this->MyCart->clear($store['id']);
                            $this->Store->sendNotification($store['user_id'], $store['user_id'], 'create_order', '/stores/manager/orders/', '', 'Store');
                            $this->StoreOrder->sendOrderEmail($order_id, $store['id']);
                        }
                    }
                }

                $this->_jsonSuccess(__d('store', 'Successfully checkout.'), false, array(
                    'url' => STORE_URL . 'orders/checkout_complete/'
                ));
        }
    }

    private function checkValidShipping($store_id, $country_id, $store_shipping_id)
    {
        $shippings = $this->StoreShipping->loadShippingByLocation($store_id, $country_id);
        if ($shippings != null)
        {
            foreach ($shippings as $shipping)
            {
                if ($shipping['id'] == $store_shipping_id)
                {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    private function each_store_checkout($data, $store)
    {
        $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
        $products = $store['Products'];
        $store = $store['Store'];
        $data['store_id'] = $store['id'];
        $data['user_id'] = MooCore::getInstance()->getViewer(true);
        $data['amount'] = $store['total_price'];
        $data['amount_credit'] = $mStoreCredit->exchangeToCredit($data['amount']);
        $data['site_profit'] = $store['site_profit'];
        $data['site_profit_credit'] = $mStoreCredit->exchangeToCredit($data['site_profit']);
        $data['currency'] = Configure::read('store.currency_code');
        $data['currency_symbol'] = Configure::read('store.currency_symbol');
        $data['currency_position'] = Configure::read('store.currency_position');
        if (!isset($data['ship_to_different_address']))
        {
            $data['shipping_email'] = $data['billing_email'];
            $data['shipping_first_name'] = $data['billing_first_name'];
            $data['shipping_last_name'] = $data['billing_last_name'];
            $data['shipping_company'] = $data['billing_company'];
            $data['shipping_phone'] = $data['billing_phone'];
            $data['shipping_address'] = $data['billing_address'];
            $data['shipping_city'] = $data['billing_city'];
            $data['shipping_postcode'] = $data['billing_postcode'];
            $data['shipping_country_id'] = $data['billing_country_id'];
        }

        //shipping
        $store_shipping_id = isset($data['store_shipping_id'][$store['id']]) ? $data['store_shipping_id'][$store['id']] : 0;
        if ((int) $data['shipping_country_id'] > 0 && $store_shipping_id > 0)
        {
            //check valid shipping

            if (!$this->checkValidShipping($store['id'], $data['shipping_country_id'], $store_shipping_id))
            {
                $this->_jsonError(sprintf(__d('store', 'Please select shipping for store %s'), $store['name']));
            }

            //shipping data
            $shipping = $this->StoreShipping->loadShippingByLocation($store['id'], $data['shipping_country_id'], $store_shipping_id);
            if ($shipping != null)
            {
                $data['store_shipping_id'] = $shipping['id'];
                $data['shipping_fee'] = $this->StoreShipping->calculateShippingPrice($store['id'], $products, $shipping['key_name'], $shipping['price'], $shipping['weight']);
                $data['shipping_fee_credit'] = $mStoreCredit->exchangeToCredit($data['shipping_fee']);
                $data['shipping_description'] = $shipping['name'];
                $data['amount'] += $data['shipping_fee'];
                $data['amount_credit'] = $mStoreCredit->exchangeToCredit($data['amount']);
                $data['site_profit'] = $store['site_profit'] + $this->parseSiteProfit($data['shipping_fee']);
                $data['site_profit_credit'] = $data['site_profit_credit'] + $this->parseSiteProfit($data['shipping_fee_credit']);
            }
            else
            {
                $data['store_shipping_id'] = '';
            }
        }
        else
        {
            $data['store_shipping_id'] = '';
        }

        //check product exist
        if (empty($products))
        {
            $this->_jsonError(sprintf(__d('store', 'Error! There are no products to checkout for store: %s.'), $store['name']));
        }
        else if (($valid_product = $this->Cart->validProductCart($products)) != null)
        {
            $this->_jsonError(__d('store', 'Some products are not available or out of stock, please remove out of cart: ') . implode(', ', $valid_product));
        }
        else
        {
            $this->StoreOrder->create();
            $this->StoreOrder->set($data);
            $this->_validateData($this->StoreOrder);
            if ($this->StoreOrder->save($data))
            {
                $store_payment = $this->StorePayment->getList($data['store_payment_id']);
                $order_id = $this->StoreOrder->id;

                //save order code
                $this->StoreOrder->updateOrderCode($order_id);

                //save detail
                $this->save_order_detail($order_id, $products);

                //payment
                if ($store_payment['StorePayment']['is_online'])
                {
                    $this->StoreOrder->updateOrderTransaction($order_id, $store_payment['StorePayment']['id'], ORDER_STATUS_NEW);
                }
                else
                {
                    $this->StoreOrder->updateOrderTransaction($order_id, $store_payment['StorePayment']['id'], ORDER_STATUS_NEW);
                }
                return array($order_id, $data['amount']);
            }
        }
        return array(0, 0);
    }

    private function save_order_detail($order_id, $data)
    {
        $result = array();
        if ($data != null)
        {
            $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
            foreach ($data as $product)
            {
                $product = $product['StoreProduct'];
                $this->StoreOrderDetail->create();
                $this->StoreOrderDetail->save(array(
                    'order_id' => $order_id,
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'product_code' => $product['product_code'],
                    'quantity' => $product['quantity'],
                    'price' => !empty($product['new_price']) ? $product['new_price'] : $product['price'],
                    'amount' => $product['total_price'],
                    'amount_credit' => $mStoreCredit->exchangeToCredit($product['total_price']),
                    'attributes' => !empty($product['attributes']) ? implode(', ', $product['attributes']) : '',
                    'attribute_ids' => !empty($product['attributes']) ? implode(', ', array_keys($product['attributes'])) : ''
                ));
                $result[] = array(
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'amount' => !empty($product['new_price']) ? $product['new_price'] : $product['price']
                );
            }
        }
        return $result;
    }

    public function checkout_complete()
    {
        $complete = $this->Session->read('checkout_complete');
        if ($complete != 1)
        {
            $this->_redirectError(null, STORE_URL . 'products/');
        }
        $this->reset_order_session();
    }

    /////checkout with paypal
    public function cancel_checkout()
    {
        $this->_redirectError(__d('store', 'You checkout was cancelled.'), STORE_URL);
    }

    public function return_checkout($store_id = null)
    {
        if ($this->Session->read('paypal_checkout'))
        {
            if ($store_id > 0)
            {
                $this->MyCart->clear($store_id);
            }
            else
            {
                $this->MyCart->clearAll();
            }
            $this->Session->write('checkout_complete', 1);
            $this->_redirectSuccess(__d('store', 'You transaction was completed and your order is being processed.'), STORE_URL . 'orders/checkout_complete');
        }
        $this->_redirectError(__d('store', 'Page not found.'), '/404');
    }

    private function reset_order_session()
    {
        $this->Session->delete('paypal_checkout');
        $this->Session->delete('checkout_complete');
    }

    private function paypal_checkout($data, $list_order_id, $store_id = null)
    {
        $data['memo'] = implode('|', $list_order_id);
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        $data['cancel_url'] = $http . '://' . $_SERVER['SERVER_NAME'] . Router::url('/') . 'stores/orders/cancel_checkout';
        $data['return_url'] = $http . '://' . $_SERVER['SERVER_NAME'] . Router::url('/') . 'stores/orders/return_checkout/' . $store_id;
        $data['callback_url'] = $http . '://' . $_SERVER['SERVER_NAME'] . Router::url('/') . 'stores/orders/callback';
        $cPaypalParallel = MooCore::getInstance()->getComponent('Store.PaypalParallel');

        //site profit
        $data = $cPaypalParallel->parseSiteProfit($data);

        $result = $cPaypalParallel->checkout($data);
        if ($result['status'] == 1)
        {
            $this->Session->write('paypal_checkout', 1);
            return $result['url'];
        }
        else
        {
            if ($list_order_id != null)
            {
                foreach ($list_order_id as $order_id)
                {
                    $this->StoreOrder->delete($order_id);
                }
            }
            $this->_jsonError($result['message']);
        }
        return false;
    }

    public function callback()
    {
        $cPaypalParallel = MooCore::getInstance()->getComponent('Store.PaypalParallel');
        $data = $cPaypalParallel->callback();
        $count = 0;
        if (!empty($data['memo']) && $data['status'] == 'COMPLETED')
        {
            $data['memo'] = explode('|', $data['memo']);
            if ($data['memo'] != null)
            {
                foreach ($data['memo'] as $k => $order_id)
                {
                    $transaction_key = 'transaction[' . $k . ']';
                    if (!empty($data[$transaction_key . '.status']) && $data[$transaction_key . '.status'] == 'Completed')
                    {
                        $transaction_id = $data[$transaction_key . '.id_for_sender_txn'];
                        $this->StoreOrder->updateOrderTransaction($order_id, '', ORDER_STATUS_PROCESSING, $transaction_id);
                        $order = $this->StoreOrder->getOrderDetai($order_id);

                        //send notification
                        if ($order['Store'] != null)
                        {
                            $store = $order['Store'];
                            $this->Store->sendNotification($store['user_id'], $store['user_id'], 'create_order', '/stores/manager/orders/', '', 'Store');
                            $this->StoreOrder->sendOrderEmail($order_id, $store['id']);
                        }
                        $cPaypalParallel->log('Transaction for order: ' . $order_id . ' was completed successfully.');

                        //add digital product to list for user
                        $this->addDigitalProduct($order);
                    }
                    else
                    {
                        $cPaypalParallel->log('Transaction for order: ' . $order_id . ' was failed.');
                    }
                }
            }
        }
        exit();
    }

    private function addDigitalProduct($order)
    {
        if (!empty($order['StoreOrder']) && !empty($order['StoreOrderDetail']))
        {
            foreach ($order['StoreOrderDetail'] as $order_detail)
            {
                $product = $this->StoreProduct->loadProductDetail($order_detail['product_id']);
                if ($product == null || $product['StoreProduct']['product_type'] == STORE_PRODUCT_TYPE_REGULAR || $this->StoreDigitalProduct->isBoughtDigitalProduct($order_detail['product_id'], $order['StoreOrder']['user_id']))
                {
                    continue;
                }

                $this->StoreDigitalProduct->create();
                $this->StoreDigitalProduct->save(array(
                    'user_id' => $order['StoreOrder']['user_id'],
                    //'store_order_id' => $store_transaction['store_product_id'],
                    'store_product_id' => $order_detail['product_id']
                ));
            }
        }
    }

    private function removeDigitalProduct($order)
    {
        if (!empty($order['StoreOrder']) && !empty($order['StoreOrderDetail']))
        {
            foreach ($order['StoreOrderDetail'] as $order_detail)
            {
                $this->StoreDigitalProduct->removeDigitalProduct($order_detail['product_id'], $order['StoreOrder']['user_id']);
            }
        }
    }

    /////checkout uses credit
    private function validCreditCheckout($stores)
    {
        if (!$this->Store->storePermission(STORE_PERMISSION_CREDIT))
        {
            $this->_jsonError(__d('store', "You don't have permission to checkout by credit"));
        }
        if ($stores != null)
        {
            $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
            $total_amount = 0;
            foreach ($stores['items'] as $k => $store)
            {
                $total_amount += $mStoreCredit->exchangeToCredit($store['Store']['total_price']);
            }

            $balance = $mStoreCredit->getCurrentCreditBalance();
            if ($balance < $total_amount)
            {
                $this->_jsonError(sprintf(__d('store', 'Your current credit is %s. You do not have enough credits to buy product. Please buy more credits <a href="%s" target="_blank">here</a>'), $balance, $this->request->base . '/credits'));
            }
        }
    }

    private function doCreditCheckout($list_order_id)
    {
        if ($list_order_id != null)
        {
            $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
            $mStoreOrder = MooCore::getInstance()->getModel('Store.StoreOrder');
            $mStorePayment = MooCore::getInstance()->getModel('Store.StorePayment');
            $mStore = MooCore::getInstance()->getModel('Store.Store');
            $payment = $mStorePayment->getPaymentByKey(ORDER_GATEWAY_CREDIT);
            foreach ($list_order_id as $order_id)
            {
                $order = $mStoreOrder->getOrderDetai($order_id);

                //send notification
                if ($order['Store'] != null)
                {
                    $store = $order['Store'];

                    //charge credit
                    if (!$mStoreCredit->chargeCredit($order['StoreOrder']['amount_credit'], $order['StoreOrder']['id']))
                    {
                        $this->_jsonError(sprintf(__d('store', 'Can not checkout by credit for store %s. Please try again!'), $store['name']));
                    }

                    //site profit
                    $seller_amount = $order['StoreOrder']['amount_credit'];
                    $site_profit = $this->parseSiteProfit($seller_amount);
                    $seller_amount = $seller_amount - $site_profit;

                    //update credit for seller
                    if ($store != null)
                    {
                        $mStoreCredit->addCreditForSeller($seller_amount, $store['user_id'], $order['StoreOrder']['id']);
                    }

                    //update profit for site admin
                    if ($site_profit > 0)
                    {
                        $mStoreCredit->addCreditProfitForSiteAdmin($site_profit, $order['StoreOrder']['id']);
                    }

                    //add digital product to list for user
                    $this->addDigitalProduct($order);

                    //update order status 
                    $mStoreOrder->updateOrderTransaction($order_id, $payment['StorePayment']['id'], ORDER_STATUS_PROCESSING, '');

                    $this->MyCart->clear($store['id']);
                    $mStore->sendNotification($store['user_id'], $store['user_id'], 'create_order', '/stores/manager/orders/', '', 'Store');
                    $mStoreOrder->sendOrderEmail($order_id, $store['id']);
                }
            }
            $this->Session->write('checkout_complete', 1);
            $this->_jsonSuccess(__d('store', 'Successfully checkout.'), false, array(
                'url' => STORE_URL . 'orders/checkout_complete/'
            ));
        }
        $this->_jsonError(__d('store', 'Something went wrong, please try again.'));
    }

    private function parseSiteProfit($credits)
    {
        $profit_percentage = Configure::read('Store.store_site_profit');
        $profit = 0;
        if (!empty($credits) && $profit_percentage > 0 && $profit_percentage <= 50)
        {
            $profit = round($credits * $profit_percentage / 100, 3);
        }
        return $profit;
    }

    /////checkout by paypal express checkout
    private function paypal_express_checkout_config($store_id = null)
    {
        $cPaypalExpress = MooCore::getInstance()->getComponent('Store.PaypalExpress');
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        $cancel_url = $http . '://' . $_SERVER['SERVER_NAME'] . Router::url('/') . 'stores/orders/cancel_express_checkout';
        $return_url = $http . '://' . $_SERVER['SERVER_NAME'] . Router::url('/') . 'stores/orders/confirm_express_checkout/' . $store_id;

        $cPaypalExpress->config($return_url, $cancel_url);
        return $cPaypalExpress;
    }

    private function paypal_express_checkout($data, $store_id = null)
    {
        $cPaypalExpress = $this->paypal_express_checkout_config($store_id);

        //site profit
        $data = $cPaypalExpress->parseSiteProfit($data);

        $result = $cPaypalExpress->setParallelData($data);

        if ($result['status'] == 1)
        {
            return $result['url'];
        }
        else
        {
            if ($data != null)
            {
                foreach ($data as $item)
                {
                    $this->StoreOrder->delete($item['request_id']);
                }
            }
            $this->_jsonError(__d('store', 'Something went wrong, please try again.'));
        }
        return false;
    }

    public function confirm_express_checkout($store_id = null)
    {
        $params = $this->request->query;
        $cPaypalExpress = $this->paypal_express_checkout_config();
        $result = $cPaypalExpress->confirmParallel($params['token'], $params['PayerID']);
        if ($result['status'] == 1 && !empty($result['details']))
        {
            foreach ($result['details'] as $detail)
            {
                $order_id = $detail['request_id'];
                $transaction_id = $detail['transaction_id'];

                //update order status and send email
                $this->StoreOrder->updateOrderTransaction($order_id, '', ORDER_STATUS_PROCESSING, $transaction_id);
                $order = $this->StoreOrder->getOrderDetai($order_id);

                //send notification
                if ($order['Store'] != null)
                {
                    $store = $order['Store'];
                    $this->Store->sendNotification($store['user_id'], $store['user_id'], 'create_order', '/stores/manager/orders/', '', 'Store');
                    $this->StoreOrder->sendOrderEmail($order_id, $store['id']);
                }
                $cPaypalExpress->log('Transaction for order: ' . $order_id . ' was completed successfully.');

                //add digital product to list for user
                $this->addDigitalProduct($order);
            }

            //clear cart
            if ($store_id > 0)
            {
                $this->MyCart->clear($store_id);
            }
            else
            {
                $this->MyCart->clearAll();
            }
            $this->Session->write('checkout_complete', 1);
            $this->_redirectSuccess(__d('store', 'You transaction was completed and your order is being processed.'), STORE_URL . 'orders/checkout_complete');
        }
        $this->_redirectError(__d('store', 'Something went wrong, please try again'), STORE_URL);
    }

    public function cancel_express_checkout()
    {
        $this->_redirectError(__d('store', 'You checkout was cancelled.'), STORE_URL);
    }

}
