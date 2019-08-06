<?php

class BusinessTransactionsController extends BusinessAppController {

    public $components = array('Paginator');

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->url = '/admin/business_transactions/';
        $this->url_create = $this->url . 'create/';
        $this->url_delete = $this->url . 'delete/';
        $this->set('url', $this->url);
        $this->set('url_create', $this->url_create);
        $this->set('url_delete', $this->url_delete);
        $this->loadModel('Business.BusinessTransaction');
        $this->loadModel('Business.BusinessPackage');
        $this->loadModel('PaymentGateway.Gateway');
    }

    public function beforeFilter() {
        parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
    }

    public function admin_index() {
        $this->Paginator->settings = array(
            'limit' => 10,
            'order' => array(
                'BusinessTransaction.created' => 'DESC'
            )
        );

        $cond = array('BusinessTransaction.admin' => 0);
        $this->request->data = array_merge($this->request->data, $this->request->params['named']);
        $data_search = array();
        if (!empty($this->request->data['business_package_id'])) {
            $cond['BusinessTransaction.business_package_id'] = $this->request->data['business_package_id'];
            $this->set('plan_id', $this->request->data['business_package_id']);
            if ($this->request->data['business_package_id'])
                $data_search['business_package_id'] = $this->request->data['business_package_id'];
        }

        if (!empty($this->request->data['gateway_id'])) {
            $cond['BusinessTransaction.gateway_id'] = $this->request->data['gateway_id'];
            $this->set('gateway_id', $this->request->data['gateway_id']);
            if ($this->request->data['gateway_id'])
                $data_search['gateway_id'] = $this->request->data['gateway_id'];
        }

        if (!empty($this->request->data['name'])) {
            $cond['Business.name LIKE'] = '%' . $this->request->data['name'] . '%';
            $this->set('name', $this->request->data['name']);
            if ($this->request->data['name'])
                $data_search['name'] = $this->request->data['name'];
        }

        if (!empty($this->request->data['status'])) {
            $cond['BusinessTransaction.status'] = $this->request->data['status'];
            $this->set('status', $this->request->data['status']);
            if ($this->request->data['status'])
                $data_search['status'] = $this->request->data['status'];
        }

        if (!empty($this->request->data['start_date'])) {
            $cond['BusinessTransaction.created >='] = $this->request->data['start_date'].' 00:00:00';
            $this->set('start_date', $this->request->data['start_date']);
            if ($this->request->data['start_date'])
                $data_search['start_date'] = $this->request->data['start_date'];
        }

        if (!empty($this->request->data['end_date'])) {
            $cond['BusinessTransaction.created <='] = $this->request->data['end_date'].' 23:59:59';
            $this->set('end_date', $this->request->data['end_date']);
            if ($this->request->data['end_date'])
                $data_search['end_date'] = $this->request->data['end_date'];
        }
        $transactions = $this->Paginator->paginate('BusinessTransaction', $cond);
        $this->set('transactions', $transactions);
        $packages = $this->BusinessPackage->getPackages();
        $gateways = $this->Gateway->find('all');
        $this->set('gateways', $gateways);
        $this->set('packages', $packages);
        $this->set('data_search', $data_search);
        $this->set('title_for_layout', __d('business', 'Business Transaction Manager'));
    }

}
