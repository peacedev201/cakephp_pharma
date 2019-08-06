<?php
/**
* 
*/
class FeedbackBlockusersController extends FeedbackAppController
{

	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);

        $this->url_blockusers = '/feedback_blockusers';
        $this->set('url_blockusers', '/feedback_blockusers');
    }

    public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_index(){

		$this->loadModel('User');
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $cond = array();
        if($keyword != '')
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond[] = "User.name LIKE '%$keyword%'";
        }
		$this->Paginator->settings = array(
            'conditions' => $cond,
			'limit' => 15,
			'order'	=> array(
					'id' => 'ASC'
				)
		);
		$aUsers = $this->Paginator->paginate('User');

        foreach ($aUsers as $key => $aUser) {
            if($aBlock = $this->FeedbackBlockuser->findById($aUser['User']['id'])){
                $aUsers[$key]['User']['block_comment'] = $aBlock['FeedbackBlockuser']['block_comment'] ;
                $aUsers[$key]['User']['block_feedback'] = $aBlock['FeedbackBlockuser']['block_feedback'] ;
            }else{
                $aUsers[$key]['User']['block_comment'] = 0 ;
                $aUsers[$key]['User']['block_feedback'] = 0 ;
            }        
        }
				
		$this->set('aUsers', $aUsers);
        $this->set('keyword', $keyword);
        $this->set('title_for_layout', __d('feedback', 'Feedback Block Users'));
    }

    public function admin_do_active($sType, $iId, $is_active = null){
        
        if($sType == 'comment'){
            $sType = 'block_comment';
        }else{
            $sType = 'block_feedback';
        }

    	if( !$this->FeedbackBlockuser->isIdExist($iId) ){
    		$this->FeedbackBlockuser->create();
            $this->FeedbackBlockuser->save(array('id' => $iId, $sType => $is_active));
    	}else{
	    	$this->FeedbackBlockuser->id = $iId;
	    	$this->FeedbackBlockuser->save(array($sType => $is_active));    		 		
    	}

        $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));   
    	$this->redirect( $this->referer() );

    }
}