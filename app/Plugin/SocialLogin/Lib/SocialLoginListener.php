<?php
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('CakeEventListener', 'Event');

class SocialLoginListener implements CakeEventListener
{

    public function implementedEvents()
    {
            return array(
                'MooView.afterLoadMooCore' => 'afterLoadMooCore',
                'MooView.beforeRenderRequreJsConfig' => 'beforeRenderRequreJsConfig',
                'View.SocialLogin.Elements' => 'renderSoicialLogin',
                'Controller.User.afterLogout' => 'doControllerUserAfterLogout',
                'View.SocialEnable' => 'socialEnable',
            );
    }

    public function afterLoadMooCore($event)
    {             
            $v = $event->subject();
            if(Configure::read('SocialLogin.social_login_enable') && ($v->Moo->socialIntegrationEnable('linkedin') || $v->Moo->socialIntegrationEnable('twitter'))){
                /*
                if(MooCore::getInstance()->isMobile(null)){ 
                    $css = 'SocialLogin.social_login_mobile.css';
                }else{
                    $css = 'SocialLogin.social_login.css';
                }
                 */
                $css = 'SocialLogin.social_login.css';
                $v->Helpers->Html->css(array(
                    $css,
                ),
                    array('block' => 'css')
                );
            }
    }

    public function beforeRenderRequreJsConfig($event)
    {
        $v = $event->subject();
        if(Configure::read('SocialLogin.social_login_enable') && ($v->Moo->socialIntegrationEnable('linkedin') || $v->Moo->socialIntegrationEnable('twitter'))){
            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }

            $v->Helpers->MooRequirejs->addPath(array(
                "mooSocialLogin" => $v->Helpers->MooRequirejs->assetUrlJS("/social_login/js/social_login.{$min}js")
            ));
        }
    }
    
    
    public function renderSoicialLogin($event){
        $view = $event->subject();
        
        if(Configure::read('SocialLogin.social_login_enable') && ($view->Moo->socialIntegrationEnable('linkedin') || $view->Moo->socialIntegrationEnable('twitter'))){
            echo $view->element('SocialLogin.providers');
        }
       
    }

     public function doControllerUserAfterLogout($event)
    {
        if(Configure::read('SocialLogin.social_login_enable')){
            $v = $event->subject();
            if ($v->Session->read('provider_sl')) {
                $provider = $v->Session->read('provider_sl');

                if(isset($_SESSION['HA_STORE_hauth_session'][strtolower($provider)])){
                    unset($_SESSION['HA_STORE_hauth_session'][strtolower($provider)]);                   
                }
             //    debug($_SESSION['HA_STORE_hauth_session']);die();
                $v->Session->delete('provider_sl');
            }
        }
    }
    
    public function socialEnable($event){
        $v = $event->subject();
        $se = false;
        if(Configure::read('SocialLogin.social_login_enable') && ($v->Moo->socialIntegrationEnable('linkedin') || $v->Moo->socialIntegrationEnable('twitter'))){
            $se = true;
        }
        Configure::write('social.social_enable', $se);
    }
    
}
