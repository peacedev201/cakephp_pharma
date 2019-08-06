<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('ReviewAppModel', 'Review.Model');

class Review extends ReviewAppModel {

    public $mooFields = array('plugin', 'type');
    public $belongsTo = array('User');

    public function getReviewRating($iReviewId) {
        $oReviewUserModel = MooCore::getInstance()->getModel('Review.ReviewUser');
        $oReviewUserModel->virtualFields = array(
            'review_count' => 'COUNT(ReviewUser.rating)',
            'rating_total' => 'SUM(ReviewUser.rating)',
        );

        $aCondReviewUser = array('ReviewUser.review_id' => $iReviewId, 'ReviewUser.parent_id' => 0);
        $aCondReviewUser = $this->addBlockCondition($aCondReviewUser, 'ReviewUser');
        $aReviewUser = $oReviewUserModel->find('first', array(
            'conditions' => $aCondReviewUser,
            'fields' => array('ReviewUser.rating_total', 'ReviewUser.review_count')
        ));

        $iReviewCount = 0;
        $iRatingAVG = 0.00;
        if (!empty($aReviewUser['ReviewUser']['rating_total']) && !empty($aReviewUser['ReviewUser']['review_count'])) {
            $iReviewCount = $aReviewUser['ReviewUser']['review_count'];
            $iRatingAVG = round($aReviewUser['ReviewUser']['rating_total'] / $aReviewUser['ReviewUser']['review_count'], 2);
        }

        $oReviewUserModel->virtualFields = array(
            'rating' => 'FLOOR(ReviewUser.rating)',
            'review_count' => 'COUNT(ReviewUser.rating)',
            'rating_total' => 'SUM(ReviewUser.rating)',
        );

        $aCondReviewUsers = array('ReviewUser.review_id' => $iReviewId, 'ReviewUser.parent_id' => 0);
        $aCondReviewUsers = $this->addBlockCondition($aCondReviewUsers, 'ReviewUser');
        $aReviewUsers = $oReviewUserModel->find('all', array(
            'conditions' => $aCondReviewUsers,
            'group' => array('ReviewUser.rating'),
            'order' => array('ReviewUser.rating' => 'DESC'),
            'fields' => array('ReviewUser.rating', 'ReviewUser.review_count', 'ReviewUser.rating_total')
        ));

        $aReviewRuler = array();
        if (!empty($aReviewUsers)) {
            foreach ($aReviewUsers as $aReviewUser) {
                $aReviewUser['ReviewUser']['percent'] = ($aReviewUser['ReviewUser']['review_count'] / $iReviewCount) * 100;
                $aReviewRuler[$aReviewUser['ReviewUser']['rating']] = $aReviewUser['ReviewUser'];
            }
        }

        return array(
            'rating_avg' => number_format($iRatingAVG, 2, '.', ''),
            'review_count' => $iReviewCount,
            'review_ruler' => $aReviewRuler,
        );
    }

    public function addBlockCondition($aCond = array(), $modal_name = null) {
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

            $aCond[] = "$sFieldName NOT IN ($sBlockedUsers)";
        }

        return $aCond;
    }

    public function loadDataWidget($aCurrentUser, $aObjectUser) {
        $oReviewUserModel = MooCore::getInstance()->getModel('Review.ReviewUser');
        $aReview = $this->findByUserId($aObjectUser['User']['id']);
        $aReviewRating = array();
        $bWriteReview = false;
        if (!empty($aCurrentUser)) {
            $aReviewUser = array();
            if (!empty($aReview)) {
                $aReviewUser = $oReviewUserModel->findByUserIdAndReviewId($aCurrentUser['id'], $aReview['Review']['id']);
            }

            $aCurrentUserAcos = explode(',', $aCurrentUser['Role']['params']);
            $aObjectUserAcos = explode(',', $aObjectUser['Role']['params']);

            if (empty($aReviewUser) && in_array('review_recieve', $aObjectUserAcos) && in_array('review_write', $aCurrentUserAcos) && $aCurrentUser['id'] != $aObjectUser['User']['id']) {
                $bWriteReview = true;
            }
        }

        if (!empty($aReview)) {
            $aReviewRating = $this->getReviewRating($aReview['Review']['id']);
        }

        return array($aReviewRating, $bWriteReview);
    }

}
