<?php
require_once APP.DS."Plugin".DS."SmsVerify".DS."Lib".DS.'Twilio'.DS.'autoload.php';

use Twilio\Rest\Client;

class SmsTwilio
{
	protected $_settings;
	protected $_service;
	public function SmsTwilio($settings)
	{
		$this->_settings = $settings;
		$this->_service = new Client($settings['user_name'],$settings['password']);
	}
	
	public function send($phone,$message)
	{
		try {
			$message = $this->_service->messages->create(
					// the number you'd like to send the message to
					$phone,
					array(
							// A Twilio phone number you purchased at twilio.com/console
							'from' => $this->_settings['from'],
							// the body of the text message you'd like to send
							'body' => $message
					)
			);
		}catch (Exception $e)
		{
			return $e->getMessage();
		}
		
		return true;
	}
}