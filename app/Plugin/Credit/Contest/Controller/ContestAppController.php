<?php 
App::uses('AppController', 'Controller');
class ContestAppController extends AppController{
    protected function _redirectError($msg, $url)
    {
        if($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $this->redirect($url);
    }
    public function beforeFilter()
    { 
        if (Configure::read("Contest.contest_by_pass_force_login"))
        {
            $this->check_force_login = false;
        }
        parent::beforeFilter();
        $this->set('is_mobile', MooCore::getInstance()->isMobile(null));
        $isandroidApp = $this->request->is('androidApp');
        $isiosApp = $this->request->is('iosApp');
        $this->set('isiosApp', $isiosApp);
        $this->set('isandroidApp', $isandroidApp);
    }
    
    protected function _redirectSuccess($msg, $url)
    {
        if($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $this->redirect($url);
    }

    protected function _jsonSuccess($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $data = array(
            'result' => 1,
            'message' => $msg
        );
        if($params != null)
        {
            $data = array_merge($data, $params);
        }
        echo json_encode($data);
        exit;
    }
    
    protected function _jsonError($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $data = array(
            'result' => 0,
            'message' => $msg
        );
        if($params != null)
        {
            $data = array_merge($data, $params);
        }
        
        echo json_encode($data);
        exit;;
    }
}