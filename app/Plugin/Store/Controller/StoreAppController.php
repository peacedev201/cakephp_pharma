<?php 
App::uses('AppController', 'Controller');
define('STORE_URL', Router::url('/', true).'stores/');
define('STORE_PATH', 'stores/');
define('STORE_MANAGER_URL', Router::url('/', true).'stores/manager/');
define('STORE_IMAGE_URL', Router::url('/', true).'store/images/');

class StoreAppController extends AppController{
    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
    }
    
    public function beforeFilter()
	{ 
        if (Configure::read("Store.store_by_pass_force_login"))
		{
			$this->check_force_login = false;
		}
        parent::beforeFilter();
        App::import('Model', 'Store.Store');
        $mStore = new Store();
        App::import('Model', 'Store.StoreProduct');
        $mProduct = new StoreProduct();
        
        $store_id = 0;
        $store = null;
        $this->uid = MooCore::getInstance()->getViewer(true);
        
        /*if (Configure::read('Store.store_integrate_business') && !Configure::read('Business.business_enabled') && !strpos($this->here, '/admin')) 
        {
            if($this->request->is('ajax'))
            {
                $this->_jsonError(__d('store', "Please install Business Plugin to continue"));
            }
            else
            {
                $this->_redirectError(__d('store', "Please install Business Plugin to continue"), "/home");
            }
        }*/

        //detect manager pages
        $isManager = false;
        $allowSeller = false;
        $store_prefix = '';
        $exceptController = array('products', 'wishlists', 'carts', 'orders');

        if(!empty($this->request->params['prefix']) && $this->request->params['prefix'] == 'manager')
        {
			if($this->uid == null)
            {
                if($this->request->is('ajax'))
                {
                    $this->_jsonError(__d('store', "Please login to continue", "Store"));
                }
                else
                {
                    $this->_redirectError(__d('store', "Please login to continue", "Store"), "/users/member_login");
                }
            }
            $isManager = true;
            $store = $mStore->findByUserId($this->uid);
            if($store == null)
            {
                $this->redirect(STORE_URL.'create');
            }
            else 
            {
                //check disable store
                if($store['Store']['enable'] == 0 && 
                   $this->request->params['controller'] != 'Stores')
                {
                    $this->_redirectError('', STORE_MANAGER_URL);
                }
                if($store['Store']['enable'] == 1) 
                {
                    $allowSeller = true;
                }
                $store_id = $store['Store']['id'];
            }
        }
        else 
        {
            if($this->uid == null)
            {
                $this->uid = 0;
            }
        }
        
        $exceptAction = array('share_product_content', 'create_store_content');
        if(in_array($this->request->action, $exceptAction))
        {
            $store_id = $mProduct->getStoreIdByProductId($this->request->pass[0]);
            $store = $mStore->findById($store_id);
        }
        $currency = $mStore->loadDefaultGlobalCurrency();
        
        Configure::write('store.uid', $this->uid);
        Configure::write('store.store_id', $store_id);
        Configure::write('store.email', !empty($store['Store']['email']) ? $store['Store']['email'] : '');
        Configure::write('store.currency_symbol', $currency['Currency']['symbol']);
        Configure::write('store.currency_code', $currency['Currency']['currency_code']);
        
        //get user role
        $role_param = $this->_getUserRoleParams();
        if(!empty($role_param))
        {
            $user = $this->_getUser();
            if(!empty($user['Role']['is_admin']) && $user['Role']['is_admin'] == 1)
            {
                $role_param = 'all';
            }
            Configure::write('store.user_role', $role_param);
        }
        
        //check has store
        $hasStore = false;
        if($this->uid > 0 && !in_array($this->request->action, $exceptAction) && $mStore->hasStore($this->uid))
        {
            $hasStore = true;
        }
        
        //check intgegrate to business
        App::import('Model', 'Store.StoreBusiness');
        $mStoreBusiness = new StoreBusiness();
        $this->is_integrate_to_business = $mStoreBusiness->isIntegrateToBusiness();

        $this->set(array(
            'isManager' => $isManager,
            'allowSeller' => $allowSeller,
            'hasStore' => $hasStore,
            'store_prefix' => $store_prefix,
            'is_mobile' => MooCore::getInstance()->isMobile(null),
            'is_integrate_to_business' => $this->is_integrate_to_business,
            'is_app' => $this->isApp()
        ));
    }
    
    protected function _validateData( $model = null )
	{
		if ( !$model->validates() )
	    {
	    	$errors = $model->invalidFields();	
			
			$response['result'] = 0;    	
	    	$response['message'] = current( current( $errors ) );
			
			echo json_encode($response);
			exit;
	    }
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

    protected function generateOrdering($model_name, $no_store_id = false)
    {
        $this->loadModel($model_name);
        $cond = array();
        if($no_store_id == false)
        {
            $cond = array(
                $model_name.'.store_id' => $this->uid
            );
        }
        $item = $this->$model_name->find('first', array(
            'conditions' => $cond,
            'order' => array($model_name.'.ordering DESC')
        ));
        if($item != null)
        {
            return $item[$model_name]['ordering'] + 1;
        }
        return 1;
    }
    
    protected function getIdFromUrl($url)
    {
        if($url != null)
        {
            $url = explode('-', $url);
            return $url[count($url) - 1];
        }
        return '';
    }
    
    protected function validEmail($email)
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
        if (preg_match($regex, $email)) {
            return true;
        } else { 
            return false;
        } 
    }
}