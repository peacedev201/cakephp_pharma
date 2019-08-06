<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('MooPlugin', 'Lib');

class ReviewPlugin implements MooPlugin {

    public function install() {

        // Permission
        $this->installPermission();

        // Block
        $this->installCoreBlock();

        // Setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('review_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }
    }
    
    public function callback_1_1() {

        // Permission
        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $aAcoProfileOption = $oAcoModel->find('first', array('conditions' => array('group' => 'review', 'key' => 'profile_option')));
        if (empty($aAcoProfileOption)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'profile_option',
                'group' => 'review',
                'description' => "Can't disable review option on profile page",
            ));
        }
        
        $aAcoReceive = $oAcoModel->find('first', array('conditions' => array('group' => 'review', 'key' => 'recieve')));
        if (!empty($aAcoReceive)) {
            $oAcoModel->id = $aAcoReceive['Aco']['id'];
            $oAcoModel->save(array('description' => 'Can receive review'));
        }
    }

    public function uninstall() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        foreach ($aRoles as $aRole) {
            $aParams = array_diff(explode(',', $aRole['Role']['params']), array('review_write', 'review_recieve'));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            __d('review', 'Settings') => array('plugin' => 'review', 'controller' => 'review_settings', 'action' => 'admin_index'),
        );
    }

    protected function installPermission() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        $aRoleIds = array();
        foreach ($aRoles as $aRole) {
            $aRoleIds[] = $aRole['Role']['id'];
            $aParams = array_unique(array_merge(explode(',', $aRole['Role']['params']), array('review_write', 'review_recieve')));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $aAcoWrite = $oAcoModel->find('first', array('conditions' => array('group' => 'review', 'key' => 'write')));
        if (empty($aAcoWrite)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'write',
                'group' => 'review',
                'description' => 'Can write review for other profiles',
            ));
        }

        $aAcoReceive = $oAcoModel->find('first', array('conditions' => array('group' => 'review', 'key' => 'recieve')));
        if (empty($aAcoReceive)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'recieve',
                'group' => 'review',
                'description' => 'Can receive review',
            ));
        }
        
        $aAcoProfileOption = $oAcoModel->find('first', array('conditions' => array('group' => 'review', 'key' => 'profile_option')));
        if (empty($aAcoProfileOption)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'profile_option',
                'group' => 'review',
                'description' => "Can't disable review option on profile page",
            ));
        }
    }

    protected function installCoreBlock() {
        // Load module
        $oModelCoreBlock = MooCore::getInstance()->getModel('CoreBlock');

        // Reviews & Ratings
        $aBlockReview = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'reviews.profile')));
        if (empty($aBlockReview)) {
            $oModelCoreBlock->create();
            $oModelCoreBlock->save(array(
                'name' => 'Reviews & Ratings',
                'path_view' => 'reviews.profile',
                'params' => '{"0":{"label":"Title","input":"text","value":"Reviews & Ratings","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"Review","name":"plugin"}}',
                'group' => 'review',
                'plugin' => 'Review',
                'restricted' => '',
            ));
        }
    }

}
