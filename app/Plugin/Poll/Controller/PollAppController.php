<?php 
App::uses('AppController', 'Controller');
class PollAppController extends AppController{
	public function beforeFilter() {
		if (Configure::read("Poll.poll_consider_force"))
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