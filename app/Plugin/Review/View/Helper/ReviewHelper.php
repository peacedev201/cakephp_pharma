<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('AppHelper', 'View/Helper');

class ReviewHelper extends AppHelper {

    public function getPhotos($sPhotoId) {
        if(empty($sPhotoId)){
            return array();
        }
        
        $oModelPhoto = MooCore::getInstance()->getModel('Photo.Photo');
        return $oModelPhoto->find('all', array('conditions' => array('Photo.id' => explode(',', $sPhotoId))));
    }

    public function getEnable() {
        return Configure::check('Review.review_enabled') ? Configure::read('Review.review_enabled') : 0;
    }

}
