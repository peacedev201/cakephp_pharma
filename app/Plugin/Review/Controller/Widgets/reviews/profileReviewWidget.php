<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Widget', 'Controller/Widgets');

class profileReviewWidget extends Widget {

    public function beforeRender(Controller $oController) {
        $aCurrentUser = $oController->_getUser(); //$aCurrentUser['id']
        $aObjectUser = MooCore::getInstance()->getSubject(); //$aObjectUser['User']['id']

        $oReviewModel = MooCore::getInstance()->getModel('Review.Review');
        list($aReviewRating, $bWriteReview) = $oReviewModel->loadDataWidget($aCurrentUser, $aObjectUser);
        
        $bShowWidget = true;
        $aObjectUserAcos = explode(',', $aObjectUser['Role']['params']);
        $aReview = $oReviewModel->findByUserId($aObjectUser['User']['id']);
        if((!empty($aReview) && empty($aReview['Review']['review_enable']) && !in_array('review_profile_option', $aObjectUserAcos)) || !in_array('review_recieve', $aObjectUserAcos)){
            $bShowWidget = false;
        }
        
        $oController->set('aReviewRating', $aReviewRating);
        $oController->set('bWriteReview', $bWriteReview);
        $oController->set('bShowWidget', $bShowWidget);
        $oController->set('bLoadHeader', true);
    }

}
