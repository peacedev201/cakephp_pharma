<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Component', 'Controller');

class InviterComponent extends Component {

    public function initialize(Controller $controller) {
        // Load model
    }

    public function getSocialProvidersConfigs() {
       
        $providers_key = Configure::read('FriendInviter.web_account_services');

        // Default config
        $config = array();
        $config = array(
                        "base_url" => "http://localhost/social/auths/endpoint",
                        "providers" => array(
                           
                    ));
        
        if(!is_array($providers_key)){
            $providers_key = array($providers_key);
        }
        foreach ($providers_key as $key) {
            $provider_name = ucfirst($key);
                               
            $config['providers'][$provider_name]['enabled'] = true;
            switch ($key) {
                case 'google':                   
                    if(Configure::read('GoogleIntegration.google_app_id') && Configure::read('GoogleIntegration.google_app_secret')){
                        $config['providers']['Google']['keys'] = array(
                            'id' => Configure::read('GoogleIntegration.google_app_id'),
                            'secret' => Configure::read('GoogleIntegration.google_app_secret'),
                        );
                        $config['providers']['Google']['redirect_uri'] = Configure::read(ucfirst($key) . 'Integration.' . $key . '_app_return_url');
                    }
                   
                    break;
                case 'yahoo':
                    if(Configure::read('FriendInviter.yahoo_app_key') && Configure::read('FriendInviter.yahoo_shared_secret')){                                                                     
                        $config['providers'][$provider_name]['keys'] = array(
                                "id" => Configure::read('FriendInviter.yahoo_app_key'), 
                                "secret" => Configure::read('FriendInviter.yahoo_shared_secret')
                            );
                    }
                //   var_dump($config);die();
                    break;
                case 'live':
                    if(Configure::read('FriendInviter.windows_live_appid') && Configure::read('FriendInviter.windows_live_secret')){
                        $config['providers'][$provider_name]['keys'] =  array(
                                "id" => Configure::read('FriendInviter.windows_live_appid'), 
                                "secret" => Configure::read('FriendInviter.windows_live_secret')
                            );
                    }
                    break;                    
                default:
                    break;
            }
                      
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

    public function is232(){
        $ver = Configure::read('core.version');
        if(!empty($ver)){
            list($id1,$id2,$id3) = explode('.', $ver);
           
            if(!empty($id1)){
                if($id1 > 2){
                   return true;
                }

                if(!empty($id2) && $id2 > 3){
                    return true;
                }
                
                if(!empty($id3) && $id3 >= 2){
                    return true;
                }
            }
        }
        return false;
    }
}
