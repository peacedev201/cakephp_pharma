<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('MooPlugin', 'Lib');

class MooinsidePlugin implements MooPlugin {

    public function install() {
        
    }

    public function uninstall() {
        
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            'Settings' => array('plugin' => 'mooinside', 'controller' => 'mooinsides', 'action' => 'admin_index'),
            //'Settings' => array('plugin' => 'mooinside', 'controller' => 'mooinside_settings', 'action' => 'admin_index'),
        );
    }

    /*
      Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
      public function callback_1_0(){}
     */
}
