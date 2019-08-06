<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('QuizAppModel', 'Quiz.Model');

class Quiz extends QuizAppModel {

    public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumbnail' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}quizzes{DS}{field}{DS}'
            )
        ),
        'Hashtag' => array(
            'field_created_get_hashtag' => 'description',
            'field_updated_get_hashtag' => 'description',
        ),
        'Storage.Storage' => array(
            'type' => array('quizzes' => 'thumbnail', 'quizzes'),
        ),
    );
    public $mooFields = array('title', 'href', 'plugin', 'type', 'url', 'thumb', 'privacy');
    public $belongsTo = array('User', 'Category');
    public $order = 'Quiz.id desc';
    public $validate = array(
        'title' => array(
            'rule' => 'notBlank',
            'message' => 'Title is required',
        ),
        'category_id' => array(
            'rule' => 'notBlank',
            'message' => 'Category is required'
        ),
        'description' => array(
            'rule' => 'notBlank',
            'message' => 'Description is required',
        ),
        'timer' => array(
            'required' => true,
            'rule' => 'naturalNumber',
            'message' => 'Enter a valid timer',
        ),
        'pass_score' => array(
            'required' => true,
            'rule' => array('range', 1, 100),
            'message' => 'Pass Score a number between 1 and 100',
        ),
        'tags' => array(
            'validateTag' => array(
                'rule' => array('validateTag'),
                'message' => 'No special characters (/,?,#,%,...) allowed in Tags',
            )
        )
    );
    public $hasMany = array(
        'Comment' => array(
            'className' => 'Comment',
            'foreignKey' => 'target_id',
            'conditions' => array('Comment.type' => 'Quiz_Quiz'),
            'dependent' => true,
        ),
        'Like' => array(
            'className' => 'Like',
            'foreignKey' => 'target_id',
            'conditions' => array('Like.type' => 'Quiz_Quiz'),
            'dependent' => true,
        ),
        'Tag' => array(
            'className' => 'Tag',
            'foreignKey' => 'target_id',
            'conditions' => array('Tag.type' => 'Quiz_Quiz'),
            'dependent' => true,
        ),
    );

    public function beforeDelete($cascade = true) {

        $aQuiz = $this->findById($this->id);
        if (!empty($aQuiz)) {
            // delete question
            $oQuizQuestionModel = MooCore::getInstance()->getModel('Quiz.QuizQuestion');
            $oQuizQuestionModel->deleteAll(array('QuizQuestion.quiz_id' => $aQuiz['Quiz']['id']));

            // delete take
            $oQuizTakeModel = MooCore::getInstance()->getModel('Quiz.QuizTake');
            $oQuizTakeModel->deleteAll(array('QuizTake.quiz_id' => $aQuiz['Quiz']['id']), true, true);

            // delete activity
            $oActivityModel = MooCore::getInstance()->getModel('Activity');
            $aActivityId = $oActivityModel->find('list', array('fields' => array('Activity.id'), 'conditions' => array('Activity.action' => 'quiz_create', 'Activity.item_type' => 'Quiz_Quiz', 'Activity.item_id' => $aQuiz['Quiz']['id'])));
            if (!empty($aActivityId)) {
                $oActivityModel->deleteAll(array('Activity.parent_id' => $aActivityId));
                $oActivityModel->deleteAll(array('Activity.id' => $aActivityId), true, true);
            }
        }

        parent::beforeDelete($cascade);
    }

    public function getQuizzes($type = null, $param = null, $page = 1, $sort = null) {

        $order = 'Quiz.created desc';
        $limit = Configure::check('Quiz.quiz_item_per_pages') ? Configure::read('Quiz.quiz_item_per_pages') : RESULTS_LIMIT;

        $viewer = MooCore::getInstance()->getViewer();
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $isAdmin = isset($viewer['Role']['is_admin']) ? $viewer['Role']['is_admin'] : false;

        $cond = array('Quiz.published' => 1, 'Quiz.approved' => 1);
        if ($isAdmin || $type == 'my' || ($type == 'user' && $viewer_id == $param)) {
            $cond = array();
        }

        switch ($type) {
            case 'category':
                if (!empty($param)) {
                    if ($isAdmin) {
                        $cond['Quiz.category_id'] = $param;
                    } else {
                        $cond['Quiz.category_id'] = $param;
                        $cond['Quiz.privacy'] = PRIVACY_EVERYONE;
                    }
                }
                break;

            case 'friends':
                if (!empty($param)) {
                    $oModelFriend = MooCore::getInstance()->getModel('Friend');
                    $friends = $oModelFriend->getFriends($param);
                    if ($isAdmin) {
                        $cond['Quiz.user_id'] = $friends;
                    } else {
                        $cond['Quiz.user_id'] = $friends;
                        array_push($cond, 'Quiz.privacy <> ' . PRIVACY_ME);
                    }
                }
                break;

            case 'home':
            case 'my':
                if (!empty($param)) {
                    $cond = array('Quiz.user_id' => $param);
                }
                break;

            case 'taken':
                if (!empty($param)) {
                    $oModelQuizTake = MooCore::getInstance()->getModel('Quiz.QuizTake');
                    $aMyTaken = $oModelQuizTake->getMyTaken($param);
                    $cond = array('Quiz.id' => $aMyTaken);
                }
                break;

            case 'user':
                if (!empty($param)) {
                    if ($isAdmin || $param == $viewer_id) {
                        $cond['Quiz.user_id'] = $param;
                    } else {
                        $cond['Quiz.user_id'] = $param;
                        $cond['Quiz.privacy'] = PRIVACY_EVERYONE;
                    }
                }
                break;

            case 'search':
                if (!empty($param)) {
                    if ($isAdmin) {
                        $cond['OR'] = array(
                            array('Quiz.title LIKE "%' . $param . '%"'),
                            array('Quiz.description LIKE "%' . $param . '%"'),
                        );
                    } else {
                        $cond['Quiz.privacy'] = PRIVACY_EVERYONE;
                        $cond['OR'] = array(
                            array('Quiz.title LIKE "%' . $param . '%"'),
                            array('Quiz.description LIKE "%' . $param . '%"'),
                        );
                    }
                }
                break;

            default:
                if (!$isAdmin) {
                    $cond['Quiz.privacy'] = PRIVACY_EVERYONE;
                }
                break;
        }

        switch ($sort) {
            case 'like':
                $order = 'Quiz.like_count desc';
                break;

            case 'taken':
                $order = 'Quiz.take_count desc';
                break;
        }

        // only get quizzes of active user
        $cond['User.active'] = 1;
        $cond = $this->addBlockCondition($cond);
        return $this->find('all', array('conditions' => $cond, 'order' => $order, 'limit' => $limit, 'page' => $page));
    }

    public function getPopularQuizzes($limit = 5) {
        $cond = array(
            'User.active' => 1,
            'Quiz.approved' => 1,
            'Quiz.published' => 1,
            'Quiz.privacy' => PRIVACY_EVERYONE,
            'DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Quiz.created' => intval(Configure::read('core.popular_interval'))
        );
        $cond = $this->addBlockCondition($cond);
        return $this->find('all', array('conditions' => $cond, 'order' => 'Quiz.like_count desc', 'limit' => intval($limit)));
    }

    public function getTakenQuizzes($limit = 5) {
        $cond = array(
            'User.active' => 1,
            'Quiz.approved' => 1,
            'Quiz.published' => 1,
            'Quiz.privacy' => PRIVACY_EVERYONE,
            'DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Quiz.created' => intval(Configure::read('core.popular_interval'))
        );
        $cond = $this->addBlockCondition($cond);
        return $this->find('all', array('conditions' => $cond, 'order' => 'Quiz.take_count desc', 'limit' => intval($limit)));
    }

    public function updateActivity($aQuiz, $bPulish) {
        $oActivityModel = MooCore::getInstance()->getModel('Activity');
        $aCreateActivityId = $oActivityModel->find('list', array('fields' => array('Activity.id'), 'conditions' => array('Activity.action' => 'quiz_create', 'Activity.item_type' => 'Quiz_Quiz', 'Activity.item_id' => $aQuiz['id'])));
        $aTakeActivityId = $oActivityModel->find('list', array('fields' => array('Activity.id'), 'conditions' => array('Activity.action' => 'quiz_take', 'Activity.item_type' => 'Quiz_Quiz', 'Activity.item_id' => $aQuiz['id'])));
        $aItemDetailActivityId = $oActivityModel->find('list', array('fields' => array('Activity.id'), 'conditions' => array('Activity.action' => 'quiz_item_detail_share', 'Activity.item_type' => 'Quiz_Quiz', 'Activity.parent_id' => $aQuiz['id'])));
        if ($bPulish) {
            if (!empty($aCreateActivityId)) {
                // activity quiz
                $oActivityModel->updateAll(array('Activity.status' => '\'ok\'', 'Activity.privacy' => $aQuiz['privacy'], 'Activity.share' => ($aQuiz['privacy'] != PRIVACY_ME) ? 1 : 0), array('Activity.id' => $aCreateActivityId));
                $oActivityModel->updateAll(array('Activity.status' => '\'ok\''), array('Activity.parent_id' => $aCreateActivityId));
            }

            if (!empty($aTakeActivityId)) {
                // activity take
                $oActivityModel->updateAll(array('Activity.status' => '\'ok\''), array('Activity.parent_id' => $aTakeActivityId));
            }
            
            if (!empty($aItemDetailActivityId)) {
                // activity quiz_item_detail_share
                $oActivityModel->updateAll(array('Activity.status' => '\'ok\''), array('Activity.id' => $aItemDetailActivityId));
            }
        } else {
            if (!empty($aCreateActivityId)) {
                // activity quiz
                $oActivityModel->updateAll(array('Activity.status' => '\'waiting\'', 'Activity.privacy' => PRIVACY_ME, 'Activity.share' => 0), array('Activity.id' => $aCreateActivityId));
                $oActivityModel->updateAll(array('Activity.status' => '\'waiting\''), array('Activity.parent_id' => $aCreateActivityId));
            }

            if (!empty($aTakeActivityId)) {
                // activity take
                $oActivityModel->updateAll(array('Activity.status' => '\'waiting\''), array('Activity.parent_id' => $aTakeActivityId));
            }
            
            if (!empty($aItemDetailActivityId)) {
                // activity quiz_item_detail_share
                $oActivityModel->updateAll(array('Activity.status' => '\'waiting\''), array('Activity.id' => $aItemDetailActivityId));
            }
        }
    }

    public function countQuizByUser($iUserId) {

        $viewer = MooCore::getInstance()->getViewer();
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $isAdmin = isset($viewer['Role']['is_admin']) ? $viewer['Role']['is_admin'] : false;

        $aCond = array('Quiz.user_id' => $iUserId);
        if (!$isAdmin && $viewer_id != $iUserId) {
            $aCond['Quiz.privacy'] = PRIVACY_EVERYONE;
            $aCond['Quiz.published'] = 1;
            $aCond['Quiz.approved'] = 1;
        }

        return $this->find('count', array('conditions' => $aCond));
    }

    public function countQuizByCategory($iCategoryId) {

        $aCond = array(
            'Quiz.category_id' => $iCategoryId,
            'User.active' => 1
        );

        $viewer = MooCore::getInstance()->getViewer();
        $isAdmin = isset($viewer['Role']['is_admin']) ? $viewer['Role']['is_admin'] : false;

        if (!$isAdmin) {
            $aCond['Quiz.published'] = 1;
            $aCond['Quiz.approved'] = 1;
        }

        return $this->find('count', array('conditions' => $aCond));
    }

    public function getQuizHashtags($QuizIds, $limit = RESULTS_LIMIT, $page = 1) {
        $aCond = array(
            'User.active' => 1,
            'Quiz.id' => $QuizIds,
        );

        $aCond['User.active'] = 1;
        return $this->find('all', array('conditions' => $aCond, 'limit' => $limit, 'page' => $page));
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

    public function updateCounter($id, $field = 'comment_count', $conditions = '', $model = 'Comment') {
        if (empty($conditions)) {
            $conditions = array('Comment.type' => 'Quiz_Quiz', 'Comment.target_id' => $id);
        }

        parent::updateCounter($id, $field, $conditions, $model);
        Cache::clearGroup('quiz');
    }

    public function getHref($aRow) {
        $oRequest = Router::getRequest();
        if (!empty($aRow['title']) && !empty($aRow['id'])) {
            return $oRequest->base . '/quizzes/view/' . $aRow['id'] . '/' . seoUrl($aRow['title']);
        } else {
            return '';
        }
    }

    public function getThumb($row) {
        return 'thumbnail';
    }

    public function getPrivacy($row) {
        if (isset($row['privacy'])) {
            return $row['privacy'];
        }
        return false;
    }

    public function afterSave($creates, $options = array()) {
        Cache::clearGroup('quiz');
    }

    public function afterDelete() {
        Cache::clearGroup('quiz');
    }

}
