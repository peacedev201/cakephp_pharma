<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

if (Configure::check('Review.review_enabled') && Configure::read('Review.review_enabled')) {
    App::uses('ReviewListener', 'Review.Lib');
    CakeEventManager::instance()->attach(new ReviewListener());
}