<?php

class GatewayController extends CreditAppController{
	public $check_subscription = false; 	
	public $check_force_login = false;
	
	public function process($type = null,$id = null)
	{
        $viewer = MooCore::getInstance()->getViewer();

        if(empty($viewer)){
            //FULL_BASE_URL.$this->request->here
            $return_url = base64_encode($_SERVER['HTTP_REFERER']);
            $this->Session->setFlash(__d('credit', 'Please login or register'));
            $this->redirect('/users/member_login?redirect_url='.$return_url);
            exit;
        }

		if (!$type || !$id)
		{
			$this->_showError( __('Item does not exist') );
		}
		$item = MooCore::getInstance()->getItemByType($type,$id);
		$this->_checkExistence($item);

		$uid = MooCore::getInstance()->getViewer(true);
		$this->loadModel("Credit.CreditBalances");
		$this->loadModel("Credit.CreditLogs");
		$this->loadModel("Credit.CreditActiontypes");

		$credit = 0;
		$balance = $this->CreditBalances->getBalancesUser($uid);
		if ($balance)
		{
			$credit = $balance['CreditBalances']['current_credit'];
		}

		
		$plugin = $item[key($item)]['moo_plugin'];
		$helperPlugin = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
		if (!$helperPlugin)
		{
			$this->_showError(__('Helper does not exist'));
			return;
		}
		
		$params = $helperPlugin->getParamsPayment($item);
		$this->set('params',$params);
		$this->set('credit',$credit);

		$amount = round($params['amount']*Configure::read('Credit.credit_currency_exchange'),1);

		if($this->request->is('post') &&  $credit >= $amount)
		{
			$this->CreditBalances->addCredit($uid,-$amount);
			$action_type = $this->CreditActiontypes->findByActionType('payment');
			$this->CreditLogs->addLog($action_type['CreditActiontypes']['id'],-$amount,$type,$uid,$id);

			$helperPlugin->onSuccessful($item,array('user_id'=>$uid,'amount'=>$params['amount']),$params['amount']);
			if (strtolower($type) == 'subscription_subscribe')
			{
				$result_current_balances = $this->CreditBalances->getBalancesUser($uid);
				$current_blances = $result_current_balances['CreditBalances']['current_credit'];
				$this->Session->setFlash(__d('credit', 'Payment is done! Your current credit balance: %s', $current_blances), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
				$this->redirect('/');
			}
			$this->redirect($params['return_url']);
		}
	}

    public function purchased($userId = 0, $sellId = 0)
    {
        $this->_checkPermission(array('aco' => 'credit_use'));
        $paymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';

        // if (!empty($paymentStatus) && $paymentStatus == 'Completed') {
        //     $this->set('message', __d('credit', 'Payment is done, please check your credits balance.'));
        // } else {
        //     $this->set('message', __d('credit', 'Buy Credits unsuccessful!'));
        // }
        $this->set('message', __d('credit', 'Your payment is being processed! It will be processed within a few minutes'));
    }

    public function returnPaypal($userId = 0, $sellId = 0)
    {
        $this->autoRender = false;
        CakeLog::write('credit_paypal', print_r($_POST,true));
        $paymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';

        $request = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
        }
        if (!Configure::read('Credit.credit_test_mode')) {
            $curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
        } else {
            $curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
        }
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        if (!$response) {
            CakeLog::write('credit_paypal', print_r('CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')',true));
            curl_close($curl);
            die();
        }
        CakeLog::write('credit_paypal', print_r($response,true));

        if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && !empty($paymentStatus) && $paymentStatus == 'Completed') {
            $cBalanceModel = MooCore::getInstance()->getModel('Credit.CreditBalances');
            $cSellModel = MooCore::getInstance()->getModel('Credit.CreditSells');
            $cTransModel = MooCore::getInstance()->getModel('Credit.CreditOrders');
            // Update credit for user and insert Transaction
            $aSell = $cSellModel->findById($sellId);
            if (!empty($aSell)) {
                if ($aSell['CreditSells']['price'] == $_POST['mc_gross']) {
                    $cBalanceModel->addCredit($userId, $aSell['CreditSells']['credit']);
                    $data = array();
                    $data['user_id'] = $userId;
                    $data['sell_id'] = $aSell['CreditSells']['id'];
                    $data['price'] = $aSell['CreditSells']['price'];
                    $data['credit'] = $aSell['CreditSells']['credit'];
                    $data['transation_id'] = $_POST['txn_id'];
                    $data['creation_date'] = date("Y-m-d H:i:s");
                    $data['status'] = 'completed';
                    $data['type'] = 'paypal';
                    $cTransModel->set($data);
                    $cTransModel->save();
                    // write log
                    $this->loadModel('Credit.CreditLogs');
                    $this->CreditLogs->addLogByType('buy_credits', $aSell['CreditSells']['credit'], $userId, 'user', $userId);
                    exit;
                }
            }
        }
    }
}