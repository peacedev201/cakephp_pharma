<?php
class RanksController extends CreditAppController{

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Credit.CreditRanks');
    }

    public $components = array('Paginator');

    public function index()
    {
        $this->_checkPermission( array('aco' => 'credit_use') );
        $ranks = $this->CreditRanks->getRanks(1);
        $this->set('ranks', $ranks);
        $more_items = $this->CreditRanks->getRanks(2);
        $this->set('more_url', '/credit/ranks/browse/page:2');
        $more_result = 0;
        if(!empty($more_items))
            $more_result = 1;
        $this->set('more_result',$more_items);

    }
    public function browse()
    {
        $page = (!empty($this->params['named']['page'])) ? $this->params['named']['page'] : 1;
        $ranks = $this->CreditRanks->getRanks($page);
        $this->set('ranks', $ranks);
        $more_items = $this->CreditRanks->getRanks( $page + 1 );
        $this->set('more_url', '/credit/ranks/browse/page:'.($page + 1));
        $more_result = 0;
        if(!empty($more_items))
            $more_result = 1;
        $this->set('more_result',$more_items);
        $this->render('/Elements/list/credit_ranks');
    }

    public function admin_index()
    {
        //$ranks = $this->CreditRanks->find( 'all' );
        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages')
        );
        $ranks = $this->Paginator->paginate('CreditRanks');
        $this->set('ranks', $ranks);
        $this->set('title_for_layout', __d('credit','Manage ranks'));
    }

    public function admin_create($id = null)
    {
        $bIsEdit = false;

        if (!empty($id)) {
            $rank = $this->CreditRanks->findById($id);
            $bIsEdit = true;
        } else {
            $rank = $this->CreditRanks->initFields();
            $rank['CreditRanks']['enable'] = 1;
        }

        $this->set('bIsEdit', $bIsEdit);
        $this->set('rank', $rank);
    }

    public function admin_save()
    {
        $this->autoRender = false;
        if (!empty($this->data['id'])) {
            $this->CreditRanks->id = $this->request->data['id'];
        }
        $values = $this->request->data;
        $this->CreditRanks->set($values);
        $this->_validateData($this->CreditRanks);
        $this->CreditRanks->save();

        $this->Session->setFlash(__d('credit','Rank has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id)
    {
        $this->CreditRanks->delete($id);

        $this->Session->setFlash(__d('credit','Rank has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

}
