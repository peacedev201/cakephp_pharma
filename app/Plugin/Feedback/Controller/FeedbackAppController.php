<?php 

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('AppController', 'Controller');

class FeedbackAppController extends AppController{
    
    public $components = array('Paginator');
    public $check_force_login = true;

	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);

        $this->url_admin_feebback = '/admin/feedback';
        $this->url_feedback = '/feedbacks/feedbacks';
        $this->url_ajax_create = '/ajax_create';
        $this->url_ajax_save = '/ajax_save';
        $this->url_create = '/create';
        $this->url_delete = '/delete';
        $this->url_edit = '/edit';

        $this->set('url_admin_feebback', '/admin/feedback');
        $this->set('url_feedback', '/feedbacks/feedbacks');
        $this->set('url_ajax_create', '/ajax_create');
        $this->set('url_ajax_save', '/ajax_save');
        $this->set('url_create', '/create');
        $this->set('url_delete', '/delete');
        $this->set('url_edit', '/edit');
        $this->set('url_ajax_edit', '/ajax_edit');
    }

    public function beforeFilter()
    {
        if (Configure::read("Feedback.feedback_consider_force"))
	    {
            $this->check_force_login = false;
        }
        parent::beforeFilter();

        if(Multibyte::strpos($this->request->params['action'], 'admin') > -1){
            $this->_checkPermission(array('super_admin' => 1));    
        }
        
    }
    
    public function checkFeedbackPermission($permission, $user_id = null)
    {
        $userParams = $this->_getUserRoleParams();
        if($userParams != null && in_array($permission, $userParams))
        {
            if($user_id != null && $user_id != MooCore::getInstance()->getViewer(true))
            {
                return false;
            }
            return true;
        }
        return false;
    }

    public function checkBlockUser()
    {
        $this->loadModel('Feedback.FeedbackBlockuser');
        $uid = MooCore::getInstance()->getViewer(true);
        $aBlockuser = $this->FeedbackBlockuser->findById( $uid );
        if($aBlockuser){
            if($aBlockuser['FeedbackBlockuser']['block_feedback']){
                return true;
            }
            if($aBlockuser['FeedbackBlockuser']['block_comment']){              
                return true;
            }           
        }
        return false;
    }
    
}