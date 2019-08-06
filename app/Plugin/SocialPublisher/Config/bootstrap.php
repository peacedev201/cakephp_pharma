<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
CakeLog::config('social', array(
    'engine' => 'FileLog',
));

App::uses('SocialPublisherListener', 'SocialPublisher.Event');
App::uses('CakeEventManager', 'Event');

CakeEventManager::instance()->attach(new SocialPublisherListener());
