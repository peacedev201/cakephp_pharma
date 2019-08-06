<?php 
class SmsVerifySettingsController extends SmsVerifyAppController{
	public $components = array('QuickSettings');
	
	public function admin_index($id = null)
    {
    	$this->set('title_for_layout', __d('sms_verify','Sms Verify Settings'));
    	$this->QuickSettings->run($this, array("SmsVerify"), $id);
    	
    	if (CakeSession::check('Message.flash')) {
    		$sms_enable = $this->Session->read("sms_enable");
    		$this->Session->write("sms_enable",Configure::read('SmsVerify.sms_verify_enable'));
    		if (Configure::read('SmsVerify.sms_verify_enable') && !$sms_enable)
    		{
    			$this->loadModel("User");
    			$this->User->query("UPDATE ".$this->User->tablePrefix."users SET sms_verify = 1");
    		}
    	}
    	else
    	{
    		$this->Session->write("sms_enable",Configure::read('SmsVerify.sms_verify_enable'));
    	}
    	
    }
}