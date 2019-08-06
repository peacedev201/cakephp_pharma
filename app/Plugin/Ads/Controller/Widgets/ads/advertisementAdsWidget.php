<?php
App::uses('Widget','Controller/Widgets');

class advertisementAdsWidget extends Widget {
    public function beforeRender(Controller $controller) {
      
        
        $is_enabled = Configure::read('Ads.ads_enabled');
        $is_mobile = MooCore::getInstance()->isMobile(null);
//        return false;
        $controller->loadModel('Ads.AdsPlacement');
        $controller->loadModel('Ads.AdsCampaign');
        $controller->loadModel('Ads.AdsReport');
        $controller->loadModel('Ads.AdsPlacementFeed');
        $role_can_add_ads = $controller->AdsCampaign->adsCheckUserRoles(ROLE_ADS_CAN_ADD_ADS);
        $role_hide_all_ads= $controller->AdsCampaign->adsCheckUserRoles(ROLE_ADS_HIDE_ALL_ADS);
        $viewer = MooCore::getInstance()->getViewer();
        /*if($viewer['Role']['is_admin']){
            $role_hide_all_ads = false;
        }*/
        $data = $this->params;
        $countCampaigns = 0;
        $listCampaignsId = array();
        $flagAjax = false;
        $newCampaigns = array();
        $time_interval = 10; // second;
        $current_appear_ads = 0;
        $adsPlacement = $controller->AdsPlacement->getAdsPlacementById($data['placement']);
        
        if ($adsPlacement && !$is_mobile && $is_enabled) {
            $adsCampaigns = $controller->AdsPlacement->loadAdsCampaignsByPlacement($data['placement'], $adsPlacement['AdsPlacement']);
            $current_appear_ads = $adsPlacement['AdsPlacement']['number_of_ads'];
        } else {
           $adsPlacement= $controller->AdsPlacement->initFields();
            $adsCampaigns = array();
        }
        if($adsCampaigns){
           // count campaigns
            foreach($adsCampaigns as $key=>$campaign) {
                $countCampaigns+=1;
                $listCampaignsId[] = $campaign['AdsCampaign']['id'];
                if($key < $current_appear_ads){
                    $newCampaigns[] = $campaign;
                    // count view
                    $campaign_id = $campaign['AdsCampaign']['id'];
                    if (!isset($_SESSION['Ads']['View'][$campaign_id])) {
                        $_SESSION['Ads']['View'][$campaign_id] = time();
                        $controller->AdsReport->clear();
                        $controller->AdsReport->_updateViewCount($campaign_id, 'view');
                    } elseif (time() - $_SESSION['Ads']['View'][$campaign_id] > ADS_SESSION_EXPIRE) {
                        unset($_SESSION['Ads']['View'][$campaign_id]);
                    }
                }
            }
            if($countCampaigns > $current_appear_ads ){
                $flagAjax = true;
            }            
        }else{
             $adsCampaigns = $controller->AdsCampaign->initFields();
        }
        
       $time_setting = Configure::read('Ads.ad_refresh_time'); 
        if(!empty($time_setting)){
            $time_interval = (int)$time_setting;
        }
        
        //check reach limit
        $reach_limit = false;
        if($controller->AdsPlacement->checkPlacementReachLimit($data['placement']))
        {
            $reach_limit = true;
        }
        $is_see_your_ad_here = false;
        if(!isset($data['see_your_ad_here'])){
            $is_see_your_ad_here = true;
        }else{
           if(is_numeric($data['see_your_ad_here'])){
               $is_see_your_ad_here = true;
           }
        }
        $this->setData('placement',$data['placement']);
        $this->setData('adsCampaigns', $newCampaigns);
        $this->setData('flagAjax',$flagAjax);
        $this->setData('title_enable', $data['title_enable']);
        $this->setData('key', $data['content_id']);
        $this->setData('num_load_start', $current_appear_ads);
        $this->setData('listCampaignsId', json_encode($listCampaignsId));
        $this->setData('time_interval', $time_interval * 1000);
        $this->setData('ads_type',$adsPlacement['AdsPlacement']['placement_type']);
        $this->setData('adsPlacement',$adsPlacement);
        $this->setData('background_block',$data['background_block']);
        $this->setData('title', $data['title']);
        $this->setData('reach_limit', $reach_limit);
        $this->setData("role_ads_can_add_ads",$role_can_add_ads);
        $this->setData("role_ads_hide_all_ads",$role_hide_all_ads);
        $this->setData("show_see_your_ad_here",$is_see_your_ad_here);
        
    }
    

    

}