<?php

App::import('Cron.Task', 'CronTaskAbstract');

class BusinessTaskPayment extends CronTaskAbstract {

    public function execute() {
        $paidModel = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $helper = MooCore::getInstance()->getHelper('Business_Business');
        // expire
        $items = $paidModel->find('all',
        	array(	
        		'conditions' => array(
        			'BusinessPaid.active' => 1,
        			'BusinessPaid.expiration_date <' => date("Y-m-d H:i:s"),
        			'BusinessPaid.expiration_date <>' => 'NULL',
        		),
        		'limit'=>10
        	)
        );
        foreach ($items as $paid)
        {
        	$helper->onExpire($paid);
        }
        // remind
        $reminds = $paidModel->find('all',
        	array(	
        		'conditions' => array(
        			'BusinessPaid.active' => 1,
        			'BusinessPaid.is_warning_email_sent' => 0,
        			'BusinessPaid.reminder_date <>' => 'NULL',
        			'BusinessPaid.reminder_date <' => date("Y-m-d H:i:s"),
        		),
        		'limit'=>10
        	)
        );
        $timeHelper = MooCore::getInstance()->getHelper('Core_Time');
        $request = Router::getRequest();
    	foreach ($reminds as $paid)
        {
            $package = $paid['BusinessPackage'];
            //Send email
            $ssl_mode = Configure::read('core.ssl_mode');
            $http = (!empty($ssl_mode)) ? 'https' :  'http';
            $mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
            $current_language = Configure::read('Config.language');
            if ($paid['User']['lang']){
                Configure::write('Config.language',$paid['User']['email']);	        
            }
            if($paid['BusinessPaid']['pay_type'] == 'featured_package') {
                $params = array(
                    'business_title' => $paid['Business']['name'],
                    'package_title' => $package['name'],
                    'package_description' =>  $helper->getPackageDescription($package, $paid['BusinessPaid']['currency_code']),
                    'link' => $http.'://'.$_SERVER['SERVER_NAME'].$request->base.'/businesses/dashboard/feature/'.$paid['BusinessPaid']['business_id'],
                    'expire_time' =>  $timeHelper->format($paid['BusinessPaid']['expiration_date'],Configure::read('core.date_format'),null,$paid['User']['timezone']),
                );
            }
            if($paid['BusinessPaid']['pay_type'] == 'business_package'){
                $params = array(
                    'business_name' => $paid['Business']['name'],
                    'package_title' => $package['name'],
                    'package_description' =>  $helper->getPackageDescription($package, $paid['BusinessPaid']['currency_code']),
                    'link' => $http.'://'.$_SERVER['SERVER_NAME'].$request->base.'/businesses/dashboard/upgrade/'.$paid['BusinessPaid']['business_id'],
                    'expire_time' =>  $timeHelper->format($paid['BusinessPaid']['expiration_date'],Configure::read('core.date_format'),null,$paid['User']['timezone']),
                );
            }
            

            if ($paid['User']['lang']){
                Configure::write('Config.language',$current_language);
            }
            $mailComponent->send($paid['User']['email'],'business_reminder_'.$paid['BusinessPaid']['pay_type'],$params);
            $paidModel->id = $paid['BusinessPaid']['id'];
            $paidModel->save(array('is_warning_email_sent'=>1));
        }
    }
}
