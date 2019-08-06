<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */


if (Configure::read('VerifyProfile.verify_profile_enable')) {
    Router::connect('/profile/verify/:action/*', array(
        'plugin' => 'VerifyProfile',
        'controller' => 'verify_profiles'
    ));

    Router::connect('/profile/verify/*', array(
        'plugin' => 'VerifyProfile',
        'controller' => 'verify_profiles',
        'action' => 'index'
    ));
}
