<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('RoleBadgeAppModel', 'RoleBadge.Model');

class AwardUser extends RoleBadgeAppModel {

    public $bLoadTranslate = true;
    public $belongsTo = array(
        'AwardBadge' => array(
            'classname' => 'RoleBadge.AwardBadge'
        )
    );

    public function beforeFind($queryData) {
        parent::beforeFind($queryData);

        if ($this->bLoadTranslate) {
            $oAwardBadgeModel = MooCore::getInstance()->getModel('RoleBadge.AwardBadge');
            $oAwardBadgeModel->virtualFields = array('name' => 'I18nName.content', 'description' => 'I18nDescription.content');

            $queryData['joins'] = array(
                array(
                    'type' => 'INNER',
                    'alias' => 'I18nName',
                    'table' => 'i18n',
                    'foreignKey' => false,
                    'conditions' => array(
                        'I18nName.locale' => Configure::read('Config.language'),
                        'I18nName.foreign_key = AwardUser.award_badge_id',
                        'I18nName.model' => 'AwardBadge',
                        'I18nName.field' => 'name',
                    ),
                ),
                array(
                    'type' => 'INNER',
                    'alias' => 'I18nDescription',
                    'table' => 'i18n',
                    'foreignKey' => false,
                    'conditions' => array(
                        'I18nDescription.locale' => Configure::read('Config.language'),
                        'I18nDescription.foreign_key = AwardUser.award_badge_id',
                        'I18nDescription.model' => 'AwardBadge',
                        'I18nDescription.field' => 'description',
                    ),
                ),
            );

            return $queryData;
        }
    }

    public function getProfileBadges($iUserId = null) {
        return $this->find('all', array('conditions' => array('AwardUser.user_id' => $iUserId), 'order' => array('AwardBadge.weight', 'AwardBadge.id')));
    }
    
    public function getAwardsShowNextName($iUserId) {
        return $this->find('all', array('conditions' => array('AwardUser.user_id' => $iUserId, 'AwardBadge.show_next_name' => 1), 'order' => array('AwardBadge.weight', 'AwardBadge.id')));
    }

}
