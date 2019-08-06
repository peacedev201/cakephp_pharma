<?php
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

App::uses('Helper', 'View');


class ChatGzipHelper extends Helper {
    public $helpers = array('Html');
    public $isLoadedManifest = false;
    public function script($name=array()){
        if($this->Html->isActiveRequirejs ){
            $this->Html->isActiveRequirejs = false;
        }
        $gzip_hash = Cache::read('gzip_hash');
        if (!$gzip_hash) {
            $gzip_hash = md5(uniqid(rand(), true));
            Cache::write('gzip_hash', $gzip_hash);
        }
        if(!$this->isLoadedManifest){
            $this->isLoadedManifest = true;
            //$this->Html->script(array('Chat.manifest.bundle.js'."?$gzip_hash"), array('block' => 'mooChatScript'));
        }
        if(!empty($name)){
            $gzipVendor = (Configure::read('debug')== 0)?true:false;
            if($gzipVendor){
               /* $this->Html->script(Router::url(array(
                    'plugin' => 'chat',
                    'controller' => 'gzip',
                    'action' => 'chunk',
                    'vendor.bundle.js.gz'."?$gzip_hash"),true), array('block' => 'mooChatScript'));
               */
               if(isset($name['zip'])){
                   /* Maybe a bug with router then  this function can not render the link : ../chats/chunk/video-calling.js.gz instead of
                   chat/chat_gzip/chunk/video-calling.js.gz
                    $this->Html->script(Router::url(array(
                        'plugin' => 'chat',
                        'controller' => 'chat_gzip',
                        'action' => 'chunk',
                        $name['zip']."?$gzip_hash"),false), array('block' => 'mooChatScript'));
                   */
                   $this->Html->script('/chats/chunk/'.$name['zip']."?$gzip_hash", array('block' => 'mooChatScript'));
                }

            }else{
                //$this->Html->script(array('MooApp.vendor.bundle'), array('block' => 'mooChatScript'));
                if(isset($name['unzip'])){
                    $this->Html->script(array($name['unzip'].".js?$gzip_hash"), array('block' => 'mooChatScript'));
                }

            }
        }

    }


}
