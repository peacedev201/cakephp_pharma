<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('CakeEventListener', 'Event');

class RoleBadgeListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'Model.beforeDelete' => 'beforeDelete',
            'MooView.beforeRender' => 'beforeRender',
            'profile.afterRenderMenu' => 'profileAfterRenderMenu',
            'View.Activities.ajaxLoadTooltip.loadItemInfo' => 'loadItemInfo',
            'View.Elements.User.headerProfile.beforeRenderSectionMenu' => 'beforeRenderSectionMenu',
            // implement priority
            'element.activities.afterRenderUserNameFeed' => array('callable' => 'afterRenderUserNameFeed', 'priority' => 1),
            'element.comments.afterRenderUserNameComment' => array('callable' => 'afterRenderUserNameFeed', 'priority' => 1),
            'element.activities.afterRenderUserNameComment' => array('callable' => 'afterRenderUserNameFeed', 'priority' => 1),
            // version moo-301
            'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu',
            'View.Mooapp.users.view.beforeRenderProfileName' => 'afterRenderProfileNameOnApp',
            'ApiHelper.afterRenderActorHtml' => array('callable' => 'afterRenderUserNameFeedOnApp', 'priority' => 1),
            'ApiHelper.afterRenderFirstCommentUserName' => array('callable' => 'afterRenderUserNameFeedOnApp', 'priority' => 1),
            'Api.View.ApiComment.beforeRenderUserNameHtml' => array('callable' => 'afterRenderUserNameFeedOnApp', 'priority' => 1),
        );
    }

    public function apiAfterRenderMenu($oEvent) {
        $aCUser = MooCore::getInstance()->getViewer();
        $aSUser = MooCore::getInstance()->getSubject();
        $oAwardUserModel = MooCore::getInstance()->getModel('RoleBadge.AwardUser');

        $bShowAssign = true;
        $aCurrentUserAcos = explode(',', $aCUser['Role']['params']);
        $aUserBadges = $oAwardUserModel->getProfileBadges($aSUser['User']['id']);
        if (empty($aSUser) || empty($aCUser) || (!in_array('role_badge_assign_badge', $aCurrentUserAcos) && !$aCUser['Role']['is_admin']) || (!empty($aCUser) && $aCUser['User']['id'] == $aSUser['User']['id'])) {
            $bShowAssign = false;
        }

        if (!empty($bShowAssign) || !empty($aUserBadges)) {
            $oEvent->data['result']['role_badge'] = array(
                'url' => FULL_BASE_URL . $oEvent->subject()->request->base . '/awards/profile/' . $aSUser['User']['id'] . '/user',
                'text' => __d('role_badge', 'Award Badges'),
                'cnt' => 0
            );
        }
    }

    public function afterRenderUserNameFeedOnApp($oEvent) {
        $aUser = $oEvent->data['user'];
        if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled') && !empty($aUser)) {
            $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge');
            $oRoleBadgeModel = MooCore::getInstance()->getModel('RoleBadge.RoleBadge');
            $oAwardUserModel = MooCore::getInstance()->getModel('RoleBadge.AwardUser');

            $aRoleBadge = $oRoleBadgeModel->findByRoleId($aUser['role_id']);
            if (!empty($aRoleBadge)) {
                $sTitleHtml = '<img class="icon-role-badge-username-feed" src="' . $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['mobile_feed']) . '">';
                $oEvent->result['result'][] = array('titleHtml' => $sTitleHtml);
            }

            $aAwards = $oAwardUserModel->getAwardsShowNextName($aUser['id']);
            if (!empty($aAwards)) {
                foreach ($aAwards as $aAward) {
                    $sTitleHtml = '<img class="icon-role-badge-username-feed" src="' . $oRoleBadgeHelper->getImage($aAward['AwardBadge']['thumbnail']) . '">';
                    $oEvent->result['result'][] = array('titleHtml' => $sTitleHtml);
                }
            }
        }
    }

    public function afterRenderProfileNameOnApp($oEvent) {
        if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
            $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge');
            $oRoleBadgeModel = MooCore::getInstance()->getModel('RoleBadge.RoleBadge');

            $aSUser = MooCore::getInstance()->getSubject();
            $aRoleBadge = $oRoleBadgeModel->findByRoleId($aSUser['User']['role_id']);
            if (!empty($aRoleBadge)) {
                $sProfile = '<a href="javascript:void(0)" class="p-role-badge"><img class="icon-role-badge-profile-name" src="' . $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['mobile_profile']) . '"></a>';
                $oEvent->result['result'][] = array('profile' => $sProfile);
            }
        }
    }

    public function beforeDelete($oEvent) {
        $oModel = $oEvent->subject();
        $sType = ($oModel->plugin ? $oModel->plugin . '_' : '') . get_class($oModel);
        if ($sType == 'Role' && Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
            $oRoleBadgeModel = MooCore::getInstance()->getModel('RoleBadge.RoleBadge');
            $aRoleBadge = $oRoleBadgeModel->findByRoleId($oModel->id);
            if (!empty($aRoleBadge)) {
                $oRoleBadgeModel->delete($aRoleBadge['RoleBadge']['id']);
            }
        }

        if ($sType == 'User' && Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
            $oAwardUserModel = MooCore::getInstance()->getModel('RoleBadge.AwardUser');

            $oAwardUserModel->bLoadTranslate = false;
            $oAwardUserModel->deleteAll(array('AwardUser.award_badge_id' => $oModel->id), true, true);
        }
    }

    public function beforeRender($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
            // Load css
            $oView->Helpers->Html->css(array('RoleBadge.main'), array('block' => 'css'));

            // init modal
            $oView->Helpers->MooPopup->register('themeModal');
        }
    }

    public function loadItemInfo($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled') && $oView->viewVars['type'] == 'User') {
            $oRoleBadgeModel = MooCore::getInstance()->getModel('RoleBadge.RoleBadge');
            $aRoleBadge = $oRoleBadgeModel->findByRoleId($oView->viewVars['object']['User']['role_id']);
            if (!empty($aRoleBadge)) {
                echo $oView->element('info/profile', array('aRoleBadge' => $aRoleBadge), array('plugin' => 'RoleBadge'));
            }
        }
    }

    public function beforeRenderSectionMenu($oEvent) {
        $oView = $oEvent->subject();
        $aSUser = MooCore::getInstance()->getSubject();
        if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
            $oRoleBadgeModel = MooCore::getInstance()->getModel('RoleBadge.RoleBadge');
            $aRoleBadge = $oRoleBadgeModel->findByRoleId($aSUser['User']['role_id']);
            if (!empty($aRoleBadge)) {
                echo $oView->element('section/profile', array('aRoleBadge' => $aRoleBadge), array('plugin' => 'RoleBadge'));
            }
        }
    }

    public function afterRenderUserNameFeed($oEvent) {
        $oView = $oEvent->subject();
        $aUser = $oEvent->data['user'];
        if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled') && !empty($aUser)) {
            $oRoleBadgeModel = MooCore::getInstance()->getModel('RoleBadge.RoleBadge');
            $oAwardUserModel = MooCore::getInstance()->getModel('RoleBadge.AwardUser');

            $aRoleBadge = $oRoleBadgeModel->findByRoleId($aUser['role_id']);
            if (!empty($aRoleBadge) && !empty($aRoleBadge['RoleBadge']['show_next_name'])) {
                echo $oView->element('feed/role_badge', array('aRoleBadge' => $aRoleBadge), array('plugin' => 'RoleBadge'));
            }

            $aAwards = $oAwardUserModel->getAwardsShowNextName($aUser['id']);
            if (!empty($aAwards)) {
                echo $oView->element('feed/award', array('aAwards' => $aAwards), array('plugin' => 'RoleBadge'));
            }
        }
    }

    public function profileAfterRenderMenu($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
            echo $oView->element('menu/profile', array(), array('plugin' => 'RoleBadge'));
        }
    }

}
