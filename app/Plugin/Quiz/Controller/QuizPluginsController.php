<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class QuizPluginsController extends QuizAppController {

    public $paginate = array(
        'limit' => RESULTS_LIMIT
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Quiz.Quiz');
    }

    public function admin_index() {

        $aCond = array();
        $sStatus = 'All';
        if (!empty($this->request->named['filter'])) {
            $aCond['Quiz.published'] = 1;
            $sStatus = $this->request->named['filter'];
            $aCond['Quiz.approved'] = ($this->request->named['filter'] == 'approved') ? 1 : 0;
        }

        if (!empty($this->request->data['keyword'])) {
            array_push($aCond, 'Quiz.title LIKE "%' . $this->request->data['keyword'] . '%"');
        }

        $this->set('sStatus', ucfirst($sStatus));
        $this->set('aQuizzes', $this->paginate('Quiz', $aCond));
        $this->set('title_for_layout', __d('quiz', 'Quizzes Manager'));
    }

    public function admin_delete($id = null) {
        $iQuizId = intval($id);
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($iQuizId) && empty($this->request->data)) {
            $aQuiz = $this->Quiz->findById($iQuizId);
            $this->_checkExistence($aQuiz);

            $this->Quiz->delete($iQuizId);
            $this->Session->setFlash(__d('quiz', 'Quiz have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        } else if (empty($iQuizId) && !empty($this->request->data)) {
            $aQuizId = array_values($this->request->data['quizzes']);
            $this->Quiz->deleteAll(array('Quiz.id' => $aQuizId), true, true);
            $this->Session->setFlash(__d('quiz', 'Quizzes have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect(array(
            'plugin' => 'quiz',
            'controller' => 'quiz_plugins',
            'action' => 'admin_index'
        ));
    }

    public function admin_approve($id) {
        $iQuizId = intval($id);
        $this->_checkPermission(array('super_admin' => 1));

        $aQuiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($aQuiz);

        if (empty($aQuiz['Quiz']['approved']) && $aQuiz['Quiz']['published']) {
            $this->Quiz->updateAll(array('Quiz.approved' => 1), array('Quiz.id' => $iQuizId));
            $this->Quiz->updateActivity($aQuiz['Quiz'], true);

            //Send mail to user
            $ssl_mode = Configure::read('core.ssl_mode');
            $http = (!empty($ssl_mode)) ? 'https' : 'http';
            $this->MooMail->send($aQuiz['User']['email'], 'quiz_approve', array(
                'quiz_title' => h($aQuiz['Quiz']['title']),
                'quiz_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $aQuiz['Quiz']['moo_href']
            ));
        } else if (empty($aQuiz['Quiz']['published'])) {
            $this->Session->setFlash(__d('quiz', 'Quiz is not publish, please publish it before approve'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
        }

        $this->redirect(array(
            'plugin' => 'quiz',
            'controller' => 'quiz_plugins',
            'action' => 'admin_index'
        ));
    }

}
