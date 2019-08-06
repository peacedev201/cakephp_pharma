<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('QuizAppModel', 'Quiz.Model');

class QuizAnswer extends QuizAppModel {

    public $belongsTo = array(
        'QuizQuestion' => array(
            'classname' => 'Quiz.QuizQuestion',
            'dependent' => true
        )
    );

    public function checkAnswer($iQuestionId, $iId) {
        return $this->hasAny(array('quiz_question_id' => $iQuestionId, 'id' => $iId));
    }
    
    public function checkCorrectAnswer($iQuestionId, $iId) {
        return $this->hasAny(array('quiz_question_id' => $iQuestionId, 'id' => $iId, 'correct' => 1));
    }

    public function getCorrectAnswer($iQuizId, $iQuestionId) {
        return $this->find('first', array('conditions' => array('QuizQuestion.quiz_id' => $iQuizId, 'QuizAnswer.quiz_question_id' => $iQuestionId, 'correct' => 1)));
    }

}
