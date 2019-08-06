<?php

App::uses('AppHelper', 'View/Helper');

class StoreHelper extends AppHelper
{

    public $helpers = array('Storage.Storage');

    public function getProductImage($item, $options = array())
    {
        if (isset($item['StoreProductImage']))
        {
            $item = $item['StoreProductImage'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        if ($item != null)
        {
            return $this->Storage->getUrl($item['id'], $item['path'] . '/' . $prefix, $item['filename'], "products");
        }
        return $this->Storage->getUrl("", "", "", "products");
    }

    public function getStoreImage($item, $options = array())
    {
        if (isset($item['Store']))
        {
            $item = $item['Store'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        return $this->Storage->getUrl($item['id'], $prefix, $item['image'], "stores");
    }

    public function getDigitalFile($item, $options = array())
    {
        if (isset($item['StoreProduct']))
        {
            $item = $item['StoreProduct'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        if ($item != null)
        {
            //return $this->Storage->getUrl($item['id'], 'files', $item['digital_file'], "products");
            return FULL_BASE_LOCAL_URL . $request->webroot . STORE_DIGITAL_PRODUCT_UPLOAD_DIR . $item['digital_file'];
        }
        return "";
        //return $this->Storage->getUrl(null, 'files', null, "products");
    }

    public function getVideo($item, $options = array())
    {
        if (isset($item['StoreProduct']))
        {
            $item = $item['StoreProduct'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        if ($item != null)
        {
            return $this->Storage->getUrl($item['id'], 'videos', $item['video'], "products");
        }
        return $this->Storage->getUrl(null, 'videos', null, "products");
    }

    function formatMoney($amount, $currency_position = null, $currency_symbol = null, $setting_show_money_type = null, $exchange_to_credit = true)
    {
        $currency_position = empty($currency_position) ? Configure::read('Store.currency_position') : $currency_position;
        $currency_symbol = empty($currency_symbol) ? Configure::read('store.currency_symbol') : $currency_symbol;
        switch ($currency_position)
        {
            case CURRENCY_POSITION_LEFT:
                $money = $currency_symbol . $amount;
                break;
            case CURRENCY_POSITION_RIGHT:
                $money = $amount . $currency_symbol;
                break;
            default :
                $money = $currency_symbol . $amount;
        }
        return $this->formatCredit($amount, $money, $setting_show_money_type, $exchange_to_credit);
    }

    function formatCredit($amount, $normal_money, $setting_show_money_type = null, $exchange_to_credit = true)
    {
        $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
        $force_show_credit = false;
        if ($setting_show_money_type == STORE_SHOW_MONEY_TYPE_CREDIT)
        {
            $force_show_credit = true;
        }
        $setting_show_money_type = $setting_show_money_type == null ? Configure::read('Store.store_show_money_type') : $setting_show_money_type;
        if ($force_show_credit || ($mStoreCredit->isAllowCredit() && ($setting_show_money_type == STORE_SHOW_MONEY_TYPE_CREDIT || $setting_show_money_type == STORE_SHOW_MONEY_TYPE_ALL)))
        {
            $credit = $exchange_to_credit ? $mStoreCredit->exchangeToCredit($amount) : $amount;
            switch ($setting_show_money_type)
            {
                case STORE_SHOW_MONEY_TYPE_ALL:
                    return $normal_money . '<br/>' . $credit . ' ' . __d('store', "credits");
                case STORE_SHOW_MONEY_TYPE_NORMAL:
                    return $normal_money;
                case STORE_SHOW_MONEY_TYPE_CREDIT:
                    return $credit . ' ' . __d('store', "credits");
            }
        }
        return $normal_money;
    }

    function exchangeToCredit($amount)
    {
        $mStoreCredit = MooCore::getInstance()->getModel('Store.StoreCredit');
        $setting_show_money_type = Configure::read('Store.store_show_money_type');
        if ($mStoreCredit->isAllowCredit() && ($setting_show_money_type == STORE_SHOW_MONEY_TYPE_CREDIT || $setting_show_money_type == STORE_SHOW_MONEY_TYPE_ALL))
        {
            $amount = $mStoreCredit->exchangeToCredit($amount);
        }
        return $amount;
    }

    public function checkPostStatus($blog, $uid)
    {
        /* if (!$uid)
          return false;
          $friendModel = MooCore::getInstance()->getModel('Friend');
          if ($uid == $blog['Blog']['user_id'])
          return true;

          if ($blog['Blog']['privacy'] == PRIVACY_EVERYONE)
          {
          return true;
          }

          if ($blog['Blog']['privacy'] == PRIVACY_FRIENDS)
          {
          $areFriends = $friendModel->areFriends( $uid, $blog['Blog']['user_id'] );
          if ($areFriends)
          return true;
          }


          return false; */
        return true;
    }

    public function checkSeeComment($blog, $uid)
    {
        /* if ($blog['Blog']['privacy'] == PRIVACY_EVERYONE)
          {
          return true;
          }

          return $this->checkPostStatus($blog,$uid); */
        return true;
    }

    public function getEnable()
    {
        return Configure::read('Store.store_enabled');
    }

    public function allowViewProductDetail()
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        return $mStore->storePermission(STORE_PERMISSION_VIEW_PRODUCT_DETAIL);
    }

    public function allowBuyProduct()
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        return $mStore->storePermission(STORE_PERMISSION_BUY_PRODUCT);
    }

    public function getOrderPaymentName($type)
    {
        $mStorePayment = MooCore::getInstance()->getModel('Store.StorePayment');
        return $mStorePayment->getPaymentNameByKey($type);
    }

    public function getPaymentName($ids)
    {
        $ids = explode(',', $ids);
        $mStorePayment = MooCore::getInstance()->getModel('Store.StorePayment');
        $data = $mStorePayment->getListName($ids);
        return implode(', ', $data);
    }

    public function getParamsPayment($item)
    {
        $store_transaction = $item['StoreTransaction'];
        $url = Router::url('/', true);
        $first_amount = 0;
        if ($store_transaction['store_product_id'] > 0)
        {
            return array(
                'cancel_url' => $url . 'stores/store_packages/cancel_featured_product/' . $store_transaction['id'],
                'return_url' => $url . 'stores/store_packages/success_featured_product/',
                'currency' => $store_transaction['currency'],
                'description' => __d('store', 'Featured Product with %s %s for %s days', $store_transaction['amount'], $store_transaction['currency'], $store_transaction['period']),
                'type' => 'Store_Store_Transaction',
                'id' => $store_transaction['id'],
                'is_recurring' => 0,
                'amount' => $store_transaction['amount'],
                'first_amount' => $store_transaction['amount'],
                'end_date' => $store_transaction['expiration_date'],
                'total_amount' => $store_transaction['amount'],
                'memo' => __d('store', 'Buy featured product')
            );
        }
        else
        {
            return array(
                'cancel_url' => $url . 'stores/store_packages/cancel_featured_store/' . $store_transaction['id'],
                'return_url' => $url . 'stores/store_packages/success_featured_store/',
                'currency' => $store_transaction['currency'],
                'description' => __d('store', 'Featured Store with %s %s for %s days', $store_transaction['amount'], $store_transaction['currency'], $store_transaction['period']),
                'type' => 'Store_Store_Transaction',
                'id' => $store_transaction['id'],
                'is_recurring' => 0,
                'amount' => $store_transaction['amount'],
                'first_amount' => $store_transaction['amount'],
                'end_date' => $store_transaction['expiration_date'],
                'total_amount' => $store_transaction['amount'],
                'memo' => __d('store', 'Buy featured store')
            );
        }
    }

    public function onSuccessful($item, $data = array(), $price = 0, $recurring = false, $admin = 0)
    {
        $store_transaction = $item['StoreTransaction'];
        $mStoreTransaction = MooCore::getInstance()->getModel('Store.StoreTransaction');
        $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $transaction_id = "";
        if (!empty($data['transaction']['senderTransactionId']))
        {
            $transaction_id = $data['transaction']['senderTransactionId'];
        }
        else if (!empty($data['txn_id']))
        {
            $transaction_id = $data['txn_id'];
        }

        if ($mStoreTransaction->updateAll(array(
                    'StoreTransaction.status' => "'" . TRANSACTION_STATUS_COMPLETED . "'",
                    'StoreTransaction.callback_params' => "'" . json_encode($data) . "'",
                    'StoreTransaction.transaction_id' => "'" . $transaction_id . "'"
                        ), array(
                    'StoreTransaction.id' => $store_transaction['id']
                )))
        {
            if ($store_transaction['store_product_id'] > 0)
            {
                $mStoreProduct->updateAll(array(
                    'StoreProduct.featured' => 1,
                    'StoreProduct.sent_expiration_email' => 0,
                    'StoreProduct.feature_expiration_date' => "'" . $store_transaction['expiration_date'] . "'"
                        ), array(
                    'StoreProduct.id' => $store_transaction['store_product_id']
                ));

                //send notification
                $mStoreProduct->sendFeaturedNotification($store_transaction['store_product_id'], 1);
            }
            else
            {
                $mStore->updateAll(array(
                    'Store.featured' => 1,
                    'Store.sent_expiration_email' => 0,
                    'Store.feature_expiration_date' => "'" . $store_transaction['expiration_date'] . "'"
                        ), array(
                    'Store.id' => $store_transaction['store_id']
                ));

                //send notification
                $mStore->sendFeaturedNotification($store_transaction['store_id'], 1);
            }
        }
    }

    public function onFailure($item, $data)
    {
        $store_transaction = $item['StoreTransaction'];
        $mStoreTransaction = MooCore::getInstance()->getModel('Store.StoreTransaction');

        $mStoreTransaction->updateAll(array(
            'StoreTransaction.status' => 'failed',
            'StoreTransaction.callback_params' => "'" . json_encode($data) . "'",
                ), array(
            'StoreTransaction.id' => $store_transaction['id']
        ));
    }

    public function getOrderStatus($select)
    {
        $mStoreOrder = MooCore::getInstance()->getModel('Store.StoreOrder');
        $statuses = $mStoreOrder->loadOrderStatusList();
        foreach ($statuses as $k => $item)
        {
            if ($select == $k)
            {
                return $item;
            }
        }
        return "";
    }

    public function loadLanguage()
    {
        $mLanguage = MooCore::getInstance()->getModel('Language');
        return $mLanguage->find('all');
    }

    public function parseLanguage($data, $model)
    {
        $mI18n = MooCore::getInstance()->getModel('I18nModel');
        $single = false;
        if ($data != null)
        {
            $lang = Configure::read("core.default_language");
            if (!isset($data[0]))
            {
                $single = true;
                $data = array($data);
            }
            foreach ($data as $key => $value)
            {
                if (!isset($value[$model]))
                {
                    continue;
                }
                $i18n = $mI18n->find("all", array(
                    "conditions" => array(
                        "Locale" => $lang,
                        "model" => $model,
                        "foreign_key" => $value[$model]["id"]
                    )
                ));
                if ($i18n == null)
                {
                    continue;
                }
                foreach ($i18n as $i18)
                {
                    if (isset($data[$key][$model][$i18["I18nModel"]["field"]]))
                    {
                        $data[$key][$model][$i18["I18nModel"]["field"]] = $i18["I18nModel"]["content"];
                    }
                }
            }
        }
        if ($single)
        {
            return $data[0];
        }
        return $data;
    }

    public function loadTransactionStatusList($key = null)
    {
        $list = array(
            TRANSACTION_STATUS_INITIAL => __d('store', 'Initial'),
            TRANSACTION_STATUS_COMPLETED => __d('store', 'Completed'),
            TRANSACTION_STATUS_PENDING => __d('store', 'Pending'),
            TRANSACTION_STATUS_EXPIRED => __d('store', 'Expired'),
            TRANSACTION_STATUS_REFUNDED => __d('store', 'Refuned'),
            TRANSACTION_STATUS_FAILED => __d('store', 'Failed'),
            TRANSACTION_STATUS_CANCEL => __d('store', 'Cancel'),
            TRANSACTION_STATUS_INACTIVE => __d('store', 'Inactive')
        );
        if ($key != null)
        {
            return isset($list[$key]) ? $list[$key] : '';
        }
        return $list;
    }

    public function loadStorePackageList($key = null)
    {
        $mStorePackage = MooCore::getInstance()->getModel('Store.StorePackage');
        $list = $mStorePackage->loadStorePackageList();
        if ($key != null)
        {
            return isset($list[$key]) ? $list[$key] : '';
        }
        return $list;
    }

    public function getCountryList()
    {
        $mStoreShipping = MooCore::getInstance()->getModel('Store.StoreShipping');
        return $mStoreShipping->getCountryList();
    }

    public function getShippingMethodList()
    {
        $mStoreShippingMethod = MooCore::getInstance()->getModel('Store.StoreShippingMethod');
        return $mStoreShippingMethod->getShippingMethodList();
    }

    public function getShippingZoneList($store_id = 0)
    {
        $mStoreShippingZone = MooCore::getInstance()->getModel('Store.StoreShippingZone');
        return $mStoreShippingZone->getShippingZoneList($store_id);
    }

    public function getShippingDetail($id, $store_id = 0)
    {
        $mStoreShipping = MooCore::getInstance()->getModel('Store.StoreShipping');
        return $mStoreShipping->loadShippingDetail($id, $store_id);
    }

    public function calculateShippingPrice($store_id, $products, $key_name, $price, $weight)
    {
        $mStoreShipping = MooCore::getInstance()->getModel('Store.StoreShipping');
        return $mStoreShipping->calculateShippingPrice($store_id, $products, $key_name, $price, $weight);
    }

    public function loadProductType($key = null)
    {
        $list = array(
            STORE_PRODUCT_TYPE_REGULAR => __d('store', 'Regular Product'),
            STORE_PRODUCT_TYPE_DIGITAL => __d('store', 'Digital Product'),
            STORE_PRODUCT_TYPE_LINK => __d('store', 'Link Product')
        );
        if ($key != null)
        {
            return isset($list[$key]) ? $list[$key] : '';
        }
        return $list;
    }

    public function isBoughtDigitalProduct($product_id)
    {
        $mStoreDigitalProduct = MooCore::getInstance()->getModel('Store.StoreDigitalProduct');
        return $mStoreDigitalProduct->isBoughtDigitalProduct($product_id, MooCore::getInstance()->getViewer(true));
    }

    public function isProductInCart($product_id)
    {
        $cMyCart = MooCore::getInstance()->getComponent('Store.MyCart');
        return $cMyCart->isProductInCart($product_id);
    }

    public function storePermission($value)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        return $mStore->storePermission($value);
    }

    public function isBoughtProduct($product_id)
    {
        $mStoreOrder = MooCore::getInstance()->getModel('Store.StoreOrder');
        return $mStoreOrder->isBoughtProduct($product_id);
    }

    public function loadProductSorting($key = null)
    {
        $list = array(
            PRODUCT_SORT_MOST_RECENT => __d('store', 'Most recent'),
            PRODUCT_SORT_NAME_ASC => __d('store', 'Sort by name: a-z'),
            PRODUCT_SORT_NAME_DESC => __d('store', 'Sort by name: z-a'),
            PRODUCT_SORT_PRICE_ASC => __d('store', 'Sort by price: low to high'),
            PRODUCT_SORT_PRICE_DESC => __d('store', 'Sort by price: high to low'),
            PRODUCT_SORT_RATING_ASC => __d('store', 'Sort by rating: low to high'),
            PRODUCT_SORT_RATING_DESC => __d('store', 'Sort by rating: high to low'),
        );
        if ($key != null)
        {
            return isset($list[$key]) ? $list[$key] : '';
        }
        return $list;
    }

    public function loadProductDetail($id)
    {
        $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        return $mStoreProduct->loadProductDetail($id);
    }
    
    public function loadBusiness($id)
    {
        $mStoreBusiness = MooCore::getInstance()->getModel('Store.StoreBusiness');
        return $mStoreBusiness->loadBusiness($id);
    }
}
