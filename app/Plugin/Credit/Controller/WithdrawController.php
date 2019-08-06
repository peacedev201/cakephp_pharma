<?php

class WithdrawController extends CreditAppController
{
    public $components = array('Paginator');

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->loadModel('Credit.CreditWithdraw');
        $this->loadModel('Credit.CreditBalances');
        $this->loadModel('Credit.CreditLogs');
        $this->url = '/admin/credit/withdraw/';
    }

    public function admin_index()
    {

        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'order' => array(
                'CreditWithdraw.created' => 'DESC'
            )
        );
        $cond = array();
        $data_search = array();

        if (!empty($this->request->data['name'])) {
            $cond['OR'] = array(
                'User.name LIKE' => '%' . $this->request->data['name'] . '%'
            );
            $this->set('name', $this->request->data['name']);
            if ($this->request->data['name'])
                $data_search['name'] = $this->request->data['name'];
        }

        if (isset($this->request->data['status']) && $this->request->data['status'] != "") {
            $cond['OR'] = array(
                'CreditWithdraw.status' => $this->request->data['status']
            );
            $this->set('status', $this->request->data['status']);
        } else {
            $cond = array('CreditWithdraw.status <= ' => CREDIT_STATUS_COMPLETED);
        }

        $withdraws = $this->Paginator->paginate('CreditWithdraw', $cond);

        $this->set('withdraws', $withdraws);
        $this->set('title_for_layout', __d('credit', 'Withdraw request'));
        $this->set('data_search', $data_search);
        $this->set('url', $this->url);
    }

    public function admin_ajax_change_status_withdraw($id = "", $value = "")
    {
        if ($id == "" && $value == "")
            return false;

        $credit_withdraw_status = $this->CreditWithdraw->findById($id, array('CreditWithdraw.status', 'CreditWithdraw.amount', 'CreditWithdraw.user_id', 'User.name'));
        if ($value == CREDIT_STATUS_DELETE) {
            if ($credit_withdraw_status['CreditWithdraw']['status'] == CREDIT_STATUS_PENDING) {
                $text = __d('credit', 'Are you sure want to delete this request, requested amount will be added back to member current credit balance.');
            } elseif ($credit_withdraw_status['CreditWithdraw']['status'] == CREDIT_STATUS_COMPLETED) {
                $text = __d('credit', 'Are you sure want to delete this request?  No credit will be returned to member credit balance as the transaction already done.');
            }
            $this->set('text', $text);
        }
        if ($this->request->isPost()) {
            $data = array();
            $is_withdraw_completed = false;
            $type_notification = "";

            if ($value == CREDIT_STATUS_COMPLETED) {
                $data['transaction_id'] = $this->request->data['transaction_id'];
                $data['status'] = CREDIT_STATUS_COMPLETED;
                $data['completed_date'] = date('Y-m-d H:i:s');
                $is_withdraw_completed = true;
                $type_notification = CREDIT_STATUS_COMPLETED;

                //add log admin approve transaction
                $uid = MooCore::getInstance()->getViewer();
                $this->CreditLogs->addLogByType('request-withdraw', intval('-'.$credit_withdraw_status['CreditWithdraw']['amount']), $credit_withdraw_status['CreditWithdraw']['user_id'], 'user', $uid['User']['id']);


            } elseif ($value == CREDIT_STATUS_DELETE) {
                if ($credit_withdraw_status['CreditWithdraw']['status'] == CREDIT_STATUS_PENDING) {
                    $data['status'] = CREDIT_STATUS_DELETE_NOT_COMPLETE;
                } else {
                    $data['status'] = CREDIT_STATUS_DELETE_AFTER_COMPLETE;
                    $type_notification = CREDIT_STATUS_DELETE;
                }
            }

            $this->CreditWithdraw->id = $id;
            if ($this->CreditWithdraw->save($data)) {

                if ($data['status'] == CREDIT_STATUS_DELETE_NOT_COMPLETE || $data['status'] == CREDIT_STATUS_COMPLETED) {
                    $data['amount'] = -1 * $credit_withdraw_status['CreditWithdraw']['amount'];

                    $this->CreditBalances->addCreditFrozen($credit_withdraw_status['CreditWithdraw']['user_id'], $data['amount'], $is_withdraw_completed);
                }

                $this->loadModel('Notification');
                $this->Notification->record(array('recipients' => $credit_withdraw_status['CreditWithdraw']['user_id'],
                    'sender_id' => MooCore::getInstance()->getViewer(true),
                    'action' => 'withdraw_request',
                    'params' => json_encode(array('status' => $type_notification, 'name' => $credit_withdraw_status['User']['name'])),
                    'url' => '/credits/index/my_withdraw_request',
                    'plugin' => 'Credit'
                ));


                $this->Session->setFlash(__d('credit', 'Change status withdrawal request successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                $this->redirect('/admin/credit/withdraw');
            } else {
                $this->Session->setFlash(__d('credit', 'Change status withdrawal request fail'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
                $this->redirect('/admin/credit/withdraw');
            }
        }


        $this->set('url', $this->url);
        $this->set('id', $id);
        $this->set('value', $value);
    }
}
