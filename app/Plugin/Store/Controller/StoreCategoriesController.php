<?php 
class StoreCategoriesController extends StoreAppController
{
    public $components = array('Paginator');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->admin_link = $this->request->base.'/admin/store/store_categories/';
        $this->admin_url = '/admin/store/store_categories/';
        $this->set('admin_link', $this->admin_link);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.StoreCategory');
    }
    
    ////////////////////////////////////////////////////////backend admin////////////////////////////////////////////////////////
    function admin_index($parent_id = 0)
    {
        if($parent_id != 0 && !$this->StoreCategory->isStoreCategoryExist((int)$parent_id))
        {
            $this->_redirectError(__d('store', 'Parent category not found'), $this->admin_url);
        }
        
        //search
        $search = !empty($this->request->query) ? $this->request->query : array();
        $search['parent_id'] = $parent_id;

        $storeCategories = $this->StoreCategory->loadManagerPaging($this, $search);
        $this->set(array(
            'storeCategories' => $storeCategories,
            'parentCategory' => $parent_id > 0 ? $this->StoreCategory->loadStoreCategoryDetail($parent_id) : null,
            'parent_id' => $parent_id,
            'search' => $search,
            'title_for_layout' => __d('store', 'Store Categories')
        ));
    }
    
    public function admin_create($id = null)
    {		
        $parent_id = !empty($this->request->query['sub']) ? $this->request->query['sub'] : 0;
        if($parent_id != 0 && !$this->StoreCategory->isStoreCategoryExist((int)$parent_id))
        {
            $this->_redirectError(__d('store', 'Parent category not found'), $this->admin_url);
        }
        else if(!empty($id) && !$this->StoreCategory->isStoreCategoryExist($id))
        {
            $this->_redirectError(__d('store', 'Category not found'), $this->admin_url);
        }
        else 
        {
            //load attribute
            $translate = $storeCats = null;
            if (!empty($id))
            {
                $storeCategory = $this->StoreCategory->loadStoreCategoryDetail($id);
                $storeCategory = $storeCategory['StoreCategory'];
                $parent_id = $storeCategory['parent_id'];
                $translate = $this->StoreCategory->loadListTranslate($id);
            }
            else 
            {
                $storeCategory = $this->StoreCategory->initFields();
                $storeCategory = $storeCategory['StoreCategory'];
            }

            $this->set(array(
                'storeCategory' => $storeCategory,
                'parent_id' => $parent_id,
                'parentCategory' => $parent_id > 0 ? $this->StoreCategory->loadStoreCategoryDetail($parent_id) : null,
                'translate' => $translate,
                'title_for_layout' => __d('store', 'Store Categories')
            ));
        }
	}
	
	public function admin_save()
    {
		$this->autoRender = false;
        $data = $this->request->data;
        if($data['parent_id'] != 0 && !$this->StoreCategory->isStoreCategoryExist((int)$data['parent_id']))
        {
            $this->_jsonError(__d('store', 'Parent category not found'));
        }
        else if($this->StoreCategory->isStoreCategoryExist($data['id']))
        {
            $this->StoreCategory->id = $data['id'];
        }
        else 
        {
            $data['ordering'] = $this->generateOrdering('StoreCategory', true);
        }
        
        $this->StoreCategory->set($data);
        $this->_validateData($this->StoreCategory);

        if($this->StoreCategory->save())
        {
            $id = $this->StoreCategory->id;
            if(empty($data['enable']) || $data['enable'] == 0)
            {
                $this->StoreCategory->activeChildField($id, 'enable', 0);
            }
            if(empty($data['enable']) || $data['enable'] == 1)
            {
                $this->StoreCategory->enableParentField($id);
            }
                    
            //show message
            $redirect = $this->admin_link;
            if($data['parent_id'] > 0)
            {
                $redirect = $this->admin_link.'index/'.$data['parent_id'];
            }
            if($this->request->data['save_type'] == 1)
            {
                $redirect = $this->admin_link.'create/'.$id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }	
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));
	}
    
    public function admin_enable()
    {		
        $this->active($this->request->data, 1, 'enable');
    }
    
    public function admin_disable()
    {
        $this->active($this->request->data, 0, 'enable');
    }

	private function active($data, $value, $task)
    {
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreCategory->isStoreCategoryExist($id))
                {
                    $this->StoreCategory->activeField($id, $task, $value);
                    $this->StoreCategory->activeChildField($id, $task, $value);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function admin_ordering()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $k => $id)
            {
                $this->StoreCategory->saveOrdering($id, $data['ordering'][$k]);
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function admin_delete()
    {
        $data = $this->request->data;
        $redirect = $this->admin_url;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreCategory->checkHasChildren($id))
                {
                    $category = $this->StoreCategory->loadStoreCategoryDetail($id);
                    $this->_redirectError(sprintf(__d('store', 'Please delete sub categories of %s first.'), $category['StoreCategory']['name']), $this->referer());
                }
                else if($this->StoreCategory->isContainProduct($id))
                {
                    $this->_redirectError(__d('store', 'Can not delete this category! Please delete related products first.'), $this->referer());
                }
                else
                {
                    $category = $this->StoreCategory->loadStoreCategoryDetail($id);
                    $redirect .= 'index/'.$category['StoreCategory']['parent_id'];
                    $this->StoreCategory->delete($id);
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully deleted'), $redirect);
    }
    
    public function suggest_category()
    {
        $data = $this->StoreCategory->suggestCategory($this->request->data['keyword']);
        echo json_encode($data);
        exit;
    }
}