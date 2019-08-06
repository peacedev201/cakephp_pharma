<?php

class CreditsController extends CreditAppController
{
    public function admin_index()
    {
        $this->loadModel('Credit.CreditBalances');
        //$items = $this->CreditBalances->getMembers(1);
        $cond = array();
        if (!empty($this->request->data['keyword']))
            $cond['User.name LIKE ?'] = '%' . $this->request->data['keyword'] . '%';

        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages')
        );

        $items = $this->paginate('CreditBalances', $cond);
        $this->set('items', $items);
        $this->set('title_for_layout', __d('credit', 'List members'));
    }

    public function admin_edit($id = null)
    {
        $this->set('id', $id);
    }

    public function home()
    {
        if ($this->isApp())
        {
            App::uses('badgeCreditWidget', 'Credit.Controller'.DS.'Widgets'.DS.'credits');
            $widget = new badgeCreditWidget(new ComponentCollection(),null);
            $widget->beforeRender($this);

            App::uses('rankCreditWidget', 'Credit.Controller'.DS.'Widgets'.DS.'credits');
            $widget = new rankCreditWidget(new ComponentCollection(),null);
            $widget->beforeRender($this);
        }
    }

    public function admin_save()
    {
        $this->loadModel('Credit.CreditBalances');
        $this->autoRender = false;
        $values = $this->request->data;
        $this->CreditBalances->set($values);
        $this->_validateData($this->CreditBalances);
        $this->CreditBalances->addCredit($values['id'], $values['credit']);
        $this->loadModel('Credit.CreditLogs');
        $uid = MooCore::getInstance()->getViewer(true);
        $this->CreditLogs->addLogByType('set_credits', $values['credit'], $uid, 'user', $uid);

        $this->Session->setFlash(__d('credit', 'successfully changed'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);
    }

    public $components = array('Paginator');

    public function admin_transaction($id = null)
    {
        $this->loadModel('Credit.CreditLogs');
        $conditions = array(
            'user_id' => $id
        );
        $join = array();
        $join[] = array(
            "table" => $this->tablePrefix . "credit_actiontypes",
            "alias" => "CreditActiontypes",
            "type" => "LEFT",
            "conditions" => array(
                "CreditLogs.action_id = CreditActiontypes.id"
            )
        );
        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'order' => array(
                'CreditLogs.creation_date' => 'DESC'
            ),
            'joins' => $join,
            'fields' => array('*'),
        );
        $items = $this->Paginator->paginate('CreditLogs', $conditions);
        $this->set('items', $items);
        $this->loadModel('User');
        $user = $this->User->findById($id);
        $this->set('user', $user);
        $this->set('title_for_layout', __d('credit', 'Credit'));
    }

    public function get_member()
    {
        $viewer = MooCore::getInstance()->getViewer(true);
        $this->loadModel('User');
        $this->_checkPermission();

        $friends = $this->User->find( 'all', 
                                        array( 
                                            'conditions' =>  array(
                                                                        'User.active' => 1,
                                                                        'User.id != ' => $viewer
                                                                    ), 
                                            'order'      => 'User.name asc'
                                        )
                                    ); 
        $this->set(compact('friends'));

    }

    public function dashboard(){
        $this->loadModel('Credit.CreditBalances');
        $this->loadModel('Credit.CreditRanks');

        $type = MooCore::getInstance()->getSubjectType();
        $subject = MooCore::getInstance()->getSubject();
        $uid = MooCore::getInstance()->getViewer(true);        
        $item = $this->CreditBalances->getBalancesUser($uid);

        //Credit Rank
        $next_rank = null;
        if ($type != 'User' || $uid == $subject['User']['id']){
            $next_rank = $this->CreditRanks->getNextRank($item['CreditBalances']['current_credit']);
        }


        $now_rank = $this->CreditRanks->getNowRank($item['CreditBalances']['current_credit']);

        $width_rank = 0;
        if(!empty($next_rank) && !empty($item)){
            $width_rank = ($item['CreditBalances']['current_credit'] / $next_rank['CreditRanks']['credit']) * 100;
        }
        if($width_rank > 100)
        {
            $width_rank = 100;
        }
        if($width_rank < 0)
        {
            $width_rank = 0;
        }

        $this->set('now_rank', $now_rank);
        $this->set('next_rank', $next_rank);
        $this->set('width_rank', $width_rank);

        //Credit Statistics 
        $this->set('item', $item);
        $this->set('uid', $uid);
        $this->set('subject_type', $type);

        //
    }

    public function index($type = "")
    {
        $this->_checkPermission(array('aco' => 'credit_use'));

        $this->loadModel('Credit.CreditBalances');
        $uid = MooCore::getInstance()->getViewer(true);

        $credit = $this->CreditBalances->getBalancesUser($uid);

        if($credit) {
            $creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');
            $creditHelper->doUpdateRankUser($credit['CreditBalances']['current_credit'], $uid);

        }
        switch ($type) {
            case 'my_credits':
                $this->set('title', __d('credit', 'Transactions'));
                $this->set('active_menu_my_credits', 'current');
                $this->my_credits();
                break;
            case 'faqs':
                $this->set('title', __d('credit', 'FAQs'));
                $this->set('active_menu_faqs', 'current');
                $this->tab_faqs();
                break;
            case 'rank':
                $this->set('title', __d('credit', 'Credit Ranks'));
                $this->set('active_menu_rank', 'current');
                $this->loadModel('Credit.CreditRanks');
                $ranks = $this->CreditRanks->getRanks(1);
                $this->set('ranks', $ranks);
                $more_items = $this->CreditRanks->getRanks(2);
                $this->set('more_url', '/credit/ranks/browse/page:2');
                $more_result = 0;
                if (!empty($more_items))
                    $more_result = 1;
                $this->set('more_result', $more_items);
                break;
            case 'withdraw_request':
                $this->set('title', __d('credit', 'Withdrawal request form'));
                $this->withdraw_request();
                break;
            case 'my_withdraw_request':
                $this->set('active_menu_my_withdraw_request', 'current');
                $this->set('title', __d('credit', 'My withdrawal request'));
                $this->my_withdraw_request();
                break;
            case 'action':
                $this->set('active_menu_action_type', 'current');
                $this->set('title', __d('credit', 'Action types and credits'));
                $this->action_type();
                break;
            default :
                $this->loadModel('Credit.CreditBalances');
                $items = $this->CreditBalances->getMembers(1);
                $this->set('active_menu_top_members', 'current');
                $this->set('title', __d('credit', 'Top members'));
                $this->set('items', $items);
                $this->set('count', count($items));
                $this->set('num_count', 1);
                $more_result = 0;
                $more_items = $this->CreditBalances->getMembers(2);
                if (!empty($more_items))
                    $more_result = 1;
                $this->set('more_result', $more_items);
                $this->loadModel('Credit.CreditRanks');
        }
        $this->set('type', $type);
        $this->set('title_for_layout', '');
    }

    public function action_type()
    {
        $this->loadModel('Credit.CreditActiontypes');
        $actions = $this->CreditActiontypes->getActions();
        $group_actions = array();
        $header = array();
        if(count($actions)){
            foreach($actions as $key => $item){
                $group_actions[$item['CreditActiontypes']['action_module']][] = $item;
                if (!isset($header[$item['CreditActiontypes']['action_module']]))
                {
                    $header[$item['CreditActiontypes']['action_module']] = $item['CreditActiontypes']['plugin'];
                }
            }
        }
        $this->set('group_actions', $group_actions);
        $this->set('header',$header);
    }

    public function browse()
    {
        $this->_checkPermission(array('aco' => 'credit_use'));
        $page = (!empty($this->params['named']['page'])) ? $this->params['named']['page'] : 1;
        $count = (!empty($this->params['named']['count'])) ? $this->params['named']['count'] : 1;
        $this->loadModel('Credit.CreditBalances');
        $items = $this->CreditBalances->getMembers($page);
        $this->set('items', $items);
        $this->set('num_count', $count + 1);
        $this->set('more_url', '/credits/browse/page:' . ($page + 1) . '/count:' . ($count + count($items)));
        $more_result = 0;
        $more_items = $this->CreditBalances->getMembers($page + 1);
        if (!empty($more_items))
            $more_result = 1;
        $this->set('more_result', $more_items);
        $this->render('/Elements/list/top_members');
    }

    private function my_credits()
    {
        $uid = MooCore::getInstance()->getViewer(true);
        if (!$uid) {
            $this->_checkPermission();
        }
        $this->loadModel('Credit.CreditLogs');
        $conditions = array(
            'user_id' => $uid
        );
        $join = array();
        $join[] = array(
            "table" => $this->tablePrefix . "credit_actiontypes",
            "alias" => "CreditActiontypes",
            "type" => "LEFT",
            "conditions" => array(
                "CreditLogs.action_id = CreditActiontypes.id"
            )
        );
        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'order' => array(
                'CreditLogs.creation_date' => 'DESC'
            ),
            'joins' => $join,
            'fields' => array('*'),
        );
        $items = $this->Paginator->paginate('CreditLogs', $conditions);
        $this->set('items', $items);
        // site timezone
        $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';
        // user timezone
        $cuser = $this->_getUser();
        if (!empty($cuser['timezone'])) {
            $utz = $cuser['timezone'];
        }
        $this->set('utz', $utz);
    }

    public function ajax_sell()
    {
        $this->_checkPermission(array('aco' => 'credit_use'));
        $viewer = MooCore::getInstance()->getViewer();
        if (empty($viewer)) {
            return false;
        }
        $viewerId = MooCore::getInstance()->getViewer(true);

        $cBalanceModel = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $cBalance = $cBalanceModel->getBalancesUser($viewerId);
        $cSellModel = MooCore::getInstance()->getModel('Credit.CreditSells');
        $cSell = $cSellModel->getAllSellCredit();

        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        $siteUrl = $http . '://' . $_SERVER['SERVER_NAME'];

        $this->set('balance', $cBalance);
        $this->set('sells', $cSell);
        $this->set('siteUrl', $siteUrl);
        $this->set('viewerId', $viewerId);

        $this->loadModel('PaymentGateway.Gateway');
        $gateways = $this->Gateway->find('all', array('conditions' => array('enabled' => "1", 'AND' => array(
            array('Plugin != ' => 'PaypalAdaptive'),
            array('Plugin != ' => 'Credit'),
            array('Plugin != ' => 'PaypalExpress')
        ))));

        $this->set('gateways', $gateways);

    }

    public function purchase_credit($type = null){
        $this->autoRender = false;
        $viewer = MooCore::getInstance()->getViewer();
        if (empty($viewer)) {
            return false;
        }
        $viewerId = MooCore::getInstance()->getViewer(true);
        $cTransModel = MooCore::getInstance()->getModel('Credit.CreditOrders');
        $sellId = $this->request->data['sell_id'];
        $cSellModel = MooCore::getInstance()->getModel('Credit.CreditSells');
        $aSell = $cSellModel->findById($sellId);
        if (!empty($aSell)) {
            $data = array();
            $data['user_id'] = $viewerId;
            $data['sell_id'] = $aSell['CreditSells']['id'];
            $data['price'] = $aSell['CreditSells']['price'];
            $data['credit'] = $aSell['CreditSells']['credit'];
            $data['transation_id'] = '';
            $data['creation_date'] = date("Y-m-d H:i:s");
            $data['status'] = 'pending';
            $data['type'] = $type;
            $cTransModel->set($data);
            $cTransModel->save();
            $gateway_id = $this->request->data['gateway_id'];
            $this->loadModel('PaymentGateway.Gateway');
            $gateway = $this->Gateway->findById($gateway_id);
            $plugin = $gateway['Gateway']['plugin'];
            $helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
            return $this->redirect($helperGateway->getUrlProcess() . '/Credit_Credit_Order/' . $cTransModel->id);
        }

    }

    public function success(){

    }

    public function cancel(){

    }

    private function tab_faqs()
    {
        $cFaqModel = MooCore::getInstance()->getModel('Credit.CreditFaq');
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;


        $faq = $cFaqModel->getFaqActive($page, Configure::read('Credit.credit_item_per_pages'));
        $more_faq = $cFaqModel->getFaqActive($page + 1, Configure::read('Credit.credit_item_per_pages'));
        $more_result = 0;
        if (!empty($more_faq)) {
            $more_result = 1;
        }

        $this->set('faqs', $faq);
        $this->set('more_result', $more_result);
        $this->set('more_url', '/credits/index/faqs/page:' . ($page + 1));
        $this->set('page', $page);
        $data = array(
            'page' => $page
        );
        $this->set('data', $data);
        if ($page > 1) {
            $this->render('/Elements/ajax/faqs_list');
        }
    }

    public function add_faq()
    {
        $this->_checkPermission(array('aco' => 'credit_use'));
        $viewer = MooCore::getInstance()->getViewer();
        if (empty($viewer)) {
            return false;
        }

    }

    public function faq_save()
    {
        $this->autoRender = false;
        $this->loadModel('Credit.CreditFaq');
        $this->request->data['user_id'] = MooCore::getInstance()->getViewer(true);
        if (empty($this->data['id'])) {
            $this->request->data['created'] = date("Y-m-d H:i:s");
        }
        $values = $this->request->data;
        $this->CreditFaq->set($values);
        $this->_validateData($this->CreditFaq);
        $this->CreditFaq->save();

        $this->Session->setFlash(__d('credit', 'FAQ has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);
    }

    public function ajax_doSend()
    {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));

        $uid = $this->Auth->user('id');

        $this->request->data['user_id'] = $uid;

        // @todo: validate recipients

        if (empty($this->request->data['friendSuggestion'])) {
            $this->_jsonError(__d('credit', 'Your Friend is required'));
        } elseif (empty($this->request->data['credit'])) {
            $this->_jsonError(__d('credit', 'Credit is required'));
        } else {
            if (!is_numeric($this->request->data['credit'])
                //|| strlen($this->request->data['credit']) != strlen(intval($this->request->data['credit']))
            ) {
                $this->_jsonError(__d('credit', 'Credit is invalid, please enter number'));
            } else {
                $recipients = explode(',', $this->request->data['friendSuggestion']);
                $pointSpent = $this->request->data['credit'] * count($recipients);
                // Check credit
                $this->loadModel('Credit.CreditBalances');
                $this->loadModel('Notification');

                
                $flag = $this->CreditBalances->spendBalanceCredit($pointSpent);
                if ($flag) {
                    // Add credit to friend select
                    foreach ($recipients as $k => $v) {
                        //$this->CreditBalances->addCredit($v, intval($this->request->data['credit']));
                        $this->CreditBalances->addCredit($v, $this->request->data['credit']);
                        $this->Notification->clear();
                        $this->Notification->save(array('user_id' => $v,
                            'sender_id' => MooCore::getInstance()->getViewer(true),
                            'action' => 'buy_credit',
                            //'params' => json_encode(array('credit' => intval($this->request->data['credit']))),
                            'params' => json_encode(array('credit' => $this->request->data['credit'])),
                            'url' => '/credits/index/my_credits',
                            'plugin' => 'Credit'
                        ));


                        // write log
                        $this->loadModel('Credit.CreditLogs');
                        $this->CreditLogs->addLogByType('transfer_from', $this->request->data['credit'], $v, 'core_user', $uid, 0);
                        $this->CreditLogs->addLogByType('transfer_to', '-' . $this->request->data['credit'], $uid, 'core_user', $v, 0);

                    }
                    $response['result'] = 1;
                    $response['message'] = __d('credit', 'Credits have been successfully sent.');
                    echo json_encode($response);
                } else {
                    $this->_jsonError(__d('credit', 'Current balance credits not enough'));
                }
            }

        }
    }

    public function log($msg, $type = '', $scope = null)
    {
        parent::log($msg, 'vie');
    }

    public function withdraw_request()
    {
        $this->loadModel('Credit.CreditBalances');
        $uid = MooCore::getInstance()->getViewer();
        $minimum_withdrawal_amount = Configure::read('Credit.minimum_withdrawal_amount');
        $maximum_withdrawal_amount = Configure::read('Credit.maximum_withdrawal_amount');

        $config_formula = Configure::read("Credit.credit_convertion_formula");
        if ($config_formula) {
            $tmp = explode('/', $config_formula);
            $formula_credit = $tmp[0];
            $formula_money = $tmp[1];
        } else {
            $formula_credit = 0;
            $formula_money = 0;
        }

        $user_num_withdrawal = $this->CreditBalances->findById($uid['User']['id'], array('CreditBalances.num_withdraw'));

        if (!empty($user_num_withdrawal)) {
            $user_num_withdrawal = $user_num_withdrawal['CreditBalances']['num_withdraw'];
        } else {
            $user_num_withdrawal = 0;
        }

        if ($this->request->isPost()) {
            $this->loadModel('Credit.CreditWithdraw');

            $data = array();

            $data['user_id'] = $uid['User']['id'];
            $data['amount'] = intval($this->request->data['amount']);
            $data['status'] = CREDIT_STATUS_PENDING;
            $data['payment'] = $this->request->data['payment'];
            $data['payment_info'] = $this->request->data['payment_info'];
            $data['total'] = ($data['amount'] * $formula_credit) / $formula_money;
            $data['CreditBalances.num_withdraw'] = "CreditBalances.num_withdraw + 1";

            $this->CreditWithdraw->set($data);

            if (!$this->CreditWithdraw->validates()) {
                $errors = $this->CreditWithdraw->validationErrors;
                $this->Session->write('errors', $errors);
                $this->redirect('/credits/index/withdraw_request');
            }


            // move credit to frozend_credit
            $flag = $this->CreditBalances->checkBalance($data['amount']);
            if ($flag) {
                $this->CreditBalances->addCreditFrozen($data['user_id'], intval($data['amount']));
            } else {
                $this->Session->setFlash(__d('credit', 'Send withdraw request fail.Current balance credits not enough'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                $this->redirect('/credits/index/withdraw_request');
            }


            $this->CreditWithdraw->create();
            if ($this->CreditWithdraw->save($data)) {
                $this->CreditBalances->updateAll(
                    array('CreditBalances.num_withdraw' => 'CreditBalances.num_withdraw + 1'),
                    array('CreditBalances.id' => $uid['User']['id'])
                );

                $current_blances = "";
                $result_current_balances = $this->CreditBalances->findById($uid['User']['id']);
                $current_blances = $result_current_balances['CreditBalances']['current_credit'];

                $this->Session->setFlash(__d('credit', 'Your Withdrawal request has been sent. Your current usable balance is %s. We will contact you soon. Thank you!', $current_blances), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                $this->redirect('/credits/index/withdraw_request');
            } else {
                $this->Session->setFlash(__d('credit', 'Send withdraw request fail'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
                $this->redirect('/credits/index/withdraw_request');
            }

        }

        $this->set('maximum_withdrawal_amount', $maximum_withdrawal_amount);
        $this->set('minimum_withdrawal_amount', $minimum_withdrawal_amount);
        $this->set('title_for_layout', __d('credit', 'Withdrawal request'));
        $this->set('formula_credit', $formula_credit);
        $this->set('formula_money', $formula_money);
        $this->set('user_num_withdrawal', $user_num_withdrawal);

    }

    public function my_withdraw_request()
    {
        $this->loadModel('Credit.CreditWithdraw');

        $uid = MooCore::getInstance()->getViewer();

        $cond = array();

        $cond = array(
            'CreditWithdraw.user_id' => $uid['User']['id'],
            'CreditWithdraw.status <= ' => CREDIT_STATUS_COMPLETED
        );

        $this->Paginator->settings = array(
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'order' => array(
                'CreditWithdraw.created' => 'DESC'
            ),
            'conditions' => $cond
        );

        $result = $this->Paginator->paginate('CreditWithdraw');

        $this->set('withdraw', $result);
        $this->set('title_for_layout', __d('credit', 'Withdrawal request'));
    }

    public function withdraw_delete($id = "")
    {
        if ($id == "")
            return false;

        $this->loadModel('Credit.CreditBalances');
        $this->loadModel('Credit.CreditWithdraw');
        $credit_withdraw_status = $this->CreditWithdraw->findById($id, array('CreditWithdraw.status', 'CreditWithdraw.amount', 'CreditWithdraw.user_id', 'User.name'));

        if ($credit_withdraw_status['CreditWithdraw']['status'] == CREDIT_STATUS_PENDING) {
            $text = __d('credit', 'Are you sure want to delete this request, requested amount will be added back to member current credit balance.');
        } elseif ($credit_withdraw_status['CreditWithdraw']['status'] == CREDIT_STATUS_COMPLETED) {
            $text = __d('credit', 'Are you sure want to delete this request?  No credit will be returned to member credit balance as the transaction already done.');
        }

        if ($this->request->isPost()) {
            $data = array();
            $is_withdraw_completed = false;

            if ($credit_withdraw_status['CreditWithdraw']['status'] == CREDIT_STATUS_PENDING) {
                $data['status'] = CREDIT_STATUS_DELETE_NOT_COMPLETE;
            } else {
                $data['status'] = CREDIT_STATUS_DELETE_AFTER_COMPLETE;
            }

            $this->CreditWithdraw->id = $id;
            if ($this->CreditWithdraw->save($data)) {

                if ($data['status'] == CREDIT_STATUS_DELETE_AFTER_COMPLETE)
                    $is_withdraw_completed = true;

                $data['amount'] = -1 * $credit_withdraw_status['CreditWithdraw']['amount'];

                $this->CreditBalances->addCreditFrozen($credit_withdraw_status['CreditWithdraw']['user_id'], $data['amount'], $is_withdraw_completed);
                $this->Session->setFlash(__d('credit', 'Change status withdrawal request successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                $this->redirect('/credits/index/my_withdraw_request');
            } else {
                $this->Session->setFlash(__d('credit', 'Change status withdrawal request fail'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
                $this->redirect('/credits/index/my_withdraw_request');
            }

        }

        $this->set('text', $text);
        $this->set('url', $this->url);
        $this->set('id', $id);
    }

    public function test()
    {
        $this->autoRender = false;

        echo __n("credit","credits",22);exit;

        $data = array(
            'user_id' => 1,
            'sender_id' => 1,
            'action' => 'got_badge',
            'url' => '/credits/index/rank',
            'params' => json_encode(array('name' => 'rank 2')),
            'plugin' => 'Credit',
        );



        App::import('Model', 'Notification');
        $mNotification = new Notification();
        $mNotification->create();
        $mNotification->save($data);
    }

}
