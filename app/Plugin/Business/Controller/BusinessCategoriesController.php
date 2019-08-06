<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessCategoriesController extends BusinessAppController {
    public $paginate = array(
            'maxLimit' => 100,
            'limit' => 10,
        );
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Business.BusinessCategory');
        $this->loadModel('Business.Business');
    }

    public function admin_index($parent_id = 0) {
        //valid parent
        $parent_cat = $this->BusinessCategory->findById($parent_id);
        if($parent_cat != null && $parent_cat['BusinessCategory']['parent_id'] > 0)
        {
            $this->_redirectError(__d('business', 'Category not found'), '/admin/business/business_categories');
        }
        
        $search = !empty($this->request->query) ? $this->request->query : array();
        $cond = array('BusinessCategory.parent_id' => $parent_id);
        if(isset($search['filter']) && $search['filter'] != '')
        {
            switch($search['filter'])
            {
                case 0:
                    $cond['BusinessCategory.enable'] = 1;
                    break;
                case 1:
                    $cond['BusinessCategory.enable'] = 0;
                    break;
                case 2:
                    $cond['BusinessCategory.user_create'] = 0;
                    break;
                case 3:
                    $cond['BusinessCategory.user_create'] = 1;
                    break;
            }
        }
        $cats = $this->paginate('BusinessCategory', $cond);
        $cat_paths = $this->BusinessCategory->getPath($parent_id);
        $this->set('parent_id', $parent_id);
        $this->set('cat_paths', $cat_paths);
        $this->set('cats', $cats);
        $this->set('search', $search);
        $this->set('title_for_layout', __d('business', 'Categories Manager'));
    }
   
    public function admin_suggest_category()
    {
        $data = $this->BusinessCategory->suggestFeatureCategory($this->request->data['keyword']);
        echo json_encode($data);
        exit;
    }
    public function admin_create($parent_id = 0, $id = null) {
        $bIsEdit = false;
        $parent_list  = $this->BusinessCategory->findCatsByParent($parent_id);
        if (!empty($id)) {
            $cat = $this->BusinessCategory->findById($id);
            $bIsEdit = true;
        } else {
            $cat = $this->BusinessCategory->initFields();
        }
        $this->set('parent_list', $parent_list);   
        $this->set('parent_id', $parent_id);   
        $this->set('cat', $cat);
        $this->set('bIsEdit', $bIsEdit);
    }
   
    public function admin_save() {
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->BusinessCategory->id = $this->request->data['id'];
        }
        if(empty($this->request->data['parent_id'])){
            $this->request->data['parent_id'] = 0 ;
        }
        else 
        {
            $parent_cat = $this->BusinessCategory->findById($this->request->data['parent_id']);
            if($parent_cat == null || $parent_cat['BusinessCategory']['parent_id'] > 0)
            {
                $this->_jsonError(__d('business', 'Parent category not found'));
            }
        }
        $this->BusinessCategory->set($this->request->data);
		
        $this->_validateData($this->BusinessCategory);
        $this->BusinessCategory->save();

        //update all child enable
        $this->BusinessCategory->updateAll(array(
            'BusinessCategory.enable' => $this->data['enable']
        ), array(
            'BusinessCategory.parent_id' => $this->data['id']
        ));

        if (!$bIsEdit) {
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->BusinessCategory->locale = $lKey;
                $this->BusinessCategory->saveField('name', $this->request->data['name']);
            }
        }
        
        $this->Session->setFlash(__d('business', 'Category has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id){
        $this->autoRender = false;
        $this->loadModel('Business.BusinessCategoryItem');
        $this->loadModel('Business.Business');
        if($this->BusinessCategory->hasAny(array(
            'BusinessCategory.parent_id' => $id
        )))
        {
            $this->_redirectError(__d('business', 'Please delete sub category first'), $this->referer());
        }
        else if($this->BusinessCategoryItem->hasAny(array(
            'BusinessCategoryItem.business_category_id' => $id
        )))
        {
            $this->_redirectError(__d('business', 'Can not delete this category. Category contains businesses'), $this->referer());
        }
        else {
            $this->BusinessCategory->deleteBusinessCategory($id);
            $this->_redirectSuccess(__d('business', 'Category has been deleted'), $this->referer());
        }
    }
    public function admin_save_order(){
        $this->autoRender = false;       
        foreach ($this->request->data['order'] as $cat_id => $ordering) {
            $this->BusinessCategory->clear();
            $this->BusinessCategory->id = $cat_id;
            $this->BusinessCategory->save(array('ordering' => $ordering));
        }
        $this->Session->setFlash(__d('business', 'Order saved'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }
    
    public function admin_ajax_translate($id) {
        if (!empty($id)) {
            $categoryModel = MooCore::getInstance()->getModel('Business.BusinessCategory');
            $category = $categoryModel->getCatById($id);
            $this->set('category', $category);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }

    public function admin_ajax_translate_save() {
        $categoryModel = MooCore::getInstance()->getModel('Business.BusinessCategory');
        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $categoryModel->id = $this->request->data['id'];
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $categoryModel->locale = $lKey;
                    if ($categoryModel->saveField('name', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                }
            } else {
                $response['result'] = 0;
            }
        } else {
            $response['result'] = 0;
        }
        echo json_encode($response);
    }
    
    public function categories($param = null)
    {
		$this->set(array(
			'title_for_layout' => ''
		));
        $parent_categories = $this->BusinessCategory->getCategories(null, 0);
        $multi_level = false;
        $breadcrumb = $param_text = null;
        if($param != null && is_numeric($param))
        {
            if(!$this->BusinessCategory->isCategoryExist($param))
            {
                $this->_redirectError(__d('business', 'Category not found'), '/pages/error');
            }
            $categories = $this->BusinessCategory->getMapCategories($param);

            $category = $this->BusinessCategory->findById($param);
            $param_text = $category['BusinessCategory']['name'];
            
            //breadcrumb
            $breadcrumb = $this->BusinessCategory->getBreadCrumb($param);
        }
        else if($param != null)
        {
            $categories = $this->BusinessCategory->getCategories(null, null, null, $param);
            $param_text = $param;
        }
        else
        {
            $multi_level = true;
            $categories = $this->BusinessCategory->getMapCategories();
        }

        $this->set(array(
            'parent_categories' => $parent_categories,
            'categories' => $categories,
            'param' => $param,
            'param_text' => $param_text,
            'multi_level' => $multi_level,
            'breadcrumb' => $breadcrumb,
            'current_link' => $this->request->base.$this->request->here(false),
        ));
    }
    
    public function admin_upload_icon()
    {
        $this->autoRender = false;
        $allowedExtensions = explode(',', BUSINESS_EXT_PHOTO);
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(BUSINESS_FILE_PATH);

        if (!empty($result['success'])) 
        {
            $result['url'] = BUSINESS_FILE_URL;
            $result['path'] = $this->request->base.'/'.BUSINESS_FILE_URL.$result['filename'];
            $result['file'] = BUSINESS_FILE_PATH . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }
}