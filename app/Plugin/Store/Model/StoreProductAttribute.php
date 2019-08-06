<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProductAttribute extends StoreAppModel
{
    public function findList($attribute_id = null, $product_id = null, $buy = null)
    {
        $cond = $data = $field = null;
        if((int)$attribute_id > 0)
        {
            $cond['attribute_id'] = $attribute_id;
            $field = array('product_id');
        }
        if((int)$product_id > 0)
        {
            $cond['product_id'] = $product_id;
            $field = array('attribute_id');
        }
        if($buy != null)
        {
            $cond['force_to_buy'] = $buy;
        }
        if($cond != null)
        {
            $data = $this->find('list', array(
                'conditions' => $cond,
                'fields' => $field
            ));
        }
        return $data;
    }
    
    function hasAttributeToBuy($product_id)
    {
        return $this->hasAny(array(
            'StoreProductAttribute.product_id' => $product_id,
            'StoreProductAttribute.force_to_buy' => 1
        ));
    }
    
    function loadProductAttributeToBuy($product_id)
    {
        $mAttribute = MooCore::getInstance()->getModel('Store.StoreAttribute');
        $mAttribute->unbindModel(array('belongsTo' => array('Store.Store')));
        
        //product attributes
        $product_attributes = $this->find('list', array(
            'conditions' => array(
                'StoreProductAttribute.product_id' => $product_id,
                'StoreProductAttribute.force_to_buy' => 1
            ),
            'fields' => array('StoreProductAttribute.attribute_id')
        ));
        
        //parent
        $parent_attributes = $mAttribute->find('list', array(
            'conditions' => array(
                'StoreAttribute.parent_id' => 0,
            ),
            'fields' => array('StoreAttribute.id')
        ));
        
        $product_attributes = array_merge($product_attributes, $parent_attributes);
        
        if($product_attributes != null)
        {
            $data = $mAttribute->find('threaded', array(
                'conditions' => array(
                    'StoreAttribute.id IN('.implode(',', $product_attributes).')'
                ),
                'order' => array('StoreAttribute.ordering' => 'ASC'))
            );
            if($data != null)
            {
                foreach($data as $k => $item)
                {
                    $list = array();
                    if($item['children'] != null)
                    {
                        foreach($item['children'] as $child)
                        {
                            $child = $child['StoreAttribute'];
                            $list[$child['id']] = $child['name'];
                        }
                    }
                    $data[$k]['child_list'] = $list;
                }
            }
            return $data;
        }
        return null;
    }
    
    function deleteByProductId($product_id)
    {
        return $this->deleteAll(array(
            'product_id' => $product_id
        ));
    }
    
    function getAllByProduct($product_id)
    {
        return $this->find('all', array(
            'conditions' => array(
                'StoreProductAttribute.product_id' => $product_id
            )
        ));
    }
    
    public function loadAttributePrice($product_id, $attribute_ids)
    {
        return $this->find('all', array(
            'conditions' => array(
                'StoreProductAttribute.product_id' => $product_id,
                'StoreProductAttribute.attribute_id' => $attribute_ids
            ),
            'fields' => array('StoreProductAttribute.plus', 'StoreProductAttribute.attribute_price')
        ));
    }
}