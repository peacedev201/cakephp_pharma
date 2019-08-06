<?php
App::uses('AppHelper', 'View/Helper');
class ReminderHelper extends AppHelper {	
	public function getLastDateShare($model,$user_id)
	{
		$date = '';
		$prefix = $model->tablePrefix;
		$row = $model->query('SELECT * FROM '.$prefix.'activities WHERE user_id = '.$user_id.' ORDER BY id DESC LIMIT 1');
		if (isset($row[0]['activities']['created']))
		{
			$date = $row[0]['activities']['created'];
		}
		
		$row = $model->query('SELECT * FROM '.$prefix.'activity_comments WHERE user_id = '.$user_id.' ORDER BY id DESC LIMIT 1');
		if (isset($row[0]['activity_comments']['created']))
		{
			if (!$date)
			{
				$date = $row[0]['activity_comments']['created'];
			}
			elseif (strtotime($date) < strtotime($row[0]['activity_comments']['created']))
			{
				$date = $row[0]['activity_comments']['created'];
			}
			
		}
		
		$row = $model->query('SELECT * FROM '.$prefix.'comments WHERE user_id = '.$user_id.' ORDER BY id DESC LIMIT 1');
		if (isset($row[0]['comments']['created']))
		{
			if (!$date)
			{
				$date = $row[0]['comments']['created'];
			}
			elseif (strtotime($date) < strtotime($row[0]['comments']['created']))
			{
				$date = $row[0]['comments']['created'];
			}
		}
		
		return $date;
	}
	
	public function runCron()
	{
		$roleModel = MooCore::getInstance()->getModel("Role");
		$roles = $roleModel->find('all',array(
			'conditions'=>array(
				'is_admin' => 0,
				'is_super' => 0
			)
		));
		$admins = array();
		foreach ($roles as $role)
		{
			$admins[]=$role['Role']['id'];
		}
		
		$core_reminder_role = Configure::read('Reminder.reminder_role');
		if ($core_reminder_role)
		{
			$admins = explode(",", $core_reminder_role);
		}
		
		if (!count($admins))
		{
			return;
		}
		
		$userModel = MooCore::getInstance()->getModel("User");
		
		$reminderModel = MooCore::getInstance()->getModel("Reminder.ReminderUser");
		$prefix = $userModel->tablePrefix;
		
		if (Configure::read('core.email_validation') && Configure::read('Reminder.reminder_enable_email_verification') && Configure::read('Reminder.reminder_day_email_verification'))
		{
			$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_day_email_verification').' days'))."'";
			$extra_time = Configure::read('Reminder.reminder_time_email_verification') ? 'AND b.verify_time <'.Configure::read('Reminder.reminder_time_email_verification'): '';
			$query = 'SELECT a.*, b.id as reminder_id, b.verify_time as verify_time FROM `'.$prefix.'users` as a
				LEFT JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
				WHERE (a.confirmed = 0) AND (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND ((b.verify_date is null AND a.created <'.$time.') OR (b.verify_date is not null '.$extra_time.' AND b.verify_date < '.$time.')) LIMIT 5
			';
			$rows = $userModel->query($query);
			
			$ssl_mode = Configure::read('core.ssl_mode');
			$http = (!empty($ssl_mode)) ? 'https' :  'http';
			$mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
			$request = Router::getRequest();
			
			if ($rows)
			{
				foreach ($rows as $row)
				{
					// update count
					$reminderModel->query("UPDATE ".$prefix."reminder_users SET verify_date = '".date('Y-m-d H:i:s')."', verify_time=verify_time+1 WHERE id=" . intval($row['b']['reminder_id']));
					$count = $row['b']['verify_time'];
					
					// send mail
					$text = '';
					if (Configure::read('Reminder.reminder_time_email_verification') && ($count >= Configure::read('Reminder.reminder_time_email_verification') - 1) && Configure::read('Reminder.reminder_disable_email_verification'))
					{
						$text = __d('reminder','Your account will be auto disabled in next %s days.',Configure::read('Reminder.reminder_disable_email_verification'));
					}
					$mailComponent->send($row['a']['email'],'reminder_email_verification',
							array(
									'site_name' => Configure::read('core.site_name'),
									'confirm_link'=> $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/do_confirm/'.$row['a']['code'],
									'text' => $text
							)
							);					
				}
			}
			
			if (Configure::read('Reminder.reminder_time_email_verification') && Configure::read('Reminder.reminder_disable_email_verification'))
			{
				$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_disable_email_verification').' days'))."'";
				$query = 'SELECT a.* FROM `'.$prefix.'users` as a
					INNER JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
					WHERE (a.confirmed = 0) AND (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND (b.verify_date < '.$time.' AND b.verify_time = '.Configure::read('Reminder.reminder_time_email_verification').') LIMIT 5
				';
				$rows = $userModel->query($query);
				foreach ($rows as $row)
				{
					$this->deactiveUser($row['a']['id']);
				}
			}
		}
		
		if(Configure::read('SmsVerify.sms_verify_enable') && Configure::read('Reminder.reminder_enable_sms_verification') && Configure::read('Reminder.reminder_day_sms_verification'))
		{
			$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_day_sms_verification').' days'))."'";
			$extra_time = Configure::read('Reminder.reminder_time_sms_verification') ? 'AND b.verify_sms_time <'.Configure::read('Reminder.reminder_time_sms_verification'): '';
			
			$extra = '';
			if (Configure::read('SmsVerify.sms_verify_enable'))
			{
				
				if (Configure::read("SmsVerify.sms_verify_pass_verify"))
				{
					$extra= '(a.sms_verify = 0) AND';
				}
				else
				{
					$extra= '(a.sms_verify_checked = 0) AND';
				}
			}
			$cond_email = '';
			if (Configure::read('core.email_validation'))
			{
				$cond_email = ' (a.confirmed = 1) AND';
			}
			
			$query = 'SELECT a.*, b.id as reminder_id, b.verify_sms_time as verify_sms_time FROM `'.$prefix.'users` as a
				LEFT JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
				WHERE '.$extra.$cond_email.' (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND ((b.verify_sms_date is null AND a.created <'.$time.') OR (b.verify_sms_date is not null '.$extra_time.' AND b.verify_sms_date < '.$time.')) LIMIT 5
			';			
			$rows = $userModel->query($query);
			
			$ssl_mode = Configure::read('core.ssl_mode');
			$http = (!empty($ssl_mode)) ? 'https' :  'http';
			$mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
			$request = Router::getRequest();
			
			if ($rows)
			{
				foreach ($rows as $row)
				{
					// update count
					$reminderModel->query("UPDATE ".$prefix."reminder_users SET verify_sms_date = '".date('Y-m-d H:i:s')."', verify_sms_time=verify_sms_time+1 WHERE id=" . intval($row['b']['reminder_id']));
					$count = $row['b']['verify_sms_time'];
					
					// send mail
					$text = '';
					if (Configure::read('Reminder.reminder_time_sms_verification') && ($count >= Configure::read('Reminder.reminder_time_sms_verification') - 1) && Configure::read('Reminder.reminder_disable_sms_verification'))
					{
						$text = __d('reminder','Your account will be auto disabled in next %s days.',Configure::read('Reminder.reminder_disable_sms_verification'));
					}
					$mailComponent->send($row['a']['email'],'reminder_sms_verification',
							array(
									'site_name' => Configure::read('core.site_name'),
									'link_login'=> $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/member_login',
									'text' => $text
							)
							);
					
				}
			}
			
			if (Configure::read('Reminder.reminder_time_sms_verification') && Configure::read('Reminder.reminder_disable_sms_verification'))
			{
				
				$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_disable_sms_verification').' days'))."'";
				$query = 'SELECT a.* FROM `'.$prefix.'users` as a
					INNER JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
					WHERE '.$extra.$cond_email.' (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND (b.verify_sms_date < '.$time.' AND b.verify_sms_time = '.Configure::read('Reminder.reminder_time_sms_verification').') LIMIT 5
				';
				$rows = $userModel->query($query);
				foreach ($rows as $row)
				{
					$this->deactiveUser($row['a']['id']);
				}
			}
		}
		
		if (Configure::read('Reminder.reminder_enable_login') && Configure::read('Reminder.reminder_day_login'))
		{
			$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_day_login').' days'))."'";
			$extra_time = Configure::read('Reminder.reminder_time_login') ? 'AND b.login_time <'.Configure::read('Reminder.reminder_time_login'): '';
			$extra = '';
			if (Configure::read('SmsVerify.sms_verify_enable'))
			{
				
				if (Configure::read("SmsVerify.sms_verify_pass_verify"))
				{
					$extra= '(a.sms_verify = 1) AND';
				}
				else
				{
					$extra= '(a.sms_verify_checked = 1) AND';
				}
			}
			$cond_email = '';
			if (Configure::read('core.email_validation'))
			{
				$cond_email = ' (a.confirmed = 1) AND';
			}
			$query = 'SELECT a.*, b.id as reminder_id ,b.login_time as login_time FROM `'.$prefix.'users` as a
				INNER JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
				WHERE '.$extra.$cond_email.' (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND (b.login_date < '.$time.' '.$extra_time.') LIMIT 5
			';
			$rows = $userModel->query($query);
			
			$ssl_mode = Configure::read('core.ssl_mode');
			$http = (!empty($ssl_mode)) ? 'https' :  'http';
			$mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
			$request = Router::getRequest();
			
			if ($rows)
			{
				foreach ($rows as $row)
				{
					// update count
					$reminderModel->query("UPDATE ".$prefix."reminder_users SET login_date = '".date('Y-m-d H:i:s')."', login_time=login_time+1 WHERE id=" . intval($row['b']['reminder_id']));
					$count = $row['b']['login_time'];
					
					// send mail
					$text = '';
					if (Configure::read('Reminder.reminder_time_login') && ($count >= Configure::read('Reminder.reminder_time_login') - 1) && Configure::read('Reminder.reminder_disable_login'))
					{
						$text = __d('reminder','Your account will be auto disabled in next %s days.',Configure::read('Reminder.reminder_disable_login'));
					}
					$mailComponent->send($row['a']['email'],'reminder_login',
							array(
									'site_name' => Configure::read('core.site_name'),
									'link_login'=> $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/member_login',
									'text' => $text
							)
							);
				}
			}
			
			if (Configure::read('Reminder.reminder_time_login') && Configure::read('Reminder.reminder_disable_login'))
			{
				$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_disable_login').' days'))."'";
				$query = 'SELECT a.* FROM `'.$prefix.'users` as a
					INNER JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
					WHERE '.$extra.$cond_email.' (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND (b.login_date < '.$time.' AND b.login_time = '.Configure::read('Reminder.reminder_time_login').') LIMIT 5
				';
				$rows = $userModel->query($query);
				foreach ($rows as $row)
				{
					$this->deactiveUser($row['a']['id']);
				}
			}
		}
		
		if (Configure::read('Reminder.reminder_enable_share') && Configure::read('Reminder.reminder_day_share'))
		{
			$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_day_share').' days'))."'";
			$extra_time = Configure::read('Reminder.reminder_time_share') ? 'AND b.share_time <'.Configure::read('Reminder.reminder_time_share'): '';
			$extra = '';
			if (Configure::read('SmsVerify.sms_verify_enable'))
			{
				
				if (Configure::read("SmsVerify.sms_verify_pass_verify"))
				{
					$extra= '(a.sms_verify = 1) AND';
				}
				else
				{
					$extra= '(a.sms_verify_checked = 1) AND';
				}
			}
			$cond_email = '';
			if (Configure::read('core.email_validation'))
			{
				$cond_email = ' (a.confirmed = 1) AND';
			}
			
			if (Configure::read('Reminder.reminder_enable_login') && Configure::read('Reminder.reminder_day_login'))
			{
				$extra.= '(b.login_time = 0) AND';
			}
			
			$query = 'SELECT a.*, b.id as reminder_id ,b.share_time as share_time FROM `'.$prefix.'users` as a
				INNER JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
				WHERE '.$extra.$cond_email.' (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND (b.share_date < '.$time.' '.$extra_time.') LIMIT 5
			';
			
			$rows = $userModel->query($query);
			
			$ssl_mode = Configure::read('core.ssl_mode');
			$http = (!empty($ssl_mode)) ? 'https' :  'http';
			$mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
			$request = Router::getRequest();
			
			if ($rows)
			{
				foreach ($rows as $row)
				{
					// update count
					$reminderModel->query("UPDATE ".$prefix."reminder_users SET share_date = '".date('Y-m-d H:i:s')."', share_time=share_time+1 WHERE id=" . intval($row['b']['reminder_id']));
					$count = $row['b']['share_time'];
					
					// send mail
					$text = '';
					if (Configure::read('Reminder.reminder_time_share') && ($count >= Configure::read('Reminder.reminder_time_share') - 1) && Configure::read('Reminder.reminder_disable_share'))
					{
						$text = __d('reminder','Your account will be auto disabled in next %s days.',Configure::read('Reminder.reminder_disable_share'));
					}
					$mailComponent->send($row['a']['email'],'reminder_share',
							array(
									'site_name' => Configure::read('core.site_name'),
									'link_login'=> $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/member_login',
									'text' => $text
							)
							);
					
				}
			}
			if (Configure::read('Reminder.reminder_time_share') && Configure::read('Reminder.reminder_disable_share'))
			{
				$time = "'".date('Y-m-d H:i:s',strtotime('- '.Configure::read('Reminder.reminder_disable_share').' days'))."'";
				$query = 'SELECT a.* FROM `'.$prefix.'users` as a
					INNER JOIN `'.$prefix.'reminder_users` as b ON a.id = b.user_id
					WHERE '.$extra.$cond_email.' (a.active = 1) AND (a.approved = 1) AND (a.role_id IN ('.implode(',', $admins).')) AND (b.share_date < '.$time.' AND b.share_time = '.Configure::read('Reminder.reminder_time_share').') LIMIT 5
				';
				$rows = $userModel->query($query);
				foreach ($rows as $row)
				{
					$this->deactiveUser($row['a']['id']);
				}
			}
		}
		
	}
	
	public function deactiveUser($user_id)
	{
		$userModel = MooCore::getInstance()->getModel("User");
		$prefix = $userModel->tablePrefix;
		$user = $userModel->findById($user_id);
		$userModel->query("DELETE FROM ".$prefix."cake_sessions WHERE user_id=" . intval($user['User']['id']));
		$userModel->query("DELETE FROM ".$prefix."oauth_access_tokens WHERE user_id=" . intval($user['User']['id']));
		$userModel->query("DELETE FROM ".$prefix."oauth_refresh_tokens WHERE user_id=" . intval($user['User']['id']));
		$userModel->query("UPDATE ".$prefix."users SET approved = 0 WHERE id=" . intval($user_id));
		
		$mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
		$request = Router::getRequest();
		$ssl_mode = Configure::read('core.ssl_mode');
		$http = (!empty($ssl_mode)) ? 'https' :  'http';
		$mailComponent->send($user['User']['email'],'unapprove_user',
				array(
						'recipient_title' => $user['User']['name'],
						'recipient_link' => $http.'://'.$_SERVER['SERVER_NAME'].$user['User']['moo_href'],
						'link'=> $http.'://'.$_SERVER['SERVER_NAME'].$request->base.'/home/contact'
				)
		);
	}
}
