<?php 
class SmsVerifysController extends SmsVerifyAppController{
	public function beforeFilter() {
		$this->_checkPermission(array('confirm' => true));
		parent::beforeFilter();
	}
    public function admin_index()
    {
    	if ( !empty( $this->request->data['keyword'] ) )
    		$this->redirect( '/admin/sms_verify/sms_verifys/index/keyword:' . $this->request->data['keyword'] );
    		
    	$cond = array();
    	if ( !empty( $this->request->named['keyword'] ) )
    	{
    		$cond['OR']= array(
    			array('User.name LIKE '=>'%'.$this->request->named['keyword'].'%'),
    			array('User.email LIKE '=>'%'.$this->request->named['keyword'].'%'),
    		);
    	}
    		
    	$users = $this->paginate( 'User', $cond );
    	
    	$this->set('users', $users);
    	$this->set('title_for_layout', __d('sms_verify','Users Manager'));
    }
    
    public function admin_verify($id)
    {
    	$this->loadModel("User");
    	$this->User->updateAll(array('sms_verify'=>1,'sms_verify_checked' => 1),array('User.id'=>$id));
    	
    	$this->Session->setFlash( __d('sms_verify','This user has been verified'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	$this->redirect($this->referer());
    }
    
    public function admin_unverify($id)
    {
    	$this->loadModel("User");
    	$this->User->updateAll(array('sms_verify'=>0,'sms_verify_checked' => 0),array('User.id'=>$id));
    	
    	$this->Session->setFlash( __d('sms_verify','This user has been unverified'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	$this->redirect($this->referer());
    }
    
    public function index()
    {
    	$this->set('title_for_layout', __d('sms_verify','Sms Verify'));
    	
    	$viewer = MooCore::getInstance()->getViewer();
    	if (!Configure::read('SmsVerify.sms_verify_enable'))
    	{
    		$this->redirect('/notfound');
    	}
    	$check = false;
    	if ($this->checkVerify($viewer))
    	{
    		$check = true;
    	}
    	$this->set('check',$check);
    }
    
    protected function checkVerify($viewer)
    {
    	if ($viewer['Role']['is_admin'])
    	{
    		return true;
    	}
    	if (Configure::read("SmsVerify.sms_verify_pass_verify"))
    	{
    		if ($viewer['User']['sms_verify'])
    		{
    			return true;
    		}
    	}
    	else
    	{
    		if ($viewer['User']['sms_verify_checked'])
    		{
    			return true;
    		}
    	}
    	
    	return false;
    }
    
    public function send()
    {
    	$phone = $this->request->data['phone'];
    	
    	$this->loadModel("SmsVerify.SmsVerifyGateway");
    	$gateway = $this->SmsVerifyGateway->find('first',array('conditions'=>array('enable'=>true)));
    	
    	$class = $gateway['SmsVerifyGateway']['class'];
    	App::import('SmsVerify.Lib',$class);
    	    	
    	$data = array('status'=>true,'message'=>'');
    	
    	if (Configure::read("SmsVerify.sms_verify_enable_captcha") && Configure::read('core.recaptcha_publickey'))
    	{

    		$recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');

    		App::import('Vendor', 'recaptchalib');
    		$reCaptcha = new ReCaptcha($recaptcha_privatekey);
    		$resp = $reCaptcha->verifyResponse(
    			$_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
    		);
    		if ($resp != null && !$resp->success) {
    			$data['status'] = false;
    			$data['message'] = __( 'Invalid security code');
    			
    			echo json_encode($data);
    			die();
    		}
    	}
    	$this->loadModel("User");
    	$row = $this->User->find('first',array(
    		'conditions' => array(
    			'User.mobile' => $phone,
    			'User.id <> ' => MooCore::getInstance()->getViewer(true)
    		),
    	));
    	
    	
    	if ($row)
    	{
    		$data['status'] = false;
    		$data['message'] = __d('sms_verify','Someone has already registered this phone number, please use another one.');
    		
    		echo json_encode($data);
    		die();
    	}

        foreach ($this->User->validate as $key => $validate){
            if($key != 'mobile'){
                unset($this->User->validate[$key]);
            }
        }
        $this->User->clear();
        $this->User->set(array('mobile'=>$phone));
        if(!$this->User->validates()){
            $errors = $this->User->invalidFields();
            $data['status'] = false;
            $data['message'] = current( current( $errors ) );
            echo json_encode($data);
            die();
        }

    	
    	$code = $this->createRandomCode();
    	$service = new $class(json_decode($gateway['SmsVerifyGateway']['params'],true));

    	$result = $service->send($phone,__d('sms_verify','Your code').': '.$code);

    	if ($result !== true)
    	{
    		$data['status'] = false;
    		$data['message'] = $result;
    	}
    	
    	$this->Session->write("sms_verify_code",$code);
    	$this->Session->write("sms_verify_phone",$phone);
    	
    	echo json_encode($data);
    	die();
    }

    public function resend()
    {
        $phone = $this->request->data['phone'];

        $this->loadModel("SmsVerify.SmsVerifyGateway");
        $gateway = $this->SmsVerifyGateway->find('first',array('conditions'=>array('enable'=>true)));

        $class = $gateway['SmsVerifyGateway']['class'];
        App::import('SmsVerify.Lib',$class);

        $data = array('status'=>1,'message'=>'');

        $this->loadModel("User");
        $row = $this->User->find('first',array(
            'conditions' => array(
                'User.mobile' => $phone,
                'User.id <> ' => MooCore::getInstance()->getViewer(true)
            ),
        ));


        if ($row)
        {
            $data['status'] = 0;
            $data['message'] = __d('sms_verify','Someone has already registered this phone number, please use another one.');

            echo json_encode($data);
            die();
        }

        foreach ($this->User->validate as $key => $validate){
            if($key != 'mobile'){
                unset($this->User->validate[$key]);
            }
        }
        $this->User->clear();
        $this->User->set(array('mobile'=>$phone));
        if(!$this->User->validates()){
            $errors = $this->User->invalidFields();
            $data['status'] = 0;
            $data['message'] = current( current( $errors ) );
        }

        $code = $this->createRandomCode();
        $service = new $class(json_decode($gateway['SmsVerifyGateway']['params'],true));

        $result = $service->send($phone,__d('sms_verify','Your code').': '.$code);

        if ($result !== true)
        {
            $data['status'] = 0;
            $data['message'] = $result;
        }

        $this->Session->write("sms_verify_code",$code);
        $this->Session->write("sms_verify_phone",$phone);

        echo json_encode($data);
        die();
    }
    
    public function check()
    {
    	$code = $this->request->data['code'];
    	$data= array('status'=>1);    	
    	if ($code != $this->Session->read("sms_verify_code"))
    	{
    		$data['status'] = false;
    		$data['message'] = __d('sms_verify','Code invalidate');
    	}
    	else
    	{
    		$phone = $this->Session->read("sms_verify_phone");
    		$this->Session->delete("sms_verify_code");
    		$uid = MooCore::getInstance()->getViewer(true);
    		$this->loadModel("User");
    		$this->User->id = $uid;
    		$this->User->save(array('sms_verify' => 1,'mobile'=>$phone,'sms_verify_checked'=> 1));
    		
    		if (!$this->isApp())
    		{
                $this->Session->delete('Message.confirm_remind');
    			$this->Session->setFlash( __d('sms_verify','You has been verified with sms'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    		}
    	}
    	
    	echo json_encode($data);die();
    }

    public function check_form(){

    }
    
    private function createRandomCode() {
    	
    	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
    	srand((double)microtime()*1000000);
    	$i = 0;
    	$pass = '' ;
    	
    	while ($i <= 4) {
    		$num = rand() % 33;
    		$tmp = substr($chars, $num, 1);
    		$pass = $pass . $tmp;
    		$i++;
    	}
    	
    	return strtoupper($pass);
    	
    }
}