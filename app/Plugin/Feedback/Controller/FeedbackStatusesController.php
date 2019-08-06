<?php
/**
* 
*/
class FeedbackStatusesController extends FeedbackAppController
{
	// public $helpers = array('Form', 'Feedback.Farbtastic');
	
	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);

        $this->url_statuses = '/feedback_statuses';
        $this->set('url_statuses', '/feedback_statuses');
    }

    public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_index(){

		$this->Paginator->settings = array(
			'limit' => 5,
			'order'	=> array(
					'id' => 'ASC'
				)
		);
		$aStatuses = $this->Paginator->paginate('FeedbackStatus');
		$this->set('aStatuses', $aStatuses);
        $this->set('title_for_layout',  __d('feedback', 'Status'));

    }

    public function admin_ajax_create($iId = null){
		$IsEdit = false;
    	if($iId){
			$IsEdit = true;
    		$aStatus = $this->FeedbackStatus->findById($iId);
    		$this->_checkExistence($aStatus);
    	}else{
    		$aStatus = $this->FeedbackStatus->initFields();
    	}

		$this->request->data = $aStatus;
		
		$this->set('aStatus', $aStatus);
        $this->set('IsEdit', $IsEdit);
    }

    public function admin_ajax_save(){

        $this->autoRender = false;
        $iId = $this->request->data['FeedbackStatus']['id'];
        $color = $this->request->data['FeedbackStatus']['color'];

        if( empty($iId) ){ //create
            $this->FeedbackStatus->create();

            if($this->FeedbackStatus->save($this->request->data)){
                foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                    $this->FeedbackStatus->locale = $lKey;
                    $this->FeedbackStatus->saveField('name', $this->request->data['FeedbackStatus']['name']);
                }
                $this->Session->setFlash(__d('feedback', 'Successfully created'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }else{ //edit
            $this->FeedbackStatus->id = $iId;

            if($this->FeedbackStatus->save($this->request->data)){
                $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }

        $this->FeedbackStatus->set($this->request->data);
        $this->_validateData($this->FeedbackStatus);
        
        $response['result'] = 1;
        echo json_encode($response);
        
    }

    public function admin_do_active($iId, $is_active = null){

    	if( !$this->FeedbackStatus->isIdExist($iId) ){
    		$this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));    		
    	}else{
	    	$this->FeedbackStatus->id = $iId;
	    	$this->FeedbackStatus->save(array('is_active' => $is_active));
    		$this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));    		
    	}

    	$this->redirect( $this->referer() );

    }

    public function admin_delete($iId){

    	if( !$this->FeedbackStatus->isIdExist($iId) ){
    		$this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));    		
    	}else{
    		$this->FeedbackStatus->delete($iId);
    		$this->Session->setFlash(__d('feedback', 'Successfully deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));    		
    	}

    	$this->redirect( $this->referer() );

	}
	
	public function admin_ajax_translate($id) {
        if (!empty($id)) {
            $statusModel = MooCore::getInstance()->getModel('Feedback.FeedbackStatus');
            $status = $statusModel->getStaById($id);
            $this->set('status', $status);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }

    public function admin_ajax_translate_save() {
        $statusModel = MooCore::getInstance()->getModel('Feedback.FeedbackStatus');
        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $statusModel->id = $this->request->data['id'];
                $origin_name = "";
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $statusModel->locale = $lKey;
                    if ($statusModel->saveField('name', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                    if($statusModel->_default_locale == $lKey)
                    {
                        $origin_name = $sContent;
                    }
                }
                if($origin_name != "")
                {
                    $statusModel->create();
                    $statusModel->updateAll(array(
                        "name" => "'$origin_name'"
                    ), array(
                        "FeedbackStatus.id" => $this->request->data['id']
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