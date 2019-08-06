<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('CakeEventListener', 'Event');

class VerifyProfileListener implements CakeEventListener {

    public $_bEditProfile = false;
    public $_bSearchPeople = false;

    public function implementedEvents() {
        return array(
            'Model.beforeFind' => 'beforeFind',
            'Model.beforeSave' => 'beforeSave',
            'Model.beforeDelete' => 'beforeDelete',
            'MooView.beforeRender' => 'beforeRender',
            'Controller.initialize' => 'beforeFilter',
            'Controller.User.ajaxAvatar' => 'ajaxAvatar',
            'User.changeBeforeQuerySearch' => 'changeBeforeQuerySearch',
            'View.Activities.ajaxLoadTooltip.loadItemInfo' => 'loadItemInfo',
            'Plugin.Controller.Review.afterChangeReview' => 'afterChangeReview',
            'View.Elements.User.searchForm.afterRender' => 'searchFormAfterRender',
            'View.Elements.User.headerProfile.beforeRenderSectionMenu' => 'beforeRenderSectionMenu',
            // implement priority
            'element.activities.afterRenderUserNameFeed' => array('callable' => 'afterRenderUserNameFeed', 'priority' => 2),
            'element.comments.afterRenderUserNameComment' => array('callable' => 'afterRenderUserNameFeed', 'priority' => 2),
            'element.activities.afterRenderUserNameComment' => array('callable' => 'afterRenderUserNameFeed', 'priority' => 2),
            // version moo-301
            'ApiHelper.renderAFeed.verify_profile_verified' => 'feedVerfiedRender',
            'View.Mooapp.users.view.beforeRenderProfileName' => 'beforeRenderProfileNameOnApp',
            'ApiHelper.afterRenderActorHtml' => array('callable' => 'afterRenderUserNameFeedOnApp', 'priority' => 2),
            'ApiHelper.afterRenderFirstCommentUserName' => array('callable' => 'afterRenderUserNameFeedOnApp', 'priority' => 2),
            'Api.View.ApiComment.beforeRenderUserNameHtml' => array('callable' => 'afterRenderUserNameFeedOnApp', 'priority' => 2),
            // hook for other plugins
            'VerifyProfile.EventListener.renderBadgeHtml' => array('callable' => 'renderBadgeHtmlForOtherPlugin', 'priority' => 2),
        );
    }
    
    public function renderBadgeHtmlForOtherPlugin($oEvent) {
        $aUser = $oEvent->data['user'];
        if (Configure::read('VerifyProfile.verify_profile_enable') && !empty($aUser)) {
            $oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile');
            $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
            $sStatus = $oVerifyProfileModel->getStatus($aUser['id']);

            $sShowStatus = __d('verify_profile', 'Not Verified');
            $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_unverify_image'));
            if ($sStatus == 'verified') {
                $sShowStatus = __d('verify_profile', 'Verified');
                $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_badge_image'));
            }

            if ($sStatus != 'verified' && !Configure::read('VerifyProfile.verify_profile_unverify')) {
                
            } else {
                echo '<img class="icon-badge-verification tip" src="' . $sImageInfo . '" original-title="' . $sShowStatus . '">';
            }
        }
    }

    public function feedVerfiedRender($oEvent) {
        $oView = $oEvent->subject();
        $aData = $oEvent->data['data'];
        $sActorHtml = $oEvent->data['actorHtml'];

        $oEvent->result['result'] = array(
            'type' => 'create',
            'title' => __d('verify_profile', 'has been verified.'),
            'titleHtml' => $sActorHtml . ' ' . __d('verify_profile', 'has been verified.'),
            'objects' => array(
                'type' => 'Activity',
                'id' => $aData['Activity']['id'],
                'url' => FULL_BASE_URL . $oView->Html->url(array('plugin' => false, 'controller' => 'activities', 'action' => 'view', $aData['Activity']['id'])),
            ),
        );
    }

    public function afterRenderUserNameFeedOnApp($oEvent) {
        $aUser = $oEvent->data['user'];
        if (Configure::read('VerifyProfile.verify_profile_enable') && Configure::read('VerifyProfile.verify_profile_show_activity_feed') && !empty($aUser)) {
            $oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile');
            $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
            $sStatus = $oVerifyProfileModel->getStatus($aUser['id']);

            $sShowStatus = __d('verify_profile', 'Not Verified');
            $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_unverify_image'));
            if ($sStatus == 'verified') {
                $sShowStatus = __d('verify_profile', 'Verified');
                $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_badge_image'));
            }

            if ($sStatus != 'verified' && !Configure::read('VerifyProfile.verify_profile_unverify')) {
                
            } else {
                $titleHtml = '<img class="icon-verification-username-feed tip" src="' . $sImageInfo . '" original-title="' . $sShowStatus . '">';
                $oEvent->result['result'][] = array('titleHtml' => $titleHtml);
            }
        }
    }

    public function beforeRenderProfileNameOnApp($oEvent) {
        $oView = $oEvent->subject();
        $sHrefUrl = 'javascript:void(0)';
        $aCUser = MooCore::getInstance()->getViewer();
        $aSUser = MooCore::getInstance()->getSubject();

        $oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile');
        $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
        $sStatus = $oVerifyProfileModel->getStatus($aSUser['User']['id']);

        $sShowStatus = __d('verify_profile', 'Not Verified');
        $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_unverify_image'));
        if ($sStatus == 'verified') {
            $sShowStatus = __d('verify_profile', 'Verified');
            $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_badge_image'));
        }

        $aUserAcos = explode(',', $aCUser['Role']['params']);
        if (!empty($aCUser) && $aSUser['User']['id'] == $aCUser['User']['id'] && in_array('verify_profile_verify', $aUserAcos) && $sStatus != 'verified') {
            $sHrefUrl = $oView->request->base . '/profile/verify/verification';
        }

        if (!empty($aCUser) && $aCUser['Role']['is_admin'] && $sStatus != 'verified') {
            $sHrefUrl = $oView->request->base . '/profile/verify/verification_by_admin/' . $aSUser['User']['id'];
        }

        if (!empty($sImageInfo) && !empty($sShowStatus) && Configure::read('VerifyProfile.verify_profile_show_profile_page')) {
            $sProfileStatus = '<a href="' . $sHrefUrl . '"><img class="icon-verification-profile-name" src="' . $sImageInfo . '" original-title="' . $sShowStatus . '"></a>';
            $oEvent->result['result'][] = array('profile' => $sProfileStatus);
        }
    }

    public function afterRenderUserNameFeed($oEvent) {
        $aUser = $oEvent->data['user'];
        if (Configure::read('VerifyProfile.verify_profile_enable') && Configure::read('VerifyProfile.verify_profile_show_activity_feed') && !empty($aUser)) {
            $oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile');
            $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
            $sStatus = $oVerifyProfileModel->getStatus($aUser['id']);

            $sShowStatus = __d('verify_profile', 'Not Verified');
            $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_unverify_image'));
            if ($sStatus == 'verified') {
                $sShowStatus = __d('verify_profile', 'Verified');
                $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_badge_image'));
            }

            if ($sStatus != 'verified' && !Configure::read('VerifyProfile.verify_profile_unverify')) {
                
            } else {
                echo '&nbsp;<img class="icon-verification-username-feed tip" src="' . $sImageInfo . '" original-title="' . $sShowStatus . '">';
            }
        }
    }

    public function afterChangeReview($oEvent) {
        $oController = $oEvent->subject();
        if (Configure::read('VerifyProfile.verify_profile_enable') && Configure::read('VerifyProfile.verify_profile_auto_verify_after_review')) {
            $iReviewId = $oEvent->data['id'];
            $oReviewModel = MooCore::getInstance()->getModel('Review.Review');
            $aReview = $oReviewModel->findById($iReviewId);
            if (!empty($aReview)) {
                $aReviewRating = $oReviewModel->getReviewRating($aReview['Review']['id']);
                $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
                $aUser = $oController->User->find('first', array('conditions' => array('Role.is_admin' => ROLE_ADMIN, 'Role.is_super' => 1)));
                $aVerifyProfile = $oVerifyProfileModel->find('first', array('conditions' => array('VerifyProfile.user_id' => $aReview['Review']['user_id'])));
                if ($aReviewRating['review_count'] >= Configure::read('VerifyProfile.verify_profile_number_review') && $aReviewRating['rating_avg'] >= Configure::read('VerifyProfile.verify_profile_average_review')) {
                    if (empty($aVerifyProfile)) {
                        $oVerifyProfileModel->create();
                        $oVerifyProfileModel->save(array(
                            'user_id' => $aReview['Review']['user_id'],
                            'status' => 'pending'
                        ));
                        $aVerifyProfile = $oVerifyProfileModel->read();
                    }

                    if ($aVerifyProfile['VerifyProfile']['status'] != 'verified') {
                        $oVerifyProfileModel->updateStatus($aVerifyProfile, "verify", array(), $aUser['User']['id']);
                    }
                } else if (Configure::read('VerifyProfile.verify_profile_auto_unverify_after_review')) {
                    if (!empty($aVerifyProfile) && $aVerifyProfile['VerifyProfile']['status'] == 'verified') {
                        $oVerifyProfileModel->updateStatus($aVerifyProfile, "unverify", array(__d('verify_profile', 'Your profile have been unverified by user reviews')), $aUser['User']['id']);
                    }
                }
            }
        }
    }

    public function beforeRender($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::read('VerifyProfile.verify_profile_enable')) {
            $sMin = "";
            if (Configure::read('debug') == 0) {
                $sMin = "min.";
                $oView->Helpers->Html->css(array('VerifyProfile.main'), array('block' => 'css', 'minify' => false, 'inline' => false));
            } else {
                $oView->Helpers->Html->css(array('VerifyProfile.main'), array('block' => 'css'));
            }

            $oView->Helpers->MooRequirejs->addPath(array(
                "mooVerifyProfile" => $oView->Helpers->MooRequirejs->assetUrlJS("VerifyProfile.js/main-v.{$sMin}js")
            ));

            if ($oView->params['controller'] == 'users' && $oView->params['action'] == 'profile') {
                echo $oView->element('VerifyProfile.view/edit_profile');
            }

            if ($oView->params['controller'] == 'users' && $oView->params['action'] == 'avatar' && Configure::read('VerifyProfile.verify_profile_avatar')) {
                echo $oView->element('VerifyProfile.view/profile_picture');
            }

            // init modal
            $oView->Helpers->MooPopup->register('themeModal');
        }
    }

    public function beforeRenderSectionMenu($oEvent) {
        $oView = $oEvent->subject();
        $aUserAcos = $oView->viewVars['uacos'];
        if (Configure::read('VerifyProfile.verify_profile_enable') && in_array('verify_profile_verify', $aUserAcos)) {
            echo $oView->element('VerifyProfile.view/profile');
        }
    }

    public function beforeDelete($oEvent) {
        $oModel = $oEvent->subject();
        $sType = ($oModel->plugin) ? $oModel->plugin . '_' : '' . get_class($oModel);
        if ($sType == 'User' && Configure::read('VerifyProfile.verify_profile_enable')) {
            $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
            $aVerifyProfile = $oVerifyProfileModel->find('first', array('conditions' => array('VerifyProfile.user_id' => $oModel->id)));
            if (!empty($aVerifyProfile)) {
                $oVerifyProfileModel->delete($aVerifyProfile['VerifyProfile']['id']);
            }
        }
    }

    public function searchFormAfterRender($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::read('VerifyProfile.verify_profile_enable')) {
            echo $oView->element('VerifyProfile.view/search_user');
        }
    }

    public function changeBeforeQuerySearch($oEvent) {
        $aData = $oEvent->data;
        if (!empty($aData['verified']) && Configure::read('VerifyProfile.verify_profile_enable')) {
            $this->_bSearchPeople = true;
        }
    }

    public function beforeFind($oEvent) {
        $oModel = $oEvent->subject();
        $sType = ($oModel->plugin ? $oModel->plugin . '_' : '') . get_class($oModel);

        if ($sType == 'User' && $this->_bSearchPeople) {
            $oModel->bindModel(array(
                'belongsTo' => array(
                    'VerifyProfile' => array(
                        'className' => 'VerifyProfile',
                        'type' => 'inner',
                        'foreignKey' => false,
                        'conditions' => array(
                            'VerifyProfile.user_id = User.id AND VerifyProfile.status = \'verified\'',
                        )
                    )
                )
            ));
        }
    }

    public function loadItemInfo($oEvent) {
        $oView = $oEvent->subject();
        if (Configure::read('VerifyProfile.verify_profile_enable') && Configure::read('VerifyProfile.verify_profile_show_profile_popup') && $oView->viewVars['type'] == 'User') {
            $oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile');
            $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
            $sStatus = $oVerifyProfileModel->getStatus($oView->viewVars['object']['User']['id']);

            $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_unverify_image'));
            if ($sStatus == 'verified') {
                $sImageInfo = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_badge_image'));
            }

            if ($sStatus != 'verified' && !Configure::read('VerifyProfile.verify_profile_unverify')) {
                
            } else {
                echo '<div class="item_stat extra_info verified-tooltip-profile"><img class="icon-verification-tooltip" src="' . $sImageInfo . '"></div>';
            }
        }
    }

    public function ajaxAvatar($oEvent) {
        if (Configure::read('VerifyProfile.verify_profile_enable')) {
            $oController = $oEvent->subject();
            $iCUserId = $oController->Auth->user('id');
            $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
            $aVerifyProfile = $oVerifyProfileModel->find('first', array('conditions' => array('VerifyProfile.user_id' => $iCUserId)));
            if (!empty($aVerifyProfile) && $aVerifyProfile['VerifyProfile']['status'] == 'verified' && Configure::read('VerifyProfile.verify_profile_avatar')) {
                $oController->render('VerifyProfile.VerifyProfiles/ajax_avatar');
            }
        }
    }

    public function beforeFilter($oEvent) {
        if (Configure::read('VerifyProfile.verify_profile_enable')) {
            $oMooController = $oEvent->subject();
            if (($oMooController->params['controller'] == 'users' && $oMooController->params['action'] == 'ajax_save_profile') || ($oMooController->params['controller'] == 'upload' && $oMooController->params['action'] == 'avatar')) {
                $this->_bEditProfile = true;
            }
        }
    }

    public function beforeSave($oEvent) {
        if (Configure::read('VerifyProfile.verify_profile_enable') && $this->_bEditProfile) {
            $oModel = $oEvent->subject();
            $iCUserId = MooCore::getInstance()->getViewer(true);
            if (!empty($oModel->data['User']) && $iCUserId == $oModel->id) {
                $oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
                $aVerifyProfile = $oVerifyProfileModel->find('first', array('conditions' => array('VerifyProfile.user_id' => $oModel->id)));
                $aUser = $oModel->find('first', array('conditions' => array('User.id' => $oModel->id), 'fields' => array('id', 'avatar', 'gender', 'birthday', 'name')));
                if (!empty($aVerifyProfile) && $aVerifyProfile['VerifyProfile']['status'] == 'verified') {
                    if ((Configure::read('VerifyProfile.verify_profile_avatar') && isset($oModel->data['User']['avatar']) && $oModel->data['User']['avatar'] != $aUser['User']['avatar']) ||
                            (Configure::read('VerifyProfile.verify_profile_gender') && isset($oModel->data['User']['gender']) && $oModel->data['User']['gender'] != $aUser['User']['gender']) ||
                            (Configure::read('VerifyProfile.verify_profile_birthday') && isset($oModel->data['User']['birthday']) && $oModel->data['User']['birthday'] != $aUser['User']['birthday']) ||
                            (Configure::read('VerifyProfile.verify_profile_full_name') && isset($oModel->data['User']['name']) && md5($oModel->data['User']['name']) != md5($aUser['User']['name']))) {
                        $oVerifyProfileModel->updateStatus($aVerifyProfile, "unverify", array(), $oModel->id);
                    }
                }
            }
        }
    }

}
