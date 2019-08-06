<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
    Router::connect("/awards/:action/*", array('plugin' => 'role_badge', 'controller' => 'award_badges'));
    Router::connect("/awards/*", array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'index'));
}

