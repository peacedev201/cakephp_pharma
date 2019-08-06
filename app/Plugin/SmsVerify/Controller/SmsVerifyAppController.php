<?php 
App::uses('AppController', 'Controller');
class SmsVerifyAppController extends AppController{
	public function beforeFilter() {
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
		{
			$this->_checkPermission(array('super_admin' => 1));
		}
		
		parent::beforeFilter();
	}
}