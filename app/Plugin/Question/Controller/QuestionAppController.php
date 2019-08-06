<?php 
App::uses('AppController', 'Controller');
class QuestionAppController extends AppController{
    public $plugin = 'Question';
    
    public function beforeFilter() {
    	if (Configure::read("Question.question_consider_force"))
    	{
    		$this->check_force_login = false;
    	}
    	
    	parent::beforeFilter();
    	if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
    	{
    		$this->_checkPermission(array('super_admin' => 1));
    	}
    }
}