<?php
App::uses('CakeEventListener', 'Event');

class ReminderListener implements CakeEventListener
{
	protected $_users = array();
    public function implementedEvents()
    {
        return array(        	
        	'Model.afterSave' => 'afterSave',
        	'Model.beforeSave' => 'beforeSave',
        	'Controller.beforeRender' => 'beforeRender',
        	'Controller.beforeRedirect' => 'beforeRedirect',
        	'Model.beforeDelete' => 'doAfterDelete',
        	'AppController.afterFilter' => 'afterFilter',
        );
    }
    
    public function afterFilter($event)
    {
    	$e = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if (!$uid)
    	{
    		return;
    	}
    	if (!$e->Session->read('admin_login'))
    	{
    		return;
    	}
    	if (empty($e->request->params['prefix']) || $e->request->params['prefix'] != 'admin')
    	{
    		return;
    	}
    	if ($e->request->params['plugin'] == 'sms_verify' && $e->request->params['controller'] == 'sms_verify_settings' && $e->request->params['action'] == 'admin_index')
    	{
    		if (CakeSession::check('Message.flash')) 
    		{
    			$sms_enable = $e->Session->read("sms_enable");
    			if (Configure::read('SmsVerify.sms_verify_enable') && !$sms_enable)
    			{    				
    				$model= MooCore::getInstance()->getModel("Reminder.ReminderUser");
    				$model->query("UPDATE ".$model->tablePrefix."reminder_users SET verify_sms_date = '".date('Y-m-d H:i:s')."', verify_sms_time=0");
    			}
    		}
    	}
    }
    
    public function beforeRedirect($event)
    {
    	$e = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if (!$uid)
    	{
    		return;
    	}
    	if (!$e->Session->read('admin_login'))
    	{
    		return;
    	}
    	if (empty($e->request->params['prefix']) || $e->request->params['prefix'] != 'admin')
    	{
    		return;
    	}
    	if ($e->request->params['plugin'] == 'sms_verify' && $e->request->params['controller'] == 'sms_verifys' && $e->request->params['action'] == 'admin_unverify' && !empty($e->request->params['pass'][0]))
    	{
    		$model= MooCore::getInstance()->getModel("Reminder.ReminderUser");
    		$model->query("UPDATE ".$model->tablePrefix."reminder_users SET verify_sms_date = '".date('Y-m-d H:i:s')."', verify_sms_time=0 WHERE user_id=" . intval($e->request->params['pass'][0]));
    	}
    	
    }
    
    public function beforeSave($event)
    {
    	$model = $event->subject();
    	$type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);
    	if ($type == 'User')
    	{
    		if ((!empty($model->data['User']['approved']) || isset($model->data['User']['confirmed'])) && $model->id)
    		{
    			$old = $model->find('first',array('conditions'=>array('User.id'=> $model->id),'recursive'=>-1));
    			if ($old)
    			{
	    			if (!$old['User']['approved']){
	    				$row = $model->query("UPDATE ".$model->tablePrefix."reminder_users SET verify_date = '".date('Y-m-d H:i:s')."',verify_sms_date = '".date('Y-m-d H:i:s')."',login_date = '".date('Y-m-d H:i:s')."',share_date = '".date('Y-m-d H:i:s')."', login_time = 0 ,share_time=0,verify_time=0,verify_sms_time=0 WHERE user_id=" . intval($model->id));
	    			}
	    			elseif ($old['User']['confirmed'] != $model->data['User']['confirmed'] && !$model->data['User']['confirmed'])
	    			{
	    				$row = $model->query("UPDATE ".$model->tablePrefix."reminder_users SET verify_date = '".date('Y-m-d H:i:s')."', verify_time=0 WHERE user_id=" . intval($model->id));
	    			}
    			}
    			
    		}
    		if (isset($model->data['User']['sms_verify']) && isset($model->data['User']['sms_verify_checked']) && $model->id)
    		{
    			if (!$model->data['User']['sms_verify'] && !$model->data['User']['sms_verify_checked'])
    			{
    				$row = $model->query("UPDATE ".$model->tablePrefix."reminder_users SET verify_sms_date = '".date('Y-m-d H:i:s')."', verify_sms_time=0 WHERE user_id=" . intval($model->id));
    			}
    		}
    	}
    }
    
    public function afterSave($event)
    {
    	$model = $event->subject();
    	$created = $event->data[0];
    	if (!$created) {
    		return;
    	}
    	$type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);
    	$array = array('Activity','ActivityComment','Comment','Like');
    	if (in_array($type, $array))
    	{
    		$user_id = 0;
    		if (isset($model->data[$type]['user_id']))
    		{
    			$user_id = $model->data[$type]['user_id'];
    		}
    		if ($user_id && !isset($this->_users[$user_id]))
    		{
    			$row = $model->query("UPDATE ".$model->tablePrefix."reminder_users SET login_date = '".date('Y-m-d H:i:s')."',share_date = '".date('Y-m-d H:i:s')."', login_time = 0 ,share_time=0 WHERE user_id=" . intval($user_id));
    			if ($row === true) 
    			{
    				$reminderModel = MooCore::getInstance()->getModel("Reminder.ReminderUser");
    				$reminderModel->clear();
    				$reminderModel->save(array(
    					'user_id' => $user_id,
    					'share_date' => date('Y-m-d H:i:s'),
    					'share_time' => 0,
    					'login_date' => date('Y-m-d H:i:s'),
    					'share_time' => 0
    				));
    			}
    			
    			$this->_users[$user_id] = true;
    		}
    	}
    	
    	if ($type == 'User')
    	{
    		$user_id = $model->data[$type]['id'];
    		$reminderModel = MooCore::getInstance()->getModel("Reminder.ReminderUser");
    		$reminderModel->clear();
    		$reminderModel->save(array(
    			'user_id' => $user_id,
    			'share_date' => date('Y-m-d H:i:s'),
    			'share_time' => 0,
    			'login_date' => date('Y-m-d H:i:s'),
    			'share_time' => 0
    		));
    		$this->_users[$user_id] = true;
    	}
    }
    
    public function beforeRender($event)
    {
    	$e = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if (!$uid)
    	{
    		return;
    	}
    	
    	$reminderModel = MooCore::getInstance()->getModel("Reminder.ReminderUser");
    	$time = "'".date('Y-m-d H:i:s',strtotime('- 1 days'))."'";
    	$row = $reminderModel->query("UPDATE ".$reminderModel->tablePrefix."reminder_users SET login_date = '".date('Y-m-d H:i:s')."', login_time = 0 WHERE login_date < $time AND user_id=" . intval($uid));
    }
    
    public function doAfterDelete($event)
    {
    	$model = $event->subject();
    	$type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);
    	if ($type == 'User')
    	{
    		//delete favorite
    		$reminderModel = MooCore::getInstance()->getModel("Reminder.ReminderUser");
    		$reminderModel->deleteAll(array('ReminderUser.user_id' => $model->id),true,true);
    	}
    }
}