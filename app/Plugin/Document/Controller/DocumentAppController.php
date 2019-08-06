<?php 
App::uses('AppController', 'Controller');
class DocumentAppController extends AppController{
	public $check_force_login = true;
	public function beforeFilter() {        
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
		{
			$this->_checkPermission(array('super_admin' => 1));
		}
		
		if (Configure::read("Document.document_consider_force"))
		{
			$this->check_force_login = false;
		}
		parent::beforeFilter();
    }
}