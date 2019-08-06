<?php
/**
* 
*/
class GiftCategoriesController extends GiftAppController
{
    public $components = array('Paginator');

    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->admin_url = '/admin/gift/gift_categories/';
        $this->set('admin_url', $this->admin_url);
        $this->loadModel("Language");
    }

    //////////////////////////////////////backend//////////////////////////////////////
	public function admin_index(){

		$this->Paginator->settings = array(
            'limit' => 5,
            'order' => array(
                'id' => 'ASC'
            )
        );
		$aCategories = $this->Paginator->paginate('GiftCategory');
        $this->set('aCategories', $aCategories);
        $this->set('title_for_layout', __d('gift','Categories'));

    }

    public function admin_ajax_create($iId = null){
        
        if( $iId ){
            $aCategory = $this->GiftCategory->findById($iId);            
            $this->_checkExistence($aCategory);
        }else{
            $aCategory = $this->GiftCategory->initFields();
        }

        $this->request->data = $aCategory;
    }

    public function admin_ajax_save(){

        $this->autoRender = false;
        $iId = $this->request->data['GiftCategory']['id'];
        
        $this->GiftCategory->set($this->request->data);
        $this->_validateData($this->GiftCategory);
        if($this->GiftCategory->save())
        {
            if( empty($iId) )
            {
                $langs = array_keys($this->Language->getLanguages());
                foreach ($langs as $lKey) {
                    $this->GiftCategory->locale = $lKey;
                    $this->GiftCategory->saveField('name', $this->request->data['GiftCategory']['name']);
                }
                foreach ($langs as $lKey) {
                    $this->GiftCategory->locale = $lKey;
                    $this->GiftCategory->saveField('description', $this->request->data['GiftCategory']['description']);
                }
                $this->_jsonSuccess(__d('gift', 'Successfully created'), true);
            }
            $this->_jsonSuccess(__d('gift', 'Successfully updated'), true);
        }
        $this->_jsonError(__d('gift', 'Something went wrong, please try again'), true);
    }

    public function admin_do_active($iId, $enable = null){

    	if( !$this->GiftCategory->isIdExist($iId) )
        {
            $this->_redirectError(__d('gift', 'This category does not exist'), $this->referer());
    	}
        else
        {
	    	$this->GiftCategory->id = $iId;
	    	$this->GiftCategory->save(array('enable' => $enable));
            $this->_redirectSuccess(__d('gift', 'Successfully updated'), $this->referer());
    	}
    }

    public function admin_delete($iId){

    	if( !$this->GiftCategory->isIdExist($iId) )
        {
            $this->_redirectError(__d('gift', 'This category does not exist'), $this->referer());
        }
        else if($this->GiftCategory->isHasGifts($iId) )
        {
    		$this->_redirectError(__d('gift', 'There are gifts belong to this category. Please remove them first.'), $this->referer());
    	}
        else
        {
    		$this->GiftCategory->deleteCategory($iId);
            $this->_redirectSuccess(__d('gift', 'Successfully deleted'), $this->referer());
    	}
    }
    
    public function admin_ajax_translate($id) {

        if (!empty($id)) {
            $category = $this->GiftCategory->getCatById($id);
            $this->set('category', $category);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }

    public function admin_ajax_translate_save() {

        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $this->GiftCategory->id = $this->request->data['id'];
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $this->GiftCategory->locale = $lKey;
                    if ($this->GiftCategory->saveField('name', $sContent)) {
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
    
    //////////////////////////////////////frontend//////////////////////////////////////
    public function get_categories()
    {
        $this->autoRender = false;
        return $this->GiftCategory->getCategories();
    }
}