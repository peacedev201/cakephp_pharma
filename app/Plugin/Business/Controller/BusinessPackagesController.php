<?php 
class BusinessPackagesController extends BusinessAppController{
    public $components = array('Paginator');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Business.BusinessPackage');
        $this->loadModel('Business.BusinessPaid');
    }
    public function admin_index()
    {
        $this->Paginator->settings = array(
            'order' => array('BusinessPackage.id' => 'ASC'),
            'limit' => 10,
        );
        $packages = $this->paginate('BusinessPackage');
        $this->set('packages', $packages);
        $this->set('title_for_layout', __d('business', 'Business Packages Manager'));
    }
    public function admin_create($id = null) {
        $bIsEdit = false;
        $isUsedPackage = false;
        if (!empty($id)) {
            $package = $this->BusinessPackage->findById($id);
            $bIsEdit = true;
            $isUsedPackage = $this->BusinessPaid->isUsedPackage($id);
        } else {
            $package = $this->BusinessPackage->initFields();
        }
        $package_selects = $this->BusinessPackage->getPackageSelect();
        $currency = Configure::read('Config.currency');
        $this->set('package', $package);
        $this->set('package_selects', $package_selects);
        $this->set('currency', $currency);
        $this->set('bIsEdit', $bIsEdit);
        $this->set('isUsedPackage', $isUsedPackage);
    }
    public function admin_save() {
        $this->autoRender = false;
        $bIsEdit = false;
        $isUsedPackage = false;
        if (!empty($this->data['id'])) {
            $this->BusinessPackage->id = $this->request->data['id'];
            $bIsEdit = true;
            $isUsedPackage = $this->BusinessPaid->isUsedPackage($this->request->data['id']);
        }
        if($bIsEdit && $isUsedPackage)
        {
            unset($this->request->data['price']);
            unset($this->request->data['billing_cycle']);
            unset($this->request->data['billing_cycle_type']);
            unset($this->request->data['duration']);
            unset($this->request->data['duration_type']);
            unset($this->request->data['expiration_reminder']);
            unset($this->request->data['expiration_reminder_type']);
        }
        $this->BusinessPackage->set($this->request->data);
        $this->_validateData($this->BusinessPackage);
        
        if ($bIsEdit)
        {
        	$package = $this->BusinessPackage->findById($this->request->data['id']);
        	$type = $package['BusinessPackage']['type'];
        }
        else
        {
        	$type = $this->request->data['type'];
        }
        if ($type == 2 && !$isUsedPackage)
        {
        	if ($this->request->data['duration'] <= $this->request->data['billing_cycle'])
        	{
	        	echo json_encode(array(
	        		'result' => 0,
	        		'message' => __d('business','Billing duration must be above time duration of each cycle')
	        	));
	        	die();
        	}
        }
        
        if($this->BusinessPackage->save()){
            $this->Session->setFlash(__d('business', 'Package has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            $response['result'] = 1;
            echo json_encode($response);
        }        
    } 
    public function admin_delete($id) {
        $this->autoRender = false;
        if($this->BusinessPackage->deleteBusinessPackage($id)){
          $this->Session->setFlash(__d('business', 'Business Package deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }else{ 
            $package = $this->BusinessPackage->findById($id);
            if($package['BusinessPackage']['is_default']) {
                $this->Session->setFlash(__d('business', 'Can not delete default package!'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            }else{
                $this->Session->setFlash(__d('business', 'Can not delete this package. Package contains businesses'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            }
        }
        $this->redirect($this->referer());
    }
    public function admin_save_order()
    {
        $this->_checkPermission(array('super_admin' => 1));
        $this->autoRender = false;
        foreach ($this->request->data['order'] as $id => $order) {
            $this->BusinessPackage->id = $id;
            $this->BusinessPackage->save(array('ordering' => $order));
        }
        $this->Session->setFlash(__d('business', 'Order saved'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }
    
    public function upgrade($business_id) {
        $this->loadModel('Business.Business');
        $business = $this->Business->findById($business_id);
        $this->_checkExistence($business);
        $businessHelper  = MooCore::getInstance()->getHeader('Business_Business');
        if(!$businessHelper->canUpgradePackage($business)) {
            $this->Session->setFlash(__d('business', 'Business can not upgrade package'), 'default', array('class' => 'error-message'));
            return $this->redirect('/pages/no-permission');
        }
        $packages = $this->BusinessPackage->getPackages();
        $this->set('packages', $packages);
        $this->set('business', $business);
        $currency = Configure::read('Config.currency');
        $this->set('currency', $currency);
    }
    public function admin_get_package(){
        $data = $this->BusinessPackage->findById($this->request->data['package_id']);
        $helper = MooCore::getInstance()->getHelper('Business_Business');
        $data['BusinessPackage']['duration_type'] = $helper->getTextDurationId($data['BusinessPackage']['duration_type']);
        $data['BusinessPackage']['expiration_reminder_type'] = $helper->getTextDurationId($data['BusinessPackage']['expiration_reminder_type']);
        $data['BusinessPackage']['billing_cycle_type'] = $helper->getTextDurationId($data['BusinessPackage']['billing_cycle_type']);
        echo json_encode($data['BusinessPackage']);
        exit;
    }
}