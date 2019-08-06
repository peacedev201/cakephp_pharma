<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessLocationsController extends BusinessAppController {
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessLocation');
    }
      public $paginate = array(
            'maxLimit' => 100,
            'limit' => 10
        );
    public function beforeFilter() {
        parent::beforeFilter();
        //$this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('Business.BusinessLocation');
    }

    public function admin_index() {
        $countries = $this->paginate('BusinessLocation', array('BusinessLocation.parent_id' => 0));
        $this->set('countries', $countries);
        $this->set('title_for_layout', __d('business', 'Location Manager'));
    }

    public function admin_state($parent_id = null) {
        $states = $this->paginate('BusinessLocation', array('BusinessLocation.parent_id' => $parent_id));
        $country = $this->BusinessLocation->getItemById($parent_id);
        $this->set('country', $country);
        $this->set('states', $states);
        $this->set('title_for_layout', __d('business', 'State/Province/City Manager'));
    }

    public function admin_create($id = null) {
        $bIsEdit = false;
        if (!empty($id)) {
            $country = $this->BusinessLocation->getItemById($id);
            $bIsEdit = true;
        } else {
            $country = $this->BusinessLocation->initFields();
        }
        $this->set('country', $country);
        $this->set('bIsEdit', $bIsEdit);
    }
    public function admin_create_state($parent_id = null, $id = null) {
        $this->_checkPermission(array('super_admin' => 1));
        $bIsEdit = false;
        if (!empty($id)) {
            $state = $this->BusinessLocation->getItemById($id);
            $bIsEdit = true;
        } else {
            $state = $this->BusinessLocation->initFields();
        }
        $country = $this->BusinessLocation->getItemById($parent_id);
        $this->set('country', $country);
        $this->set('state', $state);
        $this->set('bIsEdit', $bIsEdit);
    }
    public function admin_save() {
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->BusinessLocation->id = $this->request->data['id'];
        }
        $this->BusinessLocation->set($this->request->data);
        $this->_validateData($this->BusinessLocation);
        if($this->BusinessLocation->save())
        {
            $this->BusinessLocation->setLocationDefault($this->BusinessLocation->id);
            
            //update all child enable
            $this->BusinessLocation->updateAll(array(
                'BusinessLocation.enabled' => $this->data['enabled']
            ), array(
                'BusinessLocation.parent_id' => $this->data['id']
            ));
        }
        $this->Session->setFlash(__d('business', 'Location has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);
    }
    public function admin_delete($id) {
        $this->autoRender = false;
        if($this->BusinessLocation->deleteBusinessLocation($id)){
          $this->Session->setFlash(__d('business',  'Location has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }else{ 
            $this->Session->setFlash(__d('business', 'Can not delete this location. Location contains businesses'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
        }
        $this->redirect($this->referer());
    }
    public function admin_save_order() {
        $this->_checkPermission(array('super_admin' => 1));
        $this->autoRender = false;
        foreach ($this->request->data['order'] as $id => $order) {
            $this->BusinessLocation->id = $id;
            $this->BusinessLocation->save(array('ordering' => $order));
        }
        $this->Session->setFlash(__d('business', 'Order saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }
    
    public function admin_show_on_popup($id)
    {
        $this->admin_active($id, 1, 'on_popup');
    }
    
    public function admin_hide_on_popup($id)
    {
        $this->admin_active($id, 0, 'on_popup');
    }
    
    private function admin_active($id, $value = 1, $task)
    {
        $this->BusinessLocation->activeField($id, $task, $value);
        $this->_redirectSuccess(__d('business', 'Succesfully updated'), $this->referer());
    }
    
    public function select_location_dialog()
    {
        $locations = $this->BusinessLocation->getPopupLocation();
        $this->set(array(
            'locations' => $locations,
            'close_button' => $this->request->data['close_button']
        ));
        
        $this->render('Business.Elements/select_location_dialog');
    }
    
    public function select_location($name = null)
    {
        $data = $this->request->data;
        if(empty($name) && empty($data['location']))
        {
            $this->_jsonError(__d('business', 'Please enter location'));
        }
        else if(empty($name) && !$this->BusinessLocation->isLocationNameExist($data['location']))
        {
            $this->_jsonError(__d('business', 'Location not found! Please select or enter another location.'));
        }
        else
        {
            if(!empty($data['location']))
            {
                $this->BusinessLocation->setDefaultLocation($data['location'], null, true);
                $this->_jsonSuccess(__d('business', 'Success'));
            }
            else
            {
                $this->BusinessLocation->setDefaultLocation(urldecode($name), null, true);
                $this->redirect('/');
            }
        }
    }
    
    public function find_address_by_postcode()
    {
        $postalcode = $this->request->data['postal_code'];
        $postalcode = trim($postalcode);
        $postalcode = str_replace(' ', '', $postalcode);
        if($postalcode == '')
        {
            $this->_jsonError('Postal code is required');
        }
        else
        {
            $data = $this->BusinessLocation->getLocaionSearchByPostalCode($postalcode);
            if($data == null)
            {
                $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
                $data = $businessHelper->getAddressDetail(null, null, $postalcode);
                $this->BusinessLocation->registerLocationKeyword($data['address'], $data['lat'], $data['lng'], $postalcode, $data['country'], $data['region']);

                echo $data['address'];
                exit;
            }
            else
            {
                echo $data['BusinessLocationSearch']['address'];
                exit;
            }
        }
    }
    public function locations($param = null)
    {
		$this->set(array(
			'title_for_layout' => ''
		));
        $parent_locations = $this->BusinessLocation->getLocations(null, 0);
        $multi_level = false;
        $breadcrumb = $param_text = null;
        if($param != null && is_numeric($param))
        {
            if(!$this->BusinessLocation->isLocationExist($param))
            {
                $this->_redirectError(__d('business', 'Location not found'), '/pages/error');
            }
            $locations = $this->BusinessLocation->getMapLocations($param);

            $location = $this->BusinessLocation->findById($param);
            $param_text = $location['BusinessLocation']['name'];

            //breadcrumb
            $breadcrumb = $this->BusinessLocation->getBreadCrumb($param);
        }
        else if($param != null)
        {
            $locations = $this->BusinessLocation->getLocations(null, null, null, $param);
            $param_text = $param;
        }
        else
        {
            $multi_level = true;
            $locations = $this->BusinessLocation->getMapLocations();
        }
       // debug($parent_locations);die;
        $this->set(array(
            'parent_locations' => $parent_locations,
            'locations' => $locations,
            'param' => $param,
            'param_text' => $param_text,
            'multi_level' => $multi_level,
            'breadcrumb' => $breadcrumb,
            'current_link' => $this->request->base.$this->request->here(false),
        ));
    }
}
