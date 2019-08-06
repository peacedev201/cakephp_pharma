<?php
App::uses('CakeEventListener', 'Event');

class SmsVerifyListener implements CakeEventListener
{	
    public function implementedEvents()
    {
        return array(
        	'MooView.beforeRender' => 'beforeRender',
        	'Controller.App.checkPermission' => 'checkPermission',
        	'Controller.beforeRender' => 'ControllerBeforeRender',
        	'Controller.ApiApp.checkPermission' => 'checkPermissionApi',
        	'View.Mooapp.activities.ajax_browse.renderAppOptimizedContent' => 'renderAppOptimizedContent',
        	'Plugin.Controller.UsersApi.me' => 'UsersApiMe',
        );
    }
    public function renderAppOptimizedContent($event)
    {
    	$viewer = MooCore::getInstance()->getViewer();
    	
    	if (!$viewer)
    		return;
    		
    	if (Configure::read('core.site_offline'))
    		return;
    	
    	$result = false;
    	if ($this->checkVerify($viewer))
    	{
    		$result = true;
    	}
    	if ($result)
    	{
    		echo 'window.check_reload = true;';
    	}
    	else
    	{
    		echo 'window.check_reload = false;';
    	}
    }
    
    public function UsersApiMe($event)
    {
    	$viewer = MooCore::getInstance()->getViewer();
    	
    	if (!$viewer)
    		return;
    		
    	if (Configure::read('core.site_offline'))
    		return;
    			
    	$result = false;
    	if ($this->checkVerify($viewer))
    	{
    		$result = true;
    	}

    	$event->result['check_reload'] = $result;
    }
    
    public function checkPermissionApi($event)
    {
    	$viewer = MooCore::getInstance()->getViewer();
    	
    	if (empty($event->data['options']))
    	{
    		return;
    	}
    	
    	if ($this->checkVerify($viewer))
    	{
    		return;
    	}
    	
    	throw new ApiUnauthorizedException(__d('sms_verify', 'Please confirm your phone number.'));
    }
    
    public function ControllerBeforeRender($event)
    {
    	$viewer = MooCore::getInstance()->getViewer();
    	if (!$viewer)
    		return;
    	
    	if (Configure::read('core.site_offline'))
    		return;
    			
    	$e = $event->subject();
    	
    	if ($this->checkVerify($viewer))
    	{
    		return;
    	}
    	
    	if ($e->params['plugin'] == 'SmsVerify')
    	{
    		return;
    	}
    	
    	if (!$e->Session->read('Message.confirm_remind'))
    	{
    		if (!$e->isApp())
    		{
    			$e->Session->setFlash(__d('sms_verify','Please click the verify phone number').'<br /><br /><a href="'.$e->request->base.'/sms_verifys/" data-target="#smsVerifyModel" data-toggle="modal" data-dismiss class="btn btn-action" href="javascript:void(0);" id="sms_verify_buttom">'.__d('sms_verify','Verify').'</a>',
    			'default', array('class' => 'Metronic-alerts alert alert-success fade in'),'confirm_remind');
    		}
    		else {
    			$e->Session->setFlash(__d('sms_verify','Please click the verify phone number').'<br /><br /><a href="'.$e->request->base.'/sms_verifys/">'.__d('sms_verify','Verify').'</a>',
    					'default', array('class' => 'Metronic-alerts alert alert-success fade in'),'confirm_remind');
    		}
    	}
    	
    }
    
    public function checkPermission($event)
    {
    	$viewer = MooCore::getInstance()->getViewer();
    	if (!$viewer)
    		return;
    	
    	if (Configure::read('core.site_offline'))
    		return;
    	
    	if (empty($event->data['options']))
    	{
    		return;
    	}
    	
    	$e = $event->subject();
    	    	
    	
    	if ($viewer['Role']['is_admin'])
    	{
    		return;
    	}
    	
    	if ($viewer['User']['sms_verify'])
    	{
    		return;
    	}    	
    	
    	$authorized= &$event->data['authorized'];
    	$authorized = false;
    	$msg= &$event->data['msg'];
    	$msg = __d('sms_verify', "Please confirm your phone number");
    }
 	
    public function beforeRender($event)
    {
    	$viewer = MooCore::getInstance()->getViewer();
    	if (!$viewer)
    		return;
    	
    	if (Configure::read('core.site_offline'))
    		return;
    	
    	$e = $event->subject();
    	
    	if (isset($e->request->params['prefix']) && $e->request->params['prefix'] == 'admin')
    		return;
    	
//    	if ($this->checkVerify($viewer))
//    	{
//    		return;
//    	}
    		
    	if (Configure::read('debug') == 0){
    		$min="min.";
    	}else{
    		$min="";
    	}
    	
    	$e->addPhraseJs(array(
    		'sms_verify_error' => __d('sms_verify', "Please confirm your phone number"),
    	));
    	
    	$e->Helpers->MooRequirejs->addPath(array(
    		"mooSmsVerify"=>$e->Helpers->MooRequirejs->assetUrlJS("SmsVerify.js/main.{$min}js"),
    	));
    
    	
    	$e->Helpers->Html->scriptBlock(
    		"require(['jquery','mooSmsVerify'], function($,mooSmsVerify) {\$(document).ready(function(){ mooSmsVerify.init(); });});",
    		array(
    			'inline' => false,
    		)
    	);
    	
    	$e->Helpers->MooPopup->register('smsVerifyModel');
    	
    }
    
    public function checkVerify($viewer)
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
}