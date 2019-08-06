<?php
if(Configure::read('SmsVerify.sms_verify_enable')){
	App::uses('SmsVerifyListener','SmsVerify.Lib');
	CakeEventManager::instance()->attach(new SmsVerifyListener());
}
?>