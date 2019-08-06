<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('MooPlugin', 'Lib');

class UploadVideoPlugin implements MooPlugin {

    public function install() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        $aRoleIds = array();
        foreach ($aRoles as $aRole) {
            $aRoleIds[] = $aRole['Role']['id'];
            $aParams = array_unique(array_merge(explode(',', $aRole['Role']['params']), array('video_upload')));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $aAcoCreate = $oAcoModel->find('first', array('conditions' => array('group' => 'video', 'key' => 'upload')));
        if (empty($aAcoCreate)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'group' => 'video',
                'key' => 'upload',
                'description' => 'Upload Video',
            ));
        }

        // Setting is boot
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('uploadvideo_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }
    }

    public function uninstall() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        foreach ($aRoles as $aRole) {
            $aParams = array_diff(explode(',', $aRole['Role']['params']), array('video_upload'));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }
        
        // Cheat Save settings.php 2.6.1
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('uploadvideo_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 0));
        }
        
        // Remove key not use
        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $oAcoModel->deleteAll(array('group' => 'upload', 'key' => 'video'));
        
        // drop and clear cache check_thumb_vimeo
        $oVideoModel = MooCore::getInstance()->getModel('Video.Video');
        $oVideoModel->getDataSource()->flushMethodCache();
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            __('General') => array('plugin' => 'upload_video', 'controller' => 'upload_videos', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'upload_video', 'controller' => 'upload_video_settings', 'action' => 'admin_index'),
            __d('upload_video', 'Limitation') => array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_index'),
        );
    }

    public function callback_1_1() {
        // Get Group Setting Id
        $iGroupSettingId = $this->getSettingGroupId();
        
        // Add More Setting
        $this->callback_reInitSetting($iGroupSettingId);
    }

    public function callback_1_3() {
        // Get Group Setting Id
        $iGroupSettingId = $this->getSettingGroupId();
        
        // Add More Setting
        $this->callback_reInitSetting($iGroupSettingId);
    }

    public function callback_1_5() {
        // Get Group Setting Id
        $iGroupSettingId = $this->getSettingGroupId();
        
        // Remmove setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $oSettingModel->deleteAll(array('Setting.name' => 'video_setting_params_ffmpeg_path', 'Setting.group_id' => $iGroupSettingId));

        // Add More Setting
        $this->callback_reInitSetting($iGroupSettingId);
    }

    public function callback_1_6() {
        // Get Group Setting Id
        $iGroupSettingId = $this->getSettingGroupId();
        
        // Remmove setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $oSettingModel->deleteAll(array('Setting.name' => 'video_setting_lib_converting', 'Setting.group_id' => $iGroupSettingId));
        
        // Setting is boot
        $aSetting = $oSettingModel->findByName('uploadvideo_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }

        // Add More Setting
        $this->callback_reInitSetting($iGroupSettingId);
    }
    
    public function callback_1_7() {
        // Get Group Setting Id
        $iGroupSettingId = $this->getSettingGroupId();
        
        // Remmove setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $oSettingModel->deleteAll(array('Setting.name' => 'video_setting_params_ffmpeg_mp4', 'Setting.group_id' => $iGroupSettingId));
        
        // Setting is boot
        $aSetting = $oSettingModel->findByName('uploadvideo_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }

        // Add More Setting
        $this->callback_reInitSetting($iGroupSettingId);
    }

    protected function getSettingGroupId() {
        $oSettingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
        $aSettingGroup = $oSettingGroupModel->find('first', array('conditions' => array('group_type' => 'UploadVideo', 'module_id' => 'UploadVideo'), 'fields' => array('id')));
        
        return $aSettingGroup['SettingGroup']['id'];
    }

    protected function callback_reInitSetting($iGroupSettingId) {
        // Setting xml
        $xmlPath = sprintf(PLUGIN_INFO_PATH, 'UploadVideo');
        if (file_exists($xmlPath)) {
            $sContent = file_get_contents($xmlPath);
            $sInfo = new SimpleXMLElement($sContent);
            $oSettingModel = MooCore::getInstance()->getModel('Setting');


            $aSettings = array();
            if (!empty($sInfo->settings)) {
                $aDatas = json_decode(json_encode($sInfo->settings), true);
                foreach ($aDatas['setting'] as $aData) {
                    if (!$oSettingModel->isSettingNameExist($aData['name'])) {
                        $oSettingModel->create();
                        $sValues = $aData['values'];
                        if ($aData['type'] == 'radio' || $aData['type'] == 'checkbox' || $aData['type'] == 'select') {
                            if (isset($aData['values']['value']['name'])) {
                                if (!$aData['values']['value']['name']) {
                                    $aData['values']['value']['name'] = '';
                                }
                                $aData['values']['value'] = array($aData['values']['value']);
                            }
                            $sValues = json_encode($aData['values']['value']);
                        }

                        $aSettings = array(
                            'value_actual' => $sValues,
                            'value_default' => $sValues,
                            'group_id' => $iGroupSettingId,
                            'name' => (String) $aData['name'],
                            'label' => (String) $aData['label'],
                            'type_id' => (String) $aData['type'],
                            'description' => $aData['description'],
                            'ordering' => $oSettingModel->generateOrdering($iGroupSettingId)
                        );

                        $oSettingModel->save($aSettings);
                    }
                }
            }
        }
    }

}
