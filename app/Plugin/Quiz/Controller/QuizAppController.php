<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('AppController', 'Controller');

class QuizAppController extends AppController {

    public $check_force_login = true;

    public function beforeFilter() {
        if (Configure::read("Quiz.quiz_consider_force")) {
            $this->check_force_login = false;
        }
        parent::beforeFilter();
    }

}
