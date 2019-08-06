<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('QuizAppModel', 'Quiz.Model');

class QuizResult extends QuizAppModel {

    public function getResult($aQuizTake) {
        $iCountCorrect = 0;
        $oQuizAnswerModel = MooCore::getInstance()->getModel('Quiz.QuizAnswer');
        $oQuizQuestionModel = MooCore::getInstance()->getModel('Quiz.QuizQuestion');
        foreach ($aQuizTake['QuizResult'] as $aQuizResult) {
            $aQuizAnswer = $oQuizAnswerModel->getCorrectAnswer($aQuizTake['QuizTake']['quiz_id'], $aQuizResult['question_id']);
            if ($aQuizAnswer['QuizAnswer']['id'] == $aQuizResult['answer_id']) {
                $iCountCorrect++;
            }
        }
        
        $iCountQuestion = $oQuizQuestionModel->countQuestions($aQuizTake['QuizTake']['quiz_id']);
        return array('iCountCorrect' => $iCountCorrect, 'iCountQuestion' => $iCountQuestion, 'iPassScore' => $aQuizTake['Quiz']['pass_score']);
    }

    public function getResultView($aQuizTake) {
        $oQuizQuestionModel = MooCore::getInstance()->getModel('Quiz.QuizQuestion');
        $aQuestions = $oQuizQuestionModel->getQuestions($aQuizTake['Quiz']['id']);

        foreach ($aQuestions as $iKeyQ => $aQuestion) {
            foreach ($aQuestion['QuizAnswer'] as $iKeyA => $aQuizAnswer) {
                if ($this->hasAny(array('quiz_take_id' => $aQuizTake['QuizTake']['id'], 'question_id' => $aQuestion['QuizQuestion']['id'], 'answer_id' => $aQuizAnswer['id']))) {
                    $aQuestions[$iKeyQ]['QuizQuestion']['user_answer_title'] = $aQuizAnswer['title'];
                    if($aQuizAnswer['correct']){
                        $aQuestions[$iKeyQ]['QuizQuestion']['user_answer_correct'] = 1;
                    }
                }
                
                if($aQuizAnswer['correct']){
                    $aQuestions[$iKeyQ]['QuizQuestion']['correct_answer_title'] = $aQuizAnswer['title'];
                }
            }
            
            unset($aQuestions[$iKeyQ]['QuizAnswer']);
        }

        return $aQuestions;
    }

}
