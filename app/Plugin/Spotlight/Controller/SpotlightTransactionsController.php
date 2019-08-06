<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class SpotlightTransactionsController extends SpotlightAppController {

    public $components = array('Paginator');
    
    public function __construct($request = null, $response = null){
        parent::__construct($request, $response);
        $this->url = '/admin/spotlight/spotlight_transactions/';
        $this->set('url', $this->url);
        $this->loadModel('Spotlight.SpotlightTransaction');
        $this->loadModel('PaymentGateway.Gateway');
    }

    public function beforeFilter()    {
        parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
    }

    public function admin_index() {
        $this->Paginator->settings = array(
            'limit' => RESULTS_LIMIT,
            'order' => array(
                'SpotlightTransaction.created' => 'DESC'
            )
        );

        $cond = array();
        $this->request->data = array_merge($this->request->data,$this->request->params['named']);
        $data_search = array();       
                
        if ( !empty( $this->request->data['name'] ) )
        {
            $cond['User.name LIKE'] = '%'.$this->request->data['name'].'%';
            $this->set('name',$this->request->data['name']);
            if ($this->request->data['name'])
                $data_search['name'] = $this->request->data['name'];
        }
        
        if ( !empty( $this->request->data['type'] ) )
        {
            $cond['SpotlightTransaction.type'] = $this->request->data['type'];
            $this->set('type',$this->request->data['type']);
            if ($this->request->data['type'])
                $data_search['type'] = $this->request->data['type'];
        }
        
        if ( !empty( $this->request->data['start_date'] ) )
        {
            $cond['SpotlightTransaction.created >='] = trim($this->request->data['start_date']).' 00:00:00';
            $this->set('start_date',$this->request->data['start_date']);
            if ($this->request->data['start_date'])
                $data_search['start_date'] = $this->request->data['start_date'];
        }
        
        if ( !empty( $this->request->data['end_date'] ) )
        {
            $cond['SpotlightTransaction.created <='] = trim($this->request->data['end_date']).' 23:59:59';
            $this->set('end_date',$this->request->data['end_date']);
            if ($this->request->data['end_date'])
                $data_search['end_date'] = $this->request->data['end_date'];
        }

        $this->paginate = array(
            'conditions'=>$cond,
            'order' => array(
                'SpotlightTransaction.created' => 'DESC',
            )
        );

        $transactions = $this->Paginator->paginate('SpotlightTransaction');
        $this->set('transactions', $transactions);  

        $gateways = $this->Gateway->find('all');
        $credit = array('Gateway' => array('id' => -1, 'name' => __d('spotlight','Credit')));
        $gateways[] = $credit;
        $this->set('gateways',$gateways);      
              
        $this->set('data_search',$data_search);

        $this->set('title_for_layout', __d('spotlight','Manage Spotlight Transactions'));
    }
}
