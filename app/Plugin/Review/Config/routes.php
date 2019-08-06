<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

if (Configure::check('Review.review_enabled') && Configure::read('Review.review_enabled')) {
    Router::connect("/reviews/:action/*", array('plugin' => 'Review', 'controller' => 'reviews'));
    Router::connect("/reviews/*", array('plugin' => 'Review', 'controller' => 'reviews', 'action' => 'index'));
}

