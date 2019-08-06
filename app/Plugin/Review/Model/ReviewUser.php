<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('ReviewAppModel', 'Review.Model');

class ReviewUser extends ReviewAppModel {

    public $mooFields = array('plugin', 'type', 'href', 'url');
    public $validate = array(
        'rating' => array(
            'required' => true,
            'rule' => 'notBlank',
            'message' => 'Rating is required',
        ),
        'content' => array(
            'required' => true,
            'rule' => 'notBlank',
            'message' => 'Content is required',
        )
    );

    public function beforeDelete($cascade = true) {

        $aReviewUser = $this->findById($this->id);
        if (!empty($aReviewUser)) {
            // delete activity
            $oActivityModel = MooCore::getInstance()->getModel('Activity');
            $aActivityId = $oActivityModel->find('list', array('fields' => array('Activity.id'), 'conditions' => array('Activity.action' => 'review_write', 'Activity.item_type' => 'Review.ReviewUser', 'Activity.item_id' => $this->id)));
            if (!empty($aActivityId)) {
                $oActivityModel->deleteAll(array('Activity.parent_id' => $aActivityId));
                $oActivityModel->deleteAll(array('Activity.id' => $aActivityId), true, true);
            }
            
            // delete reply
            $this->deleteAll(array('ReviewUser.parent_id' => $this->id));
        }

        parent::beforeDelete($cascade);
    }

    public function getReviews($sType = null, $iUserId, $iPage = 1, $iReviewUserId = null) {

        $this->mooFields = array('plugin', 'type');
        if (!in_array($sType, array('user', 'reviewed')) || empty($iUserId)) {
            return array();
        }

        $aJoin = array();
        $aCond = array();
        $aField = array();
        switch ($sType) {
            case 'user':
                $oReviewModel = MooCore::getInstance()->getModel('Review.Review');
                $aReview = $oReviewModel->findByUserId($iUserId);
                if (empty($aReview)) {
                    return array();
                }

                $aJoin = array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'foreignKey' => false,
                        'conditions' => array('User.id = ReviewUser.user_id')
                    ),
                    array(
                        'table' => 'reviews',
                        'alias' => 'Review',
                        'type' => 'INNER',
                        'foreignKey' => false,
                        'conditions' => array('Review.id = ReviewUser.review_id')
                    ),
                    array(
                        'table' => 'review_users',
                        'alias' => 'ReviewReply',
                        'type' => 'LEFT',
                        'foreignKey' => false,
                        'conditions' => array('ReviewUser.id = ReviewReply.parent_id')
                    ),
                );

                $aField = array('ReviewUser.*', 'ReviewReply.*', 'User.*', 'Review.*');

                $aCond['User.active'] = 1;
                $aCond['ReviewUser.review_id'] = $aReview['Review']['id'];
                break;

            case 'reviewed':

                $aJoin = array(
                    array(
                        'table' => 'reviews',
                        'alias' => 'Review',
                        'type' => 'INNER',
                        'foreignKey' => false,
                        'conditions' => array('Review.id = ReviewUser.review_id')
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'foreignKey' => false,
                        'conditions' => array('User.id = Review.user_id')
                    ),
                    array(
                        'table' => 'review_users',
                        'alias' => 'ReviewReply',
                        'type' => 'LEFT',
                        'foreignKey' => false,
                        'conditions' => array('ReviewUser.id = ReviewReply.parent_id')
                    ),
                );

                $aField = array('ReviewUser.*', 'ReviewReply.*', 'User.*', 'Review.*');

                $aCond['User.active'] = 1;
                $aCond['ReviewUser.user_id'] = $iUserId;
                break;
        }

        if (!empty($iReviewUserId)) {
            $aCond['ReviewUser.id'] = $iReviewUserId;
        }

        $aCond = $this->addBlockCondition($aCond);
        return $this->find('all', array('conditions' => $aCond, 'joins' => $aJoin, 'fields' => $aField, 'order' => 'ReviewUser.created desc', 'limit' => RESULTS_LIMIT, 'page' => $iPage));
    }

    public function countUserReviewed($iUserId) {
        $this->bindModel(array(
            'belongsTo' => array(
                'Review' => array(
                    'classname' => 'Review.Review'
                )
            )
        ));

        $aCond = $this->addBlockCondition(array('Review.user_id' => $iUserId));
        return $this->find('count', array('conditions' => $aCond));
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

    public function getHref($aRow) {

        if (!empty($aRow['id']) && !empty($aRow['review_id'])) {
            $oReviewModel = MooCore::getInstance()->getModel('Review.Review');
            $aReview = $oReviewModel->findById($aRow['review_id']);

            return $aReview['User']['moo_href'] . '?tab=review-detail-' . $aRow['id'];
        }

        return '';
    }

    public function getUrl($aRow) {

        if (!empty($aRow['id']) && !empty($aRow['review_id'])) {
            $oReviewModel = MooCore::getInstance()->getModel('Review.Review');
            $aReview = $oReviewModel->findById($aRow['review_id']);

            return $aReview['User']['moo_url'] . '?tab=review-detail-' . $aRow['id'];
        }

        return '';
    }

}
