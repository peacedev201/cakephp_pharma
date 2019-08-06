<?php

class BusinessPaymentController extends BusinessAppController {
	
    public function beforeFilter() {
        parent::beforeFilter();        
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessPackage');
        $this->loadModel('Business.BusinessPaid');
        $this->loadModel('Business.BusinessTransaction');
        $this->loadModel('PaymentGateway.Gateway');
    }

    public function index() {
    	if ($this->request->is('post') && !isset($_POST['pay'])) {
    		$this->Session->write('business_paid',json_encode($this->request->data));
    		$this->redirect('/business_payment');
        }
        
        $data = $this->Session->read('business_paid');
        if ($data)
        {
        	$data = json_decode($data,true);
        }
        else
        {
        	$data = array();
        }
        
        if (!isset($data['business_id']))
        {
        	$this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }
        
        $this->request->data = array_merge($this->request->data,$data);
        
        $gateways = $this->Gateway->find('all', array('conditions' => array('enabled' => "1")));
        
        $pay_type = $this->request->data['pay_type'];
        $business_id = $this->request->data['business_id'];
        $package_id = !empty($this->request->data['business_package_id']) ? $this->request->data['business_package_id'] :  0 ;
        $business = $this->Business->findById($business_id);
        $helper = MooCore::getInstance()->getHelper('Business_Business');
        $package = $this->BusinessPackage->findById($package_id);
        $currency = Configure::read('Config.currency');
        if(!$this->Business->permission($business_id, BUSINESS_PERMISSION_FEATURE_PAGE, $business['Business']['moo_permissions']))
        {
        	$this->_redirectError($this->Business->permissionMessage(), '/pages/error');
        }
        if($pay_type == 'business_package') {
        	// check free package
        	if ($helper->isFreePackage($package)) {
        		$data = array(
        				'user_id' => MooCore::getInstance()->getViewer(true),
        				'business_id' => $business['Business']['id'],
        				'business_package_id' => $package_id,
        				'pay_type' => $pay_type,
        				'gateway_id' => 0,
        				'status' => 'initial',
        				'currency_code' => $currency['Currency']['currency_code']);
        		$this->BusinessPaid->clear();
        		$this->BusinessPaid->save($data);
        		$item = $this->BusinessPaid->read();
        		$helper->onSuccessful($item);
        		$this->redirect('/business/view/'.$business['Business']['id']);
        	} else {
        		if (isset($_POST['pay']))
        		{
        			// get only paypal
        			$gateway = $this->Gateway->findById($this->request->data['gateway_id']);
        			if (!$gateway) {
        				$this->Session->setFlash(__d('business', 'Gateway invalid'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in', 'service'));
        				return $this->redirect('/business_payment/');
        			}
        			$data = array(
        					'user_id' => MooCore::getInstance()->getViewer(true),
        					'business_id' => $business['Business']['id'],
        					'business_package_id' => $package_id,
        					'pay_type' => $pay_type,
        					'gateway_id' => $this->request->data['gateway_id'],
        					'status' => 'initial',
        					'currency_code' => $currency['Currency']['currency_code']);
        			$this->BusinessPaid->clear();
        			$this->BusinessPaid->save($data);
        			$business_paid_id = $this->BusinessPaid->getLastInsertId();
        			$plugin = $gateway['Gateway']['plugin'];
        			$helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
        			return $this->redirect($helperGateway->getUrlProcess() . '/Business_Business_Paid/' . $business_paid_id);
        		}
        	}
        }else{
        	// feature package
        	$featured_day =  $this->request->data['feature_day'];
        	$price =  $this->request->data['price'];
        	
        	$this->set('featured_day',$featured_day);
        	$this->set('price',$price);
        	
        	if (isset($_POST['pay']))
        	{
        		$gateway = $this->Gateway->findById($this->request->data['gateway_id']);
        		if (!$gateway) {
        			$this->Session->setFlash(__d('business', 'Gateway invalid'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in', 'service'));
        			return $this->redirect('/business_payment/');
        		}
        		$data = array(
        				'user_id' => MooCore::getInstance()->getViewer(true),
        				'business_id' => $business['Business']['id'],
        				'business_package_id' => $package_id,
        				'pay_type' => $pay_type,
        				'feature_day' => $featured_day,
        				'feature_price' => $price,
        				'gateway_id' => $this->request->data['gateway_id'],
        				'status' => 'initial',
        				'currency_code' => $currency['Currency']['currency_code']);
        		$this->BusinessPaid->clear();
        		$this->BusinessPaid->save($data);
        		$business_paid_id = $this->BusinessPaid->getLastInsertId();
        		$plugin = $gateway['Gateway']['plugin'];
        		$helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
        		return $this->redirect($helperGateway->getUrlProcess() . '/Business_Business_Paid/' . $business_paid_id);
        	}
        	
        }
        
        $this->set('pay_type',$pay_type);
        $this->set('business_id',$business_id);
        $this->set('package_id',$package_id);
        $this->set('package',$package);
        $this->set('currency_code',$currency['Currency']['currency_code']);
        $this->set('gateways',$gateways);
    }
    public function feature($business_id) {
        if(!$this->Business->isBusinessExist($business_id))
        {
            $this->_redirectError(__d('business', 'Business not found'), '/pages/error');
        }
        $business = $this->Business->findById($business_id);
        $this->_checkExistence($business);
        $businessHelper  = MooCore::getInstance()->getHelper('Business_Business');
        /*if(!$businessHelper->canFeaturedBusiness($business)) {
            $this->Session->setFlash(__d('business', 'Business can not set feature'), 'default', array('class' => 'error-message'));
            return $this->redirect('/pages/no-permission');
        }*/
        $featured_price = Configure::read('Business.featured_price') ;
        $currency = Configure::read('Config.currency');
        $this->set('business', $business);
        $this->set('featured_price', $featured_price);
        $this->set('currency', $currency);
    }
    public function success($business_id) {
        $this->Session->setFlash(__d('business', 'Thank you, Your payment has been processed. You will shortly receive an email confirmation'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        return $this->redirect('/businesses/dashboard/edit/'.$business_id);
    }

    public function cancel($business_id) {
        $this->Session->setFlash(__d('business', 'Your payment process has been canceled'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        return $this->redirect('/businesses/dashboard/edit/'.$business_id);
    }
}
