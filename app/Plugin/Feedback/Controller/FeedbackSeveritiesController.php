<?php
/**
* 
*/
class FeedbackSeveritiesController extends FeedbackAppController
{

	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);

        $this->url_severities = '/feedback_severities';
        $this->set('url_severities', '/feedback_severities');
    }

    public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_index(){

		//pr($this->_getUser());die;
		$this->loadModel('User');
		$this->Paginator->settings = array(
			'limit' => 5,
			'order'	=> array(
					'id' => 'ASC'
				)
		);
		$aSeverities = $this->Paginator->paginate('FeedbackSeverity');
              //  debug($aSeverities);die();
		foreach ($aSeverities as $key => $aSeverity) {
                        $creator = $this->User->findById($aSeverity['FeedbackSeverity']['user_id']);
                        if($creator){
                            $aSeverities[$key]['FeedbackSeverity']['User'] = $creator['User'];			
                        }
		}		
		$this->set('aSeverities', $aSeverities);

        $this->set('title_for_layout', __d('feedback', 'Feedback Severities'));
    }

    public function admin_ajax_create($iId = null){
		$IsEdit = false;
    	if($iId){
			$IsEdit = true;
    		$aSeverity = $this->FeedbackSeverity->findById($iId);
    		$this->_checkExistence($aSeverity);
    	}else{
    		$aSeverity = $this->FeedbackSeverity->initFields();
    	}

		$this->request->data = $aSeverity;
		
		$this->set('aSeverity', $aSeverity);
        $this->set('IsEdit', $IsEdit);

    }

    public function admin_ajax_save(){

        $this->autoRender = false;
        $iId = $this->request->data['FeedbackSeverity']['id'];

        if( empty($iId) ){ //create
            $this->FeedbackSeverity->create();
            $this->request->data['FeedbackSeverity']['user_id'] = MooCore::getInstance()->getViewer(true);
            if($this->FeedbackSeverity->save($this->request->data)){
                foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                    $this->FeedbackSeverity->locale = $lKey;
                    $this->FeedbackSeverity->saveField('name', $this->request->data['FeedbackSeverity']['name']);
                }
                $this->Session->setFlash(__d('feedback', 'Successfully created'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }else{ //edit
            $this->FeedbackSeverity->id = $iId;

            if($this->FeedbackSeverity->save($this->request->data)){
                $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }

        $this->FeedbackSeverity->set($this->request->data);
        $this->_validateData($this->FeedbackSeverity);
        
        $response['result'] = 1;
        echo json_encode($response);
        
    }

    public function admin_do_active($iId, $is_active = null){

    	if( !$this->FeedbackSeverity->isIdExist($iId) ){
    		$this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));    		
    	}else{
	    	$this->FeedbackSeverity->id = $iId;
	    	$this->FeedbackSeverity->save(array('is_active' => $is_active));
    		$this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));    		
    	}

    	$this->redirect( $this->referer() );

    }

    public function admin_delete($iId){

    	if( !$this->FeedbackSeverity->isIdExist($iId) ){
    		$this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));    		
    	}else{
    		$this->FeedbackSeverity->delete($iId);
    		$this->Session->setFlash(__d('feedback', 'Successfully deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));    		
    	}

    	$this->redirect( $this->referer() );

	}
	
	public function admin_ajax_translate($id) {
        if (!empty($id)) {
            $severityModel = MooCore::getInstance()->getModel('Feedback.FeedbackSeverity');
            $severity = $severityModel->getSevById($id);
            $this->set('severity', $severity);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }

    public function admin_ajax_translate_save() {
        $severityModel = MooCore::getInstance()->getModel('Feedback.FeedbackSeverity');
        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $severityModel->id = $this->request->data['id'];
                $origin_name = "";
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $severityModel->locale = $lKey;
                    if ($severityModel->saveField('name', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                    if($severityModel->_default_locale == $lKey)
                    {
                        $origin_name = $sContent;
                    }
                }
                if($origin_name != "")
                {
                    $severityModel->create();
                    $severityModel->updateAll(array(
                        "name" => "'$origin_name'"
                    ), array(
                        "FeedbackSeverity.id" => $this->request->data['id']
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