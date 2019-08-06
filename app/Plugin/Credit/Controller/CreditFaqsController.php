<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class CreditFaqsController extends CreditAppController {

    public $components = array('Paginator');

    public function __construct($request = null, $response = null){
        parent::__construct($request, $response);
        $this->url = '/admin/credit/credit_faqs/';
        $this->set('url', $this->url);
        $this->loadModel('Credit.CreditFaq');
    }

    public function beforeFilter()    {
        parent::beforeFilter();
    }

    public function admin_index() {
        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'order' => array(
                'CreditFaq.created' => 'DESC'
            )
        );

        $cond = array();
        $this->request->data = array_merge($this->request->data,$this->request->params['named']);
        $data_search = array();       
                
        if ( !empty( $this->request->data['name'] ) )
        {
            $cond['OR'] = array(
                    'User.name LIKE' => '%'.$this->request->data['name'].'%',
                    'CreditFaq.question LIKE' => '%'.$this->request->data['name'].'%',
                    'CreditFaq.answer LIKE' => '%'.$this->request->data['name'].'%'
                );
            $this->set('name',$this->request->data['name']);
            if ($this->request->data['name'])
                $data_search['name'] = $this->request->data['name'];
        }
                      
        $faqs = $this->Paginator->paginate('CreditFaq',$cond);
        $this->set('faqs', $faqs);
        $this->set('data_search',$data_search);        
        $this->set('title_for_layout', __d('credit','FAQ Manager'));
    }

    public function admin_create($id = null)
    {
        $bIsEdit = false;

        if (!empty($id)) {
            $faq = $this->CreditFaq->findById($id);
            $bIsEdit = true;
        } else {
            $faq = $this->CreditFaq->initFields();
        }

        $this->set('bIsEdit', $bIsEdit);
        $this->set('faq', $faq);
        $this->set('title_for_layout', __d('credit','FAQ Manager'));
    }

    public function admin_save()
    {
        $this->autoRender = false;
        if (!empty($this->data['id'])) {
            $this->CreditFaq->id = $this->request->data['id'];
        }
        $this->request->data['user_id'] = MooCore::getInstance()->getViewer(true);
        if(empty($this->data['id'])){
           $this->request->data['created'] = date("Y-m-d H:i:s");
        }
        $values = $this->request->data;
        $this->CreditFaq->set($values);
        $this->_validateData($this->CreditFaq);
        $this->CreditFaq->save();

        $this->Session->setFlash(__d('credit','FAQ has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id)
    {
        $this->CreditFaq->delete($id);

        $this->Session->setFlash(__d('credit','FAQ has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

}
