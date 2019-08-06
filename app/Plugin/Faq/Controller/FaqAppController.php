<?php 
App::uses('AppController', 'Controller');
class FaqAppController extends AppController{
	public $check_force_login = true;
	public function beforeFilter() {
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
		{
			$this->_checkPermission(array('super_admin' => 1));
		}
		
		if (Configure::read("Faq.faq_consider_force"))
		{
			$this->check_force_login = false;
		}
		parent::beforeFilter();
	}
}