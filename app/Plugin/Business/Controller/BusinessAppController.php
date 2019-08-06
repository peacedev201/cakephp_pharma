<?php 
App::uses('AppController', 'Controller');
class BusinessAppController extends AppController
{
    public function beforeFilter() {
        if (Configure::read("Business.business_by_pass_force_login"))
		{
			$this->check_force_login = false;
		}
        parent::beforeFilter();
        
        $cUser = $this->_getUser();
        $this->is_admin = !empty($cUser) ? $cUser['Role']['is_admin'] : false;
        $default_location_name = CakeSession::read(BUSINESS_DEFAULT_LOCATION_NAME);
        if($default_location_name == null)
        {
            $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
            $location = $mBusinessLocation->getDefaultLocation();
            if($location != null)
            {
                $mBusinessLocation->setDefaultLocation($location['BusinessLocation']['name']);
                $default_location_name = $location['BusinessLocation']['name'];
            }
        }
        
        //get user role
        $this->role_param = $this->_getUserRoleParams();
        
        //check intgegrate store
        App::import('Model', 'Business.BusinessStore');
        $mBusinessStore = new BusinessStore();
        $this->is_integrate_store = $mBusinessStore->isIntegrateStore();
        
        $this->set(array(
            'is_loggedin' => $this->isLoggedIn(),
            'is_admin' => $this->is_admin,
            'default_location_name' => $default_location_name,
            'is_app' => $this->isApp(),
			'title_for_layout' => '',
            'is_integrate_store' => $this->is_integrate_store
        ));
    }
    
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
    
    protected function _setFlash($msg, $success = true)
    {
        if($success)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        else
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
    }
    
    protected function isLoggedIn()
    {
        if(MooCore::getInstance()->getViewer(true) == null)
        {
            return false;
        }
        return true;
    }
    
    protected function buildLinkSearchParams($data)
    {
        $result = '';
        if($data != null)
        {
            foreach($data as  $k => $v)
            {
                $result[] = $k.'='.$v;
            }
            $result = implode('&', $result);
        }
        return $result;
    }
}