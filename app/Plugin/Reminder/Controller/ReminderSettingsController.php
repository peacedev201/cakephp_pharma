<?php 
class ReminderSettingsController extends ReminderAppController{
    public function admin_index()
    {
    	$this->set('title_for_layout', __d('reminder','Reminder Settings'));
    	$core_reminder_role = Configure::read('Reminder.reminder_role');
    	$this->loadModel('Role');
    	$role_select = array();
    	if ($core_reminder_role)
    	{
    		$role_select= explode(',', $core_reminder_role);
    	}
    	$roles = $this->Role->find('all',array(
    		'conditions'=>array(
    				'is_admin' => 0,
    				'is_super' => 0,
    		)
    	));
    	$rule_options = array();
    	foreach ($roles as $role)
    	{
    		$rule_options[$role['Role']['id']] = $role['Role']['name'];
    	}
    	$this->set('rule_options',$rule_options);
    	$this->set('role_select',$role_select);
    	if ($this->request->is('post'))
    	{
    		$this->loadModel('Setting');
    		$prefix = $this->Setting->tablePrefix;
    		if (!Configure::read('Reminder.reminder_enabled') && !empty($this->request->data['enabled']))
    		{
    			$this->Setting->query('INSERT IGNORE INTO '.$prefix.'reminder_users (user_id,verify_date,verify_sms_date, share_date,login_date) SELECT id , now(),now(),now(),now() FROM '.$prefix.'users');
    		}
    		$data = array();
    		foreach ($this->request->data as $key=>$value)
    		{
    			if ($key == 'role')
    			{
    				if (count($value) == count($rule_options))
    				{
    					$value = '';
    				}
    				else
    				{
    					$value = implode(',', $value);
    				}
    			}
    			$this->Setting->updateAll(
    					array('Setting.value_actual'=>'"'.$value.'"'),
    					array('Setting.name' => 'reminder_'.$key)
    					);
    		}
    		$this->Setting->save_boot_settings();
    		$this->Session->setFlash(__d('reminder','Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
    		$this->redirect('/admin/reminder/reminder_settings');
    	}
    }
    
    public function do_get_role_json()
    {
    	$this->_checkPermission(array('super_admin' => 1));
    	$this->loadModel('Role');
    	$role_options = array();
    	$q = isset($this->request->query['q']) ? $this->request->query['q'] : '';
    	if ($q)
    	{
    		$roles = $this->Role->find('all',array('conditions'=>array('name LIKE ?'=>'%'.$this->request->query['q'].'%')));
    	}
    	else
    	{
    		$roles = $this->Role->find('all');
    	}
    	foreach ($roles as $role){
    		$role_options[] = array( 'id' => $role['Role']['id'], 'name' => $role['Role']['name']);
    	}
    	
    	return json_encode( $role_options);
    }
}