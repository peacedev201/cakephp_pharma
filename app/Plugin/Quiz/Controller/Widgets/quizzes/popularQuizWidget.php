<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Widget', 'Controller/Widgets');

class popularQuizWidget extends Widget {

    public function beforeRender(Controller $oController) {
        $iNumberShow = $this->params['num_item_show'];
        $oController->loadModel('Quiz.Quiz');
        
        /*
        $aPopular = Cache::read('quiz.popular_quiz.' . $iNumberShow, 'quiz');
        if (empty($aPopular)) {
            $aPopular = $oController->Quiz->getPopularQuizzes($iNumberShow);
            Cache::write('quiz.popular_quiz.' . $iNumberShow, $aPopular, 'quiz');
        }
        */
        
        $aPopular = $oController->Quiz->getPopularQuizzes($iNumberShow);
        $this->setData('aQuizzes', $aPopular);
    }

}
