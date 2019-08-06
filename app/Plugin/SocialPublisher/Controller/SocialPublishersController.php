<?php

/**
* Copyright (c) SocialLOFT LLC
* mooSocial - The Web 2.0 Social Network Software
* @website: http://www.moosocial.com
* @author: mooSocial - Linh.LHD
* @license: https://moosocial.com/license/
 */

App::uses('CakeEvent', 'Event');
App::uses('Lib/SocialIntegration', 'Storage');
App::uses('Lib/SocialIntegration', 'Auth');
App::import('Lib/SocialIntegration', 'Storage');
App::import('Lib/SocialIntegration', 'Auth');

class SocialPublishersController extends SocialPublisherAppController {

    public $components = array('Session', 'SocialIntegration.Social');
    public $uses = array('SocialIntegration.SocialUser', 'User');
    public $helpers = array('Cache');
    private $_provider = null;

    public function beforeFilter() {
        parent::beforeFilter();
        
        if(isset($this->request->pass[0])){
           $this->_provider = $this->request->pass[0];
        }
    }
    
    public function admin_index() {
        
    }

    public function index() {
        $uid = $this->Auth->user('id');

        $fbook = array();
        $twitter = array();

        if (!empty($uid)) {
            $this->loadModel('User');
            $user = $this->User->findById($uid);
            Cache::delete('social_sharing_' . $uid);
            Cache::delete('socialshare_confirm_' . CakeSession::id());
            $social_active = Configure::read('core.publish_social_active');
            if ($social_active == 1) {
                $publish_providers = Configure::read('core.publish_providers');

                if (!empty($publish_providers)) {
                    $facebook_enable = false;
                    $twitter_enable = false;
                    if (is_array($publish_providers)) {
                        foreach ($publish_providers as $provider) {
                            if ($provider == 'facebook') {
                                $facebook_enable = true;
                            } elseif ($provider == 'twitter') {
                                $twitter_enable = true;
                            }
                        }
                    } else {
                        if ($publish_providers == 'facebook') {
                            $facebook_enable = true;
                        } elseif ($publish_providers == 'twitter') {
                            $twitter_enable = true;
                        }
                    }

                    if ($facebook_enable) {
                        $fb_user = Cache::read('social_integration_' . CakeSession::id() . '_provider_user');
                        if ($fb_user) {
                            $fbook['connect'] = true;
                            $fbook['user'] = $fb_user;
                        } else {
                            $fbook['connect'] = false;
                        }
                        $fbook['sharing'] = ($user['User']['facebook_sharing'] == 1) ? true : false;
                    }

                    if ($twitter_enable) {
                        $twitter_user = Cache::read('social_integration_' . CakeSession::id() . '_twitter');
                        //   var_dump($twitter_user);die();
                        if ($twitter_user) {
                            $twitter['connect'] = true;
                            $twitter['user'] = $twitter_user;
                        } else {
                            $twitter['connect'] = false;
                        }
                        $twitter['sharing'] = ($user['User']['twitter_sharing'] == 1) ? true : false;
                    }
                    
                    $social_sharing = array(
                        'facebook_enable' => $facebook_enable,
                        'facebook_sharing' => $user['User']['facebook_sharing'],
                        'twitter_enable' => $twitter_enable,
                        'twitter_sharing' => $user['User']['twitter_sharing']
                    );
                    Cache::write('social_sharing_' . $uid, $social_sharing);
                }
            }
        }

        $this->set('fbook', $fbook);
        $this->set('twitter', $twitter);
    }

    public function loginsocial() {         
                
               $provider = $this->_provider;
                
               $this->SocialPublisher = $this->Components->load('SocialPublisher.SocialPublisher');
                // Get providers configuration
                $config = $this->SocialPublisher->getSocialProvidersConfigs();
                $url_login = Router::url(array(
                    'plugin' => 'social_publisher',
                    'controller' => 'social_publishers',
                    'action' => 'shareendpoint',
                        ), true) . '/' . $provider;
                

              //  $config['redirect_uri'] = $url_login;
                $config['base_url'] = $url_login;

                $providers = $config['providers'];
                $provider = ucfirst($provider);
                foreach ($providers as $key=>$val) {
                    if($key == $provider){
                        $providers[$key]['redirect_uri'] = $url_login;
                    }
                }
                $config['providers'] = $providers;
                
                // Initialization
                $authnObj = new SocialIntegration_Auth($config);
               
                $storage = new SocialIntegration_Storage();
            //    $storage->clear();
           //    $authnObj->logoutAllProviders();

        // var_dump($authnObj->isConnectedWith($provider));die();
            //    $provider = 'Facebook';
               if($provider == 'Facebook'){
                    $user_social =  Cache::read('social_integration_' . CakeSession::id() . '_provider_user');
               }else{
                   $user_social =  Cache::read('social_integration_' . CakeSession::id() . '_twitter');
               }
               
               if($authnObj->isConnectedWith($provider) && !$user_social){                 
                  $authnObj->logoutAllProviders();
               }
               
                if (!$authnObj->isConnectedWith($provider) || !$user_social) {
                     // Authen with provider                 
                    $storage->clear();
                    if(isset($_SESSION['HA_STORE_hauth_session'][strtolower($provider)])){
                            unset($_SESSION['HA_STORE_hauth_session'][strtolower($provider)]);                   
                    }
                    
                   // var_dump($_SESSION);die();
                    $adapter = $authnObj->authenticate($provider);
                }else{                
                    if(isset($_GET['flag'])){                      
                        $flag = $_GET['flag'];
                        if($flag == 'true'){
                            $num = 1;
                        }else{
                            $num = 0;
                        }
                        if($provider == 'Facebook'){
                            $social_field = 'facebook_sharing';
                        }elseif($provider == 'Twitter'){
                            $social_field = 'twitter_sharing';
                        }
                        $uid = $this->Auth->user('id');
                        $this->loadModel('SocialPublisher.SpSharing');
                        $this->SpSharing->updateAll(array($social_field =>$num), array('user_id' => $uid));

                        $social_sharing = Cache::read('social_sharing_' . $uid);
                        if($social_sharing){
                            $provider = strtolower($provider);
                            $social_sharing[$provider . '_sharing'] = ($num == 1) ? true : false;
                            Cache::write('social_sharing_' . $uid, $social_sharing);
                        }
                     //   var_dump($social_sharing);die();
                    }
                    $this->redirect('/home');
                }
                exit();
    }
    
    public function updateflag(){
        $provider = $this->_provider;
        $provider = strtolower($provider);
        if(isset($_GET['flag'])){                      
            $flag = $_GET['flag'];
            if($flag == 'true'){
                $num = 1;
            }else{
                $num = 0;
            }
            if($provider == 'facebook'){
                $social_field = 'facebook_sharing';
            }elseif($provider == 'twitter'){
                $social_field = 'twitter_sharing';
            }
            $uid = $this->Auth->user('id');
            $this->loadModel('SocialPublisher.SpSharing');
            $this->SpSharing->updateAll(array($social_field =>$num), array('user_id' => $uid));

            $social_sharing = Cache::read('social_sharing_' . $uid);
            if($social_sharing){
                
                $social_sharing[$provider . '_sharing'] = ($num == 1) ? true : false;
                Cache::write('social_sharing_' . $uid, $social_sharing);
            }
            exit();
        }
    }

    public function shareendpoint() {
		if((isset($_GET['error']) && $_GET['error'] == 'access_denied') || isset($_GET['denied'])){
			 $this->redirect('/');
		}
	   
        $provider = ucfirst($this->_provider);
        $hauth = $this->_getAuthAdapter($provider);

        # if REQUESTed hauth_idprovider is wrong, session not created, etc.
        if (isset($_GET['hauth_start'])) {

            try {
                $hauth->adapter->loginBegin();
            } catch (Exception $e) {
                if(method_exists($e, 'getMessage')){
                    $this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error-message'));
                }else{
                    $this->Session->setFlash(__('Authentication failed!'), 'default', array('class' => 'error-message'));
                }
                 $this->redirect('/home');
                $hauth->returnToCallbackUrl();
            }
        } else if (isset($_GET['hauth_done'])) {

            try {
                $hauth->adapter->loginFinish();
                $hauth = SocialIntegration_Auth::setup($provider);
                $provider_user = $hauth->adapter->getUserProfile();
                if ($provider == 'Facebook') {
                    Cache::write('social_integration_' . CakeSession::id() . '_provider_user', $provider_user);
                }if ($provider == 'Twitter') {
                    Cache::write('social_integration_' . CakeSession::id() . '_twitter', $provider_user);
                }
                // Write cache
            } catch (Exception $e) {
                if(method_exists($e, 'getMessage')){
                    $this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error-message'));
                }else{
                    $this->Session->setFlash(__('Authentication failed!'), 'default', array('class' => 'error-message'));
                }
                $this->redirect('/home');
            }
            // $this->redirect(array('action' => 'login', 'provider' => $this->_provider));
            $url = Router::url(array(
                        'controller' => 'home',
                        'action' => 'index',
                            ), true) ;
            $this->set('done', $url);
        } else {
            // Finish authentication
            try {
                $hauth->adapter->loginFinish();
                $hauth = SocialIntegration_Auth::setup($provider);
                $provider_user = $hauth->adapter->getUserProfile();
                // Write cache
                if ($provider == 'Facebook') {
                    Cache::write('social_integration_' . CakeSession::id() . '_provider_user', $provider_user);
                }if ($provider == 'Twitter') {
                    Cache::write('social_integration_' . CakeSession::id() . '_twitter', $provider_user);
                }
                //    $provider_user = $hauth->adapter->setUserStatus('Test test test');
            } catch (Exception $e) {
                 if(method_exists($e, 'getMessage')){
                    $this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error-message'));
                }else{
                    $this->Session->setFlash(__('Authentication failed!'), 'default', array('class' => 'error-message'));
                }
                $this->redirect('/home');
            }
        }

        if ($provider == 'Facebook') {
            $social_field = 'facebook_sharing';
        } elseif ($provider == 'Twitter') {
            $social_field = 'twitter_sharing';
        }

        if (isset($social_field)) {
            $uid = $this->Auth->user('id');
            $this->loadModel('SocialPublisher.SpSharing');
            $this->SpSharing->updateAll(array($social_field =>1), array('user_id' => $uid));

            $social_sharing = Cache::read('social_sharing_' . $uid);
            if ($social_sharing) {
                $social_sharing[$provider . '_sharing'] = true;
                Cache::write('social_sharing_' . $uid, $social_sharing);
            }
            Cache::write('socialshare_confirm_' . CakeSession::id(), 0);
        }
        $this->redirect('/home');
    }

    public function logoutsocial() {
        $provider = ucfirst($this->_provider);    
        if ($provider == 'Facebook') {
            $social_field = 'facebook_sharing';
        } elseif ($provider == 'Twitter') {
            $social_field = 'twitter_sharing';
        }

        $uid = $this->Auth->user('id');
        $this->loadModel('SocialPublisher.SpSharing');
        $this->SpSharing->updateAll(array($social_field =>0), array('user_id' => $uid));

        $social_sharing = Cache::read('social_sharing_' . $uid);
        if ($social_sharing) {
            $social_sharing[$this->_provider . '_sharing'] = false;
            Cache::write('social_sharing_' . $uid, $social_sharing);
        }
                   
        if ($provider == 'Facebook') {
            if(Cache::read('social_integration_' . CakeSession::id() . '_provider_user')){
                Cache::delete('social_integration_' . CakeSession::id() . '_provider_user');
            }                    
        }else{
             Cache::delete('social_integration_' . CakeSession::id() . '_twitter');
        }
        $hauth = $this->_getAuthAdapter($provider);
        $hauth->logout();
        
        $this->redirect('/home');
    }

    protected function _getAuthAdapter($provider) {
        $storage = new SocialIntegration_Storage();
        // Check if SocialIntegration_Auth session already exist
       
        if (!$storage->config("CONFIG")) {
            header("HTTP/1.0 404 Not Found");
            die("You cannot access this page directly.");
        }

        SocialIntegration_Auth::initialize($storage->config("CONFIG"));

        $hauth = SocialIntegration_Auth::setup($provider);
        $hauth->adapter->initialize();

        return $hauth;
    }

}
