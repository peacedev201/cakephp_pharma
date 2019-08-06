<?php
class SellController extends CreditAppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->loadModel('Credit.CreditSells');
    }

    public $components = array('Paginator');

    public function admin_index()
    {
        //$sells = $this->CreditSells->find( 'all' );
        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages')
        );
        $sells = $this->Paginator->paginate('CreditSells');
        $this->set('sells', $sells);
        $this->set('title_for_layout', __d('credit','Credit Packages'));
    }

    public function admin_create($id = null)
    {
        $bIsEdit = false;

        if (!empty($id)) {
            $sell = $this->CreditSells->findById($id);
            $bIsEdit = true;
        } else {
            $sell = $this->CreditSells->initFields();
        }

        $this->set('bIsEdit', $bIsEdit);
        $this->set('sell', $sell);
    }

    public function admin_save()
    {
        $this->autoRender = false;
        if (!empty($this->data['id'])) {
            $this->CreditSells->id = $this->request->data['id'];
        }
        $values = $this->request->data;
        $this->CreditSells->set($values);
        $this->_validateData($this->CreditSells);
        $this->CreditSells->save();

        $this->Session->setFlash(__d('credit','Item has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id)
    {
        $this->CreditSells->delete($id);

        $this->Session->setFlash(__d('credit','Item has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }
}
