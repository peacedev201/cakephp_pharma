<?php 
class SpotlightsController extends SpotlightAppController{
	public $paginate = array( 'order' => 'SpotlightUser.ordering asc', 'limit' => RESULTS_LIMIT );

    public function admin_index(){
    	if ( !empty( $this->request->data['keyword'] ) )
            $this->redirect( '/admin/spotlight/spotlights/index/keyword:' . $this->request->data['keyword'] );
        
        $period =  Configure::read('Spotlight.spotlight_period');
        $this->loadModel('Spotlight.SpotlightUser');

        //$cond =array('DATE_SUB(NOW() ,INTERVAL ? DAY) <= SpotlightUser.created' => $period, 'SpotlightUser.status' => 'active');
		$cond = array('SpotlightUser.end_date >= DATE(NOW())');
        if ( !empty( $this->request->named['keyword'] ) ){
            $keyword = $this->request->named['keyword'];
            $cond['OR'] = array(
                'User.name LIKE ? ' => '%'.$keyword.'%',
                'User.email LIKE ? ' => '%'.$keyword.'%'
            );
        }

		$this->paginate = array(
			'conditions'=>$cond,
			'order' => array(
				'SpotlightUser.end_date' => 'DESC',
				'SpotlightUser.created' => 'DESC'
			)
		);
		$users = $this->paginate( 'SpotlightUser' );
        $this->set('users', $users);
        $this->set('title_for_layout', __d('spotlight','Spotlight User'));
		$this->set('period', $period);
		$order_setting =  Configure::read('Spotlight.spotlight_order');
		$this->set('order_setting', $order_setting);
    }

    public function admin_do_active($id){
        $this->do_active($id, 1, 'active');
    }
    
    public function admin_do_unactive($id){
        $this->do_active($id, 0, 'active');
    }

    private function do_active($id, $value = 1, $task){
    	$this->loadModel('Spotlight.SpotlightUser');
        if(!$this->SpotlightUser->hasAny(array('id' => (int)$id))){
            $this->Session->setFlash(__d('spotlight','This user does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
        }else{
            $this->SpotlightUser->id = $id;
            $this->SpotlightUser->save(array($task => $value));
            //clear cache
        	Cache::delete('spotlight.top_spotlight','spotlight');
            $this->Session->setFlash(__d('spotlight','Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
        }
        $this->redirect($this->referer());
    }

    public function admin_remove($id){
    	$this->loadModel('Spotlight.SpotlightUser');
        if(!$this->SpotlightUser->hasAny(array('id' => (int)$id))){
            $this->Session->setFlash(__d('spotlight','This user does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            $this->redirect($this->referer());
        }else{
            $this->SpotlightUser->delete($id);
            //clear cache
        	Cache::delete('spotlight.top_spotlight','spotlight');
            $this->Session->setFlash(__d('spotlight','Users has been successfully remove from Spotlight'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $this->redirect($this->referer());
        }
    }

    public function admin_remove_multiuser(){
    	$this->loadModel('Spotlight.SpotlightUser');
        $this->_checkPermission(array('super_admin' => 1));

        if ( !empty( $_POST['spotlights'] ) ){
            $spotlights = $this->SpotlightUser->findAllById($_POST['spotlights']);
            foreach ( $spotlights as $spot ){              
                $this->SpotlightUser->delete( $spot['SpotlightUser']['id'] );              
            }
            //clear cache
        	Cache::delete('spotlight.top_spotlight','spotlight');
            $this->Session->setFlash( __d('spotlight','Users have been successfully removed from Spotlight') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
        }else{
        	$this->Session->setFlash(__d('spotlight','Please select users you want to remove'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
        }

        $this->redirect( array(
            'plugin' => 'spotlight',
            'controller' => 'spotlights',
            'action' => 'admin_index'
        ) );
    }

    public function admin_save_order(){
        $slUserModel = MooCore::getInstance()->getModel('Spotlight.SpotlightUser');
        $this->autoRender = false;
        foreach ($this->request->data['spotlights'] as $spot => $order) {
            $slUserModel->id = $spot;
            $slUserModel->save(array('ordering' => $order));
        }
        //clear cache
        Cache::delete('spotlight.top_spotlight','spotlight');

        $this->Session->setFlash(__d('spotlight','Order saved'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }

    public function register_form(){
		$this->_checkPermission(array('aco' => 'spotlight_use'));
    	$viewer = MooCore::getInstance()->getViewer();
        if (empty($viewer)) {
            return false;
        }
        $credit = Configure::read('Spotlight.spotlight_credit');
        $price = Configure::read('Spotlight.spotlight_price');
        $period = Configure::read('Spotlight.spotlight_period');
        $currency = Configure::read('Config.currency');

        $this->set(compact('credit', 'price', 'period', 'currency'));

		$ssl_mode = Configure::read('core.ssl_mode');
		$http = (!empty($ssl_mode)) ? 'https' : 'http';
		$siteUrl = $http . '://' . $_SERVER['SERVER_NAME'];
		$this->set('siteUrl', $siteUrl);

		$this->set('user_id', $viewer['User']['id']);

		$this->loadModel('PaymentGateway.Gateway');
		$gateways = array();
		if( Configure::read('Credit.credit_enabled') ) {
			$gateways = $this->Gateway->find('all', array('conditions' => array('enabled' => "1", 'Plugin != ' => 'PaypalAdaptive')));
		}
		$this->set('gateways', $gateways);
    }

    public function purchase_spotlight($type = null){
    	$viewer = MooCore::getInstance()->getViewer();
        if (empty($viewer)) {
            return false;
        }
        $this->loadModel('Spotlight.SpotlightUser');
		$price = Configure::read('Spotlight.spotlight_price');
        if($type == 'paypal'){
			if($price == 0) {
				$viewerId = MooCore::getInstance()->getViewer(true);
				$checkUser = $this->SpotlightUser->findByUserId($viewerId);
				$period =  Configure::read('Spotlight.spotlight_period');
				if(empty($checkUser)){
					$data_SpotlightUser = array('user_id' => $viewerId,
						'status' => 'active',
						'created' => date("Y-m-d H:i:s"),
						'end_date' => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"))
					);
					$this->SpotlightUser->clear();
					$this->SpotlightUser->save($data_SpotlightUser);
				}else{
					$data = array(
						'status' => 'active',
						'created' => date("Y-m-d H:i:s"),
						'end_date' => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"))
					);
					$this->SpotlightUser->id = $checkUser['SpotlightUser']['id'];
					$this->SpotlightUser->save($data);
				}
				$this->loadModel('Notification');
				$this->Notification->clear();
				$this->Notification->save(array(
					'user_id' => $viewerId,
					'sender_id' => $viewerId,
					'action' => 'join_spotlight',
					'params' => json_encode(array('period' => intval($period))),
					'url' => '/home',
					'plugin' => 'Spotlight'
				));

				$this->Session->setFlash(__d('spotlight', 'You has been joined spotlight'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
				$this->redirect('/');
			}
        }else{
			$viewerId = MooCore::getInstance()->getViewer(true);
			$this->loadModel('Spotlight.SpotlightUser');
			$this->loadModel('Spotlight.SpotlightTransaction');
			$data = array(
				'user_id' => $viewerId,
				'spotlight_user_id' => 0,
				'status' => 'pending',
				'amount' => $price,
				'created' => date("Y-m-d H:i:s"),
				'type' => $type,
				'transaction_id' => '0'
			);
			$this->SpotlightTransaction->clear();
			$this->SpotlightTransaction->save($data);

			$gateway_id = $this->request->data['gateway_id'];
			$this->loadModel('PaymentGateway.Gateway');
			$gateway = $this->Gateway->findById($gateway_id);
			$plugin = $gateway['Gateway']['plugin'];
			$helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
			return $this->redirect($helperGateway->getUrlProcess() . '/Spotlight_Spotlight_Transaction/' . $this->SpotlightTransaction->id);
		}
    }

    public function success(){
        $spotlightId = $this->Session->read('spotlightId');
        if ($spotlightId){
            $this->loadModel('Spotlight.SpotlightUser'); 
            $spotlightUser = $this->SpotlightUser->findById($spotlightId);
            if ($spotlightUser && $spotlightUser['SpotlightUser']['status'] == 'initial')
            {
                $this->SpotlightUser->id = $spotlightId;
                $this->SpotlightUser->save(array('status'=>'process'));
            }
         }
    }

    public function cancel(){
        $spotlightId = $this->Session->read('spotlightId');
        if ($spotlightId){
            $this->loadModel('Spotlight.SpotlightUser'); 
            $spotlightUser = $this->SpotlightUser->findById($spotlightId);
            if ($spotlightUser && $spotlightUser['SpotlightUser']['status'] == 'initial')
            {
                $this->SpotlightUser->id = $spotlightId;
                $this->SpotlightUser->save(array('status'=>'inactive'));
            }
        }
    }

    public function credit_success(){
		$this->Session->setFlash(__d('spotlight', 'You has been joined spotlight'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
		$this->redirect('/');
    }

    public function credit_fail(){
		$this->Session->setFlash(__d('spotlight', 'Your payment has been canceled'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
		return $this->redirect('/');
    }

	public $check_subscription = false;
	public $check_force_login = false;

	public function returnPaypal($userId = 0)
	{
		$this->autoRender = false;
		CakeLog::write('spotlight_paypal', print_r($_POST,true));
		$paymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';

		$request = 'cmd=_notify-validate';

		foreach ($_POST as $key => $value) {
			$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
		}

		if (!Configure::read('Spotlight.spotlight_test_mode')) {
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
			CakeLog::write('spotlight_paypal', print_r('CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')',true));
			curl_close($curl);
			die();
		}

		CakeLog::write('spotlight_paypal', print_r($response,true));

		if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && !empty($paymentStatus) && $paymentStatus == 'Completed') {
			$this->loadModel('User');
			$user = $this->User->findById($userId);
			if (!empty($user)) {
				$price = Configure::read('Spotlight.spotlight_price');
				if ( number_format($price,2) == $_POST['payment_gross']) {
					$this->loadModel('Spotlight.SpotlightUser');
					$checkUser = $this->SpotlightUser->find('first', array('conditions' => array('SpotlightUser.user_id' => $userId)));
					$currency = Configure::read('Config.currency');
					$period =  Configure::read('Spotlight.spotlight_period');
					if(empty($checkUser)){
						$data_SpotlightUser = array('user_id' => $userId,
							'status' => 'active',
							'created' => date("Y-m-d H:i:s"),
							'end_date' => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"))
						);
						$this->SpotlightUser->clear();
						$this->SpotlightUser->save($data_SpotlightUser);
						$spotlightId = $this->SpotlightUser->getLastInsertId();
					}else{
						$data = array(
							'status' => 'active',
							'created' => date("Y-m-d H:i:s"),
							'end_date' => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"))
						);
						$this->SpotlightUser->id = $spotlightId = $checkUser['SpotlightUser']['id'];
						$this->SpotlightUser->save($data);
					}
					// save transaction
					$this->loadModel('Spotlight.SpotlightTransaction');
					$dataTrans = array(
						'user_id' => $userId,
						'spotlight_user_id' => $spotlightId,
						'status' => 'completed',
						'amount' => $price,
						'created' => date("Y-m-d H:i:s"),
						'type' => 'pay',
						'transaction_id' => $_POST['txn_id']
					);
					$this->SpotlightTransaction->clear();
					$this->SpotlightTransaction->save($dataTrans);

					// send notification
					$this->loadModel('Notification');
					$this->Notification->clear();
					$this->Notification->save(array(
						'user_id' => $userId,
						'sender_id' => $userId,
						'action' => 'join_spotlight',
						'params' => json_encode(array('period' => intval($period))),
						'url' => '/home',
						'plugin' => 'Spotlight'
					));

				}
			}
		}
		curl_close($curl);
	}

	public function purchased($userId = 0)
	{
		$this->autoRender = false;
		//CakeLog::write('paypal', print_r($_POST,true));
		//$this->_checkPermission(array('aco' => 'credit_use'));
		$paymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';
		$price = Configure::read('Spotlight.spotlight_price');
		if ( !empty($paymentStatus) && $paymentStatus == 'Completed' && number_format($price,2) == $_POST['payment_gross'] ) {
			$this->Session->setFlash(__d('spotlight', 'Buy successfully!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
			$this->redirect('/');
		} else {
			$this->Session->setFlash(__d('spotlight', 'Buy unsuccessful!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
			$this->redirect('/');
		}
	}

}