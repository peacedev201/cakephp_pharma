<?php 
App::uses('AppController', 'Controller');
class ReminderAppController extends AppController{
	public $check_subscription = false;
	public $check_force_login = false;
	public function beforeFilter() {
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
		{
			$this->_checkPermission(array('super_admin' => 1));
		}
		parent::beforeFilter();
	}
}