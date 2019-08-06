<?php 
App::uses('MooPlugin','Lib');
class AdsPlugin implements MooPlugin{
    public function install()
    {

        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        //Mail template user create ad - sent to user
        $content = 
'
<p>[header]</p>
<p>Thanks for advertising on [site_name]! We\'re reviewing your ad and will get back to you soon.</p>
<p>[footer]</p>
';
        $this->createMailTemplate('user_create_ad_user', '[site_name]', 'Thanks for advertising on [site_name]', $content);
        
        //Mail template user create ad
        $content = 
'
<p>[header]</p>
<p>New ad is submitted, please review and enable it at</p>
<p><a href="[review_url]">[review_url]</a></p>
<p>Note from member:[user_note]</p>
<p>[footer]</p>
';
    	$this->createMailTemplate('user_create_ad', '[review_url],[user_note]', 'New ad is submitted. Please review', $content);
        
        //Mail template send payment request
        $content = 
'
<p>[header]</p>
<p>Your ad has been reviewed and it\'s ready to go live. Please make payment by clicking on the below link</p>
<p><a href="[payment_url]">[payment_url]</a></p>
<p>[footer]</p>
';
    	$this->createMailTemplate('send_payment_request', '[payment_url]', 'Payment request for your ads', $content);
        
        //Mail template ad activated
        $content = 
'
    <p>[header]</p>
    <p>Thank you for your payment, your ad was activated</p>
    <p>You can find your ads specific report here:<a href="[link_report]">[link_report]</a> </p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('ad_activated', '[link_report]', 'Congrats, your ad was activated', $content);
        
        //Mail template ad expired
        $content = 
'
    <p>[header]</p>
    <p>Your ad  <site name> has been disabled because it\'s [expire_reason]. Please click on the below link to contact site admin for more details</p>
    <p><a href="[link_contact]">[link_contact]</a></p>
    <p>Check your ad report here <a href="[link_report]">[link_report]</a></p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('user_ads_expired', '[expire_reason],[link_contact],[link_report]', 'Your ad has been disabled.', $content);
        
        //Mail template ad report
        $content = 
'
    <p>[header]</p>
    <p>Here is detailed report about the number of views and click of your ads from [report_from] to [report_to]</p>
    <p>Link to view</p>
    <p><a href="[link_report]">[link_report]</a></p>
    <p>[footer]</p>
';
    	$this->createMailTemplate('user_ads_report', '[report_from],[report_to][link_report]', 'Ad report', $content);
        
                //Mail template ad create account moo
        $content = 
'
    <p>[header]</p>
    <p>Your account has been created on [site_name]. Please sign in to manage your ads</p>
    <p>Your credentials:</p>
    <p>
        Email:[email]<br>
        Pass:[pass]
    </p>
    <p>[footer]</p>
';
        $this->createMailTemplate('user_ads_create_account', '[site_name],[email][pass]', 'Your account on [site_name] already auto created', $content);
        //Setting
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $setting = $mSetting->findByName('ads_enabled');
        if ($setting)
        {
        	$mSetting->id = $setting['Setting']['id'];
        	$mSetting->save(array('is_boot'=>1));
        }
        // permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',',$role['Role']['params']);
            $params = array_unique(array_merge($params,array('ads_can_add_ads')));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }
        //core menu item
        $mCMitem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $mCMitem->set(array(
                    'name' => 'Advertise with us',
                    'url' => '/ads/create',
                    'is_active' => 1,
                    'menu_id' => 2,
                    'type' => 'link',
                    'role_access'=> json_encode($role_ids),
                    'original_name'=>'Advertise with us',
                    'menu_order' => 999,
                    'plugin' => 'Ads'
                ));
         $mCMitem->save();
        //cron
        $mTask = MooCore::getInstance()->getModel('Task');
        $mTask->save(array(
            'title' => 'Ads Cron',
            'plugin' => 'Ads',
            'timeout' => 60,
            'processes' => 1,
            'enable' => 1,
            'class' => 'Ads_Task_Cron'
        ));
        

    }
    
    private function createMailTemplate($type, $vars, $subject, $content)
    {
        $languageModel = MooCore::getInstance()->getModel('Language');
     	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $langs = $languageModel->find('all');
        $data['Mailtemplate'] = array(
            'type' => $type,
            'plugin' => 'Ads',
            'vars' => $vars
        );
        $mailModel->create();
        $mailModel->save($data);
        $id = $mailModel->id;
        foreach ($langs as $lang)
        {
            $language = $lang['Language']['key'];
            $mailModel->locale = $language;
            $data_translate['subject'] = $subject;
            $data_translate['content'] = $content;
            $mailModel->save($data_translate);
        }
    }
    
    public function uninstall()
    {
        //delete Mail template
    	$mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
    	$mails = $mailModel->find('all', array(
            'conditions' => array(
                'Mailtemplate.plugin' => 'Ads',
                "Mailtemplate.type IN('user_create_ad_user', 'user_create_ad', 'send_payment_request', 'ad_activated', 'user_ads_expired', 'user_ads_report','user_ads_create_account')" 
            ),
            'fields' => array('Mailtemplate.id')
        ));
    	if ($mails != null)
    	{
            foreach($mails as $mail)
            {
                $mailModel->delete($mail['Mailtemplate']['id']);
            }   
        }
        
        //delete setting
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $settingGroup = $mSettingGroup->findByModuleId('Ads');
        if($settingGroup != null)
        {
            $mSetting->deleteAll(array(
                'Setting.group_id' => $settingGroup['SettingGroup']['id']
            ));
            $mSettingGroup->delete($settingGroup['SettingGroup']['id']);
        }
        
        //delete cron
        $mTask = MooCore::getInstance()->getModel('Task');
        $mTask->deleteAll(array(
            'class' => 'Ads_Task_Cron'
        ));
        //delete core menu item
         $mCMitem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
         $mCMitem->deleteAll(array(
             'plugin'=>'Ads'
         ));
         //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        foreach ($roles as $role)
        {
            $params = explode(',',$role['Role']['params']);
            $params = array_diff($params,array('ads_can_add_ads','ads_hide_all_ads'));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('ads', 'Manage Placements') => array('plugin' => 'ads', 'controller' => 'ads_placement', 'action' => 'admin_index'),
            __d('ads', 'Manage Ad Campaigns') => array('plugin' => 'ads', 'controller' => '', 'action' => 'admin_index'),
            __d('ads', 'General Settings') => array('plugin' => 'ads', 'controller' => 'ads_settings', 'action' => 'admin_index'),
            __d('ads', 'Transactions') => array('plugin' => 'ads', 'controller' => 'ads_transaction', 'action' => 'admin_index'),
        );
    }
    public function callback_1_1(){
    //Mail template ad create account moo
        $content = '
    <p>[header]</p>
    <p>Your account has been created on [site_name]. Please sign in to manage your ads</p>
    <p>Your credentials:</p>
    <p>
        Email:[email]<br>
        Pass:[pass]
    </p>
    <p>[footer]</p>
';
        $this->createMailTemplate('user_ads_create_account', '[site_name],[email][pass]', 'Your account on [site_name] already auto created', $content);



        $db = ConnectionManager::getDataSource('default');
        $prefix_table = Configure::read('core.prefix');
        $mPlacement = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        $mCampaign = MooCore::getInstance()->getModel('Ads.AdsCampaign');
        $mooMail = MooCore::getInstance()->getComponent('MooMail');
        $ads_placement_table = str_replace('{PREFIX}', $prefix_table, "`{PREFIX}ads_placements`");
        $ads_campaign_table = str_replace('{PREFIX}', $prefix_table, "`{PREFIX}ads_campaigns`");
        // get all data from ads_placement table
        $aPlacements = $mPlacement->getAllDataPeriod();
        $aCampaigns = $mCampaign->getDataForUpgrade();
        // delete column period
        $query = "ALTER TABLE $ads_placement_table DROP `period`";
        $db->rawQuery($query);
        // add column period with new type
        $query = "ALTER TABLE $ads_placement_table ADD `period` INT(11) NOT NULL ;";
         $db->rawQuery($query);
        // insert old data into new period column
        foreach($aPlacements as $item){
            $item = $item['AdsPlacement'];
            switch($item['period']){
                case 'week':
                    $num_date = 7;
                    break;
                case 'month':
                    $num_date = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                    break;
                case 'year':
                    $num_date = $this->getyears(date('Y'));
                    break;
            }
            // insert back to period
            $mPlacement->insertPeriodColum($item['id'],$num_date);
        }
        
        // add is_hide and user_id column
        $query = "ALTER TABLE  $ads_campaign_table ADD `is_hide` TINYINT(1) NOT NULL DEFAULT '0'";
        $db->rawQuery($query);
        $query = "ALTER TABLE $ads_campaign_table ADD `user_id` INT(11) NULL DEFAULT NULL";
        $db->rawQuery($query);
        
        // create account if user not
        foreach ($aCampaigns as $ad) {
            $ad = $ad['AdsCampaign'];
            $emailExist = $mCampaign->checkUserExistByEmail($ad['email']);
            if (!$emailExist) {// create new account
                $password = $mCampaign->randomPassword();
                $newUser = $mCampaign->createMooAccount($ad['email'], $ad['client_name'], $password);
                if ($newUser) { 
                    // sending mail
                    $mCampaign->upgradeUserID($ad['id'],$newUser['User']['id']);
                     $mooMail->send($ad['email'], 'user_ads_create_account', array(
                        'site_name' => Configure::read('core.site_name'),
                        'email' => $ad['email'],
                        'pass' => $password
                    ));
                }
            }
        }
    }
    public function getyears($years) {
        $years = date('Y',  strtotime($years));
        if (((($years % 4) == 0) && ((($years % 100) != 0) || (($years % 400) == 0)))) {
            return 366;
        } else {
            return 365;
        }
    }
    
    public function callback_1_6(){
        // permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role)
        {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',',$role['Role']['params']);
            $params = array_unique(array_merge($params,array('ads_can_add_ads')));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params'=>implode(',', $params)));
        }
        $mCMitem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $data = $mCMitem->findByPlugin('Ads');

        if($data){
            $mCMitem->save(array('role_access'=>json_encode($role_ids),'id'=>$data['CoreMenuItem']['id']));
        }
    }
    
    public function callback_1_7(){
        // update content for coreblock
        $coreBlockModel = MooCore::getInstance()->getModel('CoreBlock');
        $coreContentModel = MooCore::getInstance()->getModel('CoreContent');
        $advertisement_widget = $coreBlockModel->find('first', array('conditions' => array('CoreBlock.name' => ADS_WIDGET, 'CoreBlock.plugin' => 'Ads')));
        if ($advertisement_widget) {
            $params = $advertisement_widget['CoreBlock']['params'];
            $params = json_decode($params, true);
            $params_elements = array(
                'label'=>'Show see your ad here',
                'input'=>'checkbox',
                'value'=>"1",
                'name'=>'see_your_ad_here'
            );
            $params[] = $params_elements;
            $params = json_encode($params);
            $coreBlockModel->id = $advertisement_widget['CoreBlock']['id'];
            $coreBlockModel->saveField('params', $params);
            
        }
        // add "See yourad here " to core content
        $core_content_data = $coreContentModel->find('all',array('conditions'=>array('CoreContent.name'=>'ads.advertisement')));
        if($core_content_data){
            foreach($core_content_data as $item){
                $params = $item['CoreContent']['params'];
                $params = json_decode($params,true);
                $params['see_your_ad_here'] = "0";
                $params = json_encode($params);
                $coreContentModel->id = $item['CoreContent']['id'];
                $coreContentModel->saveField('params',$params);
            }
        }
        $upload_path =  APP . DS.'webroot'.DS.'uploads'.DS;
         if(is_dir($upload_path.'ads')){
             // delete "commercial" folder
             if(is_dir($upload_path.'commercial')){
                 rmdir($upload_path.'commercial');
             }
             // rename ads folder to "commercial"
             rename($upload_path.'ads', $upload_path.'commercial');
         }
     
     
    }

    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}