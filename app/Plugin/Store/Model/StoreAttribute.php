<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreAttribute extends StoreAppModel
{
    public $validationDomain = 'store';
	public $belongsTo = array(
        'Store.Store'
    );
	public $actsAs = array(
        'Tree'        
    );
	public $order = 'StoreAttribute.ordering asc';
	public $validate = array(           
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide name'
        ),		
        /*'parent_id' =>   array(   
            'rule' => array('checkAttributeExist'),
            'message' => 'Please select a category'
        ),*/
    );
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreAttribute']['store_id'] = Configure::read('store.store_id');
    }
    
    public function loadAttributeTree()
    {
        $proCats = $this->find('threaded', array(
            'conditions' => array('StoreAttribute.store_id' => Configure::read('store.store_id')),
            'order' => array('StoreAttribute.ordering ASC'))
        );
        return $proCats;
    }
    
    public function loadAttribute($except_id = null, $parent_id = -1, $listId = array())
    {
        $cond = array('StoreAttribute.store_id' => Configure::read('store.store_id'));
        if((int)$except_id > 0)
        {
            $cond[] = 'StoreAttribute.id != '.$except_id;
        }
        if((int)$parent_id  >= 0)
        {
            $cond['StoreAttribute.parent_id'] = $parent_id;
        }
        if($listId != null)
        {
            $cond[] = 'StoreAttribute.id IN('.implode(',', $listId).')';
        }
        $data= $this->find('threaded', array(
            'conditions' => $cond,
            'order' => array('StoreAttribute.ordering' => 'ASC'))
        );
        return $data;
    }
    
    public function loadAttributeDetail($id, $enable = null)
    {
        $cond = array(
            'StoreAttribute.store_id' => Configure::read('store.store_id'),
            'StoreAttribute.id' => $id
        );
        if(is_bool($enable))
        {
            $cond['StoreAttribute.enable'] = $enable;
        }
        return $this->find('first', array(
            'conditions' => $cond
        ));
    }
    
    public function loadManagerPaging($obj, $parent_id = 0, $search = array())
    {
        //pagination
        $this->unbindModel(array('belongsTo' => array('Store')));
        $cond = array(
            'StoreAttribute.store_id' => Configure::read('store.store_id'),
        );
        if(empty($search['keyword']) && empty($search['search_attribute_id']))
        {
            $cond["StoreAttribute.parent_id"] = $parent_id;
        }
        if(!empty($search['keyword']))
        {
            $cond[] = "StoreAttribute.name LIKE '%".$search['keyword']."%'";
        }
        if(!empty($search['attribute_id']))
        {
            $cond['StoreAttribute.parent_id'] = $search['attribute_id'];
        }
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => array('StoreAttribute.ordering' => 'DESC')
        );
        return $obj->paginate('StoreAttribute');
    }
    
    function checkAttributeExist($id)
    {
        return $this->hasAny(array(
            'store_id' => Configure::read('store.store_id'),
            'id' => (int)$id 
        ));
    }
    
    function checkHasChildren($parent_id){
    	return $this->hasAny(array(
    	    'StoreAttribute.parent_id' => $parent_id
    	));
    }
    
    function saveOrdering($id, $value)
    {
        $this->updateAll(array(
            'StoreAttribute.ordering' => $value,
        ),array(
            'StoreAttribute.store_id' => Configure::read('store.store_id'),
            'StoreAttribute.id' => $id,
        ));
    }
    
    function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StoreAttribute.'.$task => $value
        ), array(
            'StoreAttribute.store_id' => Configure::read('store.store_id'),
            'StoreAttribute.id' => $id,
        ));
    }
    
    function deleteAttribute($id)
    {
        $this->deleteAll(array(
            'StoreAttribute.store_id' => Configure::read('store.store_id'),
            'StoreAttribute.id' => $id,
        ));
    }
    
    public function loadAttributeList($product_category_id = null)
    {
        $attribute_ids = null;
        if((int)$product_category_id > 0)
        {
            $mProductCategory = MooCore::getInstance()->getModel('Store.StoreProductCategory');
            $catIds = $mProductCategory->find('list', array(
                'conditions' => array(
                    'ProductCategory.parent_id' => $product_category_id),
                'fields' => array(
                    'ProductCategory.id'
                ))
            );
            $catIds[] = $product_category_id;
            
            $mProductCategoryAttribute = MooCore::getInstance()->getModel('Store.ProductCategoryAttribute');
            $attribute_ids = $mProductCategoryAttribute->find('list', array(
                'conditions' => array(
                    'ProductCategoryAttribute.product_category_id IN('.implode(',', $catIds).')'
                ),
                'fields' => array('ProductCategoryAttribute.attribute_id')
            ));
        }
        $cond = array(
            'StoreAttribute.store_id' => Configure::read('store.store_id'),
            'StoreAttribute.enable' => 1
        );
        if($attribute_ids != null)
        {
            $cond[] = 'StoreAttribute.id IN('.implode(',', $attribute_ids).')';
        }

        $data = $this->find('threaded', array(
            'conditions' => $cond,
            'order' => array('StoreAttribute.ordering' => 'ASC'))
        );
        return $data;
    }
    
    public function getNameMap($attribute_id)
    {
        $parent = $this->find('list', array(
            'conditions' => array(
                'StoreAttribute.parent_id' => 0
            )
        ));
        $data = $this->find('all', array(
            'conditions' => array(
                'StoreAttribute.id' => $attribute_id
            ),
            'fields' => array('StoreAttribute.id', 'StoreAttribute.parent_id', 'StoreAttribute.name')
        ));
        $result = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $item = $item['StoreAttribute'];
                $result[$item['id']] = $parent[$item['parent_id']].': '.$item['name'];
            }
        }
        return $result;
    }
    
    public function calculateAttributePrice($product_id, $attribute_ids)
    {
        $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        $mStoreProductAttribute = MooCore::getInstance()->getModel('Store.StoreProductAttribute');
        $product = $mStoreProduct->loadOnlyProduct($product_id);
        $attribute_prices = $mStoreProductAttribute->loadAttributePrice($product_id, $attribute_ids);
        $price = 0.00;
        if($product != null)
        {
            $price = $product['StoreProduct']['new_price'];
            if($attribute_prices != null)
            {
                foreach($attribute_prices as $attribute_price)
                {
                    
                    if($attribute_price['StoreProductAttribute']['plus'])
                    {
                        $price += $attribute_price['StoreProductAttribute']['attribute_price'];
                    }
                    else
                    {
                        $price -= $attribute_price['StoreProductAttribute']['attribute_price'];
                    }
                }
            }
        }
        return number_format((float)$price, 2, '.', '');
    }
}