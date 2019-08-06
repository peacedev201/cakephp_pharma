<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('MooPlugin', 'Lib');

class RoleBadgePlugin implements MooPlugin {

    public function install() {

        // Default Thumbnail
        $this->installThumbnail();

        // Block
        $this->installCoreBlock();

        // Setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('role_badge_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }

        // Permission
        $this->installPermission();
    }

    public function uninstall() {
        
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            __d('role_badge', 'Badges Manager') => array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_index'),
            __d('role_badge', 'Settings') => array('plugin' => 'role_badge', 'controller' => 'role_badge_settings', 'action' => 'admin_index'),
            __d('role_badge', 'Awards Manager') => array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_index'),
        );
    }

    public function callback_1_2() {
        // Permission
        $this->installPermission();

        // Block
        $this->installCoreBlock();

        // Rename 
        $oPluginModel = MooCore::getInstance()->getModel('Plugin');
        $aPlugin = $oPluginModel->findByKey('RoleBadge');
        if ($aPlugin) {
            $oPluginModel->id = $aPlugin['Plugin']['id'];
            $oPluginModel->save(array('name' => 'User Badges'));
        }
    }

    public function callback_1_3() {
        // Load module
        $oRoleBadgeModel = MooCore::getInstance()->getModel('RoleBadge.RoleBadge');
        $aRoleBadges = $oRoleBadgeModel->find('all');
        foreach ($aRoleBadges as $aRoleBadge) {
            $sPathUpload = 'role_badge' . DS . 'img' . DS . 'setting';
            $this->_prepareDir($sPathUpload);

            $aInfo = pathinfo($sPathUpload . DS . $aRoleBadge['RoleBadge']['thumbnail']);
            $sExt = $aInfo['extension'];
            $sDesktopProfile = md5($aRoleBadge['RoleBadge']['thumbnail'] . 'desktop_profile') . '.' . $sExt;
            $sDesktopFeed = md5($aRoleBadge['RoleBadge']['thumbnail'] . 'desktop_feed') . '.' . $sExt;
            $sMobileProfile = md5($aRoleBadge['RoleBadge']['thumbnail'] . 'mobile_profile') . '.' . $sExt;
            $sMobileFeed = md5($aRoleBadge['RoleBadge']['thumbnail'] . 'mobile_feed') . '.' . $sExt;

            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $oPhoto = PhpThumbFactory::create($sPathUpload . DS . $aRoleBadge['RoleBadge']['thumbnail']);
            $oPhoto->resize(9999, 50)->save($sPathUpload . DS . $sMobileProfile);
            $oPhoto->resize(9999, 28)->save($sPathUpload . DS . $sMobileFeed);
            $oPhoto->resize(9999, 26)->save($sPathUpload . DS . $sDesktopProfile);
            $oPhoto->resize(9999, 14)->save($sPathUpload . DS . $sDesktopFeed);

            $oRoleBadgeModel->clear();
            $oRoleBadgeModel->id = $aRoleBadge['RoleBadge']['id'];
            $oRoleBadgeModel->saveField('desktop_profile', $sDesktopProfile);
            $oRoleBadgeModel->saveField('desktop_feed', $sDesktopFeed);
            $oRoleBadgeModel->saveField('mobile_profile', $sMobileProfile);
            $oRoleBadgeModel->saveField('mobile_feed', $sMobileFeed);
        }
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

    protected function installPermission() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        $aRoleIds = array();
        foreach ($aRoles as $aRole) {
            $aRoleIds[] = $aRole['Role']['id'];
            $aParams = array_unique(array_merge(explode(',', $aRole['Role']['params']), array('role_badge_assign_badge')));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $aAcoCreate = $oAcoModel->find('first', array('conditions' => array('group' => 'role_badge', 'key' => 'assign_badge')));
        if (empty($aAcoCreate)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'assign_badge',
                'group' => 'role_badge',
                'description' => 'Assign award badge',
            ));
        }
    }

    protected function installThumbnail() {
        $oModelSetting = MooCore::getInstance()->getModel('Setting');
        $oModelRole = MooCore::getInstance()->getModel('Role');
        $aRoles = $oModelRole->find('all');
        $aValues = array();
        foreach ($aRoles as $aRole) {
            $sThumbnail = 'member_default_60b2b1ba9ad018a5a443f4e68cd2f077.png';
            $sDesktopProfile = 'member_desktop_profile_60b2b1ba9ad018a5a443f4e68cd2f077.png';
            $sDesktopFeed = 'member_desktop_feed_60b2b1ba9ad018a5a443f4e68cd2f077.png';
            $sMobileProfile = 'member_mobile_profile_60b2b1ba9ad018a5a443f4e68cd2f077.png';
            $sMobileFeed = 'member_mobile_feed_60b2b1ba9ad018a5a443f4e68cd2f077.png';

            if ($aRole['Role']['is_admin']) {
                $sThumbnail = 'admin_default_4aff2da9ad018a5a443er546cd2f077.png';
                $sDesktopProfile = 'admin_desktop_profile_4aff2da9ad018a5a443er546cd2f077.png';
                $sDesktopFeed = 'admin_desktop_feed_4aff2da9ad018a5a443er546cd2f077.png';
                $sMobileProfile = 'admin_mobile_profile_4aff2da9ad018a5a443er546cd2f077.png';
                $sMobileFeed = 'admin_mobile_feed_4aff2da9ad018a5a443er546cd2f077.png';
            } else if ($aRole['Role']['id'] == ROLE_GUEST) {
                $sThumbnail = 'guest_default_31732dc2a4aff2da4b8ac9a036f60e77.png';
                $sDesktopProfile = 'guest_desktop_profile_31732dc2a4aff2da4b8ac9a036f60e77.png';
                $sDesktopFeed = 'guest_desktop_feed_31732dc2a4aff2da4b8ac9a036f60e77.png';
                $sMobileProfile = 'guest_mobile_profile_31732dc2a4aff2da4b8ac9a036f60e77.png';
                $sMobileFeed = 'guest_mobile_feed_31732dc2a4aff2da4b8ac9a036f60e77.png';
            }

            $aValues[] = "('{$aRole['Role']['id']}', '{$sThumbnail}', '{$sDesktopProfile}', '{$sDesktopFeed}', '{$sMobileProfile}', '{$sMobileFeed}')";
        }

        $sContentInsert = "INSERT IGNORE INTO `{PREFIX}role_badges` (`role_id`, `thumbnail`, `desktop_profile`, `desktop_feed`, `mobile_profile`, `mobile_feed`) VALUES " . implode(', ', $aValues) . ';';
        $sQuery = str_replace('{PREFIX}', $oModelSetting->tablePrefix, $sContentInsert);
        $oDataSource = ConnectionManager::getDataSource('default');
        $oDataSource->rawQuery($sQuery);
    }

    protected function installCoreBlock() {
        // Load module
        $oModelCoreBlock = MooCore::getInstance()->getModel('CoreBlock');

        // User Roles
        $aBlockUserRole = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'badges.user')));
        if (empty($aBlockUserRole)) {
            $oModelCoreBlock->create();
            $oModelCoreBlock->save(array(
                'name' => 'User Roles',
                'path_view' => 'badges.user',
                'params' => '{"0":{"label":"Title","input":"text","value":"User Roles","name":"title"},"1":{"label":"Number of item to show","input":"text","value":"10","name":"num_item_show"},"2":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"3":{"element":"blocks/roles"},"4":{"label":"plugin","input":"hidden","value":"RoleBadge","name":"plugin"}}',
                'group' => 'role_badge',
                'plugin' => 'RoleBadge',
                'restricted' => '',
            ));
        }

        // Badges
        $aBlockBadges = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'badges.award')));
        if (empty($aBlockBadges)) {
            $oModelCoreBlock->create();
            $oModelCoreBlock->save(array(
                'name' => 'Award Badges',
                'path_view' => 'badges.award',
                'params' => '{"0":{"label":"Title","input":"text","value":"Awards","name":"title"},"1":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"2":{"label":"plugin","input":"hidden","value":"RoleBadge","name":"plugin"}}',
                'group' => 'role_badge',
                'plugin' => 'RoleBadge',
                'restricted' => '',
            ));
        }
    }

}
