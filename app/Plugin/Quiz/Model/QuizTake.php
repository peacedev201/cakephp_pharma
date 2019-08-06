<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('QuizAppModel', 'Quiz.Model');

class QuizTake extends QuizAppModel {

    public $recursive = 2;
    public $belongsTo = array(
        'User',
        'Quiz' => array(
            'classname' => 'Quiz.Quiz'
        )
    );
    public $hasMany = array(
        'QuizResult' => array(
            'className' => 'Quiz.QuizResult',
            'dependent' => true
        )
    );

    public function beforeDelete($cascade = true) {

        $aQuizTake = $this->findById($this->id);
        if (!empty($aQuizTake)) {
            $oActivityModel = MooCore::getInstance()->getModel('Activity');
            $aActivityId = $oActivityModel->find('list', array('fields' => array('Activity.id'), 'conditions' => array('Activity.action' => 'quiz_take', 'Activity.item_type' => 'Quiz_Quiz', 'Activity.item_id' => $aQuizTake['QuizTake']['quiz_id'])));
            if (!empty($aActivityId)) {
                $oActivityModel->deleteAll(array('Activity.id' => $aActivityId), true, true);
                $oActivityModel->deleteAll(array('Activity.parent_id' => $aActivityId));
            }
        }

        parent::beforeDelete($cascade);
    }

    public function checkTaken($iUserId, $iQuizId, $sPrivacyHash = null) {

        if (empty($iUserId) || empty($iQuizId)) {
            return false;
        }
        
        $aCond = array('user_id' => $iUserId, 'quiz_id' => $iQuizId);
        
        if(!empty($sPrivacyHash)){
            $aCond['privacy_hash'] = $sPrivacyHash;
        }

        return $this->hasAny($aCond);
    }

    public function getTakes($iQuizId, $iPage = 1, $sSort = null) {
        $order = 'QuizTake.created desc';
        switch ($sSort) {
            case 'score':
                $order = 'QuizTake.correct_answer desc';
                break;
        }

        $this->unbindModel(array('hasMany' => array('QuizResult')));
        $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
        $oQuizModel->unbindModel(array('belongsTo' => array('User', 'Category')));
        
        $cond = $this->addBlockCondition(array('QuizTake.quiz_id' => $iQuizId, 'QuizTake.privacy <> ' => PRIVACY_RESTRICTED));
        return $this->find('all', array('conditions' => $cond, 'order' => $order, 'limit' => RESULTS_LIMIT, 'page' => $iPage));
    }

    public function getMyTaken($iUserId) {
        return $this->find('list', array('conditions' => array('QuizTake.user_id' => $iUserId), 'fields' => array('quiz_id')));
    }
    
    public function addBlockCondition($cond = array(), $modal_name = null) {
        $oUserBlockModel = MooCore::getInstance()->getModel('UserBlock');
        $aBlockedUsers = $oUserBlockModel->getBlockedUsers();
        if (!empty($aBlockedUsers)) {
            $sBlockedUsers = implode(',', $aBlockedUsers);
            $sFieldName = 'User.id';
            
            if (empty($modal_name)) {
                $modal_name = $this->name;
            }
            
            if ($modal_name != 'User') {
                $sFieldName = $modal_name . '.user_id';
            }
            
            $cond[] = "$sFieldName NOT IN ($sBlockedUsers)";
        }

        return $cond;
    }

}
