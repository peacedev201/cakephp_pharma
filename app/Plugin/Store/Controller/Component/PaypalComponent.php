<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
 
App::uses('Component', 'Controller');

class PaypalComponent extends Component 
{
    public $components = array('Session');
    
    function config($paypal_sandbox, $api_username, $api_password, $api_signature, $currency_code, $return_url, $cancel_url)
    {
        if($paypal_sandbox)
        {
            $this->paypal_mode = 'sandbox';
        }
        else 
        {
            $this->paypal_mode = '';
        }
        $this->api_username = $api_username;
        $this->api_password = $api_password;
        $this->api_signature = $api_signature;
        $this->currency_code = $currency_code;
        $this->return_url = $return_url;
        $this->cancel_url = $cancel_url;
    }
    
    function PPHttpPost($methodName_, $nvpStr_) 
    {
        // Set up your API credentials, PayPal end point, and API version.
        $API_UserName = urlencode($this->api_username);
        $API_Password = urlencode($this->api_password);
        $API_Signature = urlencode($this->api_signature);

        if($this->paypal_mode == 'sandbox')
        {
            $paypalmode 	=	'.sandbox';
        }
        else
        {
            $paypalmode 	=	'';
        }

        $API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
        $version = urlencode('76.0');

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if(!$httpResponse) {
            exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if(sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

		return $httpParsedResponseAr;
	}
    
    function paypal($data, $total)
    {
        //data
        $padata = '';
        if($data != null)
        {
            foreach($data as $key => $item)
            {
                $padata .=  '&L_PAYMENTREQUEST_0_QTY'.$key.'='.urlencode($item['quantity']).
                            '&L_PAYMENTREQUEST_0_AMT'.$key.'='.urlencode($item['amount']).
                            '&L_PAYMENTREQUEST_0_NAME'.$key.'='.urlencode($item['name']).
                            '&L_PAYMENTREQUEST_0_NUMBER'.$key.'='.urlencode($key + 1);
            }
        }
        $padata .= 	'&CURRENCYCODE='.urlencode($this->currency_code).
                    '&PAYMENTACTION=Sale'.
                    '&ALLOWNOTE=1'.
                    '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($this->currency_code).
                    '&PAYMENTREQUEST_0_AMT='.urlencode($total).
                    '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($total); 
        $padata .=  '&AMT='.urlencode($total).				
                    '&RETURNURL='.urlencode($this->return_url).
                    '&CANCELURL='.urlencode($this->cancel_url);	

        //We need to execute the "SetExpressCheckOut" method to obtain paypal token
        $httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $padata);

        //Respond according to message we receive from Paypal
        if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
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
            $paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
            return array(
                'success' => 1,
                'url' => $paypalurl
            );
        }
        else
        {
            return array(
                'success' => 0,
                'msg' => $httpParsedResponseAr
            );
        }
    }
    
    function paypal_confirm($token, $playerid, $total)
    {
        if(!empty($token) && !empty($playerid))
        {
            $padata = 	'&TOKEN='.urlencode($token).
                        '&PAYERID='.urlencode($playerid).
                        '&PAYMENTACTION='.urlencode("SALE").
                        '&AMT='.urlencode($total).
                        '&CURRENCYCODE='.urlencode($this->currency_code);

            //We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
            $httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);

            //Check if everything went ok..
            if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
            {
                if($httpParsedResponseAr['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed' ||
                   $httpParsedResponseAr['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Pending')
                {
                    return array(
                        'success' => 1,
                        'transaction_id' => urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"])
                    );
                }
                else 
                {
                    return array(
                        'success' => 0,
                        'msg' => $httpParsedResponseAr
                    );
                }
            }
            else 
            {
                return array(
                    'success' => 0,
                    'msg' => $httpParsedResponseAr
                );
            }
        }
        return array(
            'success' => 0,
            'msg' => __d('store', 'Invalid token or playerid')
        );
    }
} 