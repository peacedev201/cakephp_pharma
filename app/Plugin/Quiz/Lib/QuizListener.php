<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('CakeEventListener', 'Event');

class QuizListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'Model.beforeFind' => 'beforeFind',
            'Model.beforeDelete' => 'afterDelete',
            'MooView.beforeRender' => 'beforeRender',
            'Controller.Search.search' => 'searchSearch',
            'Controller.Share.afterShare' => 'afterShare',
            'Controller.Search.hashtags' => 'searchHashtags',
            'Controller.Comment.afterComment' => 'afterComment',
            'Controller.Search.suggestion' => 'searchSuggestion',
            'View.Adm.Layout.adminGetContentInfo' => 'widgetTag',
            'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
            'profile.afterRenderMenu' => 'profileAfterRenderMenu',
            'welcomeBox.afterRenderMenu' => 'welcomeAfterRenderMenu',
            'Controller.Home.adminIndex.Statistic' => 'homeStatistic',
            'Controller.Search.hashtags_filter' => 'searchHashtagsFilter',
            'Plugin.Controller.Category.beforeDelete' => 'beforeDeleteCategory',
            // Integration S3
            'StorageHelper.quizzes.getUrl.local' => 'storageGetUrlLocal',
            'StorageHelper.quizzes.getUrl.amazon' => 'storageGetUrlAmazon',
            'StorageAmazon.quizzes.getFilePath' => 'storageAmazonGetFilePath',
            'StorageTaskAwsCronTransfer.execute' => 'storageTaskAwsCronTransfer',
            'StorageAmazon.quizzes.putObject.success' => 'storageAmazonPutObjectSuccessCallback',
            // version moo-301
            'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu',
            'ApiHelper.renderAFeed.quiz_take' => 'feedQuizTakeRender',
            'ApiHelper.renderAFeed.quiz_create' => 'feedQuizCreateRender',
            'ApiHelper.renderAFeed.quiz_item_detail_share' => 'feedQuizItemDetailShare',
            'Plugin.View.Api.Search' => 'apiSearch',
        );
    }

    public function apiAfterRenderMenu($oEvent) {
        $aUser = MooCore::getInstance()->getSubject();
        $oEvent->data['result']['quiz'] = array(
            'url' => FULL_BASE_URL . $oEvent->subject()->request->base . '/quizzes/profile/' . $aUser['User']['id'] . '/user',
            'text' => __d('quiz', 'Quizzes'),
            'cnt' => 0
        );
    }
    
    public function feedQuizTakeRender($oEvent) {
        $sActorHtml = $oEvent->data['actorHtml'];
        $aData = $oEvent->data['data'];
        $oView = $oEvent->subject();
        $aQuiz = $oEvent->data['objectPlugin'];
        $sTitle = __d('review', 'took a quiz');
        $oQuizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz');
        $aPhotoSizes = explode('|', Configure::read('core.photo_image_sizes'));

        $aImages = array();
        foreach ($aPhotoSizes as $sSize) {
            $aImages[$sSize] = $oQuizHelper->getImage($aQuiz, array('prefix' => $sSize));
        }

        $bHideLikeAndComment = true;
        if ($aQuiz['Quiz']['approved'] && $aQuiz['Quiz']['published']) {
            $bHideLikeAndComment = false;
        }
        
        $sDescription = $oView->Text->convert_clickable_links_for_hashtags($oView->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $aQuiz['Quiz']['description'])), 200, array('exact' => false)), Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0);
        $sDescription .= '<div><strong>' . __d('quiz', 'Result') . '</strong>: ' . $oQuizHelper->getResultOnApp($aQuiz, $aData['Activity']['user_id']) . '</div>';

        $oEvent->result['result'] = array(
            'type' => 'create',
            'title' => $sTitle,
            'titleHtml' => $sActorHtml . ' ' . $sTitle,
            'objects' => array(
                'id' => $aQuiz['Quiz']['id'],
                'type' => $aQuiz['Quiz']['moo_type'],
                'title' => h($aQuiz['Quiz']['moo_title']),
                'url' => FULL_BASE_URL . str_replace('?', '', mb_convert_encoding($aQuiz['Quiz']['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $sDescription,
                'images' => $aImages,
            ),
            'hideLikeAndComment' => $bHideLikeAndComment
        );
    }

    public function feedQuizCreateRender($oEvent) {
        $sActorHtml = $oEvent->data['actorHtml'];
        $oView = $oEvent->subject();
        $aQuiz = $oEvent->data['objectPlugin'];
        $sTitle = __d('review', 'added new quizzes');
        $oQuizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz');
        $aPhotoSizes = explode('|', Configure::read('core.photo_image_sizes'));

        $aImages = array();
        foreach ($aPhotoSizes as $sSize) {
            $aImages[$sSize] = $oQuizHelper->getImage($aQuiz, array('prefix' => $sSize));
        }

        $bHideLikeAndComment = true;
        if ($aQuiz['Quiz']['approved'] && $aQuiz['Quiz']['published']) {
            $bHideLikeAndComment = false;
        }

        $oEvent->result['result'] = array(
            'type' => 'create',
            'title' => $sTitle,
            'titleHtml' => $sActorHtml . ' ' . $sTitle,
            'objects' => array(
                'id' => $aQuiz['Quiz']['id'],
                'type' => $aQuiz['Quiz']['moo_type'],
                'title' => h($aQuiz['Quiz']['moo_title']),
                'url' => FULL_BASE_URL . str_replace('?', '', mb_convert_encoding($aQuiz['Quiz']['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $oView->Text->convert_clickable_links_for_hashtags($oView->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $aQuiz['Quiz']['description'])), 200, array('exact' => false)), Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0),
                'images' => $aImages,
            ),
            'hideLikeAndComment' => $bHideLikeAndComment
        );
    }

    public function feedQuizItemDetailShare($oEvent) {
        $oView = $oEvent->subject();
        $aData = $oEvent->data['data'];
        $sActorHtml = $oEvent->data['actorHtml'];
        $oQuizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz');
        $aPhotoSizes = explode('|', Configure::read('core.photo_image_sizes'));
        $aSubject = MooCore::getInstance()->getItemByType($aData['Activity']['type'], $aData['Activity']['target_id']);

        if (!empty($aData['Activity']['parent_id'])) {
            $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
            $aQuiz = $oQuizModel->findById($aData['Activity']['parent_id']);

            $sTitle = __d('quiz', "shared %s's <a href='%s'>quiz</a>", $oView->Html->link($aQuiz['User']['name'], FULL_BASE_URL . $aQuiz['User']['moo_href']), FULL_BASE_URL . $aQuiz['Quiz']['moo_href']);
            $sTitleHtml = $sActorHtml . ' ' . $sTitle;

            $aImages = array();
            foreach ($aPhotoSizes as $sSize) {
                $aImages[$sSize] = $oQuizHelper->getImage($aQuiz, array('prefix' => $sSize));
            }

            $bHideLikeAndComment = true;
            if ($aQuiz['Quiz']['approved'] && $aQuiz['Quiz']['published']) {
                $bHideLikeAndComment = false;
            }

            $aTarget = array(
                'url' => FULL_BASE_URL . $aQuiz['User']['moo_href'],
                'name' => $aQuiz['User']['name'],
                'id' => $aQuiz['User']['id'],
                'type' => 'User',
            );

            if (!empty($aSubject)) {
                list($sPluginName, $sName) = mooPluginSplit($aData['Activity']['type']);
                $bShowSubject = MooCore::getInstance()->checkShowSubjectActivity($aSubject);
                
                if ($bShowSubject) {
                    $sTitleHtml .= ' > ' . $oView->Html->link(h($aSubject[$sName]['moo_title']), FULL_BASE_URL . $aSubject[$sName]['moo_href']);
                }
            }

            $oEvent->result['result'] = array(
                'type' => 'share',
                'title' => $sTitle,
                'titleHtml' => $sTitleHtml,
                'objects' => array(
                    'id' => $aQuiz['Quiz']['id'],
                    'type' => $aQuiz['Quiz']['moo_type'],
                    'title' => h($aQuiz['Quiz']['moo_title']),
                    'url' => FULL_BASE_URL . str_replace('?', '', mb_convert_encoding($aQuiz['Quiz']['moo_href'], 'UTF-8', 'UTF-8')),
                    'description' => $oView->Text->convert_clickable_links_for_hashtags($oView->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $aQuiz['Quiz']['description'])), 200, array('exact' => false)), Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0),
                    'images' => $aImages,
                ),
                'target' => $aTarget,
                'hideLikeAndComment' => $bHideLikeAndComment
            );
        }
    }

    public function storageGetUrlLocal($oEvent) {
        $oStorageHelper = $oEvent->subject();
        $request = Router::getRequest();

        if ($oEvent->data['thumb']) {
            $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/quizzes/thumbnail/' . $oEvent->data['oid'] . '/' . $oEvent->data['prefix'] . $oEvent->data['thumb'];
        } else {
            $url = $oStorageHelper->getImage("quiz/img/noimage/quiz.png");
        }

        $oEvent->result['url'] = $url;
    }

    public function storageGetUrlAmazon($oEvent) {
        $oStorageHelper = $oEvent->subject();
        $oEvent->result['url'] = $oStorageHelper->getAwsURL($oEvent->data['oid'], "quizzes", $oEvent->data['prefix'], $oEvent->data['thumb']);
    }

    public function storageAmazonGetFilePath($oEvent) {
        $oid = $oEvent->data['oid'];
        $name = $oEvent->data['name'];
        $thumb = $oEvent->data['thumb'];

        $path = false;
        if (!empty($thumb)) {
            $path = WWW_ROOT . "uploads" . DS . "quizzes" . DS . "thumbnail" . DS . $oid . DS . $name . $thumb;
        }

        $oEvent->result['path'] = $path;
    }

    public function storageTaskAwsCronTransfer($oEvent) {
        $oStorageTask = $oEvent->subject();
        $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
        $aQuizzes = $oQuizModel->find('all', array(
            'conditions' => array("Quiz.id > " => $oStorageTask->getMaxTransferredItemId("quizzes")),
            'fields' => array('Quiz.id', 'Quiz.thumbnail'),
            'order' => array('Quiz.id'),
            'limit' => 10,
        ));

        if ($aQuizzes) {
            foreach ($aQuizzes as $aQuiz) {
                if (!empty($aQuiz["Quiz"]["thumbnail"])) {
                    $oStorageTask->transferObject($aQuiz["Quiz"]['id'], "quizzes", '', $aQuiz["Quiz"]["thumbnail"]);
                }
            }
        }
    }

    public function storageAmazonPutObjectSuccessCallback($oEvent) {
        $path = $oEvent->data['path'];
        if (Configure::read('Storage.storage_amazon_delete_image_after_adding') == "1") {
            if ($path) {
                $file = new File($path);
                $file->delete();
                $file->close();
            }
        }
    }

    public function beforeFind($oEvent) {
        $oModel = $oEvent->subject();
        $sType = ($oModel->plugin ? $oModel->plugin . '_' : '') . get_class($oModel);
        if ($sType == 'Tag') {
            if (!empty($oEvent->data[0]['conditions']['type']) && $oEvent->data[0]['conditions']['type'] == 'quizzes') {
                $oEvent->data[0]['conditions']['type'] = 'Quiz_Quiz';
            }
        }
    }

    public function afterDelete($oEvent) {
        $oModel = $oEvent->subject();
        $sType = ($oModel->plugin ? $oModel->plugin . '_' : '') . get_class($oModel);
        if ($sType == 'User') {
            $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
            $oQuizModel->deleteAll(array('Quiz.user_id' => $oEvent->subject()->id), true, true);
        }
    }

    public function beforeDeleteCategory($oEvent) {
        $aCategory = $oEvent->data['category'];
        if ($aCategory['Category']['type'] == 'Quiz_Quiz') {
            $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
            $oQuizModel->deleteAll(array('Quiz.category_id' => $aCategory['Category']['id']), true, true);
        }
    }

    public function afterShare($oEvent) {
        $oController = $oEvent->subject();
        $aData = $oEvent->data['data'];
        if (isset($aData['item_type']) && $aData['item_type'] == 'Quiz_Quiz') {
            if ($aData['action'] == 'quiz_take_share') {
                if (!empty($aData['parent_id'])) {
                    $oActivityModel = MooCore::getInstance()->getModel('Activity');
                    $aActivity = $oActivityModel->findById($aData['parent_id']);

                    $iUserId = $oController->Auth->user('id');
                    if ($iUserId == $aActivity['Activity']['user_id']) {
                        $oQuizTakeModel = MooCore::getInstance()->getModel('Quiz.QuizTake');
                        $oQuizTakeModel->updateAll(array('QuizTake.privacy' => PRIVACY_PUBLIC), array('QuizTake.user_id' => $iUserId, 'QuizTake.quiz_id' => $aActivity['Activity']['item_id']));
                    }
                }
            }

            if ($aData['action'] == 'quiz_item_detail_share') {
                if (!empty($aData['parent_id'])) {
                    $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
                    $aQuiz = $oQuizModel->findById($aData['parent_id']);
                    
                    if (!empty($aQuiz)) {
                        $oQuizModel->increaseCounter($aData['parent_id'], 'share_count');
                    }
                }
            }

            if ($aData['action'] == 'quiz_create_share') {
                if (!empty($aData['parent_id'])) {
                    $oActivityModel = MooCore::getInstance()->getModel('Activity');
                    $aActivity = $oActivityModel->findById($aData['parent_id']);

                    $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
                    $aQuiz = $oQuizModel->findById($aActivity['Activity']['item_id']);
                    
                    if (!empty($aQuiz)) {
                        $oQuizModel->increaseCounter($aActivity['Activity']['item_id'], 'share_count');
                    }
                }
            }
        }
    }

    public function afterComment($oEvent) {
        $aData = $oEvent->data['data'];
        $iTargetId = isset($aData['target_id']) ? $aData['target_id'] : null;
        $sType = isset($aData['type']) ? $aData['type'] : '';
        if ($sType == 'Quiz_Quiz' && !empty($iTargetId)) {
            $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
            $oQuizModel->updateCounter($iTargetId);
        }
    }

    public function beforeRender($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled')) {

            $sMin = "";
            if (Configure::read('debug') == 0) {
                $sMin = "min.";
            }

            $oView->Helpers->Html->css(array('Quiz.main', 'Quiz.countdown'), array('block' => 'css'));
            $oView->Helpers->MooRequirejs->addPath(array(
                "mooQuiz" => $oView->Helpers->MooRequirejs->assetUrlJS("Quiz.js/main.{$sMin}js"),
                "mooJqueryUi" => $oView->Helpers->MooRequirejs->assetUrlJS("Quiz.js/jquery-ui/jquery-ui-1.10.3.custom.min.js"),
                "mooCountdown" => $oView->Helpers->MooRequirejs->assetUrlJS("Quiz.js/jquery-countdown/jquery.countdown.min.js")
            ));

            // add phrase
            $oView->addPhraseJs(array(
                'quiz_hours' => __d('quiz', 'hours'),
                'quiz_minutes' => __d('quiz', 'minutes'),
                'quiz_seconds' => __d('quiz', 'seconds'),
                'quiz_confirm' => __d('quiz', 'Confirm'),
                'quiz_cancel' => __d('quiz', 'Cancel'),
                'quiz_please_confirm' => __d('quiz', 'Please Confirm'),
                'quiz_thank_you_for_taking_our_quiz' => __d('quiz', 'Thank you for taking our quiz!'),
                'quiz_drag_ok_click_here_to_upload_files' => __d('quiz', 'Drag or click here to upload files'),
                'quiz_are_you_sure_you_want_to_remove_this_quiz' => __d('quiz', 'Are you sure you want to remove this quiz?'),
                'quiz_are_you_sure_you_want_to_unpublish_this_quiz' => __d('quiz', 'Are you sure you want to un-publish this quiz?'),
                'quiz_are_you_sure_you_want_to_remove_this_question' => __d('quiz', 'Are you sure you want to remove this question?'),
                'quiz_you_are_not_finish_all_question_are_you_sure_you_want_to_submit_your_answers' => __d('quiz', 'You are not finished all questions. Are you sure you want to submit your answers?')
            ));

            // init modal
            $oView->Helpers->MooPopup->register('themeModal');
        }
    }

    public function profileAfterRenderMenu($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled')) {
            $aUser = MooCore::getInstance()->getSubject();
            $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
            echo $oView->element('menu/profile', array('count' => $oQuizModel->countQuizByUser($aUser['User']['id'])), array('plugin' => 'Quiz'));
        }
    }

    public function welcomeAfterRenderMenu($oEvent) {
        $oView = $oEvent->subject();
        $iUserId = MooCore::getInstance()->getViewer(true);
        if (Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled') && $iUserId) {
            $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
            echo $oView->element('menu/welcome', array('count' => $oQuizModel->countQuizByUser($iUserId)), array('plugin' => 'Quiz'));
        }
    }

    public function homeStatistic($oEvent) {
        $oRequest = Router::getRequest();
        $oQuizModel = MooCore::getInstance()->getModel("Quiz.Quiz");

        $oEvent->result['statistics'][] = array(
            'href' => $oRequest->base . '/admin/quiz/quiz_plugins',
            'item_count' => $oQuizModel->find('count'),
            'icon' => '<i class="fa fa-list-alt"></i>',
            'name' => __d('quiz', 'Quizzes'),
            'ordering' => 9999,
        );
    }

    public function searchHashtagsFilter($oEvent) {
        $oController = $oEvent->subject();
        $oController->loadModel('Quiz.Quiz');

        if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'quizzes') {
            $iPage = (!empty($oController->request->named['page'])) ? $oController->request->named['page'] : 1;
            $aQuizzes = $oController->Quiz->getQuizHashtags($oEvent->data['item_ids'], RESULTS_LIMIT, $iPage);
            $aQuizzes = $this->_filterQuizzes($aQuizzes);

            $oController->set('result', 1);
            $oController->set('quizzes', $aQuizzes);
            $oController->set('element_list_path', "Quiz.lists/quizzes_list");
            $oController->set('more_url', '/search/hashtags/' . $oController->params['pass'][0] . '/quizzes/page:' . ($iPage + 1));
        }

        $sTableName = $oController->Quiz->table;
        if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'all' && !empty($oEvent->data['item_groups'][$sTableName])) {
            $oEvent->result['quizzes'] = null;
            $aQuizzes = $oController->Quiz->getQuizHashtags($oEvent->data['item_groups'][$sTableName], 5);
            $aQuizzes = $this->_filterQuizzes($aQuizzes);

            if (!empty($aQuizzes)) {
                $oEvent->result['quizzes']['header'] = __d('quiz', 'Quizzes');
                $oEvent->result['quizzes']['icon_class'] = 'question_answer';
                $oEvent->result['quizzes']['view'] = "Quiz.lists/quizzes_list";
                $oController->set('quizzes', $aQuizzes);
            }
        }

        $oController->set('more_result', 1);
    }

    public function searchHashtags($oEvent) {
        $aQuizzes = array();
        $oController = $oEvent->subject();

        $oController->loadModel('Tag');
        $oController->loadModel('Quiz.Quiz');
        $iPage = (!empty($oController->request->named['page'])) ? $oController->request->named['page'] : 1;
        $bEnable = Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0;
        if ($bEnable) {
            if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'quizzes') {
                $aQuizzes = $oController->Quiz->getQuizHashtags($oEvent->data['item_ids'], RESULTS_LIMIT, $iPage);
                $aQuizzes = $this->_filterQuizzes($aQuizzes);
            }

            $sTableName = $oController->Quiz->table;
            if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'all' && !empty($oEvent->data['item_groups'][$sTableName])) {
                $aQuizzes = $oController->Quiz->getQuizHashtags($oEvent->data['item_groups'][$sTableName], 5);
                $aQuizzes = $this->_filterQuizzes($aQuizzes);
            }
        }

        // get tagged item
        $aTags = $oController->Tag->find('all', array('conditions' => array(
                'Tag.tag' => h(urldecode($oEvent->data['search_keyword'])),
                'Tag.type' => 'Quiz_Quiz'
        )));
        $sQuizIds = Hash::combine($aTags, '{n}.Tag.id', '{n}.Tag.target_id');

        $oController->loadModel('Friend');
        $viewer = MooCore::getInstance()->getViewer();
        $aTagQuizzes = $oController->Quiz->find('all', array('conditions' => array('User.active' => 1, 'Quiz.id' => $sQuizIds), 'limit' => RESULTS_LIMIT, 'page' => $iPage));
        $aTagQuizzes = $this->_filterQuizzes($aTagQuizzes);

        $aQuizzes = array_merge($aQuizzes, $aTagQuizzes);
        if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'all') {
            $aQuizzes = array_slice($aQuizzes, 0, 5);
        }

        $aQuizzes = array_map("unserialize", array_unique(array_map("serialize", $aQuizzes)));
        if (!empty($aQuizzes)) {

            $oEvent->result['quizzes']['header'] = __d('quiz', 'Quizzes');
            $oEvent->result['quizzes']['icon_class'] = 'question_answer';
            $oEvent->result['quizzes']['view'] = "Quiz.lists/quizzes_list";
            if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'quizzes') {
                $oController->set('result', 1);
                $oController->set('element_list_path', "Quiz.lists/quizzes_list");
                $oController->set('more_url', '/search/hashtags/' . $oController->params['pass'][0] . '/quizzes/page:' . ($iPage + 1));
            }
            $oController->set('quizzes', $aQuizzes);
        }

        $oController->set('more_result', 1);
    }

    public function searchSuggestion($oEvent) {
        $oController = $oEvent->subject();
        $oController->loadModel('Quiz.Quiz');

        $oEvent->result['quiz']['header'] = __d('quiz', 'Quizzes');
        $oEvent->result['quiz']['icon_class'] = 'question_answer';

        if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'quiz') {
            $page = (!empty($oController->request->named['page'])) ? $oController->request->named['page'] : 1;
            $more_url = isset($oController->params['pass'][1]) ? '/search/suggestion/quiz/' . $oController->params['pass'][1] . '/page:' . ( $page + 1 ) : '';

            $aQuizzes = $oController->Quiz->getQuizzes('search', $oEvent->data['searchVal'], $page);
            $aQuizzesMore = $oController->Quiz->getQuizzes('search', $oEvent->data['searchVal'], $page + 1);

            if (!empty($aQuizzesMore)) {
                $oController->set('more_result', 1);
            }

            $oController->set('result', 1);
            $oController->set('quizzes', $aQuizzes);
            $oController->set('more_url', $more_url);
            $oController->set('element_list_path', "Quiz.lists/quizzes_list");
        }

        if (isset($oEvent->data['type']) && $oEvent->data['type'] == 'all') {
            $oEvent->result['quiz'] = null;
            $aQuizzes = $oController->Quiz->getQuizzes('search', $oEvent->data['searchVal'], 1, 2);

            if (count($aQuizzes) > 2) {
                $aQuizzes = array_slice($aQuizzes, 0, 2);
            }

            if (!empty($aQuizzes)) {
                $oEvent->result['quiz'] = array(__d('quiz', 'Quiz'));
                foreach ($aQuizzes as $index => &$aQuiz) {
                    $index++;
                    $oEvent->result['quiz'][$index]['id'] = $aQuiz['Quiz']['id'];
                    if (!empty($aQuiz['Quiz']['thumbnail'])) {
                        $oQuizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz');
                        $oEvent->result['quiz'][$index]['img'] = $oQuizHelper->getImage($aQuiz, array('prefix' => '75_square'));
                    }
                    $oEvent->result['quiz'][$index]['title'] = $aQuiz['Quiz']['title'];
                    $oEvent->result['quiz'][$index]['find_name'] = __d('quiz', 'Find Quiz');
                    $oEvent->result['quiz'][$index]['icon_class'] = 'question_answer';
                    $oEvent->result['quiz'][$index]['view_link'] = 'quizzes/view/';

                    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');

                    $utz = (!is_numeric(Configure::read('core.timezone')) ) ? Configure::read('core.timezone') : 'UTC';
                    $cuser = MooCore::getInstance()->getViewer();
                    if (!empty($cuser['User']['timezone'])) {
                        $utz = $cuser['User']['timezone'];
                    }

                    $oEvent->result['quiz'][$index]['more_info'] = __d('quiz', 'Posted by') . ' ' . $mooHelper->getNameWithoutUrl($aQuiz['User'], false) . ' ' . $mooHelper->getTime($aQuiz['Quiz']['created'], Configure::read('core.date_format'), $utz);
                }
            }
        }
    }

    public function searchSearch($oEvent) {
        $oController = $oEvent->subject();
        $oController->loadModel('Quiz.Quiz');

        $results = $oController->Quiz->getQuizzes('search', $oController->keyword, 1, 5);
        if (count($results) > 5) {
            $results = array_slice($results, 0, 5);
        }

        if (isset($oController->plugin) && $oController->plugin == 'Quiz') {
            $oController->set('quizzes', $results);
            $oController->render("Quiz.Elements/lists/quizzes_list");
        } else {
            $oEvent->result['Quiz']['header'] = __d('quiz', 'Quizzes');
            $oEvent->result['Quiz']['icon_class'] = "question_answer";
            $oEvent->result['Quiz']['view'] = "lists/quizzes_list";
            if (!empty($results)) {
                $oEvent->result['Quiz']['notEmpty'] = 1;
            }
            $oController->set('quizzes', $results);
        }
    }

    public function apiSearch($oEvent) {
        $oView = $oEvent->subject();
        $sType = $oEvent->data['type'];
        $aItems = &$oEvent->data['items'];
        if ($sType == 'Quiz' && !empty($oView->viewVars['quizzes'])) {
            $oViewer = MooCore::getInstance()->getViewer();
            $oQuizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz');
            foreach ($oView->viewVars['quizzes'] as $aQuiz) {
                $aItems[] = array(
                    'id' => $aQuiz['Quiz']['id'],
                    'owner_id' => $aQuiz['Quiz']['user_id'],
                    'title_1' => $aQuiz['Quiz']['moo_title'],
                    'avatar' => $oQuizHelper->getImage($aQuiz, array('prefix' => '75_square')),
                    'url' => FULL_BASE_URL . str_replace('?', '', mb_convert_encoding($aQuiz['Quiz']['moo_href'], 'UTF-8', 'UTF-8')),
                    'title_2' => __('Posted by') . ' ' . $oView->Moo->getNameWithoutUrl($aQuiz['User'], false) . ' ' . $oView->Moo->getTime($aQuiz['Quiz']['created'], Configure::read('core.date_format'), $oViewer['User']['timezone']),
                    'created' => $aQuiz['Quiz']['created'],
                    'type_title' => __d('quiz', 'Quiz'),
                    'type' => 'Quiz',
                );
            }
        }
    }

    private function _filterQuizzes($aQuizzes) {
        if (!empty($aQuizzes)) {
            $oFriendModel = MooCore::getInstance()->getModel('Friend');
            $viewer = MooCore::getInstance()->getViewer();
            foreach ($aQuizzes as $key => &$aQuiz) {
                $iOwnerId = $aQuiz['Quiz']['user_id'];
                $privacy = isset($aQuiz['Quiz']['privacy']) ? $aQuiz['Quiz']['privacy'] : 1;
                if (empty($viewer)) { // guest can view only public item
                    if ($privacy != PRIVACY_EVERYONE || empty($aQuiz['Quiz']['approved']) || empty($aQuiz['Quiz']['published'])) {
                        unset($aQuizzes[$key]);
                    }
                } else { // viewer
                    $aFriendsList = array();
                    $aFriendsList = $oFriendModel->getFriendsList($iOwnerId);
                    if ($privacy == PRIVACY_ME) { // privacy = only_me => only owner and admin can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $iOwnerId) {
                            unset($aQuizzes[$key]);
                        }
                    } else if ($privacy == PRIVACY_FRIENDS) { // privacy = friends => only owner and friendlist of owner can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $iOwnerId && !in_array($viewer['User']['id'], array_keys($aFriendsList))) {
                            unset($aQuizzes[$key]);
                        } else if (in_array($viewer['User']['id'], array_keys($aFriendsList)) && (empty($aQuiz['Quiz']['approved']) || empty($aQuiz['Quiz']['published']))) {
                            unset($aQuizzes[$key]);
                        }
                    } else if ($privacy == PRIVACY_EVERYONE) {
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $iOwnerId && (empty($aQuiz['Quiz']['approved']) || empty($aQuiz['Quiz']['published']))) {
                            unset($aQuizzes[$key]);
                        }
                    }
                }
            }
        }
        return $aQuizzes;
    }

    public function widgetTag($oEvent) {
        if (Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled')) {
            $oEvent->result['tag']['type']['quizzes'] = 'quiz';
        }
    }

    public function hashtagEnable($oEvent) {
        if (Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled')) {
            $oEvent->result['quizzes']['enable'] = Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0;
        }
    }

}
