<?php
/**
* 
*/
class FeedbackBlockipsController extends FeedbackAppController
{

	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);

        $this->url_blockips = '/feedback_blockips';
        $this->set('url_blockips', '/feedback_blockips');
    }

    public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_index(){

		$this->loadModel('User');
		$this->Paginator->settings = array(
			'limit' => 5,
			'order'	=> array(
					'id' => 'ASC'
				)
		);
		$aIps = $this->Paginator->paginate('FeedbackBlockip');
		$this->set('aIps', $aIps);
        $this->set('title_for_layout', __d('feedback', 'Feedback Block ips'));
    }

    public function admin_ajax_create($iId = null){

    	if($iId){
    		$aIp = $this->FeedbackBlockip->findById($iId);
    		$this->_checkExistence($aIp);
    	}else{
    		$aIp = $this->FeedbackBlockip->initFields();
    	}

    	$this->request->data = $aIp;

    }

    public function admin_ajax_save(){

        $this->autoRender = false;
        $iId = $this->request->data['FeedbackBlockip']['id'];

        if( empty($iId) ){ //create
            $this->FeedbackBlockip->create();
            $this->request->data['FeedbackBlockip']['user_id'] = MooCore::getInstance()->getViewer(true);
            if($this->FeedbackBlockip->save($this->request->data)){
                $this->Session->setFlash(__d('feedback', 'Successfully created'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }else{ //edit
            $this->FeedbackBlockip->id = $iId;

            if($this->FeedbackBlockip->save($this->request->data)){
                $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }

        $this->FeedbackBlockip->set($this->request->data);
        $this->_validateData($this->FeedbackBlockip);
        
        $response['result'] = 1;
        echo json_encode($response);
        
    }

    public function admin_do_active($sType, $iId, $is_active = null){
        
        if($sType == 'comment'){
            $sType = 'blockip_comment';
        }else{
            $sType = 'blockip_feedback';
        }

        if( !$this->FeedbackBlockip->isIdExist($iId) ){
            $this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));         
        }else{
            $this->FeedbackBlockip->id = $iId;
            $this->FeedbackBlockip->save(array($sType => $is_active));
            $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
        }

        $this->redirect( $this->referer() );

    }

    public function admin_delete($iId){

        if( !$this->FeedbackBlockip->isIdExist($iId) ){
            $this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));         
        }else{
            $this->FeedbackBlockip->delete($iId);
            $this->Session->setFlash(__d('feedback', 'Successfully deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
        }

        $this->redirect( $this->referer() );

    }
}