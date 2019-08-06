<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('AppController', 'Controller');
App::uses('ProfileCompletionHelper', 'View/Helper');

class ProfileCompletionAppController extends AppController {

    public function beforeFilter() {    	
        parent::beforeFilter();
    }

}