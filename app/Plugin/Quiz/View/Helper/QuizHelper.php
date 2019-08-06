<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('AppHelper', 'View/Helper');

class QuizHelper extends AppHelper {

    public $helpers = array('Storage.Storage');

    public function getImage($item, $options) {
        $prefix = '';
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'] . '_';
        }
        
        return $this->Storage->getUrl($item[key($item)]['id'], $prefix, $item[key($item)]['thumbnail'], "quizzes");
    }

    public function checkPostStatus($quiz, $uid) {
        if (!$uid) {
            return false;
        }

        if ($uid == $quiz['Quiz']['user_id']) {
            return true;
        }

        if ($quiz['Quiz']['privacy'] == PRIVACY_EVERYONE) {
            return true;
        }

        $friendModel = MooCore::getInstance()->getModel('Friend');
        if ($quiz['Quiz']['privacy'] == PRIVACY_FRIENDS) {
            $areFriends = $friendModel->areFriends($uid, $quiz['Quiz']['user_id']);
            if ($areFriends) {
                return true;
            }
        }

        return false;
    }

    public function checkSeeComment($quiz, $uid) {
        if ($quiz['Quiz']['privacy'] == PRIVACY_EVERYONE) {
            return true;
        }

        return $this->checkPostStatus($quiz, $uid);
    }

    public function getResult($aQuiz, $iUserId) {
        $oQuizQuizTakeModel = MooCore::getInstance()->getModel('Quiz.QuizTake');
        $oQuizQuizResultModel = MooCore::getInstance()->getModel('Quiz.QuizResult');
        $aQuizTake = $oQuizQuizTakeModel->findByUserIdAndQuizId($iUserId, $aQuiz['Quiz']['id']);

        $aResult = $oQuizQuizResultModel->getResult($aQuizTake);
        $iPercentResult = round(($aResult['iCountCorrect'] / $aResult['iCountQuestion']) * 100);
        if ($iPercentResult >= $aResult['iPassScore']) {
            $sResult = '<span class="quiz-finish-pass">' . __d('quiz', 'Pass') . ' (' . $iPercentResult . '/100)</span>';
        } else {
            $sResult = '<span class="quiz-finish-fail">' . __d('quiz', 'Fail') . ' (' . $iPercentResult . '/100)</span>';
        }

        return $sResult;
    }
    
    public function getResultOnApp($aQuiz, $iUserId) {
        $oQuizQuizTakeModel = MooCore::getInstance()->getModel('Quiz.QuizTake');
        $oQuizQuizResultModel = MooCore::getInstance()->getModel('Quiz.QuizResult');
        $aQuizTake = $oQuizQuizTakeModel->findByUserIdAndQuizId($iUserId, $aQuiz['Quiz']['id']);

        $aResult = $oQuizQuizResultModel->getResult($aQuizTake);
        $iPercentResult = round(($aResult['iCountCorrect'] / $aResult['iCountQuestion']) * 100);
        if ($iPercentResult >= $aResult['iPassScore']) {
            $sResult = '<span style="color: green; font-weight: bold;">' . __d('quiz', 'Pass') . ' (' . $iPercentResult . '/100)</span>';
        } else {
            $sResult = '<span style="color: red; font-weight: bold;">' . __d('quiz', 'Fail') . ' (' . $iPercentResult . '/100)</span>';
        }

        return $sResult;
    }

    public function getItemSitemMap($name, $limit, $offset) {

        if (!empty($name)) {
            
        }

        if (!MooCore::getInstance()->checkPermission(null, 'quiz_view')) {
            return null;
        }

        $oQuizModel = MooCore::getInstance()->getModel("Quiz.Quiz");
        $aQuizzes = $oQuizModel->find('all', array(
            'conditions' => array('Quiz.privacy' => PRIVACY_PUBLIC),
            'limit' => $limit,
            'offset' => $offset
        ));

        $sUrls = array();
        foreach ($aQuizzes as $aQuiz) {
            $sUrls[] = FULL_BASE_URL . $aQuiz['Quiz']['moo_href'];
        }

        return $sUrls;
    }

    public function getEnable() {
        return Configure::check('Quiz.quiz_enabled') ? Configure::read('Quiz.quiz_enabled') : 0;
    }

}
