<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Widget', 'Controller/Widgets');

class awardRoleBadgeWidget extends Widget {

    public function beforeRender(Controller $controller) {

        $aCurrentUser = $controller->_getUser(); //$aCurrentUser['id']
        $aObjectUser = MooCore::getInstance()->getSubject(); //$aObjectUser['User']['id']
        $sSubjectType = MooCore::getInstance()->getSubjectType(); // Check Profile Page

        $iUserId = !empty($aCurrentUser) ? $aCurrentUser['id'] : 0;
        if (!empty($aObjectUser) && $sSubjectType == 'User') {
            $iUserId = $aObjectUser['User']['id'];
        }

        $controller->loadModel('RoleBadge.AwardUser');
        $aUserBadges = $controller->AwardUser->getProfileBadges($iUserId);

        $bShowAssign = true;
        $aCurrentUserAcos = $controller->_getUserRoleParams();
        if (empty($aObjectUser) || empty($aCurrentUser) || (!empty($aObjectUser) && $sSubjectType != 'User') || (!in_array('role_badge_assign_badge', $aCurrentUserAcos) && !$aCurrentUser['Role']['is_admin']) || (!empty($aCurrentUser) && $aCurrentUser['id'] == $aObjectUser['User']['id'])) {
            $bShowAssign = false;
        }

        $this->setData('iUserId', $iUserId);
        $this->setData('aUserBadges', $aUserBadges);
        $this->setData('bShowAssign', $bShowAssign);
    }

}
