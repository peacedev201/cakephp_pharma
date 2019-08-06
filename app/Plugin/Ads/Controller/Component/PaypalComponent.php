<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
 
App::uses('Component', 'Controller');

class PaypalComponent extends Component 
{
    public $components = array('Session');
    
    public function setConfig()
    {
        $ads_paypal_test = Configure::read('Ads.ads_paypal_test');
        if($ads_paypal_test)
        {
            $this->paypalmode = '.sandbox';
            $this->is_test = true;
        }
        else 
        {
            $this->paypalmode = '';
            $this->is_test = false;
        }
        $this->url = "https://www".$this->paypalmode.".paypal.com/cgi-bin/webscr";
    }
    
    public function getUrl($values)
    {
        $aSettings = array(
            'business' => Configure::read('Ads.ads_paypal_email'),
            'cmd' => "_xclick",
            'item_name' => $values['item_name'],
            'item_number' => $values['item_number'],
            'amount' => $values['amount'],
            'currency_code' => $values['currency_code'],
            'notify_url' => urlencode(stripslashes($values['notify_url'])),
            'return' => urlencode(stripslashes($values['return'])),
            'cancel_return' => urlencode(stripslashes($values['cancel_return'])),
            'custom' => !empty($values['custom']) ? $values['custom'] : '',
            'no_shipping' => '1',
            'no_note' => '1'
        );
        $dataString = null;

        foreach($aSettings as $k => $v)
        {
            $dataString[] = $k."=".$v;
        }
        $dataString = implode('&', $dataString);
        $url = $this->url."?".$dataString;
        return $url;
    }

    public function callback()
    {
        if(isset($_POST) && $_POST != null)
        {
            $this->write_log("\n");
            if($this->is_test)
            {
                $this->write_log('Paypal test');
                $this->write_log(date('Y-m-d H:i:s'));
            }
            else 
            {
                $this->write_log('Paypal real');
            }
            $this->postback_params = $_POST;
            
            //listner
            $bVerified = $this->listener();
            $this->write_log('Paypal verify created');

            if ($bVerified)
            {
                $this->write_log('Paypal verify valid');
                if (isset($_POST['payment_status']))
                {
                    $this->write_log('Paypal return params: '.$_POST);
                    $this->write_log('Paypal payment_status: '.$_POST['payment_status']);
                    return array($_POST['payment_status'], $_POST);
                }
                $this->write_log('Paypal payment_status: no status found');
                return null;
            }
            $this->write_log('Paypal verify invalid');
            return null;
        }
        $this->write_log('Paypal no post data');
        return null;
    }
    
    public static function getPostbackParams()
    {
        return $this->postback_params;
    }
    
    private function write_log($log)  
    {
        CakeLog::write('ads_paypal', $log);
    }
    
    private function listener()
    {
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) 
        {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
            {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) 
        {
            $get_magic_quotes_exists = true;
        }
        
        foreach ($myPost as $key => $value) 
        {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) 
            {
                $value = urlencode(stripslashes($value));
            } 
            else 
            {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
        // Post IPN data back to PayPal to validate the IPN data is genuine
        // Without this step anyone can fake IPN data
        $ch = curl_init($this->url);
        if ($ch == FALSE) 
        {
            return FALSE;
        }
		
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT , 'PayPal-PHP-SDK');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());

		if( !($res = curl_exec($ch)) ) 
		{
			$this->write_log(json_encode($res));
            $this->write_log("Can't connect to PayPal to validate IPN message: ".curl_error($ch));
            curl_close($ch);
            return FALSE;
		}
		else 
		{
			// Log the entire HTTP response if debug is switched on.
            $this->write_log("HTTP request of validation request:" . curl_getinfo($ch, CURLINFO_HEADER_OUT) . " for IPN payload: $req" . PHP_EOL);
            $this->write_log("HTTP response of validation request: $res" . PHP_EOL);
		}
		curl_close($ch); 
		
        // Inspect IPN validation result and act accordingly
        if (strcmp($res, "VERIFIED") == 0) 
        {

            if ($this->ipn_log) 
            {
                $this->write_log("Verified IPN: $req " . PHP_EOL);
            }
            return TRUE;
        } 
        else if (strcmp($res, "INVALID") == 0) 
        {
            // log for manual investigation
            // Add business logic here which deals with invalid IPN messages
            if ($this->ipn_log) 
            {
                $this->write_log("Invalid IPN: $req" . PHP_EOL);
            }
            return FALSE;
        }
    }
} 