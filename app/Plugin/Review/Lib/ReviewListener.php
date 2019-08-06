<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('CakeEventListener', 'Event');

class ReviewListener implements CakeEventListener {

    protected $_bRenderMenu = false;

    public function implementedEvents() {
        return array(
            'Model.beforeDelete' => 'beforeDelete',
            'MooView.beforeRender' => 'beforeRender',
            'profile.afterRenderMenu' => 'profileAfterRenderMenu',
            'View.Activities.ajaxLoadTooltip.loadItemInfo' => 'loadItemInfo',
            // version moo-301
            'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu',
            'ApiHelper.renderAFeed.review_write' => 'feedReviewWriteRender',
            'NotificationsController.beforeRedirectView' => 'notificationsControllerRedirect',
        );
    }

    public function apiAfterRenderMenu($oEvent) {
        $aUser = MooCore::getInstance()->getSubject();
        $iUserId = MooCore::getInstance()->getViewer(true);
        $oReviewModel = MooCore::getInstance()->getModel('Review.Review');

        $aObjectUserAcos = explode(',', $aUser['Role']['params']);
        $aReview = $oReviewModel->findByUserId($aUser['User']['id']);
        if (!empty($aReview) && empty($aReview['Review']['review_enable']) && !in_array('review_profile_option', $aObjectUserAcos) && (empty($iUserId) || $iUserId != $aUser['User']['id'])) {
            
        } else if (in_array('review_recieve', $aObjectUserAcos)) {
            $oEvent->data['result']['review'] = array(
                'url' => FULL_BASE_URL . $oEvent->subject()->request->base . '/reviews/profile/' . $aUser['User']['id'] . '/user',
                'text' => __d('review', 'Reviews'),
                'cnt' => 0
            );
        }
    }

    public function feedReviewWriteRender($oEvent) {
        $aData = $oEvent->data['data'];
        $sActorHtml = $oEvent->data['actorHtml'];

        $oView = $oEvent->subject();
        $aReviewUser = $oEvent->data['objectPlugin'];
        $aReview = MooCore::getInstance()->getItemByType('Review.Review', $aReviewUser['ReviewUser']['review_id']);

        $aReview['User']['moo_href'] = FULL_BASE_URL . $oView->request->base . '/reviews/profile/' . $aReview['User']['id'] . '/user';
        $sContent = '<span>' . __d('review', 'wrote a review') . '</span>&nbsp;' . $oView->Moo->getName($aReview['User']);

        $oEvent->result['result'] = array(
            'type' => 'create',
            'title' => $sContent,
            'titleHtml' => $sActorHtml . ' ' . $sContent,
            'objects' => array(
                'type' => 'Activity',
                'id' => $aData['Activity']['id'],
                'url' => FULL_BASE_URL . $oView->Html->url(array('plugin' => false, 'controller' => 'activities', 'action' => 'view', $aData['Activity']['id'])),
                'contentReview' => nl2br($oView->Text->autoLink($oView->Moo->parseSmilies($aReviewUser['ReviewUser']['content']), array_merge(array('target' => '_blank', 'rel' => 'nofollow', 'escape' => false), array('no_replace_ssl' => 1)))),
                'ratingValue' => $aReviewUser['ReviewUser']['rating'],
            ),
        );
    }

    public function notificationsControllerRedirect($oEvent) {
        $oController = $oEvent->subject();
        $aNotification = $oEvent->data['notification'];
        if (($aNotification['Notification']['action'] == 'review_write' || $aNotification['Notification']['action'] == 'review_reply') && $aNotification['Notification']['plugin'] == 'Review' && $oController->isApp()) {
            $oController->redirect('/reviews/profile/' . $aNotification['Notification']['user_id'] . '/user');
        }
    }

    public function beforeDelete($oEvent) {
        $oModel = $oEvent->subject();
        $sType = ($oModel->plugin ? $oModel->plugin . '_' : '') . get_class($oModel);
        if ($sType == 'User' && Configure::check('Review.review_enabled') && Configure::read('Review.review_enabled')) {
            $oReviewModel = MooCore::getInstance()->getModel('Review.Review');
            $oReviewModel->mooFields = array('plugin', 'type');
            $aReview = $oReviewModel->find('first', array('conditions' => array('Review.user_id' => $oModel->id)));
            if (!empty($aReview)) {
                $oReviewUserModel = MooCore::getInstance()->getModel('Review.ReviewUser');
                $oReviewUserModel->deleteAll(array('ReviewUser.review_id' => $aReview['Review']['id']), true, true);
                $oReviewModel->delete($aReview['Review']['id']);
            }
        }
    }

    public function beforeRender($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('Review.review_enabled') && Configure::read('Review.review_enabled')) {

            $sMin = "";
            if (Configure::read('debug') == 0) {
                $sMin = "min.";
            }

            $oView->Helpers->Html->css(array('Review.main', 'Review.review.rating'), array('block' => 'css'));
            $oView->Helpers->MooRequirejs->addPath(array(
                "mooReview" => $oView->Helpers->MooRequirejs->assetUrlJS("Review.js/main.{$sMin}js"),
                "mooReviewRating" => $oView->Helpers->MooRequirejs->assetUrlJS("Review.js/review.rating.{$sMin}js")
            ));

            // add phrase
            $oView->addPhraseJs(array(
                'review_are_you_sure_you_want_to_remove_this_review' => __d('review', 'Are you sure you want to remove this review?'),
                'review_are_you_sure_you_want_to_remove_this_reply' => __d('review', 'Are you sure you want to remove this reply?'),
                'review_are_you_sure_you_want_to_disable_rating' => __d('review', 'Are you sure you want to disable rating?'),
                'review_are_you_sure_you_want_to_enable_rating' => __d('review', 'Are you sure you want to enable rating?'),
                'review_cancel' => __d('review', 'Cancel'),
            ));

            // init modal
            $oView->Helpers->MooPopup->register('themeModal');
        }
    }

    public function profileAfterRenderMenu($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('Review.review_enabled') && Configure::read('Review.review_enabled')) {
            $aUser = MooCore::getInstance()->getSubject();
            $oReviewModel = MooCore::getInstance()->getModel('Review.Review');
            $oReviewUserModel = MooCore::getInstance()->getModel('Review.ReviewUser');

            $uid = $oView->viewVars['uid'];
            $aObjectUserAcos = explode(',', $aUser['Role']['params']);
            $aReview = $oReviewModel->findByUserId($aUser['User']['id']);
            if (!empty($aReview) && empty($aReview['Review']['review_enable']) && !in_array('review_profile_option', $aObjectUserAcos) && (empty($uid) || $uid != $aUser['User']['id'])) {
                
            } else if (in_array('review_recieve', $aObjectUserAcos)) {
                $iReviewDetailId = 0;
                $bLoadReviewProfile = false;
                if (!empty($oView->params['url']['tab'])) {
                    $aTabDetail = explode('-', $oView->params['url']['tab']);
                    if ($aTabDetail[0] == 'review' && $aTabDetail[1] == 'detail' && is_numeric($aTabDetail[2])) {
                        $iReviewDetailId = $aTabDetail[2];
                    } else if ($aTabDetail[0] == 'reviews') {
                        $bLoadReviewProfile = true;
                    }
                }

                // fix render seconds script on mooApp
                $bLoadReviewScript = false;
                if ($this->_bRenderMenu === false) {
                    $bLoadReviewScript = true;
                    $this->_bRenderMenu = true;
                }

                echo $oView->element('menu/profile', array('count' => $oReviewUserModel->countUserReviewed($aUser['User']['id']), 'bLoadReviewScript' => $bLoadReviewScript, 'bLoadReviewProfile' => $bLoadReviewProfile, 'iReviewUserId' => $iReviewDetailId), array('plugin' => 'Review'));
            }
        }
    }

    public function loadItemInfo($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::check('Review.review_enabled') && Configure::read('Review.review_enabled') && $oView->viewVars['type'] == 'User') {
            $oReviewModel = MooCore::getInstance()->getModel('Review.Review');

            $aReviewRating = array();
            $aUser = $oView->viewVars['object']['User'];
            $aReview = $oReviewModel->findByUserId($aUser['id']);

            $aObjectUserAcos = explode(',', $oView->viewVars['object']['Role']['params']);
            if ((!empty($aReview) && empty($aReview['Review']['review_enable']) && !in_array('review_profile_option', $aObjectUserAcos)) || !in_array('review_recieve', $aObjectUserAcos)) {
                
            } else {
                if (!empty($aReview)) {
                    $aReviewRating = $oReviewModel->getReviewRating($aReview['Review']['id']);
                }
                echo $oView->element('tooltip/profile', array('aReviewRating' => $aReviewRating, 'aUser' => $aUser), array('plugin' => 'Review'));
            }
        }
    }

}
