<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

class QuizQuestionsController extends QuizAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Quiz.Quiz');
        $this->loadModel('Quiz.QuizTake');
        $this->loadModel('Quiz.QuizAnswer');
    }

    public function index($id) {
        $iQuizId = intval($id);
        $this->_checkPermission(array('confirm' => true));

        $aQuiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($aQuiz);
        $this->_checkPermission(array('admins' => array($aQuiz['Quiz']['user_id'])));

        $aQuestions = $this->QuizQuestion->getQuestions($iQuizId);

        $this->set(array(
            'quiz' => $aQuiz,
            'questions' => $aQuestions,
            'title_for_layout' => __d('quiz', 'Quiz Questions')
        ));

        if (empty($aQuiz['Quiz']['approved']) && Configure::check('Quiz.quiz_auto_approval') && !Configure::read('Quiz.quiz_auto_approval')) {
            $this->Session->setFlash(__d('quiz', 'Your quiz is pending for approval.'));
        }
    }

    public function create($id, $question_id = null) {
        $iQuizId = intval($id);
        $iQuestionId = intval($question_id);
        $this->_checkPermission(array('confirm' => true));

        $aQuiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($aQuiz);
        $this->_checkPermission(array('admins' => array($aQuiz['Quiz']['user_id'])));

        $aAnswers = array();
        if (!empty($iQuestionId)) {
            $aQuestion = $this->QuizQuestion->findById($iQuestionId);
            $this->_checkExistence($aQuestion);
        } else {
            $aQuestion = $this->QuizQuestion->initFields();
        }

        $this->set(array(
            'quiz' => $aQuiz,
            'question' => $aQuestion
        ));

        $this->render('Quiz.QuizQuestions/create');
    }

    public function save() {

        $this->_checkPermission(array('confirm' => true));
        $this->autoRender = false;
        $bEdit = false;

        $aQuiz = $this->Quiz->findById($this->request->data['quiz_id']);
        $this->_checkExistence($aQuiz);
        $this->_checkPermission(array('admins' => array($aQuiz['Quiz']['user_id'])));

        if (!empty($this->request->data['id'])) {
            $bEdit = true;
            $aQuestion = $this->QuizQuestion->findById($this->request->data['id']);
            $this->_checkExistence($aQuestion);
            $this->QuizQuestion->id = $this->request->data['id'];
        } else {
            $aQuizQuestionWeight = $this->QuizQuestion->getLastWeight($aQuiz['Quiz']['id']);
            $this->request->data['weight'] = !empty($aQuizQuestionWeight['QuizQuestion']['weight']) ? $aQuizQuestionWeight['QuizQuestion']['weight'] + 1 : 1;
        }

        $this->QuizQuestion->set($this->request->data);
        $this->_validateData($this->QuizQuestion);
        if ($this->QuizQuestion->save()) {
            if ($bEdit) {
                $iOrder = 1;
                $aQuestionExist = array();
                array_pop($this->request->data['answers']['title']);
                foreach ($this->request->data['answers']['title'] as $iKey => $sTitle) {
                    if ($this->QuizAnswer->checkAnswer($this->QuizQuestion->id, $iKey)) {
                        array_push($aQuestionExist, $iKey);
                        $this->QuizAnswer->updateAll(array(
                            'QuizAnswer.correct' => isset($this->request->data['answers']['correct'][$iKey]) ? $this->request->data['answers']['correct'][$iKey] : 0,
                            'QuizAnswer.title' => "'" . addslashes($sTitle) . "'",
                            'QuizAnswer.weight' => $iOrder), array(
                            'QuizAnswer.quiz_question_id' => $this->QuizQuestion->id,
                            'QuizAnswer.id' => $iKey
                        ));
                    } else {
                        $this->QuizAnswer->create();
                        $this->QuizAnswer->save(array(
                            'title' => $sTitle,
                            'weight' => $iOrder,
                            'quiz_question_id' => $this->QuizQuestion->id,
                            'correct' => isset($this->request->data['answers']['correct'][$iKey]) ? $this->request->data['answers']['correct'][$iKey] : 0,
                        ));
                        array_push($aQuestionExist, $this->QuizAnswer->id);
                    }
                    $iOrder++;
                }
                $this->QuizAnswer->deleteAll(array('QuizAnswer.quiz_question_id' => $this->QuizQuestion->id, 'NOT' => array('QuizAnswer.id' => $aQuestionExist)));
            } else {
                array_pop($this->request->data['answers']['title']);
                foreach ($this->request->data['answers']['title'] as $iKey => $sTitle) {
                    $this->QuizAnswer->create();
                    $this->QuizAnswer->save(array(
                        'title' => $sTitle,
                        'quiz_question_id' => $this->QuizQuestion->id,
                        'correct' => isset($this->request->data['answers']['correct'][$iKey]) ? $this->request->data['answers']['correct'][$iKey] : 0,
                    ));
                }
            }

            // delete participant
            if (!empty($aQuiz['Quiz']['take_count'])) {
                $this->QuizTake->deleteAll(array('QuizTake.quiz_id' => $aQuiz['Quiz']['id']), true, true);
                $this->Quiz->updateAll(array('Quiz.take_count' => 0), array('Quiz.id' => $aQuiz['Quiz']['id']));
            }

            // add pending
            if (Configure::check('Quiz.quiz_auto_approval') && !Configure::read('Quiz.quiz_auto_approval') && $aQuiz['Quiz']['approved']) {
                $this->Quiz->updateAll(array('Quiz.approved' => 0), array('Quiz.id' => $aQuiz['Quiz']['id']));
            }

            $response['result'] = 1;
            echo json_encode($response);
        } else {
            $response['result'] = 0;
            $response['message'] = __d('quiz', 'Error! Please try again.');
            echo json_encode($response);
        }
    }

    public function sort($id) {
        $this->autoRender = false;
        $iQuizId = intval($id);
        $this->_checkPermission(array('confirm' => true));

        $aQuiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($aQuiz);
        $this->_checkPermission(array('admins' => array($aQuiz['Quiz']['user_id'])));

        if ($this->request->is('post')) {
            foreach ($this->request->data['ids'] as $iKey => $iId) {
                $this->QuizQuestion->updateAll(array('QuizQuestion.weight' => $iKey + 1), array('QuizQuestion.id' => $iId));
            }

            $response['result'] = 1;
            echo json_encode($response);
        } else {
            $response['result'] = 0;
            $response['message'] = __d('quiz', 'Error! Please try again.');
            echo json_encode($response);
        }
    }

    public function ajax_delete($id, $question_id) {
        $iQuizId = intval($id);
        $iQuestionId = intval($question_id);
        $this->_checkPermission(array('confirm' => true));

        $aQuiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($aQuiz);
        $this->_checkPermission(array('admins' => array($aQuiz['Quiz']['user_id'])));

        $aQuestion = $this->QuizQuestion->findById($iQuestionId);
        $this->_checkExistence($aQuestion);

        $iCountQuestionConfig = Configure::check('Quiz.quiz_questions_count') ? Configure::read('Quiz.quiz_questions_count') : 2;
        $sMessage = __d('quiz', 'Are you sure you want to remove this question?');
        $iCountQuestion = $this->QuizQuestion->countQuestions($iQuizId);
        if (($iCountQuestion - 1) < $iCountQuestionConfig) {
            $sMessage = __d('quiz', 'The quiz needs at least %s questions to be visible to member. Delete this question will auto un-publish this quiz. Are you sure you want to go ahead?', Configure::read('Quiz.quiz_questions_count'));
        }

        $this->set('aQuestion', $aQuestion);
        $this->set('sMessage', $sMessage);
        $this->set('aQuiz', $aQuiz);

        $this->render('Quiz.Elements/ajax/question_delete');
    }

    public function delete($id, $question_id) {
        $iQuizId = intval($id);
        $iQuestionId = intval($question_id);
        $this->_checkPermission(array('confirm' => true));

        $aQuiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($aQuiz);
        $this->_checkPermission(array('admins' => array($aQuiz['Quiz']['user_id'])));

        $aQuestion = $this->QuizQuestion->findById($iQuestionId);
        $this->_checkExistence($aQuestion);

        $iCountQuestionConfig = Configure::check('Quiz.quiz_questions_count') ? Configure::read('Quiz.quiz_questions_count') : 2;
        $iCountQuestion = $this->QuizQuestion->countQuestions($iQuizId);
        $this->QuizQuestion->delete($iQuestionId);
        if (($iCountQuestion - 1) < $iCountQuestionConfig) {
            $this->Quiz->updateActivity($aQuiz['Quiz'], false);
            $this->Quiz->updateAll(array('Quiz.published' => 0), array('Quiz.id' => $iQuizId));
        }

        // delete participant
        if (!empty($aQuiz['Quiz']['take_count'])) {
            $this->QuizTake->deleteAll(array('QuizTake.quiz_id' => $aQuiz['Quiz']['id']), true, true);
            $this->Quiz->updateAll(array('Quiz.take_count' => 0), array('Quiz.id' => $aQuiz['Quiz']['id']));
        }

        if ($this->isApp()) {
            $this->autoRender = false;
        } else {
            $this->Session->setFlash(__d('quiz', 'Entry has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            $this->redirect('/quizzes/question/' . $iQuizId);
        }
    }

}

?>