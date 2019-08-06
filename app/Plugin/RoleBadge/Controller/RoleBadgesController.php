<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('CakeEvent', 'Event');

class RoleBadgesController extends RoleBadgeAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('RoleBadge.RoleBadge');
    }

}
