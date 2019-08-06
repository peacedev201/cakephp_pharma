<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('MooPlugin', 'Lib');

class VerifyProfilePlugin implements MooPlugin {

    public function install() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        $aRoleIds = array();
        foreach ($aRoles as $aRole) {
            $aRoleIds[] = $aRole['Role']['id'];
            $aParams = array_unique(array_merge(explode(',', $aRole['Role']['params']), array('verify_profile_verify')));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $aAcoCreate = $oAcoModel->find('first', array('conditions' => array('group' => 'verify_profile', 'key' => 'verify')));
        if (empty($aAcoCreate)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'verify',
                'group' => 'verify_profile',
                'description' => 'Send verify profile',
            ));
        }
        
        // Mail Template
        $this->installMailtemplate();

        // Setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('verify_profile_enable');
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
            $aParams = array_diff(explode(',', $aRole['Role']['params']), array('verify_profile_verify'));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        // Mail Template
        $oModelMailtemplate = MooCore::getInstance()->getModel('Mail.Mailtemplate');

        // Verified
        $aMailVerified = $oModelMailtemplate->find('first', array('conditions' => array('type' => 'verify_profile_verified', 'plugin' => 'VerifyProfile')));
        if ($aMailVerified) {
            $oModelMailtemplate->delete($aMailVerified['Mailtemplate']['id']);
        }

        // Unverified
        $aMailUnverified = $oModelMailtemplate->find('first', array('conditions' => array('type' => 'verify_profile_unverified', 'plugin' => 'VerifyProfile')));
        if ($aMailUnverified) {
            $oModelMailtemplate->delete($aMailUnverified['Mailtemplate']['id']);
        }
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            __d('verify_profile', 'General') => array('plugin' => 'verify_profile', 'controller' => 'verify_profile_plugins', 'action' => 'admin_index'),
            __d('verify_profile', 'Settings') => array('plugin' => 'verify_profile', 'controller' => 'verify_profile_settings', 'action' => 'admin_index'),
            __d('verify_profile', 'Reasons') => array('plugin' => 'verify_profile', 'controller' => 'verify_profile_reasons', 'action' => 'admin_index'),
        );
    }

    public function callback_1_1() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        $aRoleIds = array();
        foreach ($aRoles as $aRole) {
            $aRoleIds[] = $aRole['Role']['id'];
            $aParams = array_unique(array_merge(explode(',', $aRole['Role']['params']), array('verify_profile_verify')));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $aAcoCreate = $oAcoModel->find('first', array('conditions' => array('group' => 'verify_profile', 'key' => 'verify')));
        if (empty($aAcoCreate)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'verify',
                'group' => 'verify_profile',
                'description' => 'Send verify profile',
            ));
        }

        // Setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('verify_profile_enable');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('type_id' => 'radio', 'value_actual' => '[{"name":"Disable","value":"0","select":0},{"name":"Enable","value":"1","select":1}]', 'value_default' => '[{"name":"Disable","value":"0","select":0},{"name":"Enable","value":"1","select":1}]', 'is_boot' => 1));
        }
    }

    public function callback_1_4() {
        // Add More Setting
        $this->callback_reInitSetting();
    }

    public function callback_1_6() {
        // Add More Setting
        $this->callback_reInitSetting();
        
        // Mail Template
        $this->installMailtemplate();
    }

    protected function installMailtemplate() {
        $oModelMailtemplate = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $oModelLanguage = MooCore::getInstance()->getModel('Language');
        $aLangs = $oModelLanguage->find('all');

        // Verified
        $aVerifiedCreate = $oModelMailtemplate->find('first', array('conditions' => array('type' => 'verify_profile_verified', 'plugin' => 'VerifyProfile')));
        if (empty($aVerifiedCreate)) {
            $oModelMailtemplate->create();
            $oModelMailtemplate->save(array(
                'type' => 'verify_profile_verified',
                'plugin' => 'VerifyProfile',
                'vars' => '[recipient_title],[recipient_link]'
            ));

            foreach ($aLangs as $aLang) {
                $sLocale = $aLang['Language']['key'];
                $oModelMailtemplate->locale = $sLocale;

                // Content
                $aVerifiedTranslate['subject'] = 'Your profile has been verified.';
                $sVerifiedContent = <<<EOF
<p>[header]</p>
<p>Your profile has been verified. Please check your profile for verification status.</p>
<p>Link detail: <a href="[recipient_link]">[recipient_title]</a></p>
<p>[footer]</p>
EOF;
                $aVerifiedTranslate['content'] = $sVerifiedContent;
                $oModelMailtemplate->save($aVerifiedTranslate);
            }
        }
        // End Verified
        // Unverified
        $aUnverifiedCreate = $oModelMailtemplate->find('first', array('conditions' => array('type' => 'verify_profile_unverified', 'plugin' => 'VerifyProfile')));
        if (empty($aUnverifiedCreate)) {
            $oModelMailtemplate->create();
            $oModelMailtemplate->save(array(
                'type' => 'verify_profile_unverified',
                'plugin' => 'VerifyProfile',
                'vars' => '[recipient_status],[recipient_reason],[recipient_title],[recipient_link]'
            ));

            foreach ($aLangs as $aLang) {
                $sLocale = $aLang['Language']['key'];
                $oModelMailtemplate->locale = $sLocale;

                // unverify
                $aUnverifiedTranslate['subject'] = 'Your profile has been [recipient_status].';
                $sUnverifiedContent = <<<EOF
<p>[header]</p>
<p>Your supplied document has been [recipient_status] because of the following reason(s):</p>
<p>[recipient_reason]</p>
<p>Link detail: <a href="[recipient_link]">[recipient_title]</a></p>
<p>For further information, please contact Site Admin. Thanks!</p>
<p>[footer]</p>
EOF;
                $aUnverifiedTranslate['content'] = $sUnverifiedContent;
                $oModelMailtemplate->save($aUnverifiedTranslate);
            }
        }
        // End Unverified
    }
    
    protected function callback_reInitSetting(){
        // Setting xml
        $xmlPath = sprintf(PLUGIN_INFO_PATH, 'VerifyProfile');
        if (file_exists($xmlPath)) {
            $sContent = file_get_contents($xmlPath);
            $sInfo = new SimpleXMLElement($sContent);

            $oSettingModel = MooCore::getInstance()->getModel('Setting');
            $oSettingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
            $aSettingGroup = $oSettingGroupModel->find('first', array('conditions' => array('group_type' => 'VerifyProfile', 'module_id' => 'VerifyProfile'), 'fields' => array('id')));

            $aSettings = array();
            if (!empty($sInfo->settings)) {
                $aDatas = json_decode(json_encode($sInfo->settings), true);
                foreach ($aDatas['setting'] as $aData) {
                    if (!$oSettingModel->isSettingNameExist($aData['name'])) {

                        $oSettingModel->create();
                        $sValues = $aData['values'];
                        if ($aData['type'] == 'radio' || $aData['type'] == 'checkbox' || $aData['type'] == 'select') {
                            //Fix install with checkbox one value
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
                            'name' => (String) $aData['name'],
                            'label' => (String) $aData['label'],
                            'type_id' => (String) $aData['type'],
                            'description' => $aData['description'],
                            'group_id' => $aSettingGroup['SettingGroup']['id'],
                            'ordering' => $oSettingModel->generateOrdering($aSettingGroup['SettingGroup']['id'])
                        );

                        $oSettingModel->save($aSettings);
                    }
                }
            }
        }
    }

}
