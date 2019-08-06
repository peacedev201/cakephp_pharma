<?php
require_once APP.DS."Plugin".DS."SmsVerify".DS."Lib".DS.'Clickatell'.DS.'vendor'.DS.'autoload.php';

use Clickatell\Rest;
use Clickatell\ClickatellException;

class SmsClickatell
{
	protected $_settings;
	protected $_service;
	public function SmsClickatell($settings)
	{
		$this->_settings = $settings;
		$this->_service = new \Clickatell\Rest($settings['key']);
	}
	
	public function send($phone,$message)
	{
		try {
			$result = $this->_service->sendMessage(['to' => [$phone], 'content' => $message]);
			
		} catch (ClickatellException $e) {
			return $e->getMessage();
		}
		
		return true;
	}
}