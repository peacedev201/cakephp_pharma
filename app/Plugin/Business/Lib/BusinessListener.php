<?php

App::uses('CakeEventListener', 'Event');

class BusinessListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            //'MooView.afterLoadMooCore' => 'afterLoadMooCore',
            'profile.afterRenderMenu' => 'afterRenderMenu',
            'Controller.Search.search' => 'search',
            'Plugin.View.Api.Search' => 'apiSearch',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.beforeRender' => 'beforeRender',
            'MooView.beforeRender' => 'view_beforeRender',
            'UserController.deleteUserContent' => 'deleteUserContent',
            'ActivitesController.afterShare' => 'activityAfterShare',
            'profile.afterRenderMenu' => 'profileAfterRenderMenu',
            'welcomeBox.afterRenderMenu' => 'welcomeBoxAfterRenderMenu',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
            'Plugin.Controller.Album.afterDeleteAlbum' => 'afterDeleteAlbum',
            'Model.UserBlock.afterSave' => 'afterBlockUser',
            'Controller.Home.adminIndex.Statistic' => 'statistic',
            'Model.Activity.afterSetParamsConditionsOr' => 'afterSetParamsConditionsOr',
            'Model.afterSave' => 'afterSaveModel',
            
            'StorageHelper.businesses.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.businesses.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.businesses.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageHelper.business_verifies.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.business_verifies.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.business_verifies.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageHelper.business_covers.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.business_covers.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.business_covers.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
            
            'ApiHelper.renderAFeed.business_create' => 'apiFeedBusinessCreate',
            'ApiHelper.renderAFeed.business_checkin' => 'apiFeedBusinessCheckin',
            'ApiHelper.renderAFeed.business_review' => 'apiFeedBusinessReview',
            'ApiHelper.renderAFeed.business_photo' => 'apiFeedBusinessPhoto',
            'ApiHelper.renderAFeed.business_branch' => 'apiFeedBusinessBranch',
            'ApiHelper.renderAFeed.business_verify' => 'apiFeedBusinessVerify',
            'ApiHelper.renderAFeed.business_item_detail_share' => 'apiFeedBusinessItemDetailFeed',
            
            'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu'
        );
    }
	
    public function apiAfterRenderMenu($e)
    {
        if (Configure::read('Business.business_enabled')) {
            $subject = MooCore::getInstance()->getSubject();
            $e->data['result']['business'] = array(
                            'text' => __d('business', 'Businesses'),
                            'url' => FULL_BASE_URL . $e->subject()->request->base . '/businesses/my/'. $subject['User']['id'],
                            'cnt' => 0
            );
        }
    }
    public function beforeRender($event) {
        if (Configure::read('Business.business_enabled')) {
            
        }
    }

    public function view_beforeRender($event) {
        if (Configure::read('Business.business_enabled')) {
            if (Configure::read('debug') == 0) {
                $min = "min.";
            } else {
                $min = "";
            }
            $e = $event->subject();
            $e->MooPopup->register('businessModal');

            $e->Helpers->MooRequirejs->addPath(array(
                "mooBusinessFlexslider" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/jquery.flexslider-min.js"),
                "mooBusiness" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/business.{$min}js"),
                "business_jqueryui" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/jquery-ui.{$min}js"),
                "business_markerclusterer" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/markerclusterer.{$min}js"),
                "business_timeentry" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/jquery.timeentry.{$min}js"),
                "business_star_rating" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/star-rating.{$min}js"),
                "business_slick" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/slick/slick.{$min}js"),
                "mooSlimscroll" => $e->Helpers->MooRequirejs->assetUrlJS("Business.js/jquery.slimscroll.{$min}js"),
            ));

            $e->Helpers->MooRequirejs->addShim(array(
                'mooBusinessFlexslider' => array("deps" => array('jquery')),
                'business_markerclusterer' => array("deps" => array('jquery')),
                'business_timeentry' => array("deps" => array('jquery')),
                'business_star_rating' => array("deps" => array('jquery')),
                'business_slick' => array("deps" => array('jquery')),
                'business_cloudzoom' => array("deps" => array('jquery')),
                'business_jqueryui' => array("deps" => array('jquery')),
                'mooSlimscroll' => array("deps" => array('jquery')),
                'business_star_rating' => array("deps" => array('jquery')),
            ));

            $e->addPhraseJs(array(
                "BUSINESS_DETAIL_LINK_REVIEW" => BUSINESS_DETAIL_LINK_REVIEW,
                "BUSINESS_DETAIL_LINK_PHOTO" => BUSINESS_DETAIL_LINK_PHOTO,
                "BUSINESS_DETAIL_LINK_PRODUCT" => BUSINESS_DETAIL_LINK_PRODUCT,
                "BUSINESS_DETAIL_LINK_BRANCH" => BUSINESS_DETAIL_LINK_BRANCH,
                "BUSINESS_DETAIL_LINK_CHECKIN" => BUSINESS_DETAIL_LINK_CHECKIN,
                "BUSINESS_DETAIL_LINK_FOLLOWER" => BUSINESS_DETAIL_LINK_FOLLOWER,
                "business_text_show_map" => __d('business', 'Show map'),
                "business_text_hide_map" => __d('business', 'Hide map'),
                "business_text_confirm_remove_admin" => __d('business', 'Are you sure you want to remove?'),
                "business_text_confirm_remove_review" => __d('business', 'Are you sure you want to delete your review?'),
                'confirm' => __d('business', 'Confirm'),
                'remove_tags' => __d('business', 'Remove Tags'),
                'remove_tags_contents' => __d('business', 'You wont be tagged in  this post anymore. It may appear in other places like New Feed or search.'),
                'ok' => __d('business', 'Ok'),
                'cancel' => __d('business', 'Cancel'),
                'please_confirm' => __d('business', 'Please Confirm'),
                'please_confirm_remove_this_activity' => __d('business', 'Are you sure you want to remove this activity?'),
                'tmaxsize' => __d('business', 'Can not upload file more than ') . (MooCore::getInstance()->_getMaxFileSize() / 1024 / 1024) . 'MB',
                'tdesc' => __d('business', 'Drag or click here to upload photo'),
                'business_upload_document_drag' => __d('business', 'Drag or click here to upload documents'),
                'sizeLimit' => MooCore::getInstance()->_getMaxFileSize(),
                'please_confirm' => __d('business', 'Please Confirm'),
                'confirm' => __d('business', 'Confirm'),
                'remove_tags' => __d('business', 'Remove Tags'),
                'remove_tags_contents' => __d('business', 'You wont be tagged in  this post anymore. It may appear in other places like New Feed or search.'),
                'ok' => __d('business', 'Ok'),
                'cancel' => __d('business', 'Cancel'),
                'please_confirm' => __d('business', 'Please Confirm'),
                'please_confirm_remove_this_activity' => __d('business', 'Are you sure you want to remove this activity?'),
                'text_maximum_document' => __d('business', 'Maximum number documents for verification request is %s', 5),
                'business_text_confirm_approve_business' => __d('business', 'Are your sure you want to approve this business?'),
                'business_text_confirm_vefiry_business' => __d('business', 'Are your sure you want to verify this business?'),
                'business_text_confirm_unvefiry_business' => __d('business', 'Are your sure you want to unverify this business?'),
                'business_text_show_reviews' => __d('business', 'Show reviews'),
                'business_text_hide_reviews' => __d('business', 'Hide reviews'),
                'business_text_no_reviews' => __d('business', 'No reviews found'),
                'business_text_confirm_remove_business' => __d('business', 'Are you sure you want to remove this business?'),
                'business_text_confirm_remove_branch' => __d('business', 'Are you sure you want to remove this branch?'),
                'business_text_claim_review' => __d('business', 'Are you sure you want to accept this claim request? Products link to this business if any will be removed after this action is done!'),
                'business_text_claim_reject' => __d('business', 'Are you sure you want to reject this claim request?'),
                'business_text_claim_remove' => __d('business', 'Are you sure you want to remove this claim request?'),
                'business_text_characters_remaining' => __d('business', 'Characters remaining:'),
                'business_text_unban' => __d('business', 'Un-ban'),
                'business_text_ban' => __d('business', 'Ban'),
                'business_text_confirm_remove_favourite' => __d('business', 'Are you sure you want to remove this favourite business?'),
                'business_text_confirm_unfollow' => __d('business', 'Are you sure you want to unfollow this business?'),
            ));
            $allow_main_menu = true;
            if (($e->request->params['controller'] == 'home' && $e->request->params['action'] == 'index') ||
                    ($e->request->params['controller'] == 'business' && $e->request->params['action'] == 'create' && empty($e->request->query['create']))) {
                $allow_main_menu = false;
            }

            if (isset($e->request->params['plugin']) && $e->request->params['plugin'] == 'Business') {
                $e->Helpers->Html->css(array(
                    //'https://fonts.googleapis.com/icon?family=Material+Icons',
                    'Business.business'
                        ), array('block' => 'css', 'minify' => false)
                );
				$e->Helpers->Html->scriptBlock(
					"require(['jquery','mooBusiness'], function($,mooBusiness) {mooBusiness.parseAjaxLink();});", 
					array(
						'inline' => false,
				));
            }
            if (strtolower($e->theme) == 'mooapp') {
                 $e->Helpers->Html->css(array(
                    'Business.business_app',
					'Business.star-rating',
					'Business.business-widget'
				), array('block' => 'css', 'minify' => true));
            }

            $e->set('allow_main_menu', $allow_main_menu);
            if (isset($e->request->params['prefix']) && $e->request->params['prefix'] == 'admin')
            	return;
            	
            $e->Helpers->MooPopup->register('businessModal');
            $e->Helpers->Html->scriptBlock(
                    "require(['jquery','mooBusiness'], function($,mooBusiness) {mooBusiness.initActivity();});", array(
                'inline' => false,
                    )
            );
        }
    }

    public function deleteUserContent($event) {
        if (Configure::read('Business.business_enabled')) {
            $user_id = $event->data['aUser']['User']['id'];
            MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $businesses = $mBusiness->findAllByUserId($user_id);
            foreach ($businesses as $business) {
                $mBusiness->deleteBusiness($business['Business']['id']);
            }

            //delete business review
            $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
            $mBusinessReview->deleteAll(array(
                'BusinessReview.user_id' => $user_id
            ));

            //delete business follow
            $mBusinessFollow = MooCore::getInstance()->getModel('Business.BusinessFollow');
            $mBusinessFollow->deleteAll(array(
                'BusinessFollow.user_id' => $user_id
            ));

            //delete business admin
            $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
            $mBusinessAdmin->deleteAll(array(
                'BusinessAdmin.user_id' => $user_id
            ));

            //delete business checkin
            $mBusinessCheckin = MooCore::getInstance()->getModel('Business.BusinessCheckin');
            $mBusinessCheckin->deleteAll(array(
                'BusinessCheckin.user_id' => $user_id
            ));

            //delete business review useful
            $mBusinessReviewUseful = MooCore::getInstance()->getModel('Business.BusinessReviewUseful');
            $mBusinessReviewUseful->deleteAll(array(
                'BusinessReviewUseful.user_id' => $user_id
            ));
        }
    }

    public function afterRenderMenu($event) {
        if (Configure::read('Business.business_enabled')) {
            $e = $event->subject();
            //total my business
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $totalBusiness = $mBusiness->totalMyBusiness();

            //total follow
            $mBusinessFollow = MooCore::getInstance()->getModel('Business.BusinessFollow');
            $totalFollow = $mBusinessFollow->totalBusinessFollow();

            //total my business reviews
            $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
            $totalBusinessReview = $mBusinessReview->totalMyBusinessReview();

            $tab = !empty($e->request->named['tab']) ? $e->request->named['tab'] : '';
            $tab = str_replace('#', '', $tab);
            $e->set(array(
                'tab' => $tab,
                'totalBusiness' => $totalBusiness,
                'totalFollow' => $totalFollow,
                'totalBusinessReview' => $totalBusinessReview,
                'totalFavouriteBusiness' => $mBusiness->totalFavouriteBusiness()
            ));
            echo $e->element('Business.profile');
        }
    }

    public function search($event) {
        if (Configure::read('Business.business_enabled')) {
            $e = $event->subject();
            App::import('Model', 'Business.Business');
            $mBusiness = new Business();
            $results = $mBusiness->searchBusiness($e->keyword, 1, 5);

            $event->result['Business']['header'] = __d('business', 'Businesses');
            $event->result['Business']['icon_class'] = "business";
            $event->result['Business']['view'] = "lists/search_list";
            $event->result['Business']['notEmpty'] = $results == null ? 0 : 1;
            $e->set('businesses', $results);
        }
    }
    
    public function apiSearch($event)
    {
    	$view = $event->subject();
    	$items = &$event->data['items'];
    	$type = $event->data['type'];
    	$viewer = MooCore::getInstance()->getViewer();
    	$utz = $viewer['User']['timezone'];
        $hBusiness = MooCore::getInstance()->getHelper('Business_Business');
    	if ($type == 'Business' && !empty($view->viewVars['businesses']))
    	{
    		$helper = MooCore::getInstance()->getHelper('Business_Business');
    		foreach ($view->viewVars['businesses'] as $item){
    			$items[] = array(
    					'id' => $item["Business"]['id'],
    					'url' => FULL_BASE_URL.$item['Business']['moo_href'],
    					'avatar' =>  $url = $hBusiness->getPhoto($item['Business'], array(
                            'prefix' => BUSINESS_IMAGE_SMALL_WIDTH.'_',
                            'tag' => false
                        )),
    					'owner_id' => $item["Business"]['user_id'],
    					'title_1' => $item["Business"]['moo_title'],
    					'title_2' => __( 'Posted by') . ' ' . html_entity_decode($view->Moo->getNameWithoutUrl($item['User'], false),ENT_QUOTES) . ' ' .$view->Moo->getTime( $item["Business"]['created'], Configure::read('core.date_format'), $utz ),
    					'created' => $item["Business"]['created'],
    					'type' => "Business",
                            'type_title' => __d('business', 'Businesses')
    			);
    		}
    	}
    }

    public function suggestion($event) {
        if (Configure::read('Business.business_enabled') && isset($event->data['type'])) {
            $e = $event->subject();
            App::import('Model', 'Business.Business');
            $mBusiness = new Business();
			$hBusiness = MooCore::getInstance()->getHelper('Business_Business');

            $event->result['business']['header'] = __d('business', 'Businesses');
            $event->result['business']['icon_class'] = 'business';

            if ($event->data['type'] == 'all') {
                $event->result['business'] = null;
                $businesses = $mBusiness->searchBusiness($e->request->data['searchVal'], 1, 2);
                if (count($businesses) > 2) {
                    $businesses = array_slice($businesses, 0, 2);
                }

                foreach ($businesses as $index => &$detail) {
                    $event->result['business'][$index]['id'] = $detail['Business']['id'];
					$url = $hBusiness->getPhoto($detail['Business'], array(
						'prefix' => BUSINESS_IMAGE_THUMB_WIDTH.'_',
						'tag' => false
					));
                    $event->result['business'][$index]['img'] = $url;
                    $event->result['business'][$index]['title'] = $detail['Business']['name'];
                    $event->result['business'][$index]['find_name'] = 'Find Business';
                    $event->result['business'][$index]['icon_class'] = 'business';
                    $event->result['business'][$index]['view_link'] = 'business/view/' . $detail['Business']['id'] . '/' . seoUrl($detail['Business']['name']);

                    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');

                    $utz = (!is_numeric(Configure::read('core.timezone')) ) ? Configure::read('core.timezone') : 'UTC';
                    $cuser = MooCore::getInstance()->getViewer();
                    // user timezone
                    if (!empty($cuser['User']['timezone'])) {
                        $utz = $cuser['User']['timezone'];
                    }

                    $event->result['business'][$index]['more_info'] = $mooHelper->getTime($detail['Business']['created'], Configure::read('core.date_format'), $utz);
                }
            } else if ($event->data['type'] == 'business') {
                //$results = $mBusiness->searchBusiness($e->request->pass[1]);
                //$e->set('businesses', $results);
                //$e->set('element_list_path',"Business.lists/search_list");

                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $businesses = $mBusiness->searchBusiness($e->request->pass[1], $page);
                $e->set('businesses', $businesses);
                $e->set('result', 1);
                $e->set('more_url', '/search/suggestion/business/' . $e->request->pass[1] . '/page:' . ( $page + 1 ));
                $e->set('element_list_path', "Business.lists/search_list");
            }
        }
    }

    public function activityAfterShare($event) {
        $e = $event->subject();
        if (Configure::read('Business.business_enabled') && 
			!empty($e->data['type']) && 
			$e->data['type'] == BUSINESS_ACTIVITY_TYPE && 
			($e->data['action'] == 'wall_post' || $e->data['action'] == 'wall_post_link') &&
			empty($e->data['statusBackgroundId']) && empty($e->data['userShareVideo'])) {
            App::import('Model', 'Business.Business');
            $mBusiness = new Business();
            App::import('Model', 'Activity');
            $mActivity = new Activity();

            $acitity = $mActivity->find('first', array('order' => array('Activity.id' => 'desc')));
            $mActivity->updateAll(array(
                'Activity.plugin' => "'Business'"
                    ), array(
                'Activity.id' => $acitity['Activity']['id'],
                'Activity.user_id' => $e->data['user_id'],
                'Activity.content' => $e->data['content']
            ));

            $business = $mBusiness->getOnlyBusiness($e->data['target_id']);
            $mBusiness->sendBusinessNotification(
                    $business['Business']['id'], 'business_wall_post', MooCore::getInstance()->getViewer(true), '/users/view/'.MooCore::getInstance()->getViewer(true).'/activity_id:'.$acitity['Activity']['id']
            );
        }
    }

     public function profileAfterRenderMenu($event) {
        $e = $event->subject();
        if (Configure::read('Business.business_enabled')) {
            if(!empty($e->request->params['username']))
            {
                $mUser = MooCore::getInstance()->getModel('User');
                $user = $mUser->findByUsername($e->request->params['username']);
                $profile_uid = $user['User']['id'];
            }
            else
            {
                $profile_uid = $e->request->params['pass'][0];
            }
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $total = $mBusiness->totalMyBusiness($profile_uid);
            echo $e->Html->css(array(
                //'https://fonts.googleapis.com/icon?family=Material+Icons',
                'Business.star-rating',
                'Business.business-widget'
                    ), array('block' => 'css', 'minify' => false));
            echo $e->element('menu_profile', array('count' => $total, 'profile_uid' => $profile_uid), array('plugin' => ' Business'));
        }
    }

    public function welcomeBoxAfterRenderMenu($event) {
        $e = $event->subject();
        if (Configure::read('Business.business_enabled')) {
            $uid = MooCore::getInstance()->getViewer(true);
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $total = $mBusiness->totalMyBusiness($uid);
            echo $e->Html->css(array(
                //'https://fonts.googleapis.com/icon?family=Material+Icons',
                'Business.star-rating',
                'Business.business-widget'
                    ), array('block' => 'css', 'minify' => false));
            echo $e->element('menu_welcome', array('count' => $total), array('plugin' => 'Business'));
        }
    }

    public function hashtagEnable($event) {
        if (Configure::read('Business.business_enabled')) {
            $event->result['businesses']['enable'] = Configure::read('Business.business_enable_hashtag');
        }
    }

    public function hashtags($event) {
        if (Configure::read('Business.business_enabled')) {
            $enable = Configure::read('Business.business_enable_hashtag');
            $businesses = array();
            $e = $event->subject();
            App::import('Model', 'Business.Business');
            $mHashTag = MooCore::getInstance()->getModel('HashTag');
            $this->Business = new Business();
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

            $uid = CakeSession::read('uid');
            if ($enable) {
                if (isset($event->data['type']) && $event->data['type'] == 'businesses') {
                    $businesses = $this->Business->getBusinessHashtags($event->data['item_ids'], RESULTS_LIMIT, $page);
                    $businesses = $this->_filterBusiness($businesses);
                }
                $table_name = $this->Business->table;
                if (isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name])) {
                    $businesses = $this->Business->getBusinessHashtags($event->data['item_groups'][$table_name], 5);
                    $businesses = $this->_filterBusiness($businesses);
                }
            }

            // get tagged item
            $tag = h(urldecode($event->data['search_keyword']));
            $business_ids = $mHashTag->find('list', array(
                'conditions' => array(
                    'HashTag.item_table' => 'businesses',
                    'FIND_IN_SET("'.$tag.'", REPLACE(HashTag.hashtags, " ", ""))'
                ),
                'fields' => array('HashTag.item_id', 'HashTag.item_id')
            ));

            $friendModel = MooCore::getInstance()->getModel('Friend');

            $items = $this->Business->find('all', array('conditions' => array(
                    'Business.id' => $business_ids
                ),
                'limit' => RESULTS_LIMIT,
                'page' => $page
            ));

            $viewer = MooCore::getInstance()->getViewer();

            foreach ($items as $key => $item) {
                $owner_id = $item[key($item)]['user_id'];
                $privacy = isset($item[key($item)]['privacy']) ? $item[key($item)]['privacy'] : 1;
                if (empty($viewer)) { // guest can view only public item
                    if ($privacy != PRIVACY_EVERYONE) {
                        unset($items[$key]);
                    }
                } else { // viewer
                    $aFriendsList = array();
                    $aFriendsList = $friendModel->getFriendsList($owner_id);
                    if ($privacy == PRIVACY_ME) { // privacy = only_me => only owner and admin can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id) {
                            unset($items[$key]);
                        }
                    } else if ($privacy == PRIVACY_FRIENDS) { // privacy = friends => only owner and friendlist of owner can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], array_keys($aFriendsList))) {
                            unset($items[$key]);
                        }
                    } else {
                        
                    }
                }
            }
            $businesses = array_merge($businesses, $items);

            //only display 5 items on All Search Result page
            if (isset($event->data['type']) && $event->data['type'] == 'all') {
                $businesses = array_slice($businesses, 0, 5);
            }
            $businesses = array_map("unserialize", array_unique(array_map("serialize", $businesses)));
            if (!empty($businesses)) {
                $event->result['businesses']['header'] = __('Businesses');
                $event->result['businesses']['icon_class'] = 'work';
                $event->result['businesses']['view'] = "Business.lists/search_list";
                if (isset($event->data['type']) && $event->data['type'] == 'businesses') {
                    $e->set('result', 1);
                    $e->set('more_url', '/search/hashtags/' . $e->params['pass'][0] . '/businesses/page:' . ( $page + 1 ));
                    $e->set('element_list_path', "Business.lists/search_list");
                }
                $e->set('businesses', $businesses);
            }
        }
    }

    public function hashtags_filter($event) {
        if (Configure::read('Business.business_enabled')) {
            $e = $event->subject();
            App::import('Model', 'Business.Business');
            $this->Business = new Business();

            if (isset($event->data['type']) && $event->data['type'] == 'businesses') {
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $businesses = $this->Business->getBusinessHashtags($event->data['item_ids'], RESULTS_LIMIT, $page);
                $e->set('businesses', $businesses);
                $e->set('result', 1);
                $e->set('more_url', '/search/hashtags/' . $e->params['pass'][0] . '/businesses/page:' . ( $page + 1 ));
                $e->set('element_list_path', "Business.lists/businesses_list");
            }
            $table_name = $this->Business->table;
            if (isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name])) {
                $event->result['businesses'] = null;

                $businesses = $this->Business->getBusinessHashtags($event->data['item_groups'][$table_name], 5);

                if (!empty($businesses)) {
                    $event->result['businesses']['header'] = __('Businesses');
                    $event->result['businesses']['icon_class'] = 'work';
                    $event->result['businesses']['view'] = "Business.lists/businesses_list";
                    $e->set('businesses', $businesses);
                }
            }
        }
    }

    private function _filterBusiness($businesses) {
        if (!empty($businesses)) {
            $friendModel = MooCore::getInstance()->getModel('Friend');
            $viewer = MooCore::getInstance()->getViewer();
            foreach ($businesses as $key => &$business) {
                $owner_id = $business[key($business)]['user_id'];
                $privacy = isset($business[key($business)]['privacy']) ? $business[key($business)]['privacy'] : 1;
                if (empty($viewer)) { // guest can view only public item
                    if ($privacy != PRIVACY_EVERYONE) {
                        unset($businesses[$key]);
                    }
                } else { // viewer
                    $aFriendsList = array();
                    $aFriendsList = $friendModel->getFriendsList($owner_id);
                    if ($privacy == PRIVACY_ME) { // privacy = only_me => only owner and admin can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id) {
                            unset($businesses[$key]);
                        }
                    } else if ($privacy == PRIVACY_FRIENDS) { // privacy = friends => only owner and friendlist of owner can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], array_keys($aFriendsList))) {
                            unset($businesses[$key]);
                        }
                    } else {
                        
                    }
                }
            }
        }

        return $businesses;
    }

    public function afterDeleteAlbum($event) {
        if (Configure::read('Business.business_enabled')) {
            $e = $event->subject();
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $business = isset($e->params['pass'][0]) ? $mBusiness->findByAlbumId($e->params['pass'][0]) : null;
            if ($business != null) {
                $mBusiness->deleteActivity($business['Business']['id'], BUSINESS_ACTIVITY_PHOTO_ACTION, BUSINESS_ACTIVITY_PHOTO_ITEM);
            }
        }
    }

    public function afterBlockUser($event) {
        $e = $event->subject();
        $user_id = $e->data['UserBlock']['object_id'];
        $block_user_id = $e->data['UserBlock']['user_id'];
        $mBusinessFollow = MooCore::getInstance()->getModel('Business.BusinessFollow');
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $mBusinessFavourite = MooCore::getInstance()->getModel('Business.BusinessFavourite');

        //user business list
        $mBusinessFollow->recursive = -1;
        $user_business_list = $mBusiness->find('list', array(
            'conditions' => array(
                'Business.user_id' => $user_id
            ),
            'fields' => array('Business.id', 'Business.id')
        ));
        $block_user_business_list = $mBusiness->find('list', array(
            'conditions' => array(
                'Business.user_id' => $block_user_id
            ),
            'fields' => array('Business.id', 'Business.id')
        ));

        //delete unfollow
        $mBusinessFollow->recursive = -1;
        $mBusinessFollow->deleteAll(array(
            'BusinessFollow.user_id' => $block_user_id,
            'BusinessFollow.business_id' => $user_business_list
        ));
        $mBusinessFollow->deleteAll(array(
            'BusinessFollow.user_id' => $user_id,
            'BusinessFollow.business_id' => $block_user_business_list
        ));

        //delete favourite
        $mBusinessFavourite->recursive = -1;
        $mBusinessFavourite->deleteAll(array(
            'BusinessFavourite.user_id' => $block_user_id,
            'BusinessFavourite.business_id' => $user_business_list
        ));
        $mBusinessFavourite->deleteAll(array(
            'BusinessFavourite.user_id' => $user_id,
            'BusinessFavourite.business_id' => $block_user_business_list
        ));
    }

    public function statistic($event) {
        $request = Router::getRequest();
        $mBusiness = MooCore::getInstance()->getModel("Business.Business");
        $event->result['statistics'][] = array(
            'item_count' => $mBusiness->find('count'),
            'ordering' => 9999,
            'name' => __d('business', 'Businesss'),
            'href' => $request->base . '/admin/business',
            'icon' => '<i class="fa fa-briefcase"></i>'
        );
    }

    public function afterSetParamsConditionsOr($event)
    {
        $data = array(array('Activity.type' => 'Business_Business'));
        $event->result[] = $data;
    }
    
    public function afterSaveModel($event)
    {
        $model = $event->subject();
        $type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);
        if ($type == 'Activity' && !empty($model->data['Activity']['action']) && $model->data['Activity']['action'] == 'wall_post_link' && !empty($model->data['Activity']['type']) && $model->data['Activity']['type'] == 'Business_Business')
        {
            $notificationModel = MooCore::getInstance()->getModel("Notification");
            $mBusiness = MooCore::getInstance()->getModel("Business.Business");
            $mBusinessFollow = MooCore::getInstance()->getModel("Business.BusinessFollow");
            $mBusinessAdmin = MooCore::getInstance()->getModel("Business.BusinessAdmin");
            
            $business = $mBusiness->getOnlyBusiness($model->data['Activity']['target_id']);
            if($business != null)
            {
                $followers = $mBusinessFollow->getFollowerIds($model->data['Activity']['target_id']);
                $admin_list = $mBusinessAdmin->loadAdminListId($model->data['Activity']['target_id']);
                $followers = array_merge($followers, $admin_list);
                $followers[] = $business['Business']['user_id'];
                $followers = array_unique($followers);
                if($followers != null)
                {
                    foreach($followers as $follower_id)
                    {
                        $user_id = $model->data['Activity']['user_id'];
                        if($follower_id == $user_id)
                        {
                            continue;
                        }
                        $url = '/users/view/'.$user_id.'/activity_id:'.$model->id;
                        $notificationModel->clear();
                        $notificationModel->save(array(
                            'user_id' => $follower_id,
                            'sender_id' => $user_id,
                            'action' => 'business_wall_post',
                            'url' => $url,
                            'params' => $business['Business']['name'],
                            'plugin' => 'Business',
                        ));
                    }
                }
            }
        }
    }
    
    /////////////////////////////////////amazon s3/////////////////////////////////////
    public function storage_geturl_local($e)
    {
    	$v = $e->subject();
    	$request = Router::getRequest();
    	$oid = $e->data['oid'];
    	$type = $e->data['type'];
    	$thumb = $e->data['thumb'];
    	$prefix = $e->data['prefix'];
    	
        switch($type)
        {
            case 'businesses':
                if ($e->data['thumb']) 
                {
                    $url = FULL_BASE_LOCAL_URL.$request->webroot.BUSINESS_FILE_URL.'/'.$prefix.$thumb;
                } 
                else 
                {
                    $url = $v->getImage("business/images/".$prefix."no-image.png");
                }
                break;
            case 'business_verifies':
                $url = FULL_BASE_LOCAL_URL.$request->webroot.'uploads/zip'.'/'.$prefix.$thumb;
                break;
            case 'business_covers':
                if ($e->data['thumb'])
                {
                    $url = FULL_BASE_LOCAL_URL.$request->webroot.BUSINESS_COVER_FILE_URL.$prefix.$thumb;
                }
                else
                {
                    $url = $v->getImage("business/images/cover.png");
                }
                break;
        }
    	$e->result['url'] = $url;
    }
    
    public function storage_geturl_amazon($e)
    {
    	$v = $e->subject();
    	$type = $e->data['type'];
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $business_id = $e->data['oid'];
        $business = $mBusiness->getOnlyBusiness($business_id);
        if(!empty($business['Business']) && $business['Business']['is_claim'] != 0 && $business['Business']['claim_id'] != 0)
        {
			$business_claim = $mBusiness->getOnlyBusiness($business['Business']['claim_id']);
			if($business['Business']['logo'] == $business_claim['Business']['logo'])
			{
				$business_id = $business['Business']['claim_id'];
			}
        }
        switch($type)
        {
            case 'businesses':
                $e->result['url'] = $v->getAwsURL($business_id, "businesses", $e->data['prefix'], $e->data['thumb']);
                break;
            case 'business_verifies':
                $e->result['url'] = $v->getAwsURL($business_id, "business_verifies", $e->data['prefix'], $e->data['thumb']);
                break;
            case 'business_covers':
                $e->result['url'] = $v->getAwsURL($e->data['oid'], "business_covers", $e->data['prefix'], $e->data['thumb']);
                break;
        }
    }
    
    public function storage_amazon_get_file_path($e)
    {
    	$objectId = $e->data['oid'];
    	$name = $e->data['name'];
    	$thumb = $e->data['thumb'];
    	$type = $e->data['type'];;
    	$path = false;
        switch($type)
        {
            case 'businesses':
                if (!empty($thumb)) {
                    $path = WWW_ROOT . "uploads" . DS . "businesses" . DS . $name .$thumb;
                }
                break;
            case 'business_verifies':
                if (!empty($thumb)) {
                    $path = WWW_ROOT . "uploads" . DS . "zip" . DS . $name .$thumb;
                }
                break;
            case 'business_covers':
                $path = WWW_ROOT . "uploads" . DS . "businesses" . DS . "covers" . DS .$thumb;
                break;
        }
    	$e->result['path'] = $path;
    }
	
	public function storage_task_transfer($e)
    {
    	$v = $e->subject();
    	$mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $businesses = $mBusiness->find('all', array(
			'conditions' => array("Business.id > " => $v->getMaxTransferredItemId("businesses")),
			'limit' => 10,
			'order' => array('Business.id'),
		));
        if($businesses != null)
        {
    		foreach($businesses as $business){
				$business = $business['Business'];
    			if (!empty($business["image"])) {
                    $v->transferObject($business['id'], 'businesses', BUSINESS_IMAGE_SEO_WIDTH, $business["image"]);
                    $v->transferObject($business['id'], 'businesses', BUSINESS_IMAGE_THUMB_WIDTH, $business["image"]);
                    $v->transferObject($business['id'], 'businesses', BUSINESS_IMAGE_SMALL_WIDTH, $business["image"]);
    				$v->transferObject($business['id'], 'businesses', '', $business["image"]);   				
    			}
    		}
    	}
    }
    
    /////////////////////////////////////support app 1.2/////////////////////////////////////
    public function apiFeedBusinessCreate($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $business_item = $businessHelper->getOnlyBusiness($data['Activity']['item_id']);
        $business = $business_item['Business'];
    	
        $title = __d('business', ' created a new business').' <a href="'.$business['moo_href'].'">'.$business['name'].'</a>';;
        
    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $actorHtml.' '. $title,
            'objects' => array(
                'type' => 'Business_Business',
                'id' => $business['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($business['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $this->appBusinessActivityDescription($e, $business_item),
                'title' => $business['name'],
                'images' => array('850' => $this->appBusinessActivityImage($e, $business_item)),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    public function apiFeedBusinessCheckin($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $mUserTagging = MooCore::getInstance()->getModel('UserTagging');
        $business_item = $businessHelper->getOnlyBusiness($data['Activity']['target_id']);
        $user_tagging = $mUserTagging->findById($data['Activity']['item_id']);
        $business = $business_item['Business'];
    	
        $title = __d('business', ' busines check in');
        $titleHtml = $actorHtml.' '.__d('business', ' at ').'<a href="'.$business['moo_href'].'">'.$business['name'].'</a>';
        if(!empty($user_tagging['UserTagging']['users_taggings'])) 
        {
            $titleHtml .= ' '.$businessHelper->with($user_tagging['UserTagging']['id'], $user_tagging['UserTagging']['users_taggings'], false);
        }

    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Business_Business',
                'id' => $business['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($business['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $this->appBusinessActivityDescription($e, $business_item),
                'title' => $business['name'],
                'images' => array('850' => $this->appBusinessActivityImage($e, $business_item)),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    public function apiFeedBusinessReview($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $business_item = $businessHelper->getOnlyBusiness($data['Activity']['target_id']);
        $business = $business_item['Business'];
    	
        $business_review = $businessHelper->getReviewDetail($data['Activity']['item_id'], $data['Activity']['target_id']);
        $title = __d('business', ' busines review');
        $titleHtml = $actorHtml.' '.__d('business', 'wrote a review for').' <a href="'.($business['parent_id'] > 0 ? $business['moo_href'] : $business['moo_hrefreview']."?review=".$data['Activity']['item_id']).'">'.$business['name'].'</a>';
        $url = $business['parent_id'] > 0 ? $business['moo_href'] : $business['moo_hrefreview']."?review=".$data['Activity']['item_id'];
        
    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Business_Business',
                'id' => $business['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($url, 'UTF-8', 'UTF-8')),
                'description' => $business_review['BusinessReview']['content'],
                //'description' => $this->appBusinessActivityDescription($e, $business_item),
                //'title' => $business['name'],
                //'images' => array('850' => $this->appBusinessActivityImage($e, $business_item)),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    public function apiFeedBusinessPhoto($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $business_item = $businessHelper->getOnlyBusiness($data['Activity']['target_id']);
        $business = $business_item['Business'];
        $business_photos = $businessHelper->getBusinessAlbumPhotos($business['id']);
        $photo_count = $data['Activity']['items'] != null ? explode(',', $data['Activity']['items']) : array();
    	
        $title = __d('business', ' busines review');
        $titleHtml = $actorHtml;
        if(count($business_photos) > 1) 
        {
            $titleHtml .= " ".sprintf(__d('business', ' added %s photos for '), count($photo_count)); 
        }
        else 
        {
            $titleHtml .= " ".sprintf(__d('business', ' added %s photo for '), count($business_photos));
        }
        $titleHtml .= ' <a href="'.$business['moo_href'].'">'.$business['name'].'</a>';
        
    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Business_Business',
                'id' => $business['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($business['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $this->appBusinessActivityDescription($e, $business_item),
                'title' => $business['name'],
                'images' => array('850' => $this->appBusinessActivityImage($e, $business_item)),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    public function apiFeedBusinessBranch($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $business_item = $businessHelper->getOnlyBusiness($data['Activity']['item_id']);
        $business_parent_item = $businessHelper->getOnlyBusiness($data['Activity']['target_id']);
        $business = $business_item['Business'];
        $business_parent = $business_parent_item['Business'];
    	
        $title = __d('business', ' created a new sub page for ').' <a href="'.$business_parent['moo_href'].'">'.$business_parent['name'].'</a>';;
        
    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $actorHtml.' '. $title,
            'objects' => array(
                'type' => 'Business_Business',
                'id' => $business['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($business['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $this->appBusinessActivityDescription($e, $business_item),
                'title' => $business['name'],
                'images' => array('850' => $this->appBusinessActivityImage($e, $business_item)),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    public function apiFeedBusinessVerify($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $business_item = $businessHelper->getOnlyBusiness($data['Activity']['target_id']);
        $business = $business_item['Business'];
    	
        $title = __d('business', ' busines verify');
        $titleHtml = $actorHtml.' '.__d('business', ' verified this business: ').'<a href="'.$business['moo_href'].'">'.$business['name'].'</a>';

    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Business_Business',
                'id' => $business['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($business['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $this->appBusinessActivityDescription($e, $business_item),
                'title' => $business['name'],
                'images' => array('850' => $this->appBusinessActivityImage($e, $business_item)),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    public function apiFeedBusinessItemDetailFeed($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $mUser = MooCore::getInstance()->getModel('User');
        $business_item = $businessHelper->getOnlyBusiness($data['Activity']['parent_id']);
        $business = $business_item['Business'];
        $user = $mUser->findById($business['user_id']);
    	
        $title = __d('business', ' busines verify');
        $titleHtml = $actorHtml.' '.__d("business", "shared %s's %s", "<a href=".$user['User']['moo_href'].">".$user['User']['name']."</a>", "<a href=".$business['moo_href'].">".__d('business', 'business')."</a>");

    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Business_Business',
                'id' => $business['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($business['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $this->appBusinessActivityDescription($e, $business_item),
                'title' => $business['name'],
                'images' => array('850' => $this->appBusinessActivityImage($e, $business_item)),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    private function appBusinessActivityImage($e, $business)
    {
        if($business == null)
        {
            return "";
        }
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $business = $business['Business'];
        return $businessHelper->getPhoto($business, array(
            'prefix' => BUSINESS_IMAGE_SMALL_WIDTH.'_', 
            'tag' => false
        ));
    }
    
    private function appBusinessActivityDescription($e, $business)
    {
        if($business == null)
        {
            return "";
        }
        $business_categories = !empty($business['BusinessCategory']) ? $business['BusinessCategory'] : null;
        $business = $business['Business'];
        
        $description_html = "";
        //categories
        if($business_categories != null)
        {
            $cat_html = "";
            foreach($business_categories as $k_cat => $business_category)
            {
                $cat_html .= '<a class="bus_feed_size" href="'.$business_category['moo_href'].'">
                    '. $business_category['name'].'
                </a>';
                if($k_cat < count($business_categories) - 1)
                {
                    $cat_html .= ", ";
                }
            }
            $description_html .= $cat_html."<br/>";
        }
        
        //review count
        $description_html .= '<a class="bus_feed_size" href="'.$business['moo_hrefreview'].'">
            '.(sprintf($business['review_count'] == 1 ?  __d('business', '%s review') : __d('business', '%s reviews'), $business['review_count'])).'
        </a>';
        
        //description
        $description = $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(
                strip_tags(
                    str_replace(array('<br>', '&nbsp;'), array(' ', ''),  $business['description'])
                ), 200, array('eclipse' => '')), Configure::read('Business.business_enable_hashtag'));
        $description_html .= "<br/>".$description;
        
        return $description_html;
    }
}
