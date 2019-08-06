<?php 
class StoreAttributesController extends StoreAppController
{	
    public $components = array('Paginator');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->url = STORE_MANAGER_URL.'attributes/';
        $this->set('url', $this->url);
        $this->loadModel('Store.StoreAttribute');
        $this->loadModel('Store.StoreProductAttribute');
        $this->loadModel('Store.StoreProduct');
    }
    
    public function manager_index($parent_id = 0)
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : '';
        
        //load attribute
        $attributes = $this->StoreAttribute->loadManagerPaging($this, $parent_id, $search);

        //load parent attributes for search
        $listAttributes = $this->StoreAttribute->loadAttribute(null, 0);
        
        //parent attribute
        $parent_attribute = $this->StoreAttribute->findById($parent_id);

        $this->set(array(
            'attributes' => $attributes,
            'listAttributes' => $listAttributes,
            'parent_attribute' => $parent_attribute,
            'search' => $search,
            'active_menu' => 'manage_attributes',
            'title_for_layout' => __d('store', "Manage Attributes")
        ));
    }

    public function manager_create($id = null)
    {
        if(!empty($id) && !$this->StoreAttribute->checkAttributeExist($id))
        {
            $this->_redirectError(__d('store', 'Attribute not found'), $this->url);
        }
        else 
        {
            //load attribute
            $except_id = $belongCatList = $attributeCats = null;
            if (!empty($id))
            {
                $attribute = $this->StoreAttribute->loadAttributeDetail($id);
                $attribute = $attribute['StoreAttribute'];
                $except_id = $attribute['id'];
            }
            else 
            {
                $attribute = $this->StoreAttribute->initFields();
                $attribute = $attribute['StoreAttribute'];
            }

            //load attributes
            if(empty($id) || $attribute["parent_id"] > 0){
            	$attributeCats = $this->StoreAttribute->loadAttribute($except_id);
            }

            $this->set(array(
                'attribute' => $attribute,
                'attributeCats' => $attributeCats,
                'active_menu' => 'create_attribute',
                'title_for_layout' => !empty($id) ? __d('store', "Edit Attribute") : __d('store', "Create Attribute")
            ));
        }
    }
    
    function manager_save()
    {
        $data = $this->request->data;
        if((int)$data['id'] > 0 && $this->StoreAttribute->checkAttributeExist($data['id']))
        {
            $this->StoreAttribute->id = $data['id'];
        }
        else
        {
            $data['ordering'] = $this->generateOrdering('StoreAttribute');
        }
        $this->StoreAttribute->set($data);
        $this->_validateData($this->StoreAttribute);
        if($this->StoreAttribute->save($data))
        {
            //get attribute id
            $attribute_id = $this->StoreAttribute->id;
            
            //show message
            $redirect = $this->url;
            if($data['parent_id'] > 0)
            {
                $redirect = $this->url.'index/'.$data['parent_id'];
            }
            if($data['save_type'] == 1)
            {
                $redirect = $this->url.'create/'.$attribute_id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }
        else 
        {
            $this->_jsonError(__d('store', 'Something went wrong, please try again'));
        }
    }
    
    public function manager_ordering()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $k => $id)
            {
                $this->StoreAttribute->saveOrdering($id, $data['ordering'][$k]);
            }
        }
        $this->Session->setFlash(__d('store', 'Successfully updated'));
        $this->redirect($this->referer());
    }
    
    public function manager_delete()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreAttribute->checkHasChildren($id))
                {
                    $attribute = $this->StoreAttribute->loadAttributeDetail($id);
                    $this->_redirectError(sprintf(__d('store', 'Please delete sub attributes of %s first'), $attribute['StoreAttribute']['name']), $this->referer());
                }
                $this->StoreAttribute->deleteAttribute($id);
            }
        }
        $this->Session->setFlash(__d('store', 'Successfully deleted'));
        $this->redirect($this->referer());
    }
    
    public function manager_enable()
    {
        $this->active($this->request->data, 1, 'enable');
    }
    
    public function manager_disable()
    {
        $this->active($this->request->data, 0, 'enable');
    }

    private function active($data, $value, $task)
    {
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreAttribute->checkAttributeExist($id))
                {
                    $this->StoreAttribute->create();
                    $this->StoreAttribute->activeField($id, $task, $value);
                }
            }
        }
        $this->Session->setFlash(__d('store', 'Successfully updated'));
        $this->redirect($this->referer());
    }
    
    public function manager_select_list($buy = 0)
    {
        $data = $this->request->data;
        $data = $this->StoreAttribute->loadAttribute();
        $attributes = array();
        if($data != null)
        {
            foreach($data as $attribute)
            {
                $attrs = array();
                if($attribute['children'] != null)
                {
                    foreach($attribute['children'] as $child)
                    {
                        $attributes[$child['StoreAttribute']['id']] = $attribute['StoreAttribute']['name'].'->'.$child['StoreAttribute']['name'];
                    }
                }
            }
        }
        $this->set(array(
            'attributes' => $attributes,
            'buy' => $buy,
            'select_ids' => !empty($data['ids']) ? explode(',', $data['ids']) : array(),
        ));
    }
    
    public function manager_show_select_list()
    {
        $data = $this->request->data;
        $attributes = null;
        if(!empty($data['attribute_id']))
        {
            if(count(array_unique($data['attribute_id'])) < count($data['attribute_id']))
            {
                $this->_jsonError(__d('store', 'Duplicate value'));
            }
            $map = $this->StoreAttribute->getNameMap($data['attribute_id']);
            foreach($data['attribute_id'] as $k => $attribute_id)
            {
                $attributes[] = array(
                    'attribute_id' => $attribute_id,
                    'name' => $map[$attribute_id],
                    'plus' => $data['plus'][$k],
                    'attribute_price' => $data['attribute_price'][$k]
                );
            }
            //$attributes = $this->StoreAttribute->loadAttribute(null, -1, $data['attribute_id']);
        }
        else if(!empty($data['product_id']))
        {
            $data = $this->StoreProductAttribute->getAllByProduct($data['product_id']);
            if($data != null)
            {
                //$attributes = $this->StoreAttribute->loadAttribute(null, -1, $list);
                $attribute_ids = array();
                foreach($data as $k => $attribute)
                {
                    $attribute_ids[] = $attribute['StoreProductAttribute']['attribute_id'];
                }
                $map = $this->StoreAttribute->getNameMap($attribute_ids);
                foreach($data as $k => $attribute)
                {
                    $attribute = $attribute['StoreProductAttribute'];
                    $attributes[] = array(
                        'attribute_id' => $attribute['attribute_id'],
                        'name' => $map[$attribute['attribute_id']],
                        'plus' => (int)$attribute['plus'],
                        'attribute_price' => $attribute['attribute_price']
                    );
                }
            }
        }

        $this->set(array(
            'attributes' => $attributes,
        ));
    }
    
    ////////////////////////////////////////////////////////frontend////////////////////////////////////////////////////////
    public function load_attribute_list($product_category_id = null)
    {
        return $this->StoreAttribute->loadAttributeList($product_category_id);
    }
    
    public function load_price()
    {
        $data = $this->request->data;
        $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
        $price = $this->StoreAttribute->calculateAttributePrice($data['product_id'], $data['attribute_id']);
        echo $storeHelper->formatMoney($price);
        exit;
    }
}