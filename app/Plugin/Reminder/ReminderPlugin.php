<?php 
App::uses('MooPlugin','Lib');
class ReminderPlugin implements MooPlugin{
    public function install(){
    	//Setting
    	$settingModel = MooCore::getInstance()->getModel('Setting');
    	$setting = $settingModel->findByName('reminder_enabled');
    	if ($setting)
    	{
    		$settingModel->id = $setting['Setting']['id'];
    		$settingModel->save(array('is_boot'=>1));
    	}
    	
    	//Mail template
    	$languageModel = MooCore::getInstance()->getModel('Language');
    	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
    	$langs = $languageModel->find('all');
    	$data['Mailtemplate'] = array(
    			'type' => 'reminder_email_verification',
    			'plugin' => 'Reminder',
    			'vars' => '[site_name],[link_confirm],[text]'
    	);
    	$mailModel->save($data);
    	$id = $mailModel->id;
    	foreach ($langs as $lang)
    	{
    		$language = $lang['Language']['key'];
    		$mailModel->locale = $language;
    		$data_translate['subject'] = 'Please verify your email on [site_name]';
    		$content = <<<EOF
		    <p>[header]</p>
			<p>We saw that you have not finished your email verification step yet. Please click the link below to validate:</p>
            <p><a href="[confirm_link]">[confirm_link]</a></p>
			<p>[text]</p>
			<p>[footer]</p>
EOF;
    		$data_translate['content'] = $content;
    		$mailModel->save($data_translate);
    	}
    	
    	$mailModel->clear();
    	$data['Mailtemplate'] = array(
    			'type' => 'reminder_sms_verification',
    			'plugin' => 'Reminder',
    			'vars' => '[site_name],[link_login],[text]'
    	);
    	$mailModel->save($data);
    	$id = $mailModel->id;
    	foreach ($langs as $lang)
    	{
    		$language = $lang['Language']['key'];
    		$mailModel->locale = $language;
    		$data_translate['subject'] = 'Please verify sms on [site_name]';
    		$content = <<<EOF
		    <p>[header]</p>
			<p>We saw that you have not finished your sms verify step yet. Please click the link below to validate:</p>
            <p><a href="[link_login]">[link_login]</a></p>
			<p>[text]</p>
			<p>[footer]</p>
EOF;
    		$data_translate['content'] = $content;
    		$mailModel->save($data_translate);
    	}
    	
    	
    	$mailModel->clear();
    	$data['Mailtemplate'] = array(
    			'type' => 'reminder_login',
    			'plugin' => 'Reminder',
    			'vars' => '[site_name],[link_login],[text]'
    	);
    	$mailModel->save($data);
    	$id = $mailModel->id;
    	foreach ($langs as $lang)
    	{
    		$language = $lang['Language']['key'];
    		$mailModel->locale = $language;
    		$data_translate['subject'] = 'Please login and share your lasted updates on [site_name]';
    		$content = <<<EOF
		    <p>[header]</p>
			<p>We saw that you have not loggedin and interact with members on "[site_name]" for a long time.  Please click the link below to share you lasted updates:</p>
            <p><a href="[link_login]">[link_login]</a></p>
			<p>[text]</p>
			<p>[footer]</p>
EOF;
    		$data_translate['content'] = $content;
    		$mailModel->save($data_translate);
    	}
    	
    	
    	$mailModel->clear();
    	$data['Mailtemplate'] = array(
    			'type' => 'reminder_share',
    			'plugin' => 'Reminder',
    			'vars' => '[site_name],[link_login],[text]'
    	);
    	$mailModel->save($data);
    	$id = $mailModel->id;
    	foreach ($langs as $lang)
    	{
    		$language = $lang['Language']['key'];
    		$mailModel->locale = $language;
    		$data_translate['subject'] = 'Share your lasted updates on [site_name]';
    		$content = <<<EOF
		    <p>[header]</p>
			<p>We saw that you have not posted status, liked or commented on anything on "[site_name]" for a long time.  Please click the link below to share you lasted updates:</p>
            <p><a href="[link_login]">[link_login]</a></p>
			<p>[text]</p>
			<p>[footer]</p>
EOF;
    		$data_translate['content'] = $content;
    		$mailModel->save($data_translate);
    	}
    }
    public function uninstall(){
    	//Mail
    	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
    	$mail = $mailModel->findByType('reminder_email_verification');
    	if ($mail)
    	{
    		$mailModel->delete($mail['Mailtemplate']['id']);
    	}
    	
    	$mail = $mailModel->findByType('reminder_sms_verification');
    	if ($mail)
    	{
    		$mailModel->delete($mail['Mailtemplate']['id']);
    	}
    	
    	$mail = $mailModel->findByType('reminder_login');
    	if ($mail)
    	{
    		$mailModel->delete($mail['Mailtemplate']['id']);
    	}
    	
    	$mail = $mailModel->findByType('reminder_share');
    	if ($mail)
    	{
    		$mailModel->delete($mail['Mailtemplate']['id']);
    	}
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('reminder','Settings') => array('plugin' => 'reminder', 'controller' => 'reminder_settings', 'action' => 'admin_index'),
        	__d('reminder','Logs') => array('plugin' => 'reminder', 'controller' => 'reminders', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}