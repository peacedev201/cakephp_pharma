<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessTypesController extends BusinessAppController {

    public $components = array('Paginator');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('Business.BusinessType');
    }

    public function admin_index() {
        $this->Paginator->settings = array(
            'order' => array('BusinessType.id' => 'ASC'),
            'limit' => 10,
        );
        $types = $this->paginate('BusinessType');
        $this->set('types', $types);
        $this->set('title_for_layout', __d('business', 'Business Types Manager'));
    }
    public function admin_create($id = null) {
        $bIsEdit = false;
        if (!empty($id)) {
            $type = $this->BusinessType->getItemById($id);
            $bIsEdit = true;
        } else {
            $type = $this->BusinessType->initFields();
        }
        $this->set('type', $type);
        $this->set('bIsEdit', $bIsEdit);
    }
    public function admin_save() {
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->BusinessType->id = $this->request->data['id'];
        }
        $this->BusinessType->set($this->request->data);
        $this->_validateData($this->BusinessType);
        $this->BusinessType->save();
        if (!$bIsEdit) {
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->BusinessType->locale = $lKey;
                $this->BusinessType->saveField('name', $this->request->data['name']);
            }
        }
        $this->Session->setFlash(__d('business', 'Business Type has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);
    } 
    public function admin_delete($id) {
        $this->autoRender = false;
        $this->loadModel('Business.Business');
        if($this->Business->hasAny(array(
            'Business.business_type_id' => $id
        )))
        {
            $this->_redirectError(__d('business', 'Can not delete this type. Type contains businesses'), $this->referer());
        }
        else if($this->BusinessType->deleteBusinessType($id)){
          $this->Session->setFlash(__d('business', 'Business Type deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }else{ 
            $this->Session->setFlash(__d('business', 'Can not delete this business type. Business type contains businesses'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
        }
        $this->redirect($this->referer());
    }
    public function admin_save_order()
    {
        $this->_checkPermission(array('super_admin' => 1));
        $this->autoRender = false;
        foreach ($this->request->data['order'] as $id => $order) {
            $this->BusinessType->id = $id;
            $this->BusinessType->save(array('ordering' => $order));
        }
        $this->Session->setFlash(__d('business', 'Order saved'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }
    public function admin_translate($id) {
        if (!empty($id)) {
            $item = $this->BusinessType->getItemById($id);
            $this->set('item', $item);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }
    public function admin_translate_save() {
        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $this->BusinessType->id = $this->request->data['id'];
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $this->BusinessType->locale = $lKey;
                    if ($this->BusinessType->saveField('name', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                }
            } else {
                $response['result'] = 0;
            }
        } else {
            $response['result'] = 0;
        }
        echo json_encode($response);
    }

}
