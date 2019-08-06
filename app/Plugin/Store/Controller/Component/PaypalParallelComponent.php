<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
//App::import('PaypalAdaptive.Lib/vendor','autoload');
require_once APP . "Plugin/PaypalAdaptive/Lib/vendor/autoload.php";

use PayPal\Service\AdaptivePaymentsService;
use PayPal\Types\AP\PayRequest;
use PayPal\Types\Common\RequestEnvelope;
use PayPal\Types\AP\Receiver;
use PayPal\Types\AP\PaymentDetailsRequest;
use PayPal\Types\AP\ReceiverList;
use PayPal\IPN\PPIPNMessage;
use PayPal\Types\AP\PreapprovalRequest;
use PayPal\Types\AP\CancelPreapprovalRequest;
use PayPal\Types\AP\RefundRequest;
use PayPal\Types\AP\SetPaymentOptionsRequest;
use PayPal\Types\AP\SenderOptions;
use PayPal\Types\AP\ExecutePaymentRequest;
use PayPal\Auth\Openid\PPOpenIdUserinfo;

App::uses('Component', 'Controller');

class PaypalParallelComponent extends Component {

    public $components = array('Session');

    public function __construct(\ComponentCollection $collection, $settings = array()) {
        parent::__construct($collection, $settings);
        $this->PayPalConfig = array();
        $this->currency = $this->getDefaultCurrency();
        $this->_bn_code = 'SocialLOFT_SP';
        $this->paypal_site_email = '';
        $mGateway = MooCore::getInstance()->getModel('PaymentGateway.Gateway');
        $gateway = $mGateway->findByPlugin('PaypalAdaptive');
        $this->test_mode = "sandbox";
        if (!empty($gateway['Gateway']['config'])) {
            $config = json_decode($gateway['Gateway']['config'], true);
            $this->PayPalConfig = array(
                'mode' => $gateway['Gateway']['test_mode'] ? "sandbox" : "live",
                'email' => $config['email'],
                'acct1.AppId' => $config['appid'],
                'acct1.UserName' => $config['username'],
                'acct1.Password' => $config['password'],
                'acct1.Signature' => $config['signature']
            );
            $this->test_mode = $gateway['Gateway']['test_mode'];
            $this->paypal_site_email = $config['email'];

            if ($this->PayPalConfig['mode'] == 'sandbox') {
                $this->_paypal_link = 'https://www.sandbox.paypal.com';
                $this->_svcs_link = 'https://svcs.sandbox.paypal.com';
            } else {
                $this->_paypal_link = 'https://www.paypal.com';
                $this->_svcs_link = 'https://svcs.paypal.com';
            }
        }
    }

    public function getDefaultCurrency() {
        $mCurrency = MooCore::getInstance()->getModel('Currency');
        $currency = $mCurrency->findByIsDefault(1);
        if ($currency != null) {
            return $currency['Currency']['currency_code'];
        }
        return '';
    }

    public function checkout($data) {
        $this->writeLog('Start new transaction');
        $payPal = new AdaptivePaymentsService($this->PayPalConfig);
        $payRequest = new PayRequest();
        $requestEnvelope = new RequestEnvelope(Configure::read('Config.language'));
        $receivers = array();
        if (!empty($data['receivers'])) {
            foreach ($data['receivers'] as $k => $item) {
                $receivers[$k] = new Receiver();
                $receivers[$k]->amount = $item['amount'];
                $receivers[$k]->email = $item['email'];
            }
        }
        $receiverList = new ReceiverList($receivers);

        $payRequest->requestEnvelope = $requestEnvelope;
        $payRequest->actionType = "PAY";
        $payRequest->cancelUrl = $data['cancel_url'];
        $payRequest->returnUrl = $data['return_url'];
        $payRequest->currencyCode = $this->currency;
        $payRequest->memo = !empty($data['memo']) ? $data['memo'] : '';
        $payRequest->ipnNotificationUrl = $data['callback_url'];
        $payRequest->feesPayer = "EACHRECEIVER";
        $payRequest->receiverList = $receiverList;
        $response = $payPal->Pay($payRequest);
        $result = array('status' => false, 'message' => '');
        if ($response) {
            $this->writeLog('Transaction pay response: ');
            $this->writeLog($response);
            if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {
                $token = $response->payKey;
                $result['status'] = true;
                $result['url'] = $this->_paypal_link . '/webscr?cmd=_ap-payment&paykey=' . $token;
                $this->writeLog('Transaction pay url created: ' . $result['url']);

                //set bn code
                $setPaymentOptionsRequest = new SetPaymentOptionsRequest(new RequestEnvelope("en_US"));
                $setPaymentOptionsRequest->payKey = $token;
                $setPaymentOptionsRequest->senderOptions = new SenderOptions();
                $setPaymentOptionsRequest->senderOptions->referrerCode = $this->_bn_code;

                $payPal->SetPaymentOptions($setPaymentOptionsRequest);
            } else {
                $result['message'] = $response->error[0]->message;
                $this->writeLog('Transaction pay failed');
            }
        }
        return $result;
    }

    public function callback() {
        $ipnMessage = new PPIPNMessage(null, $this->PayPalConfig);
        if ($ipnMessage->validate()) {
            $data = $ipnMessage->getRawData();
            $data = urldecode(json_encode($data));
            $data = json_decode($data, true);
            $this->writeLog($data);
            return $data;
        } else {
            $this->writeLog('failed');
        }
    }

    public function parseSiteProfit($data) {
        $profit_percentage = Configure::read('Store.store_site_profit');
        if (!empty($data['receivers']) && $profit_percentage > 0 && $profit_percentage <= 50) {
            $profit = 0;
            foreach ($data['receivers'] as $k => $item) {
                $temp = round($item['amount'] * $profit_percentage / 100, 2);
                $data['receivers'][$k]['amount'] = $item['amount'] - $temp;
                $profit += $temp;
            }
            $site_info = array(
                'amount' => $profit,
                'email' => $this->paypal_site_email
            );
            $data['receivers'][] = $site_info;
        }
        return $data;
    }

    public function writeLog($msg) {
        CakeLog::write('store_paypal', print_r($msg, true));
    }

    public function checkAccountExist($email, $firstName = null, $lastName = null) {
        $url = trim($this->_svcs_link . "/AdaptiveAccounts/GetVerifiedStatus");  //set PayPal Endpoint to sandbox
        $this->headers = array(
            "X-PAYPAL-SECURITY-USERID: " . $this->PayPalConfig['acct1.UserName'],
            "X-PAYPAL-SECURITY-PASSWORD: " . $this->PayPalConfig['acct1.Password'],
            "X-PAYPAL-SECURITY-SIGNATURE: " . $this->PayPalConfig['acct1.Signature'],
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
            "X-PAYPAL-APPLICATION-ID: " . $this->PayPalConfig['acct1.AppId']);
        if ($this->test_mode == "sandbox") {
            $this->headers["X-PAYPAL-SANDBOX-EMAIL-ADDRESS"] = $email;
            $bodyparams = array(
                "emailAddress" => $email,
                "matchCriteria" => "NONE"
            );
        } else {
			return true;
            $bodyparams = array(
                "emailAddress" => $email,
                "firstName" => $firstName,
                "lastName" => $lastName,
                "matchCriteria" => "NAME"
            );
        }

        //$body_data = http_build_query($bodyparams, "", chr(38));
        $body_data = http_build_query($bodyparams);

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body_data);


        // Get response from the server.
        $httpResponse = curl_exec($ch);
        $httpResponse = json_decode($httpResponse, true);
        if (!empty($httpResponse['error'])) {
            return false;
        }
        return true;
    }

}
