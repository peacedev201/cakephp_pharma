<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Component', 'Controller');

class SocialLoginComponent extends Component {

    public function initialize(Controller $controller) {
        // Load model
    }

    public function generatePassword($length = 8) {
        // inicializa variables
        $password = "";
        $i = 0;
        $possible = "0123456789bcdfghjkmnpqrstvwxyz";

        // agrega random
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);

            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

    public function getSocialProvidersConfigs() {
        
        $providers_key = array('facebook', 'google', 'linkedin', 'twitter');

        // Default config
        $config = array(
            "base_url" => "http://localhost/social/auths/endpoint/linkedin",
            "providers" => array(
                "Facebook" => array(
                    "enabled" => false,
                ),
                "Google" => array(
                    "enabled" => false,
                    "access_type" => "offline", // optional
                    "approval_prompt" => "force", // optional
                ),
                "Linkedin" => array(
                    "enabled" => false,
                ),
                "Twitter" => array(
                    "enabled" => false,
                ),
        ));

        foreach ($providers_key as $key) {
            $provider_name = ucfirst($key);

            $config['providers'][$provider_name]['enabled'] = true;
            // Set key and secret
            $config['providers'][$provider_name]['keys'] = array(
                'id' => Configure::read(ucfirst($key) . 'Integration.' . $key . '_app_id'),
                'secret' => Configure::read(ucfirst($key) . 'Integration.' . $key . '_app_secret'),
            );
            $config['providers'][$provider_name]['scope'] = Configure::read(ucfirst($key) . 'Integration.' . $key . '_app_scope');
            $config['providers'][$provider_name]['redirect_uri'] = Configure::read(ucfirst($key) . 'Integration.' . $key . '_app_return_url');
        }
        
        return $config;
    }

    public function socialLogout($provider) {
        App::import('Lib/SocialIntegration', 'Auth');
        App::import('Lib/SocialIntegration', 'Storage');

        $storage = new SocialIntegration_Storage();

        // Check if SocialIntegration_Auth session already exist
        if (!$storage->config("CONFIG")) {
            header("HTTP/1.0 404 Not Found");
            die("You cannot access this page directly.");
        }

        SocialIntegration_Auth::initialize($storage->config("CONFIG"));

        $hauth = SocialIntegration_Auth::setup($provider);
        $hauth->adapter->initialize();
        $hauth->adapter->logout();
    }
    
    public function is262(){
        $setting_model = MooCore::getInstance()->getModel('Setting');
        
        $version = $setting_model->findByName( 'version' );
        $ver = $version['Setting']['value'];
       
        if(!empty($ver)){
            list($id1,$id2,$id3) = explode('.', $ver);
           
            if(!empty($id1)){
                if($id1 > 2){
                   return true;
                }

                if(!empty($id2) && $id2 > 6){
                    return true;
                }
                
                if(!empty($id3) && $id3 >= 1){
                    return true;
                }
            }
        }
        return false;
    }

}
