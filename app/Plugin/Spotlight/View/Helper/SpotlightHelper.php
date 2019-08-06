<?php

App::uses('AppHelper', 'View/Helper');

class SpotlightHelper extends AppHelper {

    public function getEnable() {
        return Configure::read('Spotlight.spotlight_enabled');
    }

    public function getParamsPayment($item) {
        $spotlightTransaction = $item['SpotlightTransaction'];

        $url = Router::url('/', true);
        $first_amount = 0;
        $currency = Configure::read('Config.currency');
        $params = array(
            'cancel_url' => $url . 'spotlights/cancel',
            'return_url' => $url . 'spotlights/success',
            'currency' => $currency['Currency']['currency_code'],
            'description' => __d('spotlight',"Payment spotlight"),
            'type' => 'Spotlight_Spotlight_Transaction',
            'id' => $spotlightTransaction['id'],
            'amount' => Configure::read('Spotlight.spotlight_price'),
        );
        return $params;
    }

    public function getListStatus($type) {
        switch ($type) {
            case 'Subscribe':
                return array(
                    'initial' => __('Initial'),
                    'active' => __('Active'),
                    'pending' => __('Pending'),
                    'expired' => __('Expired'),
                    'refunded' => __('Refunded'),
                    'failed' => __('Failed'),
                    'cancel' => __('Cancel Recurring'),
                    'process' => __('Process'),
                    'inactive' => __('Inactive'),
                );
                break;
            case 'SpotlightTransaction':
                return array(
                    'completed' => __('Paid'),
                    'failed' => __('Failed'),
                    'pending' => __('Pending'),
                );
                break;
            case 'SubscriptionRefund':
                return array(
                    'initial' => __('Waiting'),
                    'denied' => __('Denied'),
                    'completed' => __('Refuned'),
                    'process' => __('Process'),
                    'failed' => __('Failed')
                );
                break;
        }
    }

    public function getTextStatusTransaction($item) {
        switch ($item['SpotlightTransaction']['status']) {
            case 'initial': return __('Initial');
            case 'pending': return __('Pending');
            case 'expired': return __('Expired');
            case 'refunded': return __('Refunded');
            case 'failed': return __('Failed');
            case 'cancel': return __('Cancel');
            case 'inactive': return __('Inactive');
            case 'completed': return __('Paid');
        }
    }

    public function onFailure($item, $data) {
        $spotUserModel = MooCore::getInstance()->getModel('Spotlight.SpotlightUser');
        $transactionModel = MooCore::getInstance()->getModel('Spotlight.SpotlightTransaction');

        $data = array(
            'status' => 'failed'
        );
        $transactionModel->id = $item['SpotlightTransaction']['id'];
        $transactionModel->save($data);
    }

    public function onSuccessful($item, $data = array(), $price = 0, $recurring = false, $admin = 0) {
        $spotlightTransaction = $item['SpotlightTransaction'];
        $spotUserModel = MooCore::getInstance()->getModel('Spotlight.SpotlightUser');
        $transactionModel = MooCore::getInstance()->getModel('Spotlight.SpotlightTransaction');
        $period =  Configure::read('Spotlight.spotlight_period');
        //Update Spotlight User
        $checkUser = $spotUserModel->findByUserId($spotlightTransaction['user_id']);
        if(empty($checkUser)){
            $data_SpotlightUser = array(
                'user_id' => $spotlightTransaction['user_id'],
                'status' => 'active',
                'active' => 1,
                'created' => date("Y-m-d H:i:s"),
                'end_date' => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"))
            );
            $spotUserModel->clear();
            $spotUserModel->save($data_SpotlightUser);
            $spotlightId = $spotUserModel->id;

        }else{
            $data = array(
                'status' => 'active',
                'active' => 1,
                'created' => date("Y-m-d H:i:s"),
                'end_date' => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"))
            );
            $spotUserModel->id = $spotlightId = $checkUser['SpotlightUser']['id'];
            $spotUserModel->save($data);
        }

        //Update SpotlightTranaction
        $data = array(
            'spotlight_user_id' => $spotlightId,
            'status' => 'completed'
        );
        $transactionModel->id = $spotlightTransaction['id'];
        $transactionModel->save($data);

        $notificationModel = MooCore::getInstance()->getModel('Notification');
        $notificationModel->clear();
        $notificationModel->save(array(
            'user_id' => $spotlightTransaction['user_id'],
            'sender_id' => $spotlightTransaction['user_id'],
            'action' => 'join_spotlight',
            'params' => json_encode(array('period' => intval($period))),
            'url' => '/home',
            'plugin' => 'Spotlight'
        ));

    }

    public function getCurrencySymbol($curency_code) {
        $mcurrency = MooCore::getInstance()->getModel('Currency');
        $row = $mcurrency->findByCurrencyCode($curency_code);
        if (isset($row['Currency']['symbol'])) {
            return $row['Currency']['symbol'];
        }
        return $curency_code;
    }

    public $helpers = array('Time', 'Text', 'Html');
    public function getItemPhoto($obj, $options_link = array(), $options_image = array(),$linkOnly=false) {
        if (empty($obj)) {
            return null;
        }
        $prefix = '';
        $thumbUrl = null;
        if (isset($options_link['prefix'])) {
            $prefix = $options_link['prefix'] . '_';
        }

        $field = $obj[key($obj)]['moo_thumb'];
        $thumb = $obj[key($obj)][$field];
        if ($thumb) {
            $thumbUrl = FULL_BASE_URL . $this->request->webroot . 'uploads/' . strtolower(Inflector::pluralize(key($obj))) . '/' . $field . '/' . $obj[key($obj)]['id'] . '/' . $prefix . $thumb;
        } else {
            if (key($obj) == 'User') {
                if ($options_link['prefix'] == '50_square') {
                    $thumbUrl = FULL_BASE_URL . $this->request->webroot . strtolower(key($obj)) . '/img/noimage/' . $obj[key($obj)]['gender'] . '-' . strtolower(key($obj)) . '-sm.png';
                } else {
                    $thumbUrl = FULL_BASE_URL . $this->request->webroot . strtolower(key($obj)) . '/img/noimage/' . $obj[key($obj)]['gender'] . '-' . strtolower(key($obj)) . '.png';
                }
            } else {
                $thumbUrl = FULL_BASE_URL . $this->request->webroot . strtolower(key($obj)) . '/img/noimage/' . strtolower(key($obj)) . '.png';
            }
        }
        if($linkOnly) return $thumbUrl;

        $text_show = $obj[key($obj)]['moo_title'];

        if( isset($obj[key($obj)]['gender']) && !empty($obj[key($obj)]['gender']) ) {
            $text_show .= ', '.__($obj[key($obj)]['gender']);
        }
        /*if( isset($obj[key($obj)]['birthday']) && !empty($obj[key($obj)]['birthday']) ) {
            $age = $this->getAge($obj[key($obj)]['birthday']);
            if( $age > 0 ) {
                $text_show .= ', '.$this->getAge($obj[key($obj)]['birthday']);
            }
        }*/
        return $this->Html->link($this->Html->image($thumbUrl, array_merge($options_image, array('alt' => h($obj[key($obj)]['moo_title']) , 'title' => h($text_show) ) ) ), FULL_BASE_URL . $obj[key($obj)]['moo_href'], array_merge($options_link, array('escape' => false)));
    }

    /*function getAge($birthdate = '0000-00-00') {
        if ($birthdate == '0000-00-00') return 0;
        $bits = explode('-', $birthdate);
        $age = date('Y') - $bits[0] - 1;
        $arr[1] = 'm';
        $arr[2] = 'd';
        for ($i = 1; $arr[$i]; $i++) {
            $n = date($arr[$i]);
            if ($n < $bits[$i])
                break;
            if ($n > $bits[$i]) {
                ++$age;
                break;
            }
        }
        return $age;
    }*/

}
