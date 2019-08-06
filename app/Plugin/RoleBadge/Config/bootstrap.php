<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

if (Configure::check('RoleBadge.role_badge_enabled') && Configure::read('RoleBadge.role_badge_enabled')) {
    App::uses('RoleBadgeListener', 'RoleBadge.Lib');
    CakeEventManager::instance()->attach(new RoleBadgeListener());
}