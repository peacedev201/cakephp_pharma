<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

if (Configure::check('VerifyProfile.verify_profile_enable') && Configure::read('VerifyProfile.verify_profile_enable')) {
    App::uses('VerifyProfileListener', 'VerifyProfile.Lib');
    CakeEventManager::instance()->attach(new VerifyProfileListener());
}
