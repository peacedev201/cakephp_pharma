<?php

/**
* Copyright (c) SocialLOFT LLC
* mooSocial - The Web 2.0 Social Network Software
* @website: http://www.moosocial.com
* @author: mooSocial - Linh.LHD
* @license: https://moosocial.com/license/
 */

App::uses('CakeEventListener', 'Event');
App::uses('AppController', 'Controller');


class SocialPublisherListener implements CakeEventListener {

    public function implementedEvents() {
			// TODO
			return array(
			  //  'welcomeBox.afterRenderMenu' => 'renderMenu',
				'View.Elements.activityForm.afterRenderItems' => 'renderSharingForm',
				'ActivitesController.afterShare' => 'sharePost',
				'MooView.beforeRender' => 'beforeRender'
			);
    }

     public function beforeRender($event)
    {
        $e = $event->subject();
        if(!$this->isApp($e)){ 
            $e->addPhraseJs(array(
                  'facebook' => __d('social_publisher', 'facebook'),
                  'twitter' => __d('social_publisher', 'twitter')
            ));

             
       // if($this->is232()){
    		$e->Helpers->Html->css( array(
    					'SocialPublisher.sp', 
    				),
    				array('block' => 'css')
    			);
    		
    		
    		if (Configure::read('debug') == 0){
    			$min="min.";
    		}else{
    			$min="";
    		}
                
    		$e->Helpers->MooRequirejs->addPath(array(
                        "mooSocialPublisher"=>$e->Helpers->MooRequirejs->assetUrlJS("SocialPublisher.js/socialpublisher.{$min}js"),
    		));

        }
    }
    /*
    public function renderMenu(CakeEvent $event) {
        $view = $event->subject();
        echo $view->element('SocialPublisher.menu');
    }
     * 
     */
    
    public function sharePost(CakeEvent $event) {
        $con = $event->subject();
        if(!$this->isApp($con)){           
            $uid = $con->Auth->user('id');
            $social_sharing = Cache::read('social_sharing_' . $uid);     
            if($social_sharing){       
                $facebook_sharing = false;
                $twitter_sharing = false;

                if($social_sharing['facebook_enable'] && $social_sharing['facebook_sharing']){
                    $facebook_sharing = true;
                }

                if($social_sharing['twitter_enable'] && $social_sharing['twitter_sharing']){
                    $twitter_sharing = true;
                }

                if($twitter_sharing){
                    $data = $event->data;
                    $con = $event->subject();

                    if(isset($data['activity']['Activity'])){
                        App::import('Lib/SocialIntegration', 'Storage');
                        App::import('Lib/SocialIntegration', 'Auth');

                        $url = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')?'https://':'http://' ;
                        $url = $url . $_SERVER['SERVER_NAME'] . $con->request->base;
                        // $url = 'http://worktrotter.co.uk/';
                        $activity_id = $data['activity']['Activity']['id'];
                        $url = $url . '/users/view/' . $uid . '/activity_id:' . $activity_id;

                        $site_name = Configure::read('core.site_name');
                        if(!empty($data['activity']['Activity']['content'])){
                            $sharing_message = $data['activity']['Activity']['content'] . ' ' . __d('social_publisher','via') . ' ' . $site_name;
                        }else{
                            if($data['activity']['Activity']['item_type'] == 'Photo_Album'){
                                $sharing_message = __d('social_publisher','Shared photo via') . ' ' . $site_name;
                            }else{
                                $sharing_message = __d('social_publisher','Posted a status via') . ' ' . $site_name;
                            }
                        }
                        /*
                        if($facebook_sharing){
                            try{

                            $hauth = $this->_getAuthAdapter('Facebook');

                            $sharing_activity_link = Configure::read('SocialPublisher.sharing_activity_link');

                            if(empty($sharing_activity_link)){
                                $sharing_message = $data['activity']['Activity']['content'];
                            }

                            $params = array(
                                 'message' => $sharing_message
                             );

                            if(!empty($sharing_activity_link)){
                                $params['link'] = $url;
                            }

                            if(!empty($hauth->adapter)){
                                $res = $hauth->adapter->setUserStatus($params);
                            }
                           } catch (Exception $e) {
                                // Silence
                            }
                        }
                         * 
                         */

                        if($twitter_sharing){
                            try{
                                /*
                                App::import('Lib/SocialIntegration', 'Bitly');
                                $login = 'o_4jea3ht0v2';
                                $appkey = 'R_b70453c87b4f4cebb1964f3f416bdefe';

                                $bitly = new SocialIntegration_Bitly();
                                $shortURL = $bitly->get_bitly_short_url($url, $login, $appkey, $format = 'txt');
                                 * 
                                 */

                                $sharing_message = CakeText::truncate(strip_tags($sharing_message), 80, array('ellipsis' => '', 'html' => false, 'exact' => false));	
                                $hauth = $this->_getAuthAdapter('Twitter');

                                if(!empty($hauth->adapter)){
                                    $hauth->adapter->setUserStatus($sharing_message . ' ' . $url);
                                }
                            } catch (Exception $e) {
                                // Silence
                            }
                        }                  
                      //  exit();
                    }
                }
            }
        }
    }
    
    public function renderSharingForm(CakeEvent $event) {
						
        $view = $event->subject();
        if(!$this->isApp($view)){
        $uid = $view->Auth->user('id');
        
        $fbook = array();
        $twitter = array();   
        if (!empty($uid)) {        			
		
           Cache::delete('social_sharing_' . $uid);
           $social_active = Configure::read('SocialPublisher.publish_social_active');
           
            if ($social_active) {
                $publish_providers = Configure::read('SocialPublisher.publish_providers');
                if (!empty($publish_providers)) {                  
                    $facebook_sharing = false;
                    $twitter_sharing = false;
                    $spSharingModel = MooCore::getInstance()->getModel('SocialPublisher.SpSharing');
                    $conditions = array(
                        'user_id' => $uid
                    );
                    $user = $spSharingModel->find('first', array('conditions' => $conditions));
                    if(empty($user)){
                        $spSharingModel->create();
                        $spSharingModel->set(array(
                            'user_id' => $uid,
                        ));
                        $spSharingModel->save();              
                    }else{
                        $facebook_sharing = ($user['SpSharing']['facebook_sharing'] == 1) ? true : false;
                        $twitter_sharing = ($user['SpSharing']['twitter_sharing'] == 1) ? true : false;
                    }
                    
                    
                    $facebook_enable = false;
                    $twitter_enable = false;
                    $helper = MooCore::getInstance()->getHelper('Core_Moo');
       
                    if (is_array($publish_providers)) {
                        foreach ($publish_providers as $provider) {
                            if ($provider == 'facebook' && $helper->socialIntegrationEnable('facebook')) {
                                $facebook_enable = true;
                            } elseif ($provider == 'twitter' && $helper->socialIntegrationEnable('twitter')) {
                                $twitter_enable = true;
                            }
                        }
                    } else {
                        if ($publish_providers == 'facebook' && $helper->socialIntegrationEnable('facebook')) {
                            $facebook_enable = true;
                        } elseif ($publish_providers == 'twitter' && $helper->socialIntegrationEnable('twitter')) {
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
                            $facebook_enable = false;
                        }
                        $fbook['sharing'] = $facebook_sharing;
                    }

                    if ($twitter_enable) {
                        $twitter_user = Cache::read('social_integration_' . CakeSession::id() . '_twitter');                        
                        if ($twitter_user) {
                            $twitter['connect'] = true;
                            $twitter['user'] = $twitter_user;
                        } else {
                            $twitter['connect'] = false;
                            $twitter_enable = false;
                        }
                        $twitter['sharing'] = $twitter_sharing;
                    }
                    
                    $social_sharing = array(
                        'facebook_enable' => $facebook_enable,
                        'facebook_sharing' => $facebook_sharing,
                        'twitter_enable' => $twitter_enable,
                        'twitter_sharing' => $twitter_sharing,
                    );
                    Cache::write('social_sharing_' . $uid, $social_sharing);
                    
                    echo $view->element('SocialPublisher.sharing', array(
                            'fbook' => $fbook,
                            'twitter' => $twitter,
                            'is232' => $this->is232()
                    ));
                }
            }            
        }
        }              
      
    }
    
    protected function _getAuthAdapter($provider) {           
        $storage = new SocialIntegration_Storage();

        // Check if SocialIntegration_Auth session already exist
        if (!$storage->config("CONFIG")) {
           return false;
        }

        SocialIntegration_Auth::initialize($storage->config("CONFIG"));

        $hauth = SocialIntegration_Auth::setup($provider);
        $hauth->adapter->initialize();

        return $hauth;
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
    
    public function isApp($v)
    {   if($v->request->is('androidApp') || $v->request->is('iosApp')){
			$pluginModel = MooCore::getInstance()->getModel('Plugin');
			$plugin = $pluginModel->findByKey('MooApp');
			if(!empty($plugin['Plugin']['version']) && $plugin['Plugin']['version'] != '1.1') {
				return true;
			}
		}
        return false;
    }
}
