<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('MooPlugin', 'Lib');

class QuizPlugin implements MooPlugin {

    public function install() {

        // Permission + Menu +
        $this->installPermissionMenu();

        // Block
        $this->installCoreBlock();

        // Page
        $this->installBrowsePage();
        $this->installDetailPage();

        // Category
        $this->installCategory();

        // Mail
        $this->installMail();

        // Setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('quiz_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }
        
        // Pass force login
        $aSettingForce = $oSettingModel->findByName('quiz_consider_force');
        if ($aSettingForce) {
            $oSettingModel->id = $aSettingForce['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }
        
    }

    public function uninstall() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        foreach ($aRoles as $aRole) {
            $aParams = array_diff(explode(',', $aRole['Role']['params']), array('quiz_create', 'quiz_view'));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        // Menu
        $oMenuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $aMenu = $oMenuModel->findByUrl('/quizzes');
        if (!empty($aMenu)) {
            $oMenuModel->delete($aMenu['CoreMenuItem']['id']);
        }

        // Delete S3
        $oStorageModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
        $oStorageModel->deleteAll(array('StorageAwsObjectMap.type' => 'quizzes'), false, false);
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            __d('quiz', 'General') => array('plugin' => 'quiz', 'controller' => 'quiz_plugins', 'action' => 'admin_index'),
            __d('quiz', 'Settings') => array('plugin' => 'quiz', 'controller' => 'quiz_settings', 'action' => 'admin_index'),
            __d('quiz', 'Categories') => array('plugin' => 'quiz', 'controller' => 'quiz_categories', 'action' => 'admin_index'),
        );
    }

    public function callback_1_2() {
        // Add More Setting
        $this->callback_reInitSetting();
        
        // Pass force login
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSettingForce = $oSettingModel->findByName('quiz_consider_force');
        if ($aSettingForce) {
            $oSettingModel->id = $aSettingForce['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }
    }

    protected function installPermissionMenu() {
        // Permission
        $oRoleModel = MooCore::getInstance()->getModel('Role');
        $aRoles = $oRoleModel->find('all');
        $aRoleIds = array();
        foreach ($aRoles as $aRole) {
            $aRoleIds[] = $aRole['Role']['id'];
            $aParams = array_unique(array_merge(explode(',', $aRole['Role']['params']), array('quiz_create', 'quiz_view')));
            $oRoleModel->id = $aRole['Role']['id'];
            $oRoleModel->save(array('params' => implode(',', $aParams)));
        }

        $oAcoModel = MooCore::getInstance()->getModel('Aco');
        $aAcoCreate = $oAcoModel->find('first', array('conditions' => array('group' => 'quiz', 'key' => 'create')));
        if (empty($aAcoCreate)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'create',
                'group' => 'quiz',
                'description' => 'Create/Edit Quiz',
            ));
        }

        $aAcoView = $oAcoModel->find('first', array('conditions' => array('group' => 'quiz', 'key' => 'view')));
        if (empty($aAcoView)) {
            $oAcoModel->create();
            $oAcoModel->save(array(
                'key' => 'view',
                'group' => 'quiz',
                'description' => 'View Quiz',
            ));
        }

        // Menu
        $oMenuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $aMenu = $oMenuModel->findByUrl('/quizzes');
        if (empty($aMenu)) {
            $oMenuModel->create();
            $oMenuModel->save(array(
                'role_access' => json_encode($aRoleIds),
                'name' => 'Quizzes',
                'original_name' => 'Quizzes',
                'url' => '/quizzes',
                'type' => 'plugin',
                'is_active' => 1,
                'menu_order' => 999,
                'menu_id' => 1
            ));

            $oModelLanguage = MooCore::getInstance()->getModel('Language');
            $iCoreMenuItemId = $oMenuModel->id;
            
            $aLangs = $oModelLanguage->getLanguages();
            foreach (array_keys($aLangs) as $sKey) {
                $oMenuModel->id = $iCoreMenuItemId;
                $oMenuModel->locale = $sKey;
                $oMenuModel->saveField('name', 'Quizzes');
            }
        }
    }

    protected function installCoreBlock() {
        // Load module
        $oModelCoreBlock = MooCore::getInstance()->getModel('CoreBlock');

        // Popular
        $aBlockPopular = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'quizzes.popular')));
        if (empty($aBlockPopular)) {
            $oModelCoreBlock->create();
            $oModelCoreBlock->save(array(
                'name' => 'Popular Quizzes',
                'path_view' => 'quizzes.popular',
                'params' => '{"0":{"label":"Title","input":"text","value":"Popular Quizzes","name":"title"},"1":{"label":"Number of item to show","input":"text","value":"5","name":"num_item_show"},"2":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"3":{"label":"plugin","input":"hidden","value":"Quiz","name":"plugin"}}',
                'group' => 'quiz',
                'plugin' => 'Quiz',
                'restricted' => '',
            ));
        }

        // Most Taken
        $aBlockMostTaken = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'quizzes.mostTaken')));
        if (empty($aBlockMostTaken)) {
            $oModelCoreBlock->create();
            $oModelCoreBlock->save(array(
                'name' => 'Most Taken Quizzes',
                'path_view' => 'quizzes.mostTaken',
                'params' => '{"0":{"label":"Title","input":"text","value":"Most Taken Quizzes","name":"title"},"1":{"label":"Number of item to show","input":"text","value":"5","name":"num_item_show"},"2":{"label":"Title","input":"checkbox","value":"Enable Title","name":"title_enable"},"3":{"label":"plugin","input":"hidden","value":"Quiz","name":"plugin"}}',
                'group' => 'quiz',
                'plugin' => 'Quiz',
                'restricted' => '',
            ));
        }
    }

    protected function installBrowsePage() {
        // Load module
        $oModelPage = MooCore::getInstance()->getModel('Page.Page');
        $oModelLanguage = MooCore::getInstance()->getModel('Language');
        $oModelCoreBlock = MooCore::getInstance()->getModel('CoreBlock');
        $oModelCoreContent = MooCore::getInstance()->getModel('CoreContent');

        $aPage = $oModelPage->find('first', array('conditions' => array('alias' => 'quizzes_index')));
        if (empty($aPage)) {
            $oModelPage->create();
            $oModelPage->save(array(
                'title' => 'Quizzes Browse Page',
                'alias' => 'quizzes_index',
                'params' => '',
                'content' => '',
                'keywords' => '',
                'permission' => '',
                'description' => '',
                'url' => '/quizzes',
                'uri' => 'quizzes.index',
                'core_content_count' => 8,
                'type' => 'plugin',
                'layout' => 1,
                'custom' => 1
            ));
            $iPageId = $oModelPage->id;

            /* Container and content page */
            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'page_id' => $iPageId,
                'type' => 'container',
                'name' => 'center'
            ));
            $iParentId = $oModelCoreContent->id;

            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'type' => 'widget',
                'page_id' => $iPageId,
                'name' => 'invisiblecontent',
                'parent_id' => $iParentId,
                'params' => '{"title":"Quizzes Browse", "maincontent":"1"}',
                'core_block_title' => 'Quizzes Browse',
                'core_block_id' => 0,
                'plugin' => 'Quiz',
                'order' => 1
            ));
            $iBrowseCenterId = $oModelCoreContent->id;
            /* Container and content page */

            /* Column west and block */
            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'page_id' => $iPageId,
                'type' => 'container',
                'name' => 'west'
            ));
            $iParentWestId = $oModelCoreContent->id;

            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'type' => 'widget',
                'page_id' => $iPageId,
                'name' => 'invisiblecontent',
                'parent_id' => $iParentWestId,
                'params' => '{"title":"Quiz Menu & Search", "maincontent":"1"}',
                'core_block_title' => 'Quiz Menu & Search',
                'core_block_id' => 0,
                'plugin' => 'Quiz',
                'order' => 1
            ));
            $iMenuSearchWestId = $oModelCoreContent->id;

            $aBlockTags = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'core.tags')));
            if (!empty($aBlockTags)) {
                $iBlockTagsId = $aBlockTags['CoreBlock']['id'];

                $oModelCoreContent->create();
                $oModelCoreContent->save(array(
                    'type' => 'widget',
                    'page_id' => $iPageId,
                    'name' => 'core.tags',
                    'parent_id' => $iParentWestId,
                    'params' => '{"title":"Tags","num_item_show":"10","type":"quizzes","order_by":"newest","title_enable":"1"}',
                    'core_block_title' => 'Tags',
                    'core_block_id' => $iBlockTagsId,
                    'plugin' => '',
                    'order' => 2
                ));
                $iTagsWestId = $oModelCoreContent->id;
            }
            /* Column west and block */

            /* Column east and block */
            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'page_id' => $iPageId,
                'type' => 'container',
                'name' => 'east'
            ));
            $iParentEastId = $oModelCoreContent->id;

            $aBlockPopular = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'quizzes.popular')));
            if (!empty($aBlockPopular)) {
                $iBlockPopularId = $aBlockPopular['CoreBlock']['id'];

                $oModelCoreContent->create();
                $oModelCoreContent->save(array(
                    'type' => 'widget',
                    'page_id' => $iPageId,
                    'name' => 'quizzes.popular',
                    'parent_id' => $iParentEastId,
                    'params' => '{"title":"Popular Quizzes","num_item_show":"5","title_enable":"1","plugin":"Quiz"}',
                    'core_block_title' => 'Popular Quizzes',
                    'core_block_id' => $iBlockPopularId,
                    'plugin' => 'Quiz',
                    'order' => 1
                ));
                $iPopularEastId = $oModelCoreContent->id;
            }

            $aBlockMostTaken = $oModelCoreBlock->find('first', array('conditions' => array('path_view' => 'quizzes.mostTaken')));
            if (!empty($aBlockMostTaken)) {
                $iBlockMostTakenId = $aBlockMostTaken['CoreBlock']['id'];

                $oModelCoreContent->create();
                $oModelCoreContent->save(array(
                    'type' => 'widget',
                    'page_id' => $iPageId,
                    'name' => 'quizzes.mostTaken',
                    'parent_id' => $iParentEastId,
                    'params' => '{"title":"Most Taken Quizzes","num_item_show":"5","title_enable":"1","plugin":"Quiz"}',
                    'core_block_title' => 'Most Taken Quizzes',
                    'core_block_id' => $iBlockMostTakenId,
                    'plugin' => 'Quiz',
                    'order' => 2
                ));
                $iMostTakenEastId = $oModelCoreContent->id;
            }
            /* Column east and block */

            /* Add languages */
            $aLangs = $oModelLanguage->getLanguages();
            foreach (array_keys($aLangs) as $sKey) {
                $oModelPage->id = $iPageId;
                $oModelPage->locale = $sKey;
                $oModelPage->saveField('content', '');
                $oModelPage->saveField('title', 'Quizzes Browse Page');
                
                $oModelCoreContent->id = $iParentId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', '');

                $oModelCoreContent->id = $iBrowseCenterId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', 'Quizzes Browse');

                $oModelCoreContent->id = $iParentWestId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', '');

                $oModelCoreContent->id = $iMenuSearchWestId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', 'Quiz Menu & Search');

                $oModelCoreContent->id = $iParentEastId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', '');

                if ($iTagsWestId) {
                    $oModelCoreContent->id = $iTagsWestId;
                    $oModelCoreContent->locale = $sKey;
                    $oModelCoreContent->saveField('core_block_title', 'Tags');
                }

                if ($iPopularEastId) {
                    $oModelCoreContent->id = $iPopularEastId;
                    $oModelCoreContent->locale = $sKey;
                    $oModelCoreContent->saveField('core_block_title', 'Popular Quizzes');
                }

                if ($iMostTakenEastId) {
                    $oModelCoreContent->id = $iMostTakenEastId;
                    $oModelCoreContent->locale = $sKey;
                    $oModelCoreContent->saveField('core_block_title', 'Most Taken Quizzes');
                }
            }
            /* Add languages */
        }
    }

    protected function installDetailPage() {
        // Load module
        $oModelPage = MooCore::getInstance()->getModel('Page.Page');
        $oModelLanguage = MooCore::getInstance()->getModel('Language');
        $oModelCoreContent = MooCore::getInstance()->getModel('CoreContent');

        $aPage = $oModelPage->find('first', array('conditions' => array('alias' => 'quizzes_view')));
        if (empty($aPage)) {
            $oModelPage->create();
            $oModelPage->save(array(
                'title' => 'Quizzes Detail Page',
                'alias' => 'quizzes_view',
                'params' => '',
                'content' => '',
                'keywords' => '',
                'permission' => '',
                'description' => '',
                'url' => '/quizzes/view/$id/{quiz\'s name}',
                'uri' => 'quizzes.view',
                'core_content_count' => 5,
                'type' => 'plugin',
                'layout' => 2,
                'custom' => 1
            ));
            $iPageId = $oModelPage->id;

            /* Container and content page */
            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'page_id' => $iPageId,
                'type' => 'container',
                'name' => 'center'
            ));
            $iParentId = $oModelCoreContent->id;

            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'type' => 'widget',
                'page_id' => $iPageId,
                'name' => 'invisiblecontent',
                'parent_id' => $iParentId,
                'params' => '{"title":"Quiz\'s Content","maincontent":"1"}',
                'core_block_title' => 'Quiz\'s Content',
                'core_block_id' => 0,
                'plugin' => 'Quiz',
                'order' => 1
            ));
            $iContentCenterId = $oModelCoreContent->id;
            /* Container and content page */

            /* Column west and block */
            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'page_id' => $iPageId,
                'type' => 'container',
                'name' => 'west'
            ));
            $iParentWestId = $oModelCoreContent->id;

            $oModelCoreContent->create();
            $oModelCoreContent->save(array(
                'type' => 'widget',
                'page_id' => $iPageId,
                'name' => 'invisiblecontent',
                'parent_id' => $iParentWestId,
                'params' => '{"title":"Quiz\'s Setting", "maincontent":"1"}',
                'core_block_title' => 'Quiz\'s Setting',
                'core_block_id' => 0,
                'plugin' => 'Quiz',
                'order' => 1
            ));
            $iSettingWestId = $oModelCoreContent->id;
            /* Column west and block */

            /* Add languages */
            $aLangs = $oModelLanguage->getLanguages();
            foreach (array_keys($aLangs) as $sKey) {
                $oModelPage->id = $iPageId;
                $oModelPage->locale = $sKey;
                $oModelPage->saveField('content', '');
                $oModelPage->saveField('title', 'Quizzes Detail Page');
                
                $oModelCoreContent->id = $iParentId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', '');

                $oModelCoreContent->id = $iContentCenterId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', 'Quiz\'s Content');

                $oModelCoreContent->id = $iParentWestId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', '');

                $oModelCoreContent->id = $iSettingWestId;
                $oModelCoreContent->locale = $sKey;
                $oModelCoreContent->saveField('core_block_title', 'Quiz\'s Setting');
            }
            /* Add languages */
        }
    }

    protected function installCategory() {
        $oCategoryModel = MooCore::getInstance()->getModel('Category');
        $oModelLanguage = MooCore::getInstance()->getModel('Language');
        $oCategoryModel->create();
        $oCategoryModel->save(array(
            'type' => 'Quiz_Quiz',
            'name' => 'Default Category',
            'active' => 1
        ));

        $iCategoryId = $oCategoryModel->id;
        $aLangs = $oModelLanguage->getLanguages();
        foreach (array_keys($aLangs) as $sKey) {
            $oCategoryModel->id = $iCategoryId;
            $oCategoryModel->locale = $sKey;
            $oCategoryModel->saveField('name', 'Default Category');
        }
    }

    protected function installMail() {
        //Mail template
        $oModelMailtemplate = MooCore::getInstance()->getModel('Mail.Mailtemplate');

        $aMailtemplate = $oModelMailtemplate->findByType('quiz_approve');
        if (empty($aMailtemplate)) {
            $aDataApprove['Mailtemplate'] = array(
                'type' => 'quiz_approve',
                'plugin' => 'Quiz',
                'vars' => '[quiz_title],[quiz_link]'
            );
            $oModelMailtemplate->save($aDataApprove);

            $oModelLanguage = MooCore::getInstance()->getModel('Language');
            $aLangs = $oModelLanguage->find('all');
            foreach ($aLangs as $aLang) {
                $sLocale = $aLang['Language']['key'];
                $oModelMailtemplate->locale = $sLocale;
                $aApprovedTranslate['subject'] = 'Your quiz has been approved.';
                $sApprovedContent = <<<EOF
                <p>[header]</p>
                <p>Your quiz has been approved.</p>
                <p>Quiz detail: <a href="[quiz_title]">[quiz_link]</a></p>
                <p>[footer]</p>
EOF;
                $aApprovedTranslate['content'] = $sApprovedContent;
                $oModelMailtemplate->save($aApprovedTranslate);
            }
        }
    }

    protected function callback_reInitSetting() {
        // Setting xml
        $xmlPath = sprintf(PLUGIN_INFO_PATH, 'Quiz');
        if (file_exists($xmlPath)) {
            $sContent = file_get_contents($xmlPath);
            $sInfo = new SimpleXMLElement($sContent);

            $oSettingModel = MooCore::getInstance()->getModel('Setting');
            $oSettingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
            $aSettingGroup = $oSettingGroupModel->find('first', array('conditions' => array('group_type' => 'Quiz', 'module_id' => 'Quiz'), 'fields' => array('id')));

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
