<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('RoleBadgeAppModel', 'RoleBadge.Model');

class AwardBadge extends RoleBadgeAppModel {

    public $recursive = 2;
    public $actsAs = array(
        'Translate' => array(
            'name' => 'nameTranslation',
            'description' => 'descriptionTranslation'
        )
    );
    public $validate = array(
        'name' => array(
            'required' => true,
            'rule' => 'notBlank',
            'message' => 'Name is required',
        ),
        'description' => array(
            'required' => true,
            'rule' => 'notBlank',
            'message' => 'Description is required',
        )
    );
    public $order = array('AwardBadge.weight', 'AwardBadge.id');

    function __construct($id = false, $table = null, $ds = null) {
        $this->locale = Configure::read('Config.language');
        parent::__construct($id, $table, $ds);
    }
    
    public function deleteAwardBadge($iAwardBadgeId) {
        $oAwardUserModel = MooCore::getInstance()->getModel('RoleBadge.AwardUser');
        
        $oAwardUserModel->bLoadTranslate = false;
        $oAwardUserModel->deleteAll(array('AwardUser.award_badge_id' => $iAwardBadgeId), true, true);
        
        $this->delete($iAwardBadgeId);
    }

}
