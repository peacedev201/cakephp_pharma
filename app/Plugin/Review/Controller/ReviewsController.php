<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('CakeEvent', 'Event');

class ReviewsController extends ReviewAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Review.Review');
        $this->loadModel('Review.ReviewUser');
    }

    public function profile($id = null, $type = 'user', $review_user_id = null) {
        $iUserId = intval($id);
        $iReviewUserId = intval($review_user_id);
        $sUrl = (!empty($type)) ? $iUserId . '/' . $type : $iUserId;
        $iPgae = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        $aUserReviews = $this->ReviewUser->getReviews($type, $iUserId, $iPgae, $iReviewUserId);
        $aMoreUserReviews = $this->ReviewUser->getReviews($type, $iUserId, $iPgae + 1, $iReviewUserId);

        if (!empty($aMoreUserReviews)) {
            $this->set('more_result', 1);
        }

        if (!empty($iUserId)) {
            $this->loadModel('User');
            $aUser = $this->User->findById($iUserId);

            $this->set('aUser', $aUser);
            $this->set('bShowProfileOption', $this->_bShowProfileOption($aUser));
        }

        $this->set('type', $type);
        $this->set('aUserReviews', $aUserReviews);
        $this->set('aReview', $this->Review->findByUserId($iUserId));
        $this->set('bProfileLoadHeader', ($iPgae == 1) ? true : false);
        $this->set('more_url', '/reviews/profile/' . h($sUrl) . '/page:' . ($iPgae + 1));

        if ($iPgae > 1) {
            $this->render('/Elements/lists/reviews_list');
        } else {
            $this->render('/Elements/ajax/profile_review');
        }
    }

    public function write($user_id, $review_user_id = null, $type = null) {
        $iRecieveId = intval($user_id);
        $iReviewUserId = intval($review_user_id);
        $aUser = $this->User->findById($iRecieveId);
        $this->_checkExistence($aUser);

        if (!empty($iReviewUserId)) {
            $aReviewUser = $this->ReviewUser->findById($iReviewUserId);
            $this->_checkExistence($aReviewUser);
            $this->_checkPermission(array('admins' => array($aReviewUser['ReviewUser']['user_id'])));
        } else {
            $aReviewUser = $this->ReviewUser->initFields();
        }

        $this->_checkReviewRecieve($aUser);
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('aco' => 'review_write'));

        $this->set('iRecieveId', $iRecieveId);
        $this->set('aReviewUser', $aReviewUser);
        $this->set('type', !empty($type) ? $type : 'user');
    }

    public function reply($review_user_id = null) {
        $iReviewUserId = intval($review_user_id);
        $aReviewUser = $this->ReviewUser->findById($iReviewUserId);
        $this->_checkExistence($aReviewUser);

        $aReplyUser = $this->ReviewUser->findByParentId($aReviewUser['ReviewUser']['id']);
        if (empty($aReplyUser)) {
            $aReplyUser = $this->ReviewUser->initFields();
        }

        $this->set('type', 'reply');
        $this->set('aReviewUser', $aReplyUser);
        $this->set('iRecieveId', $aReviewUser['ReviewUser']['id']);

        $this->render('/Reviews/write');
    }

    public function save() {
        $iRecieveId = intval($this->request->data['recieve_id']);
        $sType = strval($this->request->data['type']);
        $this->autoRender = false;

        $aPhotosOld = array();
        $iUserId = $this->Auth->user('id');
        if (!empty($this->request->data['id'])) {
            $aReviewUser = $this->ReviewUser->findById($this->request->data['id']);
            $this->_checkExistence($aReviewUser);
            $this->_checkPermission(array('admins' => array($aReviewUser['ReviewUser']['user_id'])));

            // Edit
            $aPhotosOld = explode(',', $aReviewUser['ReviewUser']['photos']);
            $this->ReviewUser->id = $this->request->data['id'];
        } else {
            $this->request->data['user_id'] = $iUserId;
        }

        if (!empty($sType) && $sType == 'reply') {
            unset($this->ReviewUser->validate['rating']);
            $this->request->data['parent_id'] = $iRecieveId;
        } else {
            $aReview = $this->Review->findByUserId($iRecieveId);
            if (empty($aReview)) {
                $this->Review->create(array(
                    'user_id' => $iRecieveId
                ));
                $this->Review->save();
                $aReview = $this->Review->read();
            }

            $this->request->data['review_id'] = $aReview['Review']['id'];
        }

        $aPhotoList = explode(',', $this->request->data['attachments']);
        $this->ReviewUser->set($this->request->data);
        $this->_validateData($this->ReviewUser);
        if ($this->ReviewUser->save()) {

            // Upload photo attachments
            $this->loadModel('Photo.Photo');
            $aDataPhoto['user_id'] = $iUserId;
            $aDataPhoto['type'] = 'Review_ReviewUser';
            $aDataPhoto['target_id'] = $this->ReviewUser->id;

            $aPhotoId = !empty($this->request->data['attachments_remain']) ? explode(',', $this->request->data['attachments_remain']) : array();
            foreach ($aPhotoList as $sPhotoItem) {
                if (!empty($sPhotoItem)) {
                    $aDataPhoto['thumbnail'] = $sPhotoItem;
                    $this->Photo->create($aDataPhoto);
                    $this->Photo->save();

                    // Pust to attachment
                    array_push($aPhotoId, $this->Photo->id);
                }
            }

            $this->ReviewUser->updateAll(array('photos' => "'" . join(',', $aPhotoId) . "'"), array('id' => $this->ReviewUser->id));

            // Delete old photo
            /*
            $aPhotosD = array_diff($aPhotosOld, $aPhotoId);
            if (!empty($aPhotosD)) {
                
            }
            */
            
            if (empty($this->request->data['id'])) {

                if (!empty($sType) && $sType == 'reply') {
                    // Add Notification
                    $aReviewUser = $this->ReviewUser->findById($iRecieveId);
                    $this->loadModel('Notification');
                    $this->Notification->record(array(
                        'plugin' => 'Review',
                        'sender_id' => $iUserId,
                        'action' => 'review_reply',
                        'recipients' => $aReviewUser['ReviewUser']['user_id'],
                        'url' => $aReviewUser['ReviewUser']['moo_url'],
                    ));
                } else {
                    // Add Activity
                    $this->loadModel('Activity');
                    $this->Activity->save(array(
                        'type' => 'user',
                        'target_id' => 0,
                        'user_id' => $iUserId,
                        'action' => 'review_write',
                        'item_type' => 'Review.ReviewUser',
                        'item_id' => $this->ReviewUser->id,
                        'plugin' => 'Review',
                        'params' => 'item',
                        'share' => 0,
                        'query' => 1,
                    ));

                    // Add Notification
                    $aReviewUser = $this->ReviewUser->read();
                    $this->loadModel('Notification');
                    $this->Notification->record(array(
                        'plugin' => 'Review',
                        'sender_id' => $iUserId,
                        'action' => 'review_write',
                        'recipients' => $iRecieveId,
                        'url' => $aReviewUser['ReviewUser']['moo_url'],
                    ));
                }
            }

            if (empty($sType) || $sType != 'reply') {
                // Integration Verify Plugin
                $oEvent = new CakeEvent('Plugin.Controller.Review.afterChangeReview', $this, array('id' => $aReview['Review']['id']));
                $this->getEventManager()->dispatch($oEvent);
            }

            $response['type'] = (!empty($sType) && $sType == 'reviewed') ? 'reviewed' : 'user';
            $response['result'] = 1;
            echo json_encode($response);
        } else {
            $response['result'] = 0;
            $response['message'] = __d('review', 'Error! Please try again.');
            echo json_encode($response);
        }
    }

    public function reload($user_id, $theme = null) {
        $iUserId = intval($user_id);

        $this->_checkPermission(array('confirm' => true));
        $aCurrentUser = $this->_getUser();
        $aObjectUser = $this->User->findById($iUserId);

        $this->_checkExistence($aObjectUser);
        list($aReviewRating, $bWriteReview) = $this->Review->loadDataWidget($aCurrentUser, $aObjectUser);

        $bShowWidget = true;
        $aObjectUserAcos = explode(',', $aObjectUser['Role']['params']);
        $aReview = $this->Review->findByUserId($aObjectUser['User']['id']);
        if ((!empty($aReview) && empty($aReview['Review']['review_enable'])) || !in_array('review_recieve', $aObjectUserAcos)) {
            $bShowWidget = false;
        }

        $this->set('aReviewRating', $aReviewRating);
        $this->set('bWriteReview', $bWriteReview);
        $this->set('bShowWidget', $bShowWidget);
        $this->set('bLoadHeader', false);
        $this->set('user', $aObjectUser);

        if (!empty($theme)) {
            $this->theme = $theme;
        }

        $this->render('Review.Widgets/reviews/profile');
    }

    public function enable() {
        $this->autoRender = false;
        $iUserId = $this->Auth->user('id');
        $aReview = $this->Review->findByUserId($iUserId);

        $bEnable = 0;
        if (!empty($aReview)) {
            $this->Review->id = $aReview['Review']['id'];
            $bEnable = 1 - $aReview['Review']['review_enable'];
        }

        $this->Review->set(array(
            'user_id' => $iUserId,
            'review_enable' => $bEnable,
        ));
        $this->Review->save();

        $aReviewR = $this->Review->read();
        $aReviewUsers = $this->ReviewUser->find('list', array('conditions' => array('ReviewUser.review_id' => $aReviewR['Review']['id']), 'fields' => array('ReviewUser.id')));

        if (!empty($aReviewUsers)) {
            $this->loadModel('Activity');

            $sStatus = '\'waiting\'';
            if ($bEnable) {
                $sStatus = '\'ok\'';
            }
            $this->Activity->updateAll(array('Activity.status' => $sStatus), array('Activity.item_type' => 'Review.ReviewUser', 'Activity.item_id' => $aReviewUsers));
        }
    }

    public function delete() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $iReviewUserId = intval($this->request->data['id']);
            $aReviewUser = $this->ReviewUser->findById($iReviewUserId);

            $this->_checkExistence($aReviewUser);
            $this->_checkPermission(array('confirm' => true));
            $this->_checkPermission(array('admins' => array($aReviewUser['ReviewUser']['user_id'])));

            $this->ReviewUser->delete($iReviewUserId);

            // Integration Verify Plugin
            if (!empty($aReviewUser['ReviewUser']['review_id'])) {
                $oEvent = new CakeEvent('Plugin.Controller.Review.afterChangeReview', $this, array('id' => $aReviewUser['ReviewUser']['review_id']));
                $this->getEventManager()->dispatch($oEvent);
            }
        }
    }

    public function attachments() {
        $this->autoRender = false;
        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();
        $allowedExtensions = MooCore::getInstance()->_getPhotoAllowedExtension();

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        $path = 'uploads' . DS . 'tmp';
        $this->_prepareDir($path);
        $result = $uploader->handleUpload(WWW_ROOT . $path);

        if (!empty($result['success'])) {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);

            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $result['filename']);
            $result['photo'] = $path . DS . $result['filename'];
        }

        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function photo_view($id) {
        $iPhotoId = intval($id);
        $this->loadModel('Photo.Photo');
        $aPhoto = $this->Photo->findById($iPhotoId);

        $this->_checkExistence($aPhoto);
        $this->set('aPhoto', $aPhoto);
    }

    private function _checkReviewRecieve($aUser) {
        $aUserAcos = explode(',', $aUser['Role']['params']);
        if (!in_array('review_recieve', $aUserAcos)) {
            $this->set('msg', __d('review', 'Access denied'));
            echo $this->render('/Elements/error');
            exit;
        }
    }

    private function _bShowProfileOption($aUser) {
        $aUserAcos = explode(',', $aUser['Role']['params']);
        if (in_array('review_profile_option', $aUserAcos)) {
            return false;
        }

        return true;
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

}
