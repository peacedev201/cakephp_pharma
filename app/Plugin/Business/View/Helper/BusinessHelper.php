<?php

App::uses('AppHelper', 'View/Helper');

class BusinessHelper extends AppHelper {
    protected $_enable = null;
    protected $_active_business = array();
    public $helpers = array('Storage.Storage');
    public $is_app = false;
    public function getLngLatByAddress($address) {
        if($address != null)
        {
            $address = urlencode($address);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key='.Configure::read('core.google_dev_key'));
            $output = json_decode($geocode);
            $lat = !empty($output->results[0]->geometry->location->lat) ? $output->results[0]->geometry->location->lat : 0;
            $lng = !empty($output->results[0]->geometry->location->lng) ? $output->results[0]->geometry->location->lng : 0;
            $lat = str_replace(',', '.', $lat);
            $lng = str_replace(',', '.', $lng);
            return array(
                'lng' => $lng,
                'lat' => $lat
            );
        }
        return array('lng' => 0, 'lat' => 0);
    }
    
    public function isTrial($input)
    {
    	return false;
    }

    public function getAddressDetail($lat = null, $lng = null, $address = null) {
        if($address != null)
        {
            $address = urlencode($address);
            $url = 'https://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key='.Configure::read('core.google_dev_key');
        }
        else
        {
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false&key='.Configure::read('core.google_dev_key');
        }
        $json = @file_get_contents($url);
        $result = json_decode($json);
        $address_value = !empty($result->results[0]->formatted_address) ? $result->results[0]->formatted_address : '';
        $lng = !empty($result->results[0]->geometry->location->lng) ? $result->results[0]->geometry->location->lng : 0;
        $lat = !empty($result->results[0]->geometry->location->lat) ? $result->results[0]->geometry->location->lat : 0;
        $country = $region = $postal_code = $city = null;
        $lat = str_replace(',', '.', $lat);
		$lng = str_replace(',', '.', $lng);
        if(!empty($result->results))
        {
            foreach($result->results as $result)
            {
                if(!empty($result->address_components))
                {
                    foreach ($result->address_components as $item) 
                    {
                        if (!empty($item->types[0]) && $item->types[0] == 'country' && $country == null) 
                        {
                            $country = $item->long_name;
                        }
                        if (!empty($item->types[0]) && $item->types[0] == 'locality' && $city == null)
                        {
                            $city = $item->long_name;
                        }
                        if (!empty($item->types[0]) && $item->types[0] == 'locality' && $region == null)
                        {
                            $region = $item->long_name;
                        }
                        else if (!empty($item->types[0]) && $item->types[0] == 'administrative_area_level_2' && $region == null) 
                        {
                            $region = $item->long_name;
                        }
                        else if (!empty($item->types[0]) && $item->types[0] == 'administrative_area_level_1' && $region == null) 
                        {
                            $region = $item->long_name;
                        }
                        if (!empty($item->types[0]) && $item->types[0] == 'postal_code' && $postal_code == null) 
                        {
                            $postal_code = $item->long_name;
                        }
                    }
                }
            }
        }
      
        return array(
            'address' => $address_value,
            'country' => $country,
            'region' => $region,
            'postal_code' => $postal_code,
            'lat' => $lat,
            'lng' => $lng,
            'city' => $city
        );
    }
    
    public function getAddressByPostCode($postcode) 
    {
        $prepAddr = str_replace(' ', '+', $postcode);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false&key='.Configure::read('core.google_dev_key'));
        $output = json_decode($geocode);
        
        return array(
            'address' => !empty($output->results[0]->formatted_address) ? $output->results[0]->formatted_address : '',
            'lng' => !empty($output->results[0]->geometry->location->lng) ? $output->results[0]->geometry->location->lng : 0,
            'lat' => !empty($output->results[0]->geometry->location->lat) ? $output->results[0]->geometry->location->lat : 0
        );
    }
    
    public function findNearByCity($latitude = null, $longitude = null)
    {
        // set request options
        if($latitude != null && $longitude != null)
        {
            $responseStyle = 'short'; // the length of the response
            $citySize = 'cities15000'; // the minimal number of citizens a city must have
            $radius = 30; // the radius in KM
            $maxRows = 30; // the maximum number of rows to retrieve
            $username = 'minhtuan6023'; // the username of your GeoNames account

            // get nearby cities based on range as array from The GeoNames API
            $data = json_decode(file_get_contents('http://api.geonames.org/findNearbyPlaceNameJSON?lat='.$latitude.'&lng='.$longitude.'&style='.$responseStyle.'&cities='.$citySize.'&radius='.$radius.'&maxRows='.$maxRows.'&username='.$username.'&key='.Configure::read('core.google_dev_key'), true), true);
            return $data['geonames'];
        }
        return null;
    }

    public function getPhoto($item, $params = array()) {
        if(isset($item['Business']))
        {
            $item = $item['Business'];
        }
        $class = '';
        $prefix = '';
        if (!empty($params['prefix'])) {
            $prefix = $params['prefix'];
        }
        $url = $this->Storage->getUrl($item['id'], $prefix, $item['logo'], "businesses");
        if (!empty($params['class'])) {
            $class = 'class="' . $params['class'] . '"';
        }
        if (isset($params['tag']) && $params['tag'] == false) {
            return $url;
        }
        return '<img src="'.$url.'" '.$class.' />';
    }
    
    public function getVerifyFile($item, $params = array()) {
        if(isset($item['BusinessVerify']))
        {
            $item = $item['BusinessVerify'];
        }
        return $this->Storage->getUrl($item['id'], '', $item['document'], "business_verifies");;
    }

    public function canUpgradePackage($business) {
        return true;
    }
    
    public function canFeaturedBusiness($business) {
        return true;
    }

    public function getParamsPayment($item) {
        $paid = $item['BusinessPaid'];
        $url = Router::url('/', true);
        $first_amount = 0;
        if($paid['pay_type'] == 'featured_package') {
            $params = array(
                'cancel_url' => $url . 'business_payment/cancel/'.$paid['business_id'],
                'return_url' => $url . 'business_payment/success/'.$paid['business_id'],
                'currency' => $paid['currency_code'],
                'description' => __d('business', 'Featured Business with %s %s for %s days', $paid['feature_price'], $paid['currency_code'],$paid['feature_day']),
                'type' => 'Business_Business_Paid',
                'id' => $paid['id'],
                'is_recurring' => 0,
                'amount' => $paid['feature_price'],
                'first_amount' => $first_amount,
                'end_date' => $this->calculateFeaturedEndDate($paid,  0),
                'total_amount' => $paid['feature_price'],
                'memo' => __d('business', 'Featured Price')
            );
        }else {
            $package = $item['BusinessPackage'];
            switch ($package['type']) {
                case BUSINESS_ONE_TIME:
                case BUSINESS_RECURRING:
                    $first_amount = $package['price'];
                    break;
            }
            $params = array(
                'cancel_url' => $url . 'business_payment/cancel/'.$paid['business_id'],
                'return_url' => $url . 'business_payment/success/'.$paid['business_id'],
                'currency' => $paid['currency_code'],
                'description' => $package['name'] . ' - ' . $this->getPackageDescription($package, $paid['currency_code']),
                'type' => 'Business_Business_Paid',
                'id' => $paid['id'],
                'is_recurring' => $this->isRecurring($item),
                'amount' => $package['price'],
                'first_amount' => $first_amount,
                'end_date' => $this->calculateEndDate($package),
                'total_amount' => $this->totalAmount($package),
            	'cycle' => $package['billing_cycle'],
            	'cycle_type' => $package['billing_cycle_type'],
            	'duration' => $package['duration'],
            	'duration_type' => $package['duration_type'],
                'memo' => __d('business', 'Package Price')
            );
        }
        return $params;
    }
    public function isRecurring($item) {
    	if (!$item)
    		return true;
    	
        return (in_array($item['BusinessPackage']['type'], array(BUSINESS_RECURRING)) ? true : false);
    }
    public function getPackageDescription($package, $currency) {
        $info = '';
        switch ($package['type']) {
            case BUSINESS_ONE_TIME:
                if ($package['price'] > 0) {
                    $info = $package['price'] . ' ' . $currency . ' ' . __d('business', 'for') . ' ' . $this->getTextDuration($package['duration'], $package['duration_type']);
                } else {
                    $info = __d('business', 'free for') . ' ' . $this->getTextDuration($package['duration'], $package['duration_type']);
                }
                break;
            case BUSINESS_RECURRING:
                if ($package['price'] > 0) {
                    $info = $package['price'] . ' ' . $currency . ' ' . __d('business', 'for each') . ' ' . $this->getTextDuration($package['billing_cycle'], $package['billing_cycle_type']) . ' ' . __d('business', 'in') . ' ' . $this->getTextDuration($package['duration'], $package['duration_type']);
                } else {
                    $info = __d('business', 'free for') . ' ' . $this->getTextDuration($package['duration'], $package['duration_type']);
                }
                break;
        }

        return $info;
    }
    
    public function getTextDuration($num, $type) {
        switch ($type) {
            case 'forever': return __d('business', 'forever');
                break;
            case 'day': return $num . ' ' . __d('business', 'day(s)');
                break;
            case 'week': return $num . ' ' . __d('business', 'week(s)');
                break;
            case 'month': return $num . ' ' . __d('business', 'month(s)');
                break;
            case 'year': return $num . ' ' . __d('business', 'year(s)');
                break;
        }
    }
    public function totalAmount($package) {
        $total = 0;
        switch ($package['type']) {
            case BUSINESS_ONE_TIME:
                $total = $package['price'];
                break;
            case BUSINESS_RECURRING:
                $cycle = $package['billing_cycle'];
                $end = $this->getTotalTimeByType($package['billing_cycle_type'], $package['duration_type'], $package['duration']);
                if ($cycle && $end) {
                    $total = floor($end / $cycle) * $package['price'];
                }
                break;
        }
        return $total;
    }
    
    public function calculateEndDate($package) {
        $end_date = '';
        if ($package['duration_type'] != 'forever') {
            $end_date = $this->calculateTime($package['duration_type'], $package['duration'], $end_date);
            return date('Y-m-d H:i:s', strtotime("-1 hours", strtotime($end_date)));
        }
        return '';
    }
    
    public function calculateTime($type, $duration, $time = '') {
        if ($time == '')
            $time = date("Y-m-d H:i:s");

        $result = '';
        switch ($type) {
            case 'day':
                $result = strtotime("+$duration Day", strtotime($time));
                break;
            case 'week':
                $result = strtotime("+$duration Week", strtotime($time));
                break;
            case 'month':
                $result = strtotime("+$duration Month", strtotime($time));
                break;
            case 'year':
                $result = strtotime("+$duration Year", strtotime($time));
                break;
        }

        return date('Y-m-d H:i:s', $result);
    }
    public function getTotalTimeByType($type, $duration_type, $duration) {
        $result = 0;
        switch ($type) {
            case 'day':
                switch ($duration_type) {
                    case 'day':
                        $result = $duration;
                        break;
                    case 'week':
                        $result = $duration * 7;
                        break;
                    case 'month':
                        $result = $duration * 30;
                        break;
                    case 'year':
                        $result = $duration * 365;
                        break;
                }

                break;
            case 'week':
                switch ($duration_type) {
                    case 'week':
                        $result = $duration;
                        break;
                    case 'month':
                        $result = $duration * 4;
                        break;
                    case 'year':
                        $result = floor($duration * (365 / 7));
                        break;
                }
                break;
            case 'month':
                switch ($duration_type) {
                    case 'month':
                        $result = $duration;
                        break;
                    case 'year':
                        $result = $duration * 12;
                        break;
                }
                break;
            case 'year':
                switch ($duration_type) {
                    case 'year':
                        $result = $duration;
                        break;
                }
                break;
        }

        return $result;
    }
    
    public function checkEnablePackage() {
        if ($this->_enable !== null) {
            return $this->_enable;
        }

        $gateway = MooCore::getInstance()->getModel('PaymentGateway.Gateway');
        $mPackage = MooCore::getInstance()->getModel('Business.BusinessPackage');

        if (!$gateway->hasAny(array('enable' => 1))) {
            $this->_enable = false;
            return false;
        }

        if (!$mPackage->hasAny(array('enable' => 1))) {
            $this->_enable = false;
            return false;
        }
        $this->_enable = true;
        return true;
    }
    public function isFreePackage($package) {
        if ($package['BusinessPackage']['price'] == 0) {
            return true;
        }
        return false;
    }

    public function onFailure($item, $data) {
        $paidModel = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $transactionModel = MooCore::getInstance()->getModel('Business.BusinessTransaction');

        $data = array('user_id' => $item['BusinessPaid']['user_id'],
            'business_paid_id' => $item['BusinessPaid']['id'],
            'business_package_id' => $item['BusinessPaid']['business_package_id'],
            'pay_type' => $item['BusinessPaid']['pay_type'] ,
            'status' => 'failed',
            'callback_params' => json_encode($data)
        );
        
        $transactionModel->save($data);
        
        $paidModel->id = $item['BusinessPaid']['id'];
        $paidModel->save(array('status' => 'pending', 'business_transaction_id' => $transactionModel->id));
    }

    public function onExpire($item, $expire = false) {
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        $request = Router::getRequest();
        if($item['BusinessPaid']['pay_type'] == 'featured_package') {
            $package = $item['BusinessPackage'];
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $mBusiness->id = $item['BusinessPaid']['business_id'];
            $mBusiness->save(array('featured' => 0));
            $mail_type = 'business_expire_feature';
            $params = array(
                'package_title' => $package['name'],
                'expire_time' => $item['BusinessPaid']['expiration_date'],
                'link' => $http.'://'.$_SERVER['SERVER_NAME'].$request->base.'/businesses/dashboard/feature/'. $item['Business']['id'],
                'business_name' => $item['Business']['name'],
            );
        }
        if($item['BusinessPaid']['pay_type'] == 'business_package') {
            $package = $item['BusinessPackage'];
            $transaction = $item['BusinessTransaction'];
            if ($transaction) {
                $gateway = $item['Gateway']['plugin'];
                $helper = MooCore::getInstance()->getHelper($gateway . '_' . $gateway);
                if (!$expire) {
                    if (($this->isRecurring($item) && $item['BusinessPaid']['status'] == 'active')) {
                        if ($item['BusinessPaid']['end_date']) {
                            $time_end = strtotime($item['BusinessPaid']['end_date']);
                        }
                        $time_end = time() + 1000;
                        if (method_exists($helper, 'expire') && $time_end > time()) {
                            $result = $helper->expire('Business_Business_Paid', $item['BusinessPaid']['id'], json_decode($transaction['callback_params'], true));
                            if ($result) {
                                return;
                            }
                        }
                    }
                }

                if (method_exists($helper, 'cancelExpire')) {
                    $helper->cancelExpire(json_decode($transaction['callback_params'], true));
                }
            }
            $mail_type = 'business_expire';
            $params = array(
                'package_title' => $package['name'],
                'expire_time' => $item['BusinessPaid']['expiration_date'],
                'link' => $http . '://' . $_SERVER['SERVER_NAME'] . $request->base . '/businesses/dashboard/upgrade/'. $item['Business']['id'],
                'business_name' => $item['Business']['name'],
            );
        }
        $mPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $mPaid->id = $item['BusinessPaid']['id'];
        $mPaid->save(array('status' => 'expired', 'active' => 0));

        
        // downgrade function
        $this->onDowngrade($item);
        
        //Send email
        $mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
        $mailComponent->send($item['User']['email'], $mail_type, $params);
    }
    public function onDowngrade($item) {
        $mBusiness = MooCore::getInstance()->getModel('Business_Business');
        return $mBusiness->onDowngrade($item['Business']['id']);
    }
    public function onSuccessful($item, $data = array(), $price = 0, $txn = '', $recurring = false, $admin = 0) {
        $paid = $item['BusinessPaid'];
        if($paid['pay_type'] == 'featured_package')  {
        	return $this->onFeaturedSuccessful($item, $data, $price, $txn, $admin);
        }else{
        	return $this->onPackageSuccessful($item, $data, $price, $txn, $recurring, $admin);
        }
        
    }
    public function onFeaturedSuccessful($item, $data = array(), $price = 0, $txn = '', $admin = 0){
        $paid = $item['BusinessPaid'];
        $mPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $mTransaction = MooCore::getInstance()->getModel('Business.BusinessTransaction');
        /*if($mTransaction->hasAny(array(
            'BusinessTransaction.business_paid_id' => $paid['id']
        )))
        {
            return;
        }*/
        
        // get expired time 
        list($expire_time, $end_time) = $mPaid->getFeaturedExpiredActivePaid($paid['business_id']);
        $expire = $this->getFeaturedTimeExpire($paid, $expire_time);
        // active only recently paid
        $mPaid->updateAll(  array('BusinessPaid.active' => 0),
                            array(  'BusinessPaid.pay_type' => $paid['pay_type'], 
                                    'BusinessPaid.business_id' => $paid['business_id'],
                                    'BusinessPaid.id <>' => $paid['id']));
        //Update subscribe
        $mPaid->id = $paid['id'];
        $data_sub = array(
            'status' => 'active',
            'active' => 1,
            'expiration_date' => $expire,
            'pay_date' => date('Y-m-d H:i:s'),
            'is_warning_email_sent' => 0,
        );
        $data_sub['reminder_date'] = $this->calculateReminderDate('day', 1, $expire);
        $data_sub['end_date'] = $this->calculateFeaturedEndDate($paid, $end_time);

        //Insert tranaction
        $data = array(
            'user_id' => $paid['user_id'],
            'business_id' => $paid['business_id'],
            'business_package_id' => $paid['business_package_id'],
            'business_paid_id' => $paid['id'],
            'pay_type' => $paid['pay_type'],
            'status' => 'completed',
            'amount' => $price,
            'currency' => $paid['currency_code'],
            'callback_params' => json_encode($data),
            'gateway_id' => $paid['gateway_id'],
            'admin' => $admin,
        	'txn' => $txn
        );
        $mTransaction->clear();
        $mTransaction->save($data);

        $data_sub['business_transaction_id'] = $mTransaction->id;
        $mPaid->save($data_sub);
        $mBusiness->afterPaymentComplete($paid);
    }
    public function onPackageSuccessful($item, $data = array(), $price = 0, $txn = '', $recurring = false, $admin = 0){
        $package = $item['BusinessPackage'];
        $paid = $item['BusinessPaid'];
        $mPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $mTransaction = MooCore::getInstance()->getModel('Business.BusinessTransaction');
        /*if($mTransaction->hasAny(array(
            'BusinessTransaction.business_paid_id' => $paid['id']
        )))
        {
            return;
        }*/
        $expire = $this->getTimeExpire($package, $paid);
         // active only recently paid
        $mPaid->updateAll(  array('BusinessPaid.active' => 0),
                            array(  'BusinessPaid.pay_type' => $paid['pay_type'], 
                                    'BusinessPaid.business_id' => $paid['business_id'],
                                    'BusinessPaid.id <>' => $paid['id']));
        //Update subscribe
        $mPaid->id = $paid['id'];
        $data_sub = array(
            'status' => 'active',
            'active' => 1,
            'expiration_date' => $expire,
            'pay_date' => date('Y-m-d H:i:s'),
            'is_warning_email_sent' => 0,
        );

        if ($package['expiration_reminder']) {
            $data_sub['reminder_date'] = $this->calculateReminderDate($package['expiration_reminder_type'], $package['expiration_reminder'], $expire);
        }

        $data_sub['end_date'] = $this->calculateEndDate($package);

        //Insert tranaction
        $data = array(
            'user_id' => $paid['user_id'],
            'business_id' => $paid['business_id'],
            'business_package_id' => $paid['business_package_id'],
            'business_paid_id' => $paid['id'],
            'pay_type' => $paid['pay_type'],
            'status' => 'completed',
            'amount' => $price,
            'currency' => $paid['currency_code'],
            'callback_params' => json_encode($data),
            'gateway_id' => $paid['gateway_id'],
            'admin' => $admin,
        	'txn' => $txn
        );
        $mTransaction->clear();
        $mTransaction->save($data);

        $data_sub['business_transaction_id'] = $mTransaction->id;
        $mPaid->save($data_sub);
		
		$ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        $mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
        $request = Router::getRequest();
		
        $params = array(
            'link' => $http . '://' . $_SERVER['SERVER_NAME'] . $request->base . '/businesses/dashboard/edit/'. $paid['business_id'],
        );
        $mailComponent->send($item['User']['email'], 'business_success_package', $params);
        $mBusiness->afterPaymentComplete($paid);
		
    }
    
    public function getTimeExpire($package, $paid) {
        $expire = '';
        switch ($package['type']) {
            case BUSINESS_ONE_TIME:
                $expire = $this->calculateExpirationDate($package['duration_type'], $package['duration']);
                break;
            case BUSINESS_RECURRING:
                $expire = $this->calculateExpirationDate($package['billing_cycle_type'], $package['billing_cycle']);
                break;
        }

        return $expire;
    }
    public function getFeaturedTimeExpire($paid, $expire_time) {
        if(strtotime($expire_time) <= strtotime(date('Y-m-d H:i:s'))) {
            $expiration_date = strtotime("+".$paid['feature_day']." days");
        }else{
            $expiration_date = strtotime("+".$paid['feature_day']." days", strtotime($expire_time));
        }
        return date("Y-m-d H:i:s", $expiration_date);
    }
    public function calculateFeaturedEndDate($paid, $end_time) {
        if(strtotime($end_time) <= strtotime(date('Y-m-d H:i:s'))) {
            $end_date = strtotime("+".$paid['feature_day']." days");
        }else{
            $end_date = strtotime("+".$paid['feature_day']." days", strtotime($end_time));
        }
        return date("Y-m-d H:i:s", $end_date);
    }
    public function calculateReminderDate($type, $duration, $expire) {
        if (!$expire)
            return '';

        $reminder_date = '';
        switch ($type) {
            case 'day':
                $reminder_date = strtotime("-$duration Day", strtotime($expire));
                break;
            case 'week':
                $reminder_date = strtotime("-$duration Week", strtotime($expire));
                break;
            case 'month':
                $reminder_date = strtotime("-$duration Month", strtotime($expire));
                break;
            case 'year':
                $reminder_date = strtotime("-$duration Year", strtotime($expire));
                break;
        }
        return $reminder_date != '' ? date('Y-m-d H:i:s', $reminder_date) : '';
    }

    public function calculateExpirationDate($type, $duration) {
        $expiration_date = '';
        switch ($type) {
            case 'day':
                $expiration_date = strtotime("+$duration Day");
                break;
            case 'week':
                $expiration_date = strtotime("+$duration Week");
                break;
            case 'month':
                $expiration_date = strtotime("+$duration Month");
                break;
            case 'year':
                $expiration_date = strtotime("+$duration Year");
                break;
            case 'forever':
                $expiration_date = '';
        }
        return $expiration_date != '' ? date('Y-m-d H:i:s', $expiration_date) : '';
    }

    public function onCancel($item) {
        $mTransaction = MooCore::getInstance()->getModel('Business.BusinessTransaction');
        $transaction = $item['BusinessTransaction'];
        if ($transaction) {
            $gateway = $item['Gateway']['plugin'];
            $helper_gateway = MooCore::getInstance()->getHelper($gateway . '_' . $gateway);
            if ($helper_gateway && method_exists($helper_gateway, 'cancelRecurring')) {
                $result_cancel = $helper_gateway->cancelRecurring(json_decode($transaction['callback_params'], true));
                if (!$result_cancel) {
                    return false;
                }
            }
        }
        $mPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $mPaid->id = $item['BusinessPaid']['id'];
        $mPaid->save(array('status' => 'cancel'));

        return true;
    }

    public function inActiveAll($package_id, $business_id, $pay_type) {
        $mPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $conditions = array(
            'BusinessPaid.active' => 1,
            'BusinessPaid.business_id' => $business_id,
            'BusinessPaid.business_package_id' => $package_id,
            'BusinessPaid.pay_type' => $pay_type
        );
        
        $list = $mPaid->find('all', array(
            'conditions' => $conditions
        ));

        foreach ($list as $item) {
            $this->onInActive($item);
        }
    }

    public function onInActive($item) {
        $mPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $mPaid->id = $item['BusinessPaid']['id'];
        $mPaid->save(array('status' => 'inactive', 'active' => 0));
        $transaction = $item['BusinessTransaction'];
        if ($transaction) {
            $gateway = $item['Gateway']['plugin'];
            $helper = MooCore::getInstance()->getHelper($gateway . '_' . $gateway);
            if ($helper && method_exists($helper, 'cancel')) {
                $helper->cancel(json_decode($transaction['callback_params'], true));
            }
        }
    }

    public function canActive($item) {
        if ($item['BusinessPaid']['status'] == 'active' || $item['BusinessPaid']['status'] == 'cancel') {
            return false;
        }
        return true;
    }

    public function canCancel($item) {
        if ($item['BusinessPaid']['status'] != 'active') {
            return false;
        }

        $transaction = $item['BusinessTransaction'];

        if (!$this->isRecurring($item)) {
            return false;
        }

        if (!$transaction) {
            return false;
        }
        $params = json_decode($transaction['callback_params'], true);
        if (!count($params)) {
            return false;
        }

        return true;
    }

    public function getTextStatus($item) {
        switch ($item['BusinessPaid']['status']) {
            case 'initial': return __d('business', 'Initial');
            case 'active': return __d('business', 'Active');
            case 'pending': return __d('business', 'Pending');
            case 'reversed': return __d('business', 'Reversed');
            case 'expired': return __d('business', 'Expired');
            case 'refunded': return __d('business', 'Refunded');
            case 'failed': return __d('business', 'Failed');
            case 'free': return __d('business', 'Free');
            case 'cancel': return __d('business', 'Cancel');
            case 'process' : return __d('business', 'Process');
            case 'inactive' : return __d('business', 'Inactive');
        }
    }

    public function getListStatus($type) {
        switch ($type) {
            case 'BusinessPaid':
                return array(
                    'initial' => __d('business', 'Initial'),
                    'active' => __d('business', 'Active'),
                    'pending' => __d('business', 'Pending'),
                    'expired' => __d('business', 'Expired'),
                    'refunded' => __d('business', 'Refunded'),
                    'failed' => __d('business', 'Failed'),
                    'cancel' => __d('business', 'Cancel Recurring'),
                    'process' => __d('business', 'Process'),
                    'inactive' => __d('business', 'Inactive'),
                );
                break;
            case 'BusinessTransaction':
                return array(
                    'completed' => __d('business', 'Paid'),
                    'failed' => __d('business', 'Failed'),
                    'pending' => __d('business', 'Pending'),
                );
                break;
        }
    }
    
    public function getTextStatusTransaction($item) {
        switch ($item['BusinessTransaction']['status']) {
            case 'initial': return __d('business', 'Initial');
            case 'pending': return __d('business', 'Pending');
            case 'expired': return __d('business', 'Expired');
            case 'refunded': return __d('business', 'Refunded');
            case 'failed': return __d('business', 'Failed');
            case 'cancel': return __d('business', 'Cancel');
            case 'inactive': return __d('business', 'Inactive');
            case 'completed': return __d('business', 'Paid');
        }
    }
    public function getExpiredTime($business_id) {
        $mBusiness = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $paid = $mBusiness->find('first', array('conditions' => array(
                                                                        'BusinessPaid.business_id' => $business_id,
                                                                        'BusinessPaid.pay_type' => 'featured_package',
                                                                        'BusinessPaid.active' => 1,
                                                                        'BusinessPaid.status' => 'active',
                                                                    )));
        if(!empty($paid)) {
            return $paid['BusinessPaid']['expiration_date'];
        }
        return false ;
    }

    public function getAdminList($business)
	{
		return array($business['Business']['user_id']);
	}

    public function getTimezoneOffset($remote_tz) 
    {
        if($remote_tz != null)
        {
            $tz=timezone_open($remote_tz);
            $dateTimeOslo=date_create("now",timezone_open("UTC"));
            $offset = timezone_offset_get($tz,$dateTimeOslo) / 3600;
            if($offset > 0)
            {
                return '+'.$offset;
            }
            return $offset;
        }
        return '';
    }
    public function getTextPayType($item) {
         switch ($item['BusinessTransaction']['pay_type']) {
            case 'featured_package': return __d('business', 'Featured Day');
            case 'business_package': return __d('business', 'Business Package');
        }
    }
    
    public function checkPostStatus($blog,$uid)
	{
        return true;
	}
	
	public function getEnable()
	{
		return Configure::check('Business.business_enabled') ? Configure::read('Business.business_enabled') : 0;
	}
	
	public function checkSeeComment($blog,$uid)
	{
		/*if ($blog['Blog']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		return $this->checkPostStatus($blog,$uid);*/
        return true;
	}
	
	public function getTagUnionsBlog($blogids)
	{
		return "SELECT i.id, i.title, i.body, i.like_count, i.created, 'Blog_Blog' as moo_type, i.privacy, i.user_id
						 FROM " . Configure::read('core.prefix') . "blogs i
						 WHERE i.id IN (" . implode(',', $blogids) . ")";
	}
    
    public function isAllowModule($business_id, $module)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->isAllowModule($business_id, $module);
    }
    
    public function isFavourited($business_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->isFavourited(MooCore::getInstance()->getViewer(true), $business_id);
    }
    
    public function getCountryList()
    {
        return array(
            'Afghanistan' => 'Afghanistan',
            'Albania' => 'Albania',
            'Algeria' => 'Algeria',
            'American Samoa' => 'American Samoa',
            'Andorra' => 'Andorra',
            'Angola' => 'Angola',
            'Anguilla' => 'Anguilla',
            'Antarctica' => 'Antarctica',
            'Antigua and Barbuda' => 'Antigua and Barbuda',
            'Argentina' => 'Argentina',
            'Armenia' => 'Armenia',
            'Aruba' => 'Aruba',
            'Australia' => 'Australia',
            'Austria' => 'Austria',
            'Azerbaijan' => 'Azerbaijan',
            'Bahamas' => 'Bahamas',
            'Bahrain' => 'Bahrain',
            'Bangladesh' => 'Bangladesh',
            'Barbados' => 'Barbados',
            'Belarus' => 'Belarus',
            'Belgium' => 'Belgium',
            'Belize' => 'Belize',
            'Benin' => 'Benin',
            'Bermuda' => 'Bermuda',
            'Bhutan' => 'Bhutan',
            'Bolivia' => 'Bolivia',
            'Bosnia and Herzegowina' => 'Bosnia and Herzegowina',
            'Botswana' => 'Botswana',
            'Bouvet Island' => 'Bouvet Island',
            'Brazil' => 'Brazil',
            'British Indian Ocean Territory' => 'British Indian Ocean Territory',
            'Brunei Darussalam' => 'Brunei Darussalam',
            'Bulgaria' => 'Bulgaria',
            'Burkina Faso' => 'Burkina Faso',
            'Burundi' => 'Burundi',
            'Cambodia' => 'Cambodia',
            'Cameroon' => 'Cameroon',
            'Canada' => 'Canada',
            'Cape Verde' => 'Cape Verde',
            'Cayman Islands' => 'Cayman Islands',
            'Central African Republic' => 'Central African Republic',
            'Chad' => 'Chad',
            'Chile' => 'Chile',
            'China' => 'China',
            'Christmas Island' => 'Christmas Island',
            'Cocos (Keeling) Islands' => 'Cocos (Keeling) Islands',
            'Colombia' => 'Colombia',
            'Comoros' => 'Comoros',
            'Congo' => 'Congo',
            'Congo, the Democratic Republic of the' => 'Congo, the Democratic Republic of the',
            'Cook Islands' => 'Cook Islands',
            'Costa Rica' => 'Costa Rica',
            'Cote d&#39;Ivoire' => 'Cote d&#39;Ivoire',
            'Croatia (Hrvatska)' => 'Croatia (Hrvatska)',
            'Cuba' => 'Cuba',
            'Cyprus' => 'Cyprus',
            'Czech Republic' => 'Czech Republic',
            'Denmark' => 'Denmark',
            'Djibouti' => 'Djibouti',
            'Dominica' => 'Dominica',
            'Dominican Republic' => 'Dominican Republic',
            'East Timor' => 'East Timor',
            'Ecuador' => 'Ecuador',
            'Egypt' => 'Egypt',
            'El Salvador' => 'El Salvador',
            'Equatorial Guinea' => 'Equatorial Guinea',
            'Eritrea' => 'Eritrea',
            'Estonia' => 'Estonia',
            'Ethiopia' => 'Ethiopia',
            'Falkland Islands (Malvinas)' => 'Falkland Islands (Malvinas)',
            'Faroe Islands' => 'Faroe Islands',
            'Fiji' => 'Fiji',
            'Finland' => 'Finland',
            'France' => 'France',
            'France Metropolitan' => 'France Metropolitan',
            'French Guiana' => 'French Guiana',
            'French Polynesia' => 'French Polynesia',
            'French Southern Territories' => 'French Southern Territories',
            'Gabon' => 'Gabon',
            'Gambia' => 'Gambia',
            'Georgia' => 'Georgia',
            'Germany' => 'Germany',
            'Ghana' => 'Ghana',
            'Gibraltar' => 'Gibraltar',
            'Greece' => 'Greece',
            'Greenland' => 'Greenland',
            'Grenada' => 'Grenada',
            'Guadeloupe' => 'Guadeloupe',
            'Guam' => 'Guam',
            'Guatemala' => 'Guatemala',
            'Guinea' => 'Guinea',
            'Guinea-Bissau' => 'Guinea-Bissau',
            'Guyana' => 'Guyana',
            'Haiti' => 'Haiti',
            'Heard and Mc Donald Islands' => 'Heard and Mc Donald Islands',
            'Holy See (Vatican City State)' => 'Holy See (Vatican City State)',
            'Honduras' => 'Honduras',
            'Hong Kong' => 'Hong Kong',
            'Hungary' => 'Hungary',
            'Iceland' => 'Iceland',
            'India' => 'India',
            'Indonesia' => 'Indonesia',
            'Iran (Islamic Republic of)' => 'Iran (Islamic Republic of)',
            'Iraq' => 'Iraq',
            'Ireland' => 'Ireland',
            'Israel' => 'Israel',
            'Italy' => 'Italy',
            'Jamaica' => 'Jamaica',
            'Japan' => 'Japan',
            'Jordan' => 'Jordan',
            'Kazakhstan' => 'Kazakhstan',
            'Kenya' => 'Kenya',
            'Kiribati' => 'Kiribati',
            'Korea, Democratic People&#39;s Republic of' => 'Korea, Democratic People&#39;s Republic of',
            'Korea, Republic of' => 'Korea, Republic of',
            'Kuwait' => 'Kuwait',
            'Kyrgyzstan' => 'Kyrgyzstan',
            'Lao' => 'Lao',
            'Latvia' => 'Latvia',
            'Lebanon' => 'Lebanon',
            'Lesotho' => 'Lesotho',
            'Liberia' => 'Liberia',
            'Libyan Arab Jamahiriya' => 'Libyan Arab Jamahiriya',
            'Liechtenstein' => 'Liechtenstein',
            'Lithuania' => 'Lithuania',
            'Luxembourg' => 'Luxembourg',
            'Macau' => 'Macau',
            'Macedonia, The Former Yugoslav Republic of' => 'Macedonia, The Former Yugoslav Republic of',
            'Madagascar' => 'Madagascar',
            'Malawi' => 'Malawi',
            'Malaysia' => 'Malaysia',
            'Maldives' => 'Maldives',
            'Mali' => 'Mali',
            'Malta' => 'Malta',
            'Marshall Islands' => 'Marshall Islands',
            'Martinique' => 'Martinique',
            'Mauritania' => 'Mauritania',
            'Mauritius' => 'Mauritius',
            'Mayotte' => 'Mayotte',
            'Mexico' => 'Mexico',
            'Micronesia, Federated States of' => 'Micronesia, Federated States of',
            'Moldova, Republic of' => 'Moldova, Republic of',
            'Monaco' => 'Monaco',
            'Mongolia' => 'Mongolia',
            'Montserrat' => 'Montserrat',
            'Morocco' => 'Morocco',
            'Mozambique' => 'Mozambique',
            'Myanmar' => 'Myanmar',
            'Namibia' => 'Namibia',
            'Nauru' => 'Nauru',
            'Nepal' => 'Nepal',
            'Netherlands' => 'Netherlands',
            'Netherlands Antilles' => 'Netherlands Antilles',
            'New Caledonia' => 'New Caledonia',
            'New Zealand' => 'New Zealand',
            'Nicaragua' => 'Nicaragua',
            'Niger' => 'Niger',
            'Nigeria' => 'Nigeria',
            'Niue' => 'Niue',
            'Norfolk Island' => 'Norfolk Island',
            'Northern Mariana Islands' => 'Northern Mariana Islands',
            'Norway' => 'Norway',
            'Oman' => 'Oman',
            'Pakistan' => 'Pakistan',
            'Palau' => 'Palau',
            'Panama' => 'Panama',
            'Papua New Guinea' => 'Papua New Guinea',
            'Paraguay' => 'Paraguay',
            'Peru' => 'Peru',
            'Philippines' => 'Philippines',
            'Pitcairn' => 'Pitcairn',
            'Poland' => 'Poland',
            'Portugal' => 'Portugal',
            'Puerto Rico' => 'Puerto Rico',
            'Qatar' => 'Qatar',
            'Reunion' => 'Reunion',
            'Romania' => 'Romania',
            'Russian Federation' => 'Russian Federation',
            'Rwanda' => 'Rwanda',
            'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
            'Saint Lucia' => 'Saint Lucia',
            'Saint Vincent and the Grenadines' => 'Saint Vincent and the Grenadines',
            'Samoa' => 'Samoa',
            'San Marino' => 'San Marino',
            'Sao Tome and Principe' => 'Sao Tome and Principe',
            'Saudi Arabia' => 'Saudi Arabia',
            'Senegal' => 'Senegal',
            'Seychelles' => 'Seychelles',
            'Sierra Leone' => 'Sierra Leone',
            'Singapore' => 'Singapore',
            'Slovakia (Slovak Republic)' => 'Slovakia (Slovak Republic)',
            'Slovenia' => 'Slovenia',
            'Solomon Islands' => 'Solomon Islands',
            'Somalia' => 'Somalia',
            'South Africa' => 'South Africa',
            'South Georgia and the South Sandwich Islands' => 'South Georgia and the South Sandwich Islands',
            'Spain' => 'Spain',
            'Sri Lanka' => 'Sri Lanka',
            'St. Helena' => 'St. Helena',
            'St. Pierre and Miquelon' => 'St. Pierre and Miquelon',
            'Sudan' => 'Sudan',
            'Suriname' => 'Suriname',
            'Svalbard and Jan Mayen Islands' => 'Svalbard and Jan Mayen Islands',
            'Swaziland' => 'Swaziland',
            'Sweden' => 'Sweden',
            'Switzerland' => 'Switzerland',
            'Syrian Arab Republic' => 'Syrian Arab Republic',
            'Taiwan, Province of China' => 'Taiwan, Province of China',
            'Tajikistan' => 'Tajikistan',
            'Tanzania, United Republic of' => 'Tanzania, United Republic of',
            'Thailand' => 'Thailand',
            'Togo' => 'Togo',
            'Tokelau' => 'Tokelau',
            'Tonga' => 'Tonga',
            'Trinidad and Tobago' => 'Trinidad and Tobago',
            'Tunisia' => 'Tunisia',
            'Turkey' => 'Turkey',
            'Turkmenistan' => 'Turkmenistan',
            'Turks and Caicos Islands' => 'Turks and Caicos Islands',
            'Tuvalu' => 'Tuvalu',
            'Uganda' => 'Uganda',
            'Ukraine' => 'Ukraine',
            'United Arab Emirates' => 'United Arab Emirates',
            'United Kingdom' => 'United Kingdom',
            'United States' => 'United States',
            'United States Minor Outlying Islands' => 'United States Minor Outlying Islands',
            'Uruguay' => 'Uruguay',
            'Uzbekistan' => 'Uzbekistan',
            'Vanuatu' => 'Vanuatu',
            'Venezuela' => 'Venezuela',
            'Vietnam' => 'Vietnam',
            'Virgin Islands (British)' => 'Virgin Islands (British)',
            'Virgin Islands (U.S.)' => 'Virgin Islands (U.S.)',
            'Wallis and Futuna Islands' => 'Wallis and Futuna Islands',
            'Western Sahara' => 'Western Sahara',
            'Yemen' => 'Yemen',
            'Yugoslavia' => 'Yugoslavia',
            'Zambia' => 'Zambia',
            'Zimbabwe' => 'Zimbabwe'
        );
    }
    
    public function getBadgesIcon($icon)
    {
        return $this->request->base.'/business/images/medals/'.$icon;
    }
    
    public function hasBusinesses($user_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->hasBusinesses($user_id);
    }
    
    public function checkHasBusiness($user_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->checkHasBusiness($user_id);
    }
    
    public function checkHasApprovedBusiness($user_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->hasApprovedBusinesses($user_id);
    }
    public function getModuleText($model) {
        switch ($model) {
            case 'BusinessReview':
            $txt = __d('business', 'Rating & Reviews');
            break;
            case 'BusinessBranch':
            $txt = __d('business', 'Add business sub pages');
            break;
            default:
            $txt = '';
        }
        return $txt;
    }
    
    public function getDefaultLocationName()
    {
        $mBusinessLocation = MooCore::getInstance()->getModel("Business.BusinessLocation");
        return $mBusinessLocation->getDefaultLocationName();
    }
    
    public function viewMenuItemCounter()
    {
        //check has business
        $uid = MooCore::getInstance()->getViewer(true);
        if($uid > 0)
        {
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
            $subject = MooCore::getInstance()->getSubject();
            return array(
                'has_business' => $mBusiness->hasBusinesses($uid),
                'total_review' => $mBusinessReview->totalMyBusinessReview($uid)
            );
        }
    }

    public function getTextDurationId($text){
         switch ($text) {
            case 'day':
            $txt = 1 ;
            break;
            case 'week':
            $txt = 2 ;
            break;
            case 'month':
            $txt = 3;
            break;
            case 'year':
            $txt = 4;
            break;
            case 'forever':
            $txt = 5;
            break;
            default:
            $txt = 1;
        }
        return $txt;
    }
    public function getPackageTrial($business_id, $package_id){
        $mPackage = MooCore::getInstance()->getModel('Business.BusinessPackage');
        $trial_package = $mPackage->findByTrialAndEnable($package_id, 1);
        if(!empty($trial_package)) {
            $mPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
            $paid_id =  $mPaid->find('list', array('fields' => array('BusinessPaid.id') , 
                                                 'conditions' => array('BusinessPaid.business_id' => $business_id,
                                                                    'BusinessPaid.business_package_id' => $trial_package['BusinessPackage']['id'],
                                                                    'BusinessPaid.status' => array('active','inactive','expired'),
                                                                    'BusinessPaid.pay_type' => 'business_package')));
            if(!empty($paid_id)){
                return array();
            }
        }
        return $trial_package ;

    }
    
    public function getFullUrl($url){
        if(!is_numeric(strpos($url, 'http://')) && !is_numeric(strpos($url, 'https://'))){ 
            $url =  'http://' . $url;            
        }
       
        return $url;
    }
    
    public function getOnlyBusiness($business_id){
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->getOnlyBusiness($business_id);
    }
    
    public function getReviewDetail($business_review_id, $business_id = null)
    {
        $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
        return $mBusinessReview->getReviewDetail($business_review_id, $business_id);
    }
    
    public function getBusinessAlbumPhotos($business_id)
    {
        $mBusinessPhoto = MooCore::getInstance()->getModel('Business.BusinessPhoto');
        return $mBusinessPhoto->getBusinessAlbumPhotos($business_id);
    }
    
    public function areFriends($uid1, $uid2)
    {
        $mFriend = MooCore::getInstance()->getModel('Friend');
        return $mFriend->areFriends($uid1, $uid2);
    }
    
    public function existRequest($uid1, $uid2)
    {
        $mFriendRequest = MooCore::getInstance()->getModel('FriendRequest');
        return $mFriendRequest->existRequest($uid1, $uid2);
    }
    
    public function with($tagging_id = null, $ids=array(),$autoRender=true){
        if(!($ids =$this->_convertToArray($ids))) return false;
        $count = count($ids);
        $with1 = addslashes(__(' — with %s'));
        $with2 = addslashes(__(' — with %s and %s'));
        $with3 = addslashes(__(' — with %s and %d others.'));
        $with = "";
        switch($count){
            case 1:
                $with= sprintf($with1,$this->getName($ids[0]));
                break;
            case 2:
                $with = sprintf($with2,$this->getName($ids[0]),$this->getName($ids[1]));
                break;
            case 3:
            default:
            $with3a = explode('%d',$with3);
            $tooltipText = sprintf('%d'.$with3a[1],$count-1);
            $with3 = str_replace('%d'.$with3a[1],$this->tooltip($tagging_id, $ids,$tooltipText),$with3);

            $with = sprintf($with3,$this->getName($ids[0]));
        }
        if($autoRender) {
            echo $with;
            return true;
        }
        return $with;
    }
    
    private function tooltip($tagging_id, $ids=array(),$tooltipText){
        if(!($ids =$this->_convertToArray($ids))) return false;
        $this->initTooltipJs();
        $title = '';
        unset($ids[0]);
        foreach($ids as $id){
            $title .= $this->getName($id,true,true,true)."<br/>";
        }
        return '<a '.(!$this->is_app ? 'data-toggle="modal" data-target="#businessModal"' : "").' href="' . Router::url(array('controller'=>'users', 'action'=>'tagging', 'tagging_id' => $tagging_id, 'plugin' => false)) .'">' . '<span class="tip" original-title="'.$title.'"><b>' . $tooltipText . '</b></span>' . '</a>';
    }
    
    private function initTooltipJs(){
        if($this->isLoadedInitJs) return true;
        $this->isLoadedTooltipJs = true;
        $this->_View->addInitJs('$(function(){$(\'[data-toggle="tooltip"]\').tooltip()});');
    }
    
    private function _convertToArray($data){
        if(empty($data)) return false;
        if(!is_array($data)){
            return explode(',',$data);
        }else{
            return $data;
        }
        return false;
    }
    
    private function getName($data, $bold = true ,$idOnly=true,$textOnly=false) {
        if($idOnly){
            $mUser = MooCore::getInstance()->getModel('User');
            $user = $mUser->findById($data);

        }else{
            $user = $data;
        }

        if (!empty($user)) {
            $name = $user['User']['name'];
            if($textOnly)
                return $name;
            $url = $user['User']['moo_href'];

            $class = '';
            $moo_type = isset($user['User']['moo_type'])?$user['User']['moo_type']:'';
            if($moo_type == 'User' && !isset($user['User']['no_tooltip'])){
                $show_popup = MooCore::getInstance()->checkViewPermission($user['User']['privacy'],$user['User']['id']);
                if($show_popup){
                    $class = 'moocore_tooltip_link';
                }
            }
            
            if ($bold)
                return '<a  href="' . $url . '" class="' . $class . '" data-item_type="' . strtolower($moo_type) . '" data-item_id="' . $user['User']['id'] . '"><b>' . $name . '</b></a>';
            else
                return '<a  href="' . $url . '" class="' .  $class  . '" data-item_type="' . strtolower($moo_type) . '" data-item_id="' . $user['User']['id'] . '">' . $name . '</a>';
        }
    }
    
    public function viewMore($string, $moreLength = null, $maxLength = null,  $lessLength = null, $nl2br = true, $options = array())
  	{
	  	if( !is_numeric($moreLength) || $moreLength <= 0 ) {
			$moreLength = 500;
	    }
	    if( !is_numeric($maxLength) || $maxLength <= 0 ) {
			$maxLength = 1027;
	    }
	    if( !is_numeric($lessLength) || $lessLength <= 0 ) {
			$lessLength = 511;
	    }
        //$string = preg_replace('/<script\b[^>]*>(.*?)<\/script>/i', "", $string);

	    $string = preg_replace('/(\r?\n){2,}/', "\n\n", $string);

        //$string = $this->Text->autoLink($string, array_merge(array('target' => '_blank', 'rel' => 'nofollow', 'escape' => false),$options));
        //$string = $this->Moo->parseSmilies($string);

	    $shortText = $this->truncateHtml($string,$moreLength,'');
	    $fullText = $string;
	    
            // MOOSOCIAL-1588
            if ($shortText == $fullText){
                // limit to 10 lines
                $limit_lines = 10;
                if (count(explode("\n", $fullText)) > $limit_lines){
                    $shortText = '';
                    $arr = array_slice(explode("\n", $fullText), 0, 10);
                    foreach ($arr as $line){
                        $shortText .= $line;
                    }
                }
            }
	    
            if (strlen($fullText) <= strlen($shortText))
	    	return nl2br($fullText);
	    // Do nl2br
	    if( $nl2br ) {
	      $shortText = nl2br($shortText);
	      $fullText = nl2br($fullText);
	    }
	    
	    $tag = 'span';
	    $strLen = strlen($string);
	
	    $content = '<'
	      . $tag
	      . ' class="view_more"'
	      . '>'
	      . $shortText
	      . __('... &nbsp;')
	      . '<a class="view_more_link" href="javascript:void(0);" onclick="$(this).parent().next().show();$(this).parent().hide();">'.__('more').'</a>'
	      . '</'
	      . $tag
	      . '>'
	      . '<'
	      . $tag
	      . ' class="view_more"'
	      . ' style="display:none;"'
	      . '>'
	      . $fullText
	      . ' &nbsp;'
	      ;
	    $content .= '</'
	      . $tag
	      . '>'
	      ;

	    return $content;
  	}
    
    function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
						unset($open_tags[$pos]);
						}
					// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
    
    public function parseDayName($val)
    {
        switch ($val)
        {
            case 'monday':
                $val = __d('business', 'Mon');
                break;
            case 'tuesday':
                $val = __d('business', 'Tue');
                break;
            case 'wednesday':
                $val = __d('business', 'Wed');
                break;
            case 'thursday':
                $val = __d('business', 'Thu');
                break;
            case 'friday':
                $val = __d('business', 'Fri');
                break;
            case 'saturday':
                $val = __d('business', 'Sat');
                break;
            case 'sunday':
                $val = __d('business', 'Sun');
                break;
        }
        return $val;
    }
    
    public function getImage($path)
    {
        return $this->Storage->getImage($path);
    }
    
    public function defaultCoverUrl(){
        return $this->Storage->getImage('business/images/cover.png');
    }
    
    public function hasStore($business_id){
        $mBusinessStore = MooCore::getInstance()->getModel('Business.BusinessStore');
        return $mBusinessStore->hasStore($business_id);
    }
}
