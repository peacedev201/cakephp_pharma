<?php

App::uses('StoreAppModel', 'Store.Model');

class StoreOrder extends StoreAppModel
{

    public $validationDomain = 'store';
    public $recursive = 1;
    public $hasMany = array(
        'StoreOrderDetail' => array(
            'className' => 'StoreOrderDetail',
            'foreignKey' => 'order_id',
            'dependent' => true
    ));
    public $belongsTo = array('User', 'Store.Store', 'Store.StorePayment');
    public $validate = array(
        'billing_first_name' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide billing first name'
        ),
        'billing_last_name' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide billing last name'
        ),
        'billing_email' => array(
            'rule' => 'email',
            'message' => 'Invalid billing email'
        ),
        'billing_phone' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide billing phone'
        ),
        'billing_country_id' => array(
            'rule' => array('checkBillingCountryExist'),
            'message' => 'Please select billing country'
        ),
        'billing_address' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide billing address'
        ),
        'billing_city' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide billing city'
        ),
        'shipping_first_name' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide shipping first name'
        ),
        'shipping_last_name' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide shipping last name'
        ),
        'shipping_email' => array(
            'rule' => 'email',
            'message' => 'Invalid shipping email'
        ),
        'shipping_phone' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide shipping phone'
        ),
        'shipping_country_id' => array(
            'rule' => array('checkShippingCountryExist'),
            'message' => 'Please select shipping country'
        ),
        'shipping_address' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide shipping address'
        ),
        'shipping_city' => array(
            'rule' => 'notBlank',
            'message' => 'Please provide shipping city'
        ),
    );

    public function beforeSave($options = array())
    {
        parent::beforeSave($options);
        $mCountry = MooCore::getInstance()->getModel('Country');
        if (!empty($this->data['StoreOrder']['billing_country_id']))
        {
            $billing_country = $mCountry->findById($this->data['StoreOrder']['billing_country_id']);
            $this->data['StoreOrder']['billing_country'] = $billing_country['Country']['name'];
        }
        if (!empty($this->data['StoreOrder']['shipping_country_id']))
        {
            $shipping_country = $mCountry->findById($this->data['StoreOrder']['shipping_country_id']);
            $this->data['StoreOrder']['shipping_country'] = $shipping_country['Country']['name'];
        }
    }

    function checkBillingCountryExist($id)
    {
        $mCountry = MooCore::getInstance()->getModel('Country');
        return $mCountry->hasAny(array(
                    'Country.id' => $this->data['StoreOrder']['billing_country_id']
        ));
    }

    function checkShippingCountryExist($id)
    {
        $mCountry = MooCore::getInstance()->getModel('Country');
        return $mCountry->hasAny(array(
                    'Country.id' => $this->data['StoreOrder']['shipping_country_id']
        ));
    }

    function checkOrderExist($id, $store_id = null, $user_id = null, $all = false)
    {
        if ($store_id == null)
        {
            $store_id = Configure::read('store.store_id');
        }
        $cond = array(
            'id' => (int) $id
        );
		if(!$all)
		{
			$cond['store_id'] = $store_id;
		}
        if ($user_id > 0)
        {
            $cond['user_id'] = $user_id;
            unset($cond['store_id']);
        }
        return $this->hasAny($cond);
    }

    public function loadManagerPaging($obj, $search = array(), $all = false)
    {
        $storeHelper = MooCore::getInstance()->getHelper("Store_Store");
        //pagination
        if($all)
        {
            $cond = array();
        }
        else
        {
            $cond = array(
                'StoreOrder.store_id' => Configure::read('store.store_id'),
            );
        }

        if (!empty($search['search_type']) && !empty($search['keyword']))
        {
            $keyword = $search['keyword'];
            switch ($search['search_type'])
            {
                case 1:
                    $cond[] = "CONCAT_WS('', StoreOrder.billing_first_name, StoreOrder.billing_last_name) LIKE '%" . $keyword . "%'";
                    break;
                case 2:
                    $cond[] = "CONCAT_WS('', StoreOrder.shipping_first_name, StoreOrder.shipping_last_name) LIKE '%" . $keyword . "%'";
                    break;
                case 3:
                    $cond[] = "StoreOrder.billing_email LIKE '%" . $keyword . "%'";
                    break;
                case 4:
                    $cond[] = "StoreOrder.shipping_email LIKE '%" . $keyword . "%'";
                    break;
                case 5:
                    $cond[] = "StoreOrder.order_code LIKE '%" . $keyword . "%'";
                    break;
                case 6:
                    $cond[] = "Store.name LIKE '%" . $keyword . "%'";
                    break;
            }
        }

        if (!empty($search['search_from']))
        {
            $cond[] = "DATE(StoreOrder.created) >= DATE('" . date('Y-m-d', strtotime($search['search_from'])) . "')";
        }
        if (!empty($search['search_to']))
        {
            $cond[] = "DATE(StoreOrder.created) <= DATE('" . date('Y-m-d', strtotime($search['search_to'])) . "')";
        }
        if (!empty($search['order_status']))
        {
            $cond['StoreOrder.order_status'] = $search['order_status'];
        }
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => array('StoreOrder.id' => 'DESC')
        );
        $data = $obj->paginate('StoreOrder');
        return $storeHelper->parseLanguage($data, "StorePayment");
    }

    public function updateOrderTransaction($order_id, $store_payment_id, $status, $transaction_id = '')
    {
        $data = array(
            'StoreOrder.order_status' => "'" . $status . "'",
            'StoreOrder.transaction_id' => "'" . $transaction_id . "'"
        );
        if ($store_payment_id > 0)
        {
            $data['StoreOrder.store_payment_id'] = "'" . $store_payment_id . "'";
        }
        return $this->updateAll($data, array(
                    'StoreOrder.id' => $order_id
        ));
    }

    function getOrderDetai($order_id, $store_id = null, $user_id = null)
    {
        $storeHelper = MooCore::getInstance()->getHelper("Store_Store");
        $cond = array(
            'StoreOrder.id' => $order_id
        );
        if ($store_id > 0)
        {
            $cond['StoreOrder.store_id'] = $store_id;
        }
        if ($user_id > 0)
        {
            $cond['StoreOrder.user_id'] = $user_id;
        }
        $data = $this->find('first', array(
            'conditions' => $cond
        ));
        return $storeHelper->parseLanguage($data, "StorePayment");
    }

    function updateOrderCode($order_id)
    {
        $this->updateAll(array(
            'StoreOrder.order_code' => "'" . Configure::read('Store.order_code_prefix') . $order_id . "'"
                ), array(
            'StoreOrder.id' => $order_id
        ));
    }

    function updateOrderAmount($order_id, $amount)
    {
        $this->updateAll(array(
            'StoreOrder.amount' => "'" . $amount . "'"
                ), array(
            'StoreOrder.store_id' => Configure::read('store.store_id'),
            'StoreOrder.id' => $order_id
        ));
    }

    function totalSiteOrders()
    {
        return $this->find('count', array(
                    'conditions' => array(
                        'store_id' => Configure::read('store.store_id')
                    )
        ));
    }

    function getCurrentUserOrders($obj)
    {
        $cond = array(
            'StoreOrder.user_id' => MooCore::getInstance()->getViewer(true)
        );

        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'order' => array('StoreOrder.id' => 'DESC'),
            'limit' => Configure::read('Store.my_orders_item_per_page'),
        );
        return $obj->paginate('StoreOrder');
    }

    public function totalMyOrder()
    {
        return $this->find('count', array(
                    'conditions' => array(
                        'StoreOrder.user_id' => MooCore::getInstance()->getViewer(true)
                    )
        ));
    }

    public function loadOrderStatusList()
    {
        return array(
            ORDER_STATUS_NEW => __d('store', 'NEW'),
            ORDER_STATUS_PROCESSING => __d('store', 'PROCESSING'),
            ORDER_STATUS_PENDING => __d('store', 'PENDING'),
            ORDER_STATUS_CANCELLED => __d('store', 'CANCEL'),
            ORDER_STATUS_REFUNDED => __d('store', 'REFUND'),
            ORDER_STATUS_COMPLETED => __d('store', 'COMPLETE')
        );
    }

    public function sendOrderEmail($order_id, $store_id)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $order = $this->getOrderDetai($order_id, $store_id);

        //send email
        $cMooMail = MooCore::getInstance()->getComponent('MooMail');
        $cMooMail->send($order['StoreOrder']['billing_email'], 'store_order', array(
            'content' => $this->getOrderTemplate($order),
        ));
    }

    private function getOrderTemplate($order)
    {
        ob_start();
        require_once('Email/order.ctp');

        return ob_get_clean();
    }

    public function getPrintOrderTemplate($order)
    {
        ob_start();
        require_once('Email/print_order.ctp');

        return ob_get_clean();
    }

    public function isBoughtProduct($product_id)
    {
        $mStoreOrderDetail = MooCore::getInstance()->getModel('Store.StoreOrderDetail');
        $order_status = array(ORDER_STATUS_PROCESSING, ORDER_STATUS_COMPLETED);
        $order_ids = $this->find('list', array(
            'conditions' => array(
                'StoreOrder.user_id' => MooCore::getInstance()->getViewer(true),
                'StoreOrder.order_status' => $order_status
            ),
            'fields' => array('StoreOrder.id', 'StoreOrder.id')
        ));
        if ($order_ids == null)
        {
            return false;
        }
        if ($mStoreOrderDetail->hasAny(array(
                    'StoreOrderDetail.product_id' => $product_id,
                    'StoreOrderDetail.order_id' => $order_ids
                )))
        {
            return true;
        }
        return false;
    }

    public function totalSiteProfit()
    {
        $site_profit = $site_profit_credit = 0;
        
        //normal money
        $data = $this->find('all', array(
            'conditions' => array(
                'StoreOrder.store_payment_id !=' => 5,
                '(StoreOrder.order_status = "'.ORDER_STATUS_PROCESSING.'" || StoreOrder.order_status = "'.ORDER_STATUS_COMPLETED.'")'
            ),
            'fields' => array(
                'SUM(StoreOrder.site_profit) as site_profit'
            )
        ));
        if(!empty($data[0][0]['site_profit']))
        {
            $site_profit = $data[0][0]['site_profit'];
        }
        
        //credit
        $data = $this->find('all', array(
            'conditions' => array(
                'StoreOrder.store_payment_id' => 5,
                '(StoreOrder.order_status = "'.ORDER_STATUS_PROCESSING.'" || StoreOrder.order_status = "'.ORDER_STATUS_COMPLETED.'")'
            ),
            'fields' => array(
                'SUM(StoreOrder.site_profit_credit) as site_profit'
            )
        ));
        if(!empty($data[0][0]['site_profit']))
        {
            $site_profit_credit = $data[0][0]['site_profit'];
        }
        
        return array($site_profit, $site_profit_credit);
    }
}
