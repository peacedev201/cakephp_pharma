<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
 
App::uses('Component', 'Controller');

class MyCartComponent extends Component 
{
    public $components = array('Session');
    function add($store_id, $product_id, $quantity = 1, $attribute_id = null)
    {
        $quantity = $this->validQuantity($quantity);
        if($this->isExist($store_id, $product_id, $attribute_id))
        {
            $this->update($store_id, $product_id, $quantity, $attribute_id);
        }
        else
        {
            $cart = $this->Session->read("shop_cart");
            $id = md5(uniqid());
            while ($this->isIdExist($id)) 
            {
                $id .= rand(10, 99);
            }
            $cart[$store_id][] = array(
                'id' => $id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'attribute_id' => $attribute_id
            );;
            $this->Session->write("shop_cart", $cart);
        }
    }
    
    function update($store_id, $product_id, $quantity, $attribute_id)
    {
        $cart = $this->Session->read("shop_cart");
        if(!empty($cart[$store_id]))
        {
            foreach($cart[$store_id] as $k => $item)
            {
                if($item['product_id'] == $product_id &&
                   (empty($item['attribute_id']) ||
                   (!empty($item['attribute_id']) && array_diff($item['attribute_id'], $attribute_id) == null))
                )
                {
                    $cart[$store_id][$k]['quantity'] += $quantity;
                }
            }
        }
        $this->Session->write("shop_cart", $cart);
    }
    
    function updateQuantity($cart_id, $quantity)
    {
        $quantity = $this->validQuantity($quantity);
        $cart = $this->Session->read("shop_cart");
        if($cart != null)
        {
            foreach($cart as $k => $items)
            {
                if($items != null)
                {
                    foreach($items as $k2 => $item)
                    {
                        if($item['id'] == $cart_id)
                        {
                            $cart[$k][$k2]['quantity'] = $quantity;
                        }
                    }
                }
            }
        }
        $this->Session->write("shop_cart", $cart);
    }
    
    function validQuantity($quantity)
    {
        if(!is_numeric($quantity) || $quantity < 1 || $quantity > 99)
        {
            $quantity = 1;
        }
        return $quantity;
    }

    function isEmpty()
    {
        if($this->Session->read("shop_cart") == null)
        {
            return true;
        }
        return false;
    }
    
    function isExist($store_id, $product_id, $attribute_id)
    {
        $cart = $this->Session->read("shop_cart");
        if(!empty($cart[$store_id]))
        {
            foreach($cart[$store_id] as $item)
            {
                if($item['product_id'] == $product_id &&
                   (empty($item['attribute_id']) ||
                   (!empty($item['attribute_id']) && array_diff($item['attribute_id'], $attribute_id) == null))
                )
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    function isProductInCart($product_id)
    {
        $cart = $this->Session->read("shop_cart");
        if($cart != null)
        {
            foreach($cart as $mycart)
            {
                foreach($mycart as $item)
                {
                    if($item['product_id'] == $product_id)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    function productQuantityInCart($product_id)
    {
        $cart = $this->Session->read("shop_cart");
        if($cart != null)
        {
            foreach($cart as $mycart)
            {
                foreach($mycart as $item)
                {
                    if($item['product_id'] == $product_id)
                    {
                        return $item['quantity'];
                    }
                }
            }
        }
        return false;
    }
    
    function isIdExist($cart_id)
    {
        $cart = $this->Session->read("shop_cart");
        if($cart != null)
        {
            foreach($cart as $items)
            {
                if($items != null)
                {
                    foreach($items as $item)
                    {
                        if($item['id'] == $cart_id)
                        {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    
    function isOverQuantity($product_id, $quantity, $max_quantity, $attribute_id = null, $branch_id = null)
    {
        $cart = $this->Session->read("shop_cart");
        foreach($cart as $item)
        {
            if($item['product_id'] == $product_id &&
               $item['attribute_id'] == $attribute_id &&
               $item['branch_id'] == $branch_id
               )
            {
                $quantity = $item['quantity'] + $quantity;
                if($quantity > $max_quantity)
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    function totalQuantity()
    {
        $cart = $this->Session->read("shop_cart");
        if($cart != null)
        {
            $num = 0;
            foreach($cart as $item)
            {
                if(empty($item))
                {
                    continue;
                }
                foreach($item as $it)
                {
                    $num += $it['quantity'];
                }
            }
            return $num;
        }
        return 0;
    }
    
    function show($store_id = null)
    {
        $cart = $this->Session->read("shop_cart");
        if($store_id == null)
        {
            return $cart;
        }
        else if(isset($cart[$store_id]))
        {
            $result[$store_id] = $cart[$store_id];
            return $result;
        }
        return null;
    }
    
    function clear($store_id, $cart_id = null)
    {
        $cart = $this->Session->read("shop_cart");
        if($cart != null)
        {
            foreach($cart as $k => $items)
            {
                if($store_id == $k)
                {
                    if($cart_id != null)
                    {
                        foreach($items as $k2 => $item)
                        {
                            if($item['id'] == $cart_id)
                            {
                                unset($cart[$k][$k2]);
                                break;
                            }
                        }
                        $cart[$k] = array_values($cart[$k]);
                    }
                    else 
                    {
                        unset($cart[$k]);
                        break;
                    }
                }
            }
            foreach($cart as $k => $items)
            {
                if($items == null)
                {
                    unset($cart[$k]);
                }
                else 
                {
                    $cart[$k] = array_values($cart[$k]);
                }
            }
        }
        $this->Session->write("shop_cart", $cart);
    }
    
    function clearMulti($list)
    {
        $cart = $this->Session->read("shop_cart");
        $count = 0;
        foreach($cart as $item)
        {
            foreach($list as $id)
            {
                if($item['id'] == $id)
                {
                    unset($cart[$count]);
                    $cart = array_values($cart);
                }
            }
        }
        $this->Session->write("shop_cart", $cart);
    }
    
    function clearAll()
    {
        $this->Session->delete("shop_cart");
    }
} 