<?php
/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

App::uses('Component', 'Controller');
class PaypalExpressComponent extends Component 
{
    public function __construct(\ComponentCollection $collection, $settings = array()) 
    {
        parent::__construct($collection, $settings);
        
        $mGateway = MooCore::getInstance()->getModel('PaymentGateway.Gateway');
        $gateway = $mGateway->findByPlugin('PaypalExpress');
        $this->test_mode = "sandbox";
        $this->api_username = "";
        $this->api_password = "";
        $this->api_signature = "";
        $this->currency_code = "";
        $this->return_url = "";
        $this->cancel_url = "";
        $this->paypal_site_email = '';
        if (!empty($gateway['Gateway']['config'])) 
        {
            $config = json_decode($gateway['Gateway']['config'], true);
            if($gateway['Gateway']['test_mode'])
            {
                $this->paypal_mode = 'sandbox';
            }
            else 
            {
                $this->paypal_mode = '';
            }
            $this->api_username = $config['username'];
            $this->api_password = $config['password'];
            $this->api_signature = $config['signature'];
            $this->currency_code = $this->getDefaultCurrency();
            $this->paypal_site_email = str_replace("_api1.", "@", $this->api_username);
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
    
    function config($return_url, $cancel_url)
    {
        $this->return_url = $return_url;
        $this->cancel_url = $cancel_url;
    }
    
    function PPHttpPost($methodName, $data) 
    {
        // Set up your API credentials, PayPal end point, and API version.
        $api_username = urlencode($this->api_username);
        $api_password = urlencode($this->api_password);
        $api_signature = urlencode($this->api_signature);

        if($this->paypal_mode == 'sandbox')
        {
            $paypalmode 	=	'.sandbox';
        }
        else
        {
            $paypalmode 	=	'';
        }
        $API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
        $version = urlencode('93.0');
        
        // Set the API operation, version, and API signature in the request.
        $request_data = array(
            'METHOD' => $methodName,
            'VERSION' => $version,
            'PWD' => $api_password,
            'USER' => $api_username,
            'SIGNATURE' => $api_signature
        );
        $request_data = array_merge($request_data, $data);
        $request_data = http_build_query($request_data);

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        $httpResponse = curl_exec($ch);

        if(!$httpResponse) {
            exit("$methodName failed: ".curl_error($ch).'('.curl_errno($ch).')');
        }

        // Extract the response details.
        $httpResponse = urldecode($httpResponse);
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if(sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($request_data) to $API_Endpoint.");
        }

		return $httpParsedResponseAr;
	}
    
    function setParallelData($data)
    {
        //data
        if(empty($data))
        {
            return array(
                'success' => 0,
                'msg' => __d('store', 'Data can not be empty')
            );
        }
        
        $this->writeLog('Start new transaction');
        $padata = array(
            'RETURNURL' => $this->return_url,
            'CANCELURL' => $this->cancel_url,
        );
        foreach($data as $key => $buyer)
        {
            $total_amount = $buyer['amount']; //total amount = amount + tax
            $tax = !empty($buyer['tax']) ? $buyer['tax'] : 0;
            $amount = $buyer['amount'] - $tax;
            $email = $buyer['email'];
            $description = $buyer['description'];
            $request_id = $buyer['request_id'];

            $padata['PAYMENTREQUEST_'.$key.'_CURRENCYCODE'] = $this->currency_code;
            $padata['PAYMENTREQUEST_'.$key.'_AMT'] = $total_amount;
            $padata['PAYMENTREQUEST_'.$key.'_ITEMAMT'] = $amount;
            $padata['PAYMENTREQUEST_'.$key.'_TAXAMT'] = $tax;
            $padata['PAYMENTREQUEST_'.$key.'_PAYMENTACTION']= 'Order';
            $padata['PAYMENTREQUEST_'.$key.'_DESC'] = $description;
            $padata['PAYMENTREQUEST_'.$key.'_SELLERPAYPALACCOUNTID'] = $email;
            $padata['PAYMENTREQUEST_'.$key.'_PAYMENTREQUESTID'] = $request_id;

            /*foreach($buyer['products'] as $product_key => $product)
            {
                $product_quantity = $product['quantity'];
                $product_amount = $product['amount'];
                $product_tax = !empty($product['tax']) ? $product['tax'] : 0;
                $product_description = $product['description'];

                $padata['L_PAYMENTREQUEST_'.$key.'_QTY'.$product_key] = $product_quantity;
                $padata['L_PAYMENTREQUEST_'.$key.'_AMT'.$product_key] = $product_amount;
                $padata['L_PAYMENTREQUEST_'.$key.'_TAXAMT'.$product_key] = $product_tax;
                $padata['L_PAYMENTREQUEST_'.$key.'_DESC'.$product_key] = $product_description;
            }*/
        }
        
        //We need to execute the "SetExpressCheckOut" method to obtain paypal token
        $response = $this->PPHttpPost('SetExpressCheckout', $padata);
        
        $this->writeLog('Transaction pay response: ');
        $this->writeLog($response);

        //Respond according to message we receive from Paypal
        if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"]))
        {
            // If successful set some session variable we need later when user is redirected back to page from paypal. 
            if($this->paypal_mode == 'sandbox')
            {
                $paypalmode 	=	'.sandbox';
            }
            else
            {
                $paypalmode 	=	'';
            }
            //Redirect user to PayPal store with Token received.
            $paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$response["TOKEN"].'';
            $this->writeLog('Transaction pay url created: ' . $paypalurl);
            return array(
                'status' => 1,
                'url' => $paypalurl
            );
        }
        else
        {
            $this->writeLog('Transaction pay failed');
            return array(
                'status' => 0,
                'msg' => $response
            );
        }
    }
    
    function confirmParallel($token, $payerid)
    {
        if(!empty($token) && !empty($payerid))
        {
            //parse data
            $padata = array(
                'TOKEN' => $token,
                'PAYERID' => $payerid
            );
			
			//get order detail
			$order_detail = $this->PPHttpPost('GetExpressCheckoutDetails', $padata);
			if($order_detail == null)
			{
				return array(
					'status' => 0,
					'msg' => __d('store', 'Invalid token or payerid')
				);
			}
			$item_index = -1;
			$max_item = false;
			while($max_item === false)
			{
				$item_index += 1;
				if(isset($order_detail['PAYMENTREQUEST_'.$item_index.'_SELLERPAYPALACCOUNTID']))
				{
					$total_amount = $order_detail['PAYMENTREQUEST_'.$item_index.'_AMT'];
					$email = $order_detail['PAYMENTREQUEST_'.$item_index.'_SELLERPAYPALACCOUNTID'];
					$request_id = $order_detail['PAYMENTREQUEST_'.$item_index.'_PAYMENTREQUESTID'];
					$currency_code = $order_detail['PAYMENTREQUEST_'.$item_index.'_CURRENCYCODE'];

					$padata['PAYMENTREQUEST_'.$item_index.'_CURRENCYCODE'] = $currency_code;
					$padata['PAYMENTREQUEST_'.$item_index.'_AMT'] = $total_amount;
					$padata['PAYMENTREQUEST_'.$item_index.'_SELLERPAYPALACCOUNTID'] = $email;
					$padata['PAYMENTREQUEST_'.$item_index.'_PAYMENTREQUESTID'] = $request_id;
				}
				else
				{
					$max_item = true;
				}
			}
            
            //get response
            $response = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);
            
            $this->writeLog('Transaction confirmation: ');
            $this->writeLog($response);
            
            //Check if everything went ok..
            if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"])) 
            {
                $results = array(
                    'status' => 1
                );
                for($key = 0; $key < $item_index; $key++)
                {
                    $result_buyer = array(
                        'email' => $order_detail['PAYMENTREQUEST_'.$key.'_SELLERPAYPALACCOUNTID']
                    );
                    if(empty($response['PAYMENTINFO_'.$key.'_PAYMENTSTATUS']))
                    {
                        continue;
                    }
                    $result_buyer['status'] = $response['PAYMENTINFO_'.$key.'_PAYMENTSTATUS'];
                    $result_buyer['transaction_id'] = $response['PAYMENTINFO_'.$key.'_TRANSACTIONID'];
                    $result_buyer['request_id'] = $order_detail['PAYMENTREQUEST_'.$key.'_PAYMENTREQUESTID'];
                    $results['details'][] = $result_buyer;
                }
                $this->writeLog('Success');
                return $results;
            }
            else 
            {
                $this->writeLog('Failed');
                return array(
                    'status' => 0,
                    'msg' => $response
                );
            }
        }
        $this->writeLog('Invalid token or payerid');
        return array(
            'status' => 0,
            'msg' => __d('store', 'Invalid token or payerid')
        );
    }
    
    public function writeLog($msg) 
    {
        CakeLog::write('store_paypal_express', print_r($msg, true));
    }
    
    public function checkAccountExist($email, $firstName = null, $lastName = null) {
        if($this->paypal_mode == 'sandbox')
        {
            $paypalmode 	=	'.sandbox';
        }
        else
        {
            $paypalmode 	=	'';
        }
        $url = trim("https://svcs".$paypalmode.".paypal.com/AdaptiveAccounts/GetVerifiedStatus");  //set PayPal Endpoint to sandbox
        $headers = array(
            "X-PAYPAL-SECURITY-USERID: " . $this->api_username,
            "X-PAYPAL-SECURITY-PASSWORD: " . $this->api_password,
            "X-PAYPAL-SECURITY-SIGNATURE: " . $this->api_signature,
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON");
        if ($this->paypal_mode == "sandbox") {
            $headers[] = "X-PAYPAL-SANDBOX-EMAIL-ADDRESS: ".$email;
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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
    
    public function parseSiteProfit($data) 
    {
        $profit_percentage = Configure::read('Store.store_site_profit');
        if (!empty($data) && $profit_percentage > 0 && $profit_percentage <= 50) {
            $profit = 0;
            foreach ($data as $k => $item) {
                $temp = round($item['amount'] * $profit_percentage / 100, 2);
                $data[$k]['amount'] = $item['amount'] - $temp;
                $profit += $temp;
            }
            $site_info = array(
                "request_id" => 0,
                "amount" => $profit,
                "email" => $this->paypal_site_email,
                "description" => __d('store', 'Site profit')
            );
            $data[] = $site_info;
        }
        return $data;
    }
} 