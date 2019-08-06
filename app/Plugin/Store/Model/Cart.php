<?php 
App::uses('StoreAppModel', 'Store.Model');
class Cart extends StoreAppModel
{
    function loadCart($cart, $store_shipping_id_data = null)
    {
		$profit_percentage = Configure::read('Store.store_site_profit');
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        $mAttribute = MooCore::getInstance()->getModel('Store.StoreAttribute');
        $mStoreShipping = MooCore::getInstance()->getModel('Store.StoreShipping');
        
        $stores = array();
        if($cart != null)
        {
            $total_price = 0;
            $amount_zero_online_transaction = true;
            foreach($cart as $store_id => $items)
            {
                $shipping_price = 0;
                if(isset($store_shipping_id_data[$store_id]))
                {
                    $shipping = $mStoreShipping->findById($store_shipping_id_data[$store_id]);
                    if($shipping != null)
                    {
                        $shipping_price = $shipping['StoreShipping']['price'];
                    }
                }
                $store = $mStore->findById($store_id);
                $total = 0;
                $total_weight = 0;
                if($items != null)
                {
                    foreach($items as $item)
                    {
                        $product = $mProduct->loadProductDetail($item['product_id'], false, false, $store_id, 1, 1);
                        if($product != null)
                        {
                            //load attribute name
                            $attributes = null;
                            if(!empty($item['attribute_id']))
                            {
                                $attributes = $mAttribute->find('list', array(
                                    'conditions' => array(
                                        'StoreAttribute.id IN('.implode(',', $item['attribute_id']).')'
                                    ),
                                    'fields' => array('StoreAttribute.name')
                                ));
                            }
                            $product['StoreProduct']['attributes'] = $attributes;
                            $product['StoreProduct']['cart_id'] = $item['id'];
                            $product['StoreProduct']['quantity'] = $item['quantity'];
                            $price = $mAttribute->calculateAttributePrice($product['StoreProduct']['id'], $item['attribute_id']);;
                            $product['StoreProduct']['new_price'] = $price;
                            $product['StoreProduct']['total_price'] = (int)$item['quantity'] * $price;
                            $total += $product['StoreProduct']['total_price'];
                            $store['Products'][] = $product;
                            $total_weight += $product['StoreProduct']['weight'] * $product['StoreProduct']['quantity'];
                        }
                    }
                    
                }
                $store['Store']['total_price'] = $total;
                $store['Store']['site_profit'] = $profit_percentage > 0 ? round($total * $profit_percentage / 100, 2) : 0;
                $store['Store']['total_weight'] = $total_weight;
                $total_price += $total;
                $stores['items'][] = $store;
                if($amount_zero_online_transaction && $total == 0 && $shipping_price == 0)
                {
                    $amount_zero_online_transaction = false;
                }
            }
            $stores['total'] = $total_price;
            $stores['amount_zero_online_transaction'] = $amount_zero_online_transaction;
        }
        return $stores;
    }
    
    function loadProductCartByStore($store_id, $cart)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        $mAttribute = MooCore::getInstance()->getModel('Store.StoreAttribute');
        
        $products = array();
        if($cart != null)
        {
            foreach($cart as $sid => $items)
            {
                if($items != null)
                {
                    foreach($items as $item)
                    {
                        if($mProduct->checkProductExist($item['product_id'], true, $store_id))
                        {
                            $product = $mProduct->loadProductDetail($item['product_id'], false, false, $store_id, 1, 1);
                            if($product != null)
                            {
                                //load attribute name
                                $attributes = null;
                                if(!empty($item['attribute_id']))
                                {
                                    $attributes = $mAttribute->find('list', array(
                                        'conditions' => array(
                                            'StoreAttribute.id IN('.implode(',', $item['attribute_id']).')'
                                        ),
                                        'fields' => array('StoreAttribute.name')
                                    ));
                                }
                                $product['StoreProduct']['attributes'] = $attributes;
                                $product['StoreProduct']['cart_id'] = $item['id'];
                                $product['StoreProduct']['quantity'] = $item['quantity'];
                                $price = $mAttribute->calculateAttributePrice($product['StoreProduct']['id'], $item['attribute_id']);;
                                $product['StoreProduct']['new_price'] = $price;
                                $product['StoreProduct']['total_price'] = (int)$item['quantity'] * $price;
                                $products[] = $product;
                            }
                        }
                    }
                }
            }
        }
        return $products;
    }
    
    function loadListStore($cart)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        
        $stores = array();
        if($cart != null)
        {
            foreach($cart as $store_id => $items)
            {
                $store = $mStore->findById($store_id);
                if($store != null)
                {
                    $stores[$store['Store']['id']] = $store['Store']['name'];
                }
            }
        }
        return $stores;
    }
    
    function validCartAttribute($product_id, $attribute_id)
    {
        $mProductAttribute = MooCore::getInstance()->getModel('Store.StoreProductAttribute');
        
        $attributes = $mProductAttribute->loadProductAttributeToBuy($product_id);
        if($attributes != null)
        {
            foreach($attributes as $attribute)
            {
                $exist = false;
                foreach($attribute_id as $id)
                {
                    if($attribute['child_list'] == null)
                    {
                        $exist = true;
                        break;
                    }
                    else if(array_key_exists($id, $attribute['child_list']))
                    {
                        $exist = true;
                        break;
                    }
                }
                if($exist == false)
                {
                    return $attribute['StoreAttribute']['name'];
                }
            }
        }
        return true;
    }
    
    public function validProductCart($products)
    {
        $product_name = array();
        if($products != null)
        {
            foreach($products as $product)
            {
                if($product['StoreProduct']['enable'] == 0 || $product['StoreProduct']['out_of_stock'])
                {
                    $product_name[] = $product['StoreProduct']['name'];
                }
            }
        }
        return $product_name;
    }
    
    public function isProductInCart($product_id)
    {
        $cCart = MooCore::getInstance()->getComponent('Store.MyCart');
        return $cCart->isProductInCart($product_id);
    }
    
    public function productQuantityInCart($product_id)
    {
        $cCart = MooCore::getInstance()->getComponent('Store.MyCart');
        return $cCart->productQuantityInCart($product_id);
    }
}