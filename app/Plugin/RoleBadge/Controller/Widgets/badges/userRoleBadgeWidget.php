<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Widget', 'Controller/Widgets');

class userRoleBadgeWidget extends Widget {

    public function beforeRender(Controller $oController) {
        
        if (!empty($this->params['role_id'])) {
            $iRoleId = intval($this->params['role_id']);
            $iItemShow = intval($this->params['num_item_show']);
            $oUserBlockModal = MooCore::getInstance()->getModel('UserBlock');
            $aBlockedUsers = $oUserBlockModal->getBlockedUsers();
            $aCond = array('User.role_id' => $iRoleId);

            if (!empty($aBlockedUsers)) {
                $sBlockedUsers = implode(',', $aBlockedUsers);
                $aCond[] = "User.id NOT IN ($sBlockedUsers)";
            }

            $aRoleUsers = $oController->User->find('all', array('conditions' => $aCond, 'order' => array('User.created'), 'limit' => $iItemShow));
            $this->setData('aRoleUsers', $aRoleUsers);
        }
    }

}
