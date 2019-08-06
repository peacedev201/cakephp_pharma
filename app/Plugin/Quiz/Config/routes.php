<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

if (Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled')) {

    // Question
    Router::connect("/quizzes/question/:id", array('plugin' => 'Quiz', 'controller' => 'quiz_questions', 'action' => 'index'), array('pass' => array('id'), 'id' => '[0-9]+'));
    Router::connect("/quizzes/question/:action/*", array('plugin' => 'Quiz', 'controller' => 'quiz_questions'));

    // Main
    Router::connect("/quizzes/:action/*", array('plugin' => 'Quiz', 'controller' => 'quizzes'));
    Router::connect("/quizzes/*", array('plugin' => 'Quiz', 'controller' => 'quizzes', 'action' => 'index'));
}

