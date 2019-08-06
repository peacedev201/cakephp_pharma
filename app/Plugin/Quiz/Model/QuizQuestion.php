<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('QuizAppModel', 'Quiz.Model');

class QuizQuestion extends QuizAppModel {

    public $recursive = 2;
    public $hasMany = array(
        'QuizAnswer' => array(
            'className' => 'Quiz.QuizAnswer',
            'order' => 'QuizAnswer.weight, QuizAnswer.id',
            'dependent' => true
        )
    );
    public $validate = array(
        'title' => array(
            'rule' => 'notBlank',
            'message' => 'Question title is required',
        ),
        'answers' => array(
            'checkAnswers' => array(
                'rule' => 'checkAnswers'
            ),
            'checkResults' => array(
                'rule' => 'checkResults',
                'message' => 'Please choose correct answer'
            )
        )
    );

    public function checkAnswers($aData = array()) {
        $iAnswerValidate = Configure::check('Quiz.quiz_answers_count') ? Configure::read('Quiz.quiz_answers_count') : 2;
        $aAnswers = $aData['answers']['title'];
        array_pop($aAnswers);

        if (count($aAnswers) < $iAnswerValidate) {
            $this->validator()->getField('answers')->getRule('checkAnswers')->message = __d('quiz', 'Question must have at least %s answers', $iAnswerValidate);
            return false;
        } else {
            foreach ($aAnswers as $sValue) {
                if (empty($sValue)) {
                    $this->validator()->getField('answers')->getRule('checkAnswers')->message = __d('quiz', 'Answer title is required all');
                    return false;
                }
            }
        }

        return true;
    }

    public function checkResults($aData = array()) {
        $aResults = $aData['answers']['correct'];
        array_pop($aResults);

        foreach ($aResults as $sValue) {
            if (!empty($sValue)) {
                return true;
            }
        }

        return false;
    }
    
    public function getLastWeight($iQuizId){
        return $this->find('first', array('conditions' => array('QuizQuestion.quiz_id' => $iQuizId), 'fields' => array('weight'), 'order' => 'QuizQuestion.weight DESC'));
    }

    public function getQuestions($iQuizId) {
        return $this->find('all', array('conditions' => array('QuizQuestion.quiz_id' => $iQuizId), 'order' => 'QuizQuestion.weight, QuizQuestion.id'));
    }

    public function countQuestions($iQuizId) {
        return $this->find('count', array('conditions' => array('QuizQuestion.quiz_id' => $iQuizId)));
    }

    public function getThumb($row) {
        return 'thumbnail';
    }

}
