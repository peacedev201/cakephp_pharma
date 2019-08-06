<?php 
App::uses('AppController', 'Controller');
class SliderAppController extends AppController{
    public function beforeFilter() {
        parent::beforeFilter();
        if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
        {
            $this->_checkPermission(array('super_admin' => 1));
        }
    }
}