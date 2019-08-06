<?php
/**
* 
*/
class FeedbackCategoriesController extends FeedbackAppController
{
	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);

        $this->url_categories = '/feedback_categories';
        $this->set('url_categories', '/feedback_categories');
    }

    public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_index(){

		$this->Paginator->settings = array(
            'limit' => 5,
            'order' => array(
                'id' => 'ASC'
            )
        );
		$aCategories = $this->Paginator->paginate('FeedbackCategory');
        $this->set('aCategories', $aCategories);
        $this->set('title_for_layout', __d('feedback','Categories'));

    }

    public function admin_ajax_create($iId = null){
        $IsEdit = false;
        if( $iId ){
            $IsEdit = true;
            $aCategory = $this->FeedbackCategory->findById($iId);            
            $this->_checkExistence($aCategory);
        }else{
            $aCategory = $this->FeedbackCategory->initFields();
        }

        $this->request->data = $aCategory;

        $this->set('category', $aCategory);
        $this->set('IsEdit', $IsEdit);
        
    }

    public function admin_ajax_save(){

        $this->autoRender = false;
        $iId = $this->request->data['FeedbackCategory']['id'];

        if( empty($iId) ){ //create
            $this->FeedbackCategory->create();

            if($this->FeedbackCategory->save($this->request->data)){
                foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                    $this->FeedbackCategory->locale = $lKey;
                    $this->FeedbackCategory->saveField('name', $this->request->data['FeedbackCategory']['name']);
                }
                
                $this->Session->setFlash(__d('feedback', 'Successfully created'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }else{ //edit
            $this->FeedbackCategory->id = $iId;
            if($this->FeedbackCategory->save($this->request->data)){
                $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }

        $this->FeedbackCategory->set($this->request->data);
        $this->_validateData($this->FeedbackCategory);
        
        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_do_active($iId, $is_active = null){

    	if( !$this->FeedbackCategory->isIdExist($iId) ){
    		$this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
    	}else{
	    	$this->FeedbackCategory->id = $iId;
	    	$this->FeedbackCategory->save(array('is_active' => $is_active));
    		$this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));    		
    	}

    	$this->redirect( $this->referer() );

    }

    public function admin_delete($iId){

    	if( !$this->FeedbackCategory->isIdExist($iId) ){
    		$this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));    		
    	}else{
    		$this->FeedbackCategory->deleteCategory($iId);
    		$this->Session->setFlash(__d('feedback', 'Successfully deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));    		
    	}

    	$this->redirect( $this->referer() );

    }

    public function admin_ajax_translate($id) {
        if (!empty($id)) {
            $categoryModel = MooCore::getInstance()->getModel('Feedback.FeedbackCategory');
            $category = $categoryModel->getCatById($id);
            $this->set('category', $category);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }

    public function admin_ajax_translate_save() {
        $categoryModel = MooCore::getInstance()->getModel('Feedback.FeedbackCategory');
        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $categoryModel->id = $this->request->data['id'];
                $origin_name = "";
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $categoryModel->locale = $lKey;
                    if ($categoryModel->saveField('name', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                    if($categoryModel->_default_locale == $lKey)
                    {
                        $origin_name = $sContent;
                    }
                }
                if($origin_name != "")
                {
                    $categoryModel->create();
                    $categoryModel->updateAll(array(
                        "name" => "'$origin_name'"
                    ), array(
                        "FeedbackCategory.id" => $this->request->data['id']
                    ));
                }
            } else {
                $response['result'] = 0;
            }
        } else {
            $response['result'] = 0;
        }
        echo json_encode($response);
    }
}