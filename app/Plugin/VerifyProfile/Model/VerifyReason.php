<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('VerifyProfileAppModel', 'VerifyProfile.Model');

class VerifyReason extends VerifyProfileAppModel {

    public $validate = array(
        'description' => array(
            'rule' => 'notBlank',
            'message' => 'Description is required'
        )
    );

}
