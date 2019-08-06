<?php

App::uses('CakeEventListener', 'Event');

class AdsListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            //'MooView.afterLoadMooCore' => 'afterLoadMooCore',
            'welcomeBox.afterRenderMenu' => 'welcomeBoxAfterRenderMenu',
            'profile.afterRenderMenu' => 'afterRenderMenu',
            'MooView.beforeRender' => 'beforeRender',
            'element.activities.afterRenderOneFeed' => 'afterRenderOneFeed',
            'Controller.Role.afterSave'=>'afterSaveRoles',
			'ApiHelper.afterRenderApiOneFeed' => 'afterRenderApiOneFeed',
        );
    }

    public function afterRenderMenu($event) {
        $e = $event->subject();
        $adsModel = MooCore::getInstance()->getModel('Ads.AdsCampaign');
        $role_ads_can_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_CAN_ADD_ADS);
        $role_ads_hide_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_HIDE_ALL_ADS);
        if (Configure::read('Ads.ads_enabled') && $role_ads_can_add_ads && !$role_ads_hide_add_ads) {
            $user_id = AuthComponent::user('id');
            if (!empty($user_id)) {
                $userProile = MooCore::getInstance()->getSubject();
                $user_profile_id = 0;
                if (!empty($userProile)) {
                    $user_profile_id = $userProile['User']['id'];
                }
                if ($user_profile_id == $user_id) {
                    $mAdsCampaign = MooCore::getInstance()->getModel('Ads.AdsCampaign');
                    $totalAds = $mAdsCampaign->countTotalAdsCamExist($user_id);
                    echo $e->element('Ads.myAds/my_ads', array('user_id' => $user_id, 'number' => $totalAds));
                }
            }
        }
    }
    public function welcomeBoxAfterRenderMenu($event) {
        $e = $event->subject();
        $adsModel = MooCore::getInstance()->getModel('Ads.AdsCampaign');
        $role_ads_can_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_CAN_ADD_ADS);
        $role_ads_hide_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_HIDE_ALL_ADS);
        if (Configure::read('Ads.ads_enabled') && $role_ads_can_add_ads && !$role_ads_hide_add_ads) {
            $user_id = AuthComponent::user('id');
            if (!empty($user_id)) {
                $mAdsCampaign = MooCore::getInstance()->getModel('Ads.AdsCampaign');
                $totalAds = $mAdsCampaign->countTotalAdsCamExist($user_id);
                echo $e->element('Ads.myAds/welcome_ads', array('user_id' => $user_id, 'number' => $totalAds));
            }
        }
    }

    public function beforeRender($event) {
        $url = Router::url('/', true) . 'ads/js/ads-test.js';
        $notify = "
                alert('" . __d("ads", "We\'ve detected that you have an ad blocker enabled! Please disable and refresh this page!") . "');
            ";
        //$js .= "<script src=\'ads/js/ads-test.js\'></script>";
        $js = "$.getScript('$url').fail(function(){alert('" . __d("ads", "We\'ve detected that you have an ad blocker enabled! Please disable and refresh this page!") . "')});";
        //  $js.="if(ads_test_adblock_exist == 0){alert('adblock detected');}";



        $e = $event->subject();
        if (isset($e->request->params['admin']) && $e->request->params['admin']) {
            
        } else {
            /*if ($e instanceof MooView) {
                $e->Helpers->Html->scriptBlock(
                        "require(['jquery'], function($) {\$(document).ready(function(){ $js});});", array(
                    'inline' => false,
                        )
                );
            }*/
        }
        if (Configure::read('Ads.ads_enabled')) {
            $e->Helpers->Html->css(array(
                '/commercial/css/commercial.css'
                    ), array('block' => 'css')
            );
            $e->Helpers->MooRequirejs->addPath(array(
                "ads_main" => $e->Helpers->MooRequirejs->assetUrlJS("commercial/js/main.js"),
                "ads_jquery-ui" => $e->Helpers->MooRequirejs->assetUrlJS("commercial/js/jquery-ui.js"),
            ));
            $e->Helpers->MooRequirejs->addShim(array(
                'ads_jquery-ui' => array("deps" => array('jquery')),
            ));

            $e->addPhraseJs(array(
                'delete_question_confirm' => __d('question', 'Are you sure you want to delete this question?')
            ));
        }
    }

    public function afterRenderOneFeed($event) {
        $position = $event->data['index'] + 1;
        $mPlace = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        $mPlaceFeed = MooCore::getInstance()->getModel('Ads.AdsPlacementFeed');
        $aPlaces = $mPlaceFeed->getPlacementByFeedPosition($position);
        $adsModel = MooCore::getInstance()->getModel('Ads.AdsCampaign');
        $role_ads_can_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_CAN_ADD_ADS);
        $role_ads_hide_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_HIDE_ALL_ADS);
        $is_admin = false;
        $viewer = MooCore::getInstance()->getViewer();
       /*if($viewer && $viewer['Role']['is_admin']){
           $is_admin = true;
       }*/
        if ($aPlaces && Configure::read('Ads.ads_enabled') && (!$role_ads_hide_add_ads || $is_admin == true)) {

            $aAds = array();
            foreach ($aPlaces as $place) {
                $place = $place['AdsPlacement'];
                $results = $mPlace->loadAdsCampaignsByPlacement($place['id'], $place);
                if ($results) {
                    foreach ($results as $data) {
                        $aAds[] = $data['AdsCampaign'];
                    }
                }
            }
            if ($aAds) {
                $aView = array();
                $aClick = array();
                foreach ($aAds as $ad) {
                    $aView[] = $ad['view_count'];
                    $aClick[] = $ad['click_count'];
                }
                array_multisort($aView, SORT_ASC, SORT_NUMERIC, $aClick, SORT_DESC, SORT_NUMERIC, $aAds);
                $e = $event->subject();
                $ads_uid = uniqid();
                $time_interval = 10; // second
                $time_setting = Configure::read('Ads.ad_refresh_time');
                if (!empty($time_setting)) {
                    $time_interval = (int) $time_setting;
                }
                $time_interval*= 1000;
                $campaign_id = $aAds[0]['id'];
                $mReport = MooCore::getInstance()->getModel('Ads.AdsReport');

                if (!isset($_SESSION['Ads']['View'][$campaign_id])) {
                    $_SESSION['Ads']['View'][$campaign_id] = time();
                    $mReport->clear();
                    $mReport->_updateViewCount($campaign_id, 'view');
                } elseif (time() - $_SESSION['Ads']['View'][$campaign_id] > ADS_SESSION_EXPIRE) {
                    unset($_SESSION['Ads']['View'][$campaign_id]);
                }
                $base_url = Router::url('/', true);
                echo $e->element('Ads./ads_feed', array('aAds' => $aAds, 'ads_uid' => $ads_uid, 'time_interval' => $time_interval, 'base_url' => $base_url));
            }
        }
    }
    
    public function afterSaveRoles($event){
        $e = $event->subject();
        if (Configure::read('Ads.ads_enabled')) {
            $data = $e->data;
            $is_admin = $data['is_admin'];
            $mCMitem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $adsMitem = $mCMitem->findByPlugin('Ads');
            $item_roles = array();
            if($adsMitem){
                $adsMitem = $adsMitem['CoreMenuItem'];
                $item_roles = json_decode($adsMitem['role_access'],true);
            }
            if(empty($data['param_ads_can_add_ads'])){
                // eleminate ads in core menuitem
                 if($item_roles){
                     $item_roles = array_diff($item_roles,array($data['id']));
                     $item_roles = array_values($item_roles);
                     $mCMitem->save(array('id'=>$adsMitem['id'] ,'role_access'=>json_encode($item_roles)));
                 }
            }else{
                if($item_roles){
                    $item_roles[] = $data['id'];
                    $item_roles = array_unique($item_roles);
                    $mCMitem->save(array('id'=>$adsMitem['id'] ,'role_access'=>json_encode($item_roles)));
                }
            }
            
        }

    }

	public function afterRenderApiOneFeed($event)
	{
		$e = $event->subject();
		$position = $event->data['index'] + 1;
        $mPlace = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        $mPlaceFeed = MooCore::getInstance()->getModel('Ads.AdsPlacementFeed');
        $aPlaces = $mPlaceFeed->getPlacementByFeedPosition($position);
        $adsModel = MooCore::getInstance()->getModel('Ads.AdsCampaign');
        $role_ads_can_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_CAN_ADD_ADS);
        $role_ads_hide_add_ads = $adsModel->adsCheckUserRoles(ROLE_ADS_HIDE_ALL_ADS);
        $is_admin = false;
        $viewer = MooCore::getInstance()->getViewer();
		
		$aAd = array();
        if ($aPlaces && Configure::read('Ads.ads_enabled') && (!$role_ads_hide_add_ads || $is_admin == true)) {
			$aAds = array();
            foreach ($aPlaces as $place) {
                $place = $place['AdsPlacement'];
                $results = $mPlace->loadAdsCampaignsByPlacement($place['id'], $place);
                if ($results) {
                    foreach ($results as $data) {
                        $aAds[] = $data['AdsCampaign'];
                    }
                }
            }
            if ($aAds) {
                $aView = array();
                $aClick = array();
                foreach ($aAds as $ad) {
                    $aView[] = $ad['view_count'];
                    $aClick[] = $ad['click_count'];
                }
                array_multisort($aView, SORT_ASC, SORT_NUMERIC, $aClick, SORT_DESC, SORT_NUMERIC, $aAds);
                $ads_uid = uniqid();
                $time_interval = 10; // second
                $time_setting = Configure::read('Ads.ad_refresh_time');
                if (!empty($time_setting)) {
                    $time_interval = (int) $time_setting;
                }
                $time_interval*= 1000;
                $campaign_id = $aAds[0]['id'];
                $mReport = MooCore::getInstance()->getModel('Ads.AdsReport');

                if (!isset($_SESSION['Ads']['View'][$campaign_id])) {
                    $_SESSION['Ads']['View'][$campaign_id] = time();
                    $mReport->clear();
                    $mReport->_updateViewCount($campaign_id, 'view');
                } elseif (time() - $_SESSION['Ads']['View'][$campaign_id] > ADS_SESSION_EXPIRE) {
                    unset($_SESSION['Ads']['View'][$campaign_id]);
                }
				
				//get one random ad
				$random_key = rand(0, count($aAds) - 1);
				$aAd = $aAds[$random_key];
            }
        }
		
		if($aAd != null)
		{
			$page = !empty($e->request->query('page')) ? $e->request->query('page') : 1;
			$event->result['result'] = array(
				'type' => 'FeedAds',
				'id' => "ads_".$page."_".$event->data['index'],
				'objects' => array(
					'type' => 'FeedAds',
					'id' => "ads_".$event->data['index']."_".$aAd['id'],
					'url' => $aAd['link'],
					'images' => array('850' =>  FULL_BASE_URL . Router::getRequest()->webroot.'uploads/commercial/'.$aAd['ads_image']),
				),
				'target' => array(),
				'hideFeedHeader' => true,
			);
		}
	}
}
