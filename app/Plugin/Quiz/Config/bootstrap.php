<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

MooCache::getInstance()->setCache('quiz', array('groups' => array('quiz')));
if (Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled')) {
    App::uses('QuizListener', 'Quiz.Lib');
    CakeEventManager::instance()->attach(new QuizListener());
    MooSeo::getInstance()->addSitemapEntity("Quiz", array('quiz'));
}