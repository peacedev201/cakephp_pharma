<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('RoleBadgeAppModel', 'RoleBadge.Model');

class RoleBadge extends RoleBadgeAppModel {

    public $belongsTo = array('Role');
    public $validate = array(
        'role_id' => array(
            'required' => true,
            'rule' => 'notBlank',
            'message' => 'Role is required',
        )
    );

}
