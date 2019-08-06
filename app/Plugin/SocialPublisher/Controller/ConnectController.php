<?php

/**
* Copyright (c) SocialLOFT LLC
* mooSocial - The Web 2.0 Social Network Software
* @website: http://www.moosocial.com
* @author: mooSocial - Linh.LHD
* @license: https://moosocial.com/license/
 */
App::import('Lib/SocialIntegration', 'Auth');
App::import('Lib/SocialIntegration', 'Storage');

class ConnectController extends SocialPublisherAppController {   
    public $components = array('Session', 'SocialPublisher.SocialPublisher');
    
    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {

        $uid = $this->Session->read('uid');
        if (!$uid) {
            $this->redirect('/');
        }

        $this->loadModel('SocialIntegration.SocialUser');
        $provider = array(
            array(
                'provider' => 'facebook',
                'connect' => $this->SocialUser->connect('facebook')
            ),
            array(
                'provider' => 'google',
                'connect' => $this->SocialUser->connect('google')
            )
        );
        $this->set('providers', $provider);
    }

    public function sharingpost(){
     // Cache::write('socialshare_confirm_' . CakeSession::id(), 0);die();
        $facebook = $this->request->data['facebook'];
	$twitter = $this->request->data['twitter'];
        $sharing_message = $this->request->data['sharing_message'];
        $socialshare_confirm = $this->request->data['socialshare_confirm'];
        $activity_id = (int)$this->request->data['activity_id'];
        $uid = $this->Session->read('uid');
       
         $fb_post = false;
         $twitter_post = false;
          // $url = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')?'https://':'http://' . $_SERVER['HTTP_HOST'] . $this->request->base;
             $url = 'http://worktrotter.co.uk/';
             $url .= '/users/view/' . $uid . '/activity_id:' . $activity_id;
        if($facebook == 'true'){
             $hauth = $this->_getAuthAdapter('Facebook');
           
             $params = array(
                 'link' =>  $url,
                 'message' => $sharing_message
             );
             $fb_post =  $hauth->adapter->setUserStatus($params);
        }
        
        if($twitter == 'true'){
            App::import('Lib/SocialIntegration', 'Bitly');
            $login = 'o_4jea3ht0v2';
            $appkey = 'R_b70453c87b4f4cebb1964f3f416bdefe';

            $bitly = new SocialIntegration_Bitly();
            $shortURL = $bitly->get_bitly_short_url($url, $login, $appkey, $format = 'txt');
            
            $hauth = $this->_getAuthAdapter('Twitter');
            $twitter_response = $hauth->adapter->setUserStatus($sharing_message . ' ' . $shortURL);
            $twitter_res_arr = get_object_vars($twitter_response);
            if(isset($twitter_res_arr['id'])){
                $twitter_post = true;
            }
        }
        
        if($socialshare_confirm == 'true'){
           //  $uid = $this->Session->read('uid');
             $fb_sharing = 0;
             if($facebook == 'true'){
                 $fb_sharing = 1;
             }
             $twitter_sharing = 0;
             if($twitter == 'true'){
                 $twitter_sharing = 1;
             }
             $this->User->id = $uid;
             $this->User->save(array('facebook_sharing' => $fb_sharing, 'twitter_sharing' => $twitter_sharing, 'socialshare_confirm' => 1));

            $social_sharing = Cache::read('social_sharing_' . CakeSession::id());
            if($social_sharing){      
                $social_sharing['facebook_sharing'] = $fb_sharing;
                $social_sharing['twitter_sharing'] = $twitter_sharing;
                Cache::write('social_sharing_' . CakeSession::id(), $social_sharing);
            }
            
            Cache::write('socialshare_confirm_' . CakeSession::id(), 1);
        }
        $result = array(
            'facebook' => $fb_post,
            'twitter' => $twitter_post
        );
        echo json_encode($result);
        exit();
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
