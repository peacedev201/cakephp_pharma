<?php

class DocumentLicensesController extends DocumentAppController {    
    public function admin_index() {
        $this->set('title_for_layout', __d('document','License Manager'));
        $this->loadModel('Document.Document');
        $this->loadModel('Document.DocumentLicense'); 
        $licenses = $this->DocumentLicense->find('all');
        foreach($licenses as &$license){
            $num_license = $this->Document->countDocumentByLicense($license['DocumentLicense']['id']);
            $license['DocumentLicense']['item_count'] = $num_license;
        }
        $this->set('licenses', $licenses);

    }

    public function admin_create($id = null) {
    	$this->loadModel('Document.DocumentLicense'); 
        $bIsEdit = false;
        if (!empty($id)) {
            $license = $this->DocumentLicense->findById($id);
            $bIsEdit = true;
        } else {
            $license = $this->DocumentLicense->initFields();            
        }
        
        $this->set('license', $license);
        $this->set('bIsEdit', $bIsEdit);
    }

    public function admin_save() {
    	$this->loadModel('Document.DocumentLicense'); 
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->DocumentLicense->id = $this->request->data['id'];
        }
        $this->DocumentLicense->set($this->request->data);

        $this->_validateData($this->DocumentLicense);

        $this->DocumentLicense->save();       
        $this->Session->setFlash(__d('document','License has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id) {
        $this->autoRender = false;
		$this->loadModel('Document.DocumentLicense'); 
        $this->DocumentLicense->delete($id);

        $this->Session->setFlash(__d('document','License has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

}
