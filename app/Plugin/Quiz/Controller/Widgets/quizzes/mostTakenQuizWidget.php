<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Widget', 'Controller/Widgets');

class mostTakenQuizWidget extends Widget {

    public function beforeRender(Controller $oController) {
        $iNumberShow = $this->params['num_item_show'];
        $oController->loadModel('Quiz.Quiz');
        
        /*
        $aTaken = Cache::read('quiz.taken_quiz.' . $iNumberShow, 'quiz');
        if (empty($aTaken)) {
            $aTaken = $oController->Quiz->getTakenQuizzes($iNumberShow);
            Cache::write('quiz.taken_quiz.' . $iNumberShow, $aTaken, 'quiz');
        }
        */
        
        $aTaken = $oController->Quiz->getTakenQuizzes($iNumberShow);
        $this->setData('aQuizzes', $aTaken);
    }

}
