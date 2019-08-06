<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */


class QuizzesController extends QuizAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Quiz.QuizTake');
        $this->loadModel('Quiz.QuizResult');
        $this->loadModel('Quiz.QuizQuestion');
    }

    public function index($cat_id = null) {
        $this->loadModel('Tag');
        $this->loadModel('Category');

        if (!empty($cat_id)) {
            $quizzes = $this->Quiz->getQuizzes('category', $cat_id);
            $more_quizzes = $this->Quiz->getQuizzes('category', $cat_id, 2);

            $this->set('aCategory', $this->Category->findById($cat_id));
        } else {
            $quizzes = $this->Quiz->getQuizzes();
            $more_quizzes = $this->Quiz->getQuizzes(null, null, 2);
        }

        if (!empty($more_quizzes)) {
            $this->set('more_result', 1);
        }

        $this->set('cat_id', $cat_id);
        $this->set('quizzes', $quizzes);
        $this->set('bLoadHeader', true);
        $this->set('title_for_layout', ''); // if empty auto get name in admin
        $this->set('tags', $this->Tag->getTags('Quiz_Quiz', Configure::read('core.popular_interval')));
    }

    public function browse($type = null, $param = null, $sort = null) {
        $iUserId = $this->Auth->user('id');
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = (!empty($param)) ? $type . '/' . $param . ((!empty($sort)) ? '/' . $sort : '') : $type;

        if (empty($sort)) {
            $sort = $param;
        }

        switch ($type) {
            case 'my':
            case 'home':
            case 'taken':
            case 'friends':
                $this->_checkPermission();
                $param = $iUserId;
                break;

            case 'category':
                $this->loadModel('Category');
                $this->set('cat_id', $param);
                $this->set('aCategory', $this->Category->findById($param));
                break;

            case 'search':
                $param = urldecode($param);
                if (!Configure::read('core.guest_search') && empty($iUserId)) {
                    $this->_checkPermission();
                }
                break;

            default:
                break;
        }

        $quizzes = $this->Quiz->getQuizzes($type, $param, $page, $sort);
        $more_quizzes = $this->Quiz->getQuizzes($type, $param, $page + 1, $sort);
        if (!empty($more_quizzes)) {
            $this->set('more_result', 1);
        }

        $this->set('type', $type);
        $this->set('quizzes', $quizzes);
        $this->set('bLoadHeader', ($page == 1) ? true : false);
        $this->set('more_url', '/quizzes/browse/' . h($url) . '/page:' . ($page + 1));

        if ($page == 1 && $type == 'home') {
            $this->set('bLoadHeader', false);
        }

        if ($this->request->is('ajax')) {
            $this->render('/Elements/lists/quizzes_list');
        } else {
            $this->render('/Elements/lists/quizzes_list_m');
        }
    }

    public function home() {
        $iUserId = $this->Auth->user('id');
        $quizzes = $this->Quiz->getQuizzes('home', $iUserId, 1);
        $more_quizzes = $this->Quiz->getQuizzes('home', $iUserId, 2);

        if (!empty($more_quizzes)) {
            $this->set('more_result', 1);
        }

        $this->set('quizzes', $quizzes);
        $this->set('bHomeLoadHeader', true);
        $this->set('more_url', '/quizzes/profile/home/page:2');
        $this->render('/Elements/ajax/home_quiz');
    }

    public function profile($id = null, $type = 'user') {
        $iUserId = intval($id);
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = (!empty($type)) ? $iUserId . '/' . $type : $iUserId;

        $quizzes = $this->Quiz->getQuizzes($type, $iUserId, $page);
        $more_quizzes = $this->Quiz->getQuizzes($type, $iUserId, $page + 1);

        if (!empty($more_quizzes)) {
            $this->set('more_result', 1);
        }

        if (!empty($iUserId)) {
            $this->loadModel('User');
            $this->set('aUser', $this->User->findById($iUserId));
        }

        $this->set('type', $type);
        $this->set('quizzes', $quizzes);
        $this->set('bProfileLoadHeader', ($page == 1) ? true : false);
        $this->set('more_url', '/quizzes/profile/' . h($url) . '/page:' . ($page + 1));

        if ($page > 1) {
            $this->render('/Elements/lists/quizzes_list');
        } else {
            $this->render('/Elements/ajax/profile_quiz');
        }
    }

    public function create($id = null) {
        $iQuizId = intval($id);
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'quiz_create'));

        $this->loadModel('Category');
        $role_id = $this->_getUserRoleId();
        $cats = $this->Category->getCategoriesList('Quiz_Quiz', $role_id);

        if (!empty($iQuizId)) {
            $quiz = $this->Quiz->findById($iQuizId);
            $this->_checkExistence($quiz);
            $this->_checkPermission(array('admins' => array($quiz['Quiz']['user_id'])));

            $this->loadModel('Tag');
            $tags = $this->Tag->getContentTags($iQuizId, 'Quiz_Quiz');

            $this->set('tags', $tags);
            $this->set('title_for_layout', __d('quiz', 'Edit Quiz'));

            if (empty($quiz['Quiz']['approved']) && Configure::check('Quiz.quiz_auto_approval') && !Configure::read('Quiz.quiz_auto_approval')) {
                $this->Session->setFlash(__d('quiz', 'Your quiz is pending for approval.'));
            }
        } else {
            $quiz = $this->Quiz->initFields();
            $this->set('title_for_layout', __d('quiz', 'Create New Quiz'));
        }

        $this->set('quiz', $quiz);
        $this->set('cats', $cats);
    }

    public function save() {

        $this->_checkPermission(array('aco' => 'quiz_create'));
        $this->_checkPermission(array('confirm' => true));
        $iUserId = $this->Auth->user('id');
        $this->autoRender = false;

        if (!empty($this->request->data['id'])) {
            $quiz = $this->Quiz->findById($this->request->data['id']);
            $this->_checkPermission(array('admins' => array($quiz['Quiz']['user_id'])));
            $this->Quiz->id = $this->request->data['id'];
        } else {
            $this->request->data['user_id'] = $iUserId;
            $this->request->data['approved'] = Configure::check('Quiz.quiz_auto_approval') ? intval(Configure::read('Quiz.quiz_auto_approval')) : 0;
        }

        if (!empty($this->request->data['unlimit_timer'])) {
            $this->Quiz->validator()->remove('timer');
            $this->request->data['timer'] = 0;
        }

        $this->request->data['description'] = str_replace('../', '/', $this->request->data['description']);
        $this->Quiz->set($this->request->data);
        $this->_validateData($this->Quiz);
        if ($this->Quiz->save()) {

            // add activity
            if (empty($this->request->data['id'])) {
                $this->loadModel('Activity');
                $this->Activity->save(array(
                    'type' => 'user',
                    'target_id' => 0,
                    'user_id' => $iUserId,
                    'action' => 'quiz_create',
                    'item_type' => 'Quiz_Quiz',
                    'item_id' => $this->Quiz->id,
                    'privacy' => PRIVACY_ME,
                    'params' => 'item',
                    'plugin' => 'Quiz',
                    'share' => 0,
                    'query' => 1
                ));

                if (Configure::check('Quiz.quiz_auto_approval') && !Configure::read('Quiz.quiz_auto_approval')) {
                    $this->Session->setFlash(__d('quiz', "Quiz has been added and is pending for admin's approval, then you can publish it."), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                }
            } else {
                $aQuiz = $this->Quiz->read();
                if ($aQuiz['Quiz']['approved'] && $aQuiz['Quiz']['published']) {
                    $this->Quiz->updateActivity($aQuiz['Quiz'], true);
                }
            }

            // update Quiz item_id for photo thumbnail
            $this->loadModel('Photo.Photo');
            $this->Photo->updateAll(array('Photo.target_id' => $this->Quiz->id), array(
                'Photo.type' => 'Quiz_Quiz',
                'Photo.user_id' => $iUserId,
                'Photo.target_id' => 0
            ));

            // Tag
            $this->loadModel('Tag');
            $this->Tag->saveTags($this->request->data['tags'], $this->Quiz->id, 'Quiz_Quiz');

            $response['result'] = 1;
            $response['id'] = $this->Quiz->id;
            echo json_encode($response);
        } else {
            $response['result'] = 0;
            $response['message'] = __d('quiz', 'Error! Please try again.');
            echo json_encode($response);
        }
    }

    public function publish($id, $status = null) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        
        $this->_checkExistence($quiz);
        $this->_checkPermission(array('admins' => array($quiz['Quiz']['user_id'])));
        
        if ($status !== null) {

            if ($quiz['Quiz']['approved']) {
                if ($status) {
                    $this->Quiz->updateActivity($quiz['Quiz'], true);
                } else {
                    $this->Quiz->updateActivity($quiz['Quiz'], false);
                }
            }

            $this->Quiz->updateAll(array('Quiz.published' => intval($status)), array('Quiz.id' => $iQuizId));
        }
        
        $this->Quiz->clear();
        $quiz = $this->Quiz->findById($iQuizId);
        $bVerifyQuestion = $this->verify_question($iQuizId);

        $this->set('quiz', $quiz);
        $this->set('bVerifyQuestion', $bVerifyQuestion);
        $this->set('title_for_layout', __d('quiz', 'Quiz Publish'));

        if (empty($quiz['Quiz']['approved']) && Configure::check('Quiz.quiz_auto_approval') && !Configure::read('Quiz.quiz_auto_approval')) {
            $this->Session->setFlash(__d('quiz', 'Your quiz is pending for approval.'));
        }
    }

    public function review($id) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);
        $this->_checkPermission(array('admins' => array($quiz['Quiz']['user_id'])));
        $aQuestions = $this->QuizQuestion->getQuestions($iQuizId);

        $this->set('quiz', $quiz);
        $this->set('questions', $aQuestions);
        $this->set('title_for_layout', __d('quiz', 'Quiz Review'));
        $this->set('description_for_layout', $this->getDescriptionForMeta($quiz['Quiz']['description']));

        $this->loadModel('Tag');
        $this->set('tags', $this->Tag->getContentTags($iQuizId, 'Quiz_Quiz'));
    }
    
    public function areview($id) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);
        $this->_checkPermission(array('admins' => array($quiz['Quiz']['user_id'])));
        $aQuestions = $this->QuizQuestion->getQuestions($iQuizId);

        $this->set('quiz', $quiz);
        $this->set('questions', $aQuestions);
        $this->set('title_for_layout', __d('quiz', 'Quiz Review'));
        $this->set('description_for_layout', $this->getDescriptionForMeta($quiz['Quiz']['description']));

        $this->loadModel('Tag');
        $this->set('tags', $this->Tag->getContentTags($iQuizId, 'Quiz_Quiz'));
    }

    public function view($id) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        // block
        $this->_checkPermission(array('user_block' => $quiz['Quiz']['user_id']));

        $this->_checkPermission(array('aco' => 'quiz_view'));
        if (!$this->checkPrivacy($quiz)) {
            $this->redirect('/pages/no-permission');
        }

        MooCore::getInstance()->setSubject($quiz);

        $this->loadModel('Like');
        $likes = $this->Like->getLikes($quiz['Quiz']['id'], 'Quiz_Quiz');
        $this->set('likes', $likes);
        $this->set('quiz', $quiz);

        $this->loadModel('Tag');
        $this->set('tags', $this->Tag->getContentTags($iQuizId, 'Quiz_Quiz'));
        $this->set('bTaken', $this->QuizTake->checkTaken($this->Auth->user('id'), $iQuizId));

        // MooSeo
        $this->set('og', array('type' => 'quiz'));
        $this->set('title_for_layout', h($quiz['Quiz']['title']));

        $sDescription = $this->getDescriptionForMeta($quiz['Quiz']['description']);
        if ($sDescription) {
            $this->set('description_for_layout', $sDescription);

            $aTags = $this->viewVars['tags'];
            if (count($aTags)) {
                $sTags = implode(",", $aTags) . ' ';
            } else {
                $sTags = '';
            }
            $this->set('mooPageKeyword', $this->getKeywordsForMeta($sTags . $sDescription));
        }

        if ($quiz['Quiz']['thumbnail']) {
            $quizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz');
            $this->set('og_image', $quizHelper->getImage($quiz, array('prefix' => '850')));
        }
    }

    public function view_detail($id) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        $this->_checkPermission(array('aco' => 'quiz_view'));
        if (!$this->checkPrivacy($quiz)) {
            $this->redirect('/pages/no-permission');
        }

        MooCore::getInstance()->setSubject($quiz);

        $this->loadModel('Like');
        $likes = $this->Like->getLikes($quiz['Quiz']['id'], 'Quiz_Quiz');
        $this->set('likes', $likes);

        $this->set('quiz', $quiz);
        $this->set('bTaken', $this->QuizTake->checkTaken($this->Auth->user('id'), $iQuizId));

        $this->render('Quiz.Elements/detail/view_detail');
    }

    public function view_participant($id, $sort = null) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        $this->_checkPermission(array('aco' => 'quiz_view'));
        if (!$this->checkPrivacy($quiz)) {
            $this->redirect('/pages/no-permission');
        }

        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = (!empty($sort)) ? $iQuizId . '/' . $sort : $iQuizId;

        $aQuizTakes = $this->QuizTake->getTakes($iQuizId, $page, $sort);
        $aQuizTakesMore = $this->QuizTake->getTakes($iQuizId, $page + 1, $sort);
        $iCountQuestion = $this->QuizQuestion->countQuestions($iQuizId);

        if (!empty($aQuizTakesMore)) {
            $this->set('more_result', 1);
        }
        
        $bViewTaken = false;
        $iUserId = $this->Auth->user('id');
        if ($iUserId == $quiz['Quiz']['user_id']) {
            $bViewTaken = true;
        } else {
            $bViewTaken = $this->QuizTake->checkTaken($iUserId, $iQuizId);
        }

        $this->set('bViewTaken', $bViewTaken);
        $this->set('bLoadHeader', ($page == 1) ? true : false);
        $this->set('more_url', '/quizzes/view_participant/' . h($url) . '/page:' . ($page + 1));

        $this->set('quiz', $quiz);
        $this->set('aQuizTakes', $aQuizTakes);
        $this->set('iCountQuestion', $iCountQuestion);
        $this->render('Quiz.Elements/detail/view_participant');
    }

    public function view_take() {
        if (empty($this->request->data['quiz_id'])) {
            $this->redirect('/pages/no-permission');
        }

        $iQuizId = intval($this->request->data['quiz_id']);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'quiz_view'));
        if (!$this->checkPrivacy($quiz) || !$this->QuizTake->checkTaken($this->Auth->user('id'), $iQuizId, $this->request->data['privacy_hash'])) {
            $this->redirect('/pages/no-permission');
        }

        $aQuestions = $this->QuizQuestion->getQuestions($iQuizId);
        $this->set(array(
            'quiz' => $quiz,
            'questions' => $aQuestions
        ));

        $this->render('Quiz.Elements/detail/view_take');
    }

    public function view_taken($id) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'quiz_view'));
        if (!$this->checkPrivacy($quiz)) {
            $this->redirect('/pages/no-permission');
        }

        MooCore::getInstance()->setSubject($quiz);

        $this->loadModel('Like');
        $likes = $this->Like->getLikes($quiz['Quiz']['id'], 'Quiz_Quiz');
        $this->set('likes', $likes);

        $aQuizTake = $this->QuizTake->findByUserIdAndQuizId($this->Auth->user('id'), $iQuizId);
        $aResultQuestion = $this->QuizResult->getResultView($aQuizTake);
        $aResult = $this->QuizResult->getResult($aQuizTake);

        $this->loadModel('Activity');
        $aActivity = $this->Activity->find('first', array('conditions' => array('Activity.action' => 'quiz_take', 'Activity.item_type' => 'Quiz_Quiz', 'Activity.user_id' => $this->Auth->user('id'), 'Activity.item_id' => $iQuizId)));

        $this->set(array(
            'quiz' => $quiz,
            'aResult' => $aResult,
            'aActivity' => $aActivity,
            'aResultQuestion' => $aResultQuestion,
            'sUserName' => $aQuizTake['User']['name']
        ));

        $this->render('Quiz.Elements/detail/view_taken');
    }

    public function view_finish($id = null) {
        $iTakeId = intval($id);
        $aQuizTake = $this->QuizTake->findById($iTakeId);
        $this->_checkExistence($aQuizTake);

        $this->_checkPermission(array('confirm' => true));
        $aResult = $this->QuizResult->getResult($aQuizTake);
        $this->set('aResult', $aResult);
        $this->render('Quiz.Elements/detail/view_finish');
    }

    public function view_result($id = null) {
        $iTakeId = intval($id);
        $aQuizTake = $this->QuizTake->findById($iTakeId);
        $this->_checkExistence($aQuizTake);

        $this->_checkPermission(array('confirm' => true));
        $aResultQuestion = $this->QuizResult->getResultView($aQuizTake);
        $aResult = $this->QuizResult->getResult($aQuizTake);

        $this->set('aResult', $aResult);
        $this->set('aResultQuestion', $aResultQuestion);
        $this->set('sUserName', $aQuizTake['User']['name']);
        $this->render('Quiz.Elements/detail/view_result');
    }

    public function ajax_take_privacy($id = null) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        $this->_checkPermission(array('aco' => 'quiz_view'));
        $this->_checkPermission(array('confirm' => true));

        if (!$this->checkPrivacy($quiz) || $this->QuizTake->checkTaken($this->Auth->user('id'), $iQuizId)) {
            $this->redirect('/pages/no-permission');
        }

        $this->set('quiz', $quiz);
        $this->render('Quiz.Elements/ajax/take_privacy');
    }

    public function do_take_privacy() {

        $this->_checkPermission(array('aco' => 'quiz_view'));
        $this->_checkPermission(array('confirm' => true));
        $this->autoRender = false;

        if (!empty($this->request->data['id'])) {
            $iQuizId = intval($this->request->data['id']);
            $quiz = $this->Quiz->findById($iQuizId);
            $this->_checkExistence($quiz);

            $iUserId = $this->Auth->user('id');
            if ($this->QuizTake->checkTaken($iUserId, $iQuizId)) {
                $response['result'] = 0;
                echo json_encode($response);
                exit();
            }

            $this->QuizTake->create();
            $this->QuizTake->set(array(
                'user_id' => $iUserId,
                'quiz_id' => $iQuizId,
                'privacy' => $this->request->data['privacy'],
                'privacy_hash' => md5($iQuizId . $iUserId . time())
            ));

            if ($this->QuizTake->save()) {
                $aQuizTake = $this->QuizTake->read();

                // Add activity
                $this->loadModel('Activity');
                $this->Activity->save(array(
                    'type' => 'user',
                    'target_id' => 0,
                    'user_id' => $iUserId,
                    'action' => 'quiz_take',
                    'item_type' => 'Quiz_Quiz',
                    'item_id' => $iQuizId,
                    'status' => 'waiting',
                    'params' => 'item',
                    'plugin' => 'Quiz',
                    'share' => 1
                ));

                // Update Take Count
                $this->Quiz->increaseCounter($iQuizId, 'take_count');

                // Clear Cache widget
                Cache::clearGroup('quiz');

                $response['privacy_hash'] = $aQuizTake['QuizTake']['privacy_hash'];
                $response['quiz_id'] = $iQuizId;
                $response['result'] = 1;
                echo json_encode($response);
            }
        } else {
            $response['result'] = 0;
            echo json_encode($response);
        }
    }

    public function do_take($id = null) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        $this->_checkPermission(array('aco' => 'quiz_view'));
        $this->_checkPermission(array('confirm' => true));
        $iUserId = $this->Auth->user('id');
        $this->autoRender = false;

        if (!$this->checkPrivacy($quiz)) {
            $this->redirect('/pages/no-permission');
        }

        if (empty($this->request->data)) {
            $response['result'] = 3;
            echo json_encode($response);
        } elseif (!empty($this->request->data['correct'])) {
            $iCountAnswer = count($this->request->data['correct']);
            $iCountQuestions = $this->QuizQuestion->countQuestions($iQuizId);
            if ($iCountAnswer != $iCountQuestions && !empty($this->request->data['direct_point'])) {
                $response['result'] = 2;
                echo json_encode($response);
            } else {
                if (!$this->QuizTake->checkTaken($iUserId, $iQuizId)) {
                    $response['result'] = 3;
                    echo json_encode($response);
                } else {

                    $aQuizTake = $this->QuizTake->findByUserIdAndQuizId($iUserId, $iQuizId);
                    if (empty($aQuizTake)) {
                        $response['result'] = 3;
                        echo json_encode($response);
                    } else {
                        $iCountCorrectAnswer = 0;
                        $this->loadModel('Quiz.QuizAnswer');
                        foreach ($this->request->data['correct'] as $iQuestionId => $iAnswerId) {
                            if ($this->QuizAnswer->checkAnswer($iQuestionId, $iAnswerId)) {
                                $this->QuizResult->create();
                                $this->QuizResult->save(array(
                                    'quiz_take_id' => $aQuizTake['QuizTake']['id'],
                                    'question_id' => $iQuestionId,
                                    'answer_id' => $iAnswerId,
                                ));

                                if ($this->QuizAnswer->checkCorrectAnswer($iQuestionId, $iAnswerId)) {
                                    $iCountCorrectAnswer++;
                                }
                            }
                        }

                        // Update Result
                        $this->QuizTake->updateAll(array('QuizTake.correct_answer' => $iCountCorrectAnswer), array('QuizTake.id' => $aQuizTake['QuizTake']['id']));

                        $response['result'] = 1;
                        $response['take_id'] = $aQuizTake['QuizTake']['id'];
                        echo json_encode($response);
                    }
                }
            }
        } else {
            $response['result'] = 0;
            $response['message'] = __d('quiz', 'Please add questions into quiz.');
            echo json_encode($response);
        }
    }

    public function delete($id = null) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);

        $this->_checkExistence($quiz);
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('admins' => array($quiz['Quiz']['user_id'])));

        $this->Quiz->delete($iQuizId);
        $this->Session->setFlash(__d('quiz', 'Quiz has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect('/quizzes');
    }
    
    public function confirm_delete($id = null) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);

        $this->_checkExistence($quiz);
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('admins' => array($quiz['Quiz']['user_id'])));

        $this->Quiz->delete($iQuizId);
        $this->set('title_for_layout', __d('quiz', 'Delete') . ' ' . __d('quiz', 'Quiz'));
    }

    public function categories_list() {
        if ($this->request->is('requested')) {
            $this->loadModel('Category');
            $role_id = $this->_getUserRoleId();
            $categories = $this->Category->getCategories('Quiz_Quiz', $role_id);
            foreach ($categories as &$aCategory) {
                if (!empty($aCategory['children'])) {
                    foreach ($aCategory['children'] as $iKey => $aChildren) {
                        $iNumCategory = $this->Quiz->countQuizByCategory($aChildren['Category']['id']);
                        $aCategory['children'][$iKey]['Category']['item_count'] = $iNumCategory;
                    }
                } else {
                    $iNumCategory = $this->Quiz->countQuizByCategory($aCategory['Category']['id']);
                    $aCategory['Category']['item_count'] = $iNumCategory;
                }
            }

            return $categories;
        }
    }

    public function verify_question($id) {
        $iQuizId = intval($id);
        $quiz = $this->Quiz->findById($iQuizId);
        $this->_checkExistence($quiz);

        $iCountQuestionConfig = Configure::check('Quiz.quiz_questions_count') ? Configure::read('Quiz.quiz_questions_count') : 2;
        $iCountQuestion = $this->QuizQuestion->countQuestions($iQuizId);
        if ($iCountQuestion < $iCountQuestionConfig) {
            return false;
        }

        return true;
    }

    public function checkPrivacy($aQuiz) {

        $iUserId = $this->Auth->user('id');
        $viewer = MooCore::getInstance()->getViewer();

        // check published
        if (empty($aQuiz['Quiz']['published']) && ($iUserId == $aQuiz['Quiz']['user_id'] || (!empty($viewer) && $viewer['Role']['is_admin']))) {
            $this->redirect('/quizzes/review/' . $aQuiz['Quiz']['id']);
        } elseif (empty($aQuiz['Quiz']['published'])) {
            $this->redirect('/pages/no-permission');
        }

        if ($iUserId == $aQuiz['Quiz']['user_id']) { // owner
            return true;
        }

        if (!empty($viewer) && $viewer['Role']['is_admin']) {
            return true;
        }

        switch ($aQuiz['Quiz']['privacy']) {
            case PRIVACY_FRIENDS:

                $areFriends = false;
                if (!empty($iUserId)) { //  check if user is a friend
                    $this->loadModel('Friend');
                    $areFriends = $this->Friend->areFriends($iUserId, $aQuiz['Quiz']['user_id']);
                }

                if (!$areFriends) {
                    $this->redirect('/pages/no-permission');
                }

                break;

            case PRIVACY_ME:
                $this->redirect('/pages/no-permission');
                break;
        }

        return true;
    }

}
