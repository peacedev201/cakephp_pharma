<?php
class TransactionsController extends CreditAppController{
	public $components = array('Paginator');

    public function __construct($request = null, $response = null){
        parent::__construct($request, $response);
        $this->url = '/admin/credit/transactions/';
        $this->set('url', $this->url);
        $this->loadModel('Credit.CreditOrder');
    }

    public function beforeFilter()    {
        parent::beforeFilter();
    }

    public function admin_index()    {
        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'order' => array(
                'CreditOrder.creation_date' => 'DESC'
            )
        );

        $cond = array();
        $this->request->data = array_merge($this->request->data,$this->request->params['named']);
        $data_search = array();       
                
        if ( !empty( $this->request->data['name'] ) )
        {
        	$cond['OR'] = array(
        			'User.name LIKE' => '%'.$this->request->data['name'].'%',
        			'CreditOrder.transation_id LIKE' => '%'.$this->request->data['name'].'%'
        		);
            $this->set('name',$this->request->data['name']);
            if ($this->request->data['name'])
                $data_search['name'] = $this->request->data['name'];
        }
                      
        $transactions = $this->Paginator->paginate('CreditOrder',$cond);
        $this->set('transactions', $transactions);  
                 
        $this->set('data_search',$data_search);

        $this->set('title_for_layout', __d('credit', 'Manage Credit Transactions'));
    }

}
