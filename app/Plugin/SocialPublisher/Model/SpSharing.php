<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('SocialPublisherAppModel', 'SocialPublisher.Model');

class SpSharing extends SocialPublisherAppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'user_id';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
    );
        

}
