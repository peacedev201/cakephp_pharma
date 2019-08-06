<?php 
class AdsPlacementController extends AdsAppController{
    public $components = array('Paginator');
    public function admin_index() 
    {
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $ads_placements = $this->AdsPlacement->loadAdsPlacementPaging($this, $keyword);
        $this->set('ads_placements',$ads_placements);
          $this->set('keyword',$keyword);
        $this->set('title_for_layout', __d('ads', 'Ad Placements'));
        
    }
    public function index()
    {
       
    }
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->url = $this->request->base.'/ads/';
        $this->admin_url = $this->request->base.'/admin/ads/';
        $this->set('url', $this->url);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Ads.AdsPlacement');
        $this->loadModel('Ads.AdsCampaign');
        $this->loadModel('Ads.AdsPlacementFeed');
    }
    
    public function load_placement_detail()
    {
        $adsHelper = MooCore::getInstance()->getHelper('Ads_Ads');
        $ads_placement = $this->AdsPlacement->getAdsPlacementDetail($this->request->data['ads_placement_id']);
        $data = null;
        if(!empty($ads_placement['AdsPlacement']))
        {
            $ads_placement = $ads_placement['AdsPlacement'];
            $price = $adsHelper->formatMoney($ads_placement['price'], $ads_placement['period']);
            $view_limit_text = empty($ads_placement['view_limit']) || $ads_placement['view_limit'] == 0 ? __d('ads', 'Unlimited') : $ads_placement['view_limit'];
            $click_limit_text = empty($ads_placement['click_limit']) || $ads_placement['click_limit'] == 0 ? __d('ads', 'Unlimited') : $ads_placement['click_limit'];
            $info = '('.$view_limit_text.' '.__d('ads','views').', '.$click_limit_text.' '.__d('ads','clicks').', '.$price.' )';
            $data = array(
                'info' => $info,
                'placement' => $ads_placement,
                'required_size' => __d('ads', "required size").' '.$ads_placement['dimension_width'].'x'.$ads_placement['dimension_height'].', '.__d('ads', "allow extensions").': jpg, jpeg, png, gif'
            );
        }
        echo json_encode($data);
        exit;
    }
    
    public function load_placement_info()
    {
        $ads_placements = $this->AdsPlacement->getAdsPlacements(true);
        $this->set(array(
            'ads_placements' => $ads_placements
        ));
        $this->render('Elements/AdsPlacement/placement_info');
    }
    
    public function load_placement_info_detail()
    {
        $ads_placement = $this->AdsPlacement->getAdsPlacementDetail($this->request->data['id'], true);
        $this->set(array(
            'ads_placement' => $ads_placement
        ));
        $this->render('Elements/AdsPlacement/placement_info_detail');
    }
    
    public function admin_change_status($key=null,$id=null)
    {
        $this->autoRender = false;
        if(!empty($id)) {
            $id = explode('_',$id);
            $id = $id[1];
            $this->AdsPlacement->update_status($key,$id);
            $response['result'] = 1;
            echo json_encode($response);
            $this->Session->setFlash(__d('ads','Status have been updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }
    }
    
    public function admin_delete()
    {
        $this->autoRender = false;
        if(isset($this->request->data['ads_place'])){
            $data = $this->request->data['ads_place'];
            $this->AdsPlacement->delete_ads_place($data);
            if(!isset($_SESSION['Ads']['mss']['placement'])){
                 $this->Session->setFlash(__d('ads','Ads have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
               
            }else{
                 $this->Session->setFlash($_SESSION['Ads']['mss']['placement'], 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
                 unset($_SESSION['Ads']['mss']['placement']);        
            }
           
        }
         $this->redirect($this->referer());
    }
    public function admin_create($id = null)
    {
      
       $positions = $this->AdsPlacement->getAllPlacementPosition();
       $options_type = array('html'=>__d('ads','Html'),'image'=>__d('ads','Image'),'feed'=>__d('ads','Feed'));
       $options_status = array('1'=>__d('ads','Enable'),'0'=>__d('ads','Disable'));
       //$options_time = array('week'=>'per week','month'=>'per month','year'=>'per year');
       $feed_position = array();
       if(!empty($id)){
           $placement = $this->AdsPlacement->getAdsPlacementById($id);
           if(isset($placement['AdsPlacementFeed']) && !empty($placement['AdsPlacementFeed'])){
               foreach($placement['AdsPlacementFeed'] as $feed){
                   $feed_position[] = $feed['feed_position'];
               }
           }
           
           $this->_checkExistence($placement);
       }else {
          
            $placement = $this->AdsPlacement->initFields();
            $placement['AdsPlacement']['ads_position_id'] = 1;
       }
       
       $this->set(array(
           'positions'=>$positions,
           'options_type'=>$options_type,
           'placement'=>$placement,
           'options_status'=>$options_status,
            'title_for_layout' => __d('ads', 'Ad Placements') ,
           'feed_position'=>$feed_position
           //'options_time'=>$options_time
       ));
      
    }
    
    public function admin_save() {
        $this->autoRender = false;
        if ($this->request->is(array('post', 'put'))) {
            $data = $this->request->data;
            $data['created'] = $data['updated'] = date("Y-m-d H:i:s");
            $ads_placement_id = '';
            if (!empty($data['id'])) {
                $this->AdsPlacement->id = $data['id'];
                $data['updated'] = date("Y-m-d H:i:s");
                $this->AdsPlacementFeed->deleteByPlacementId($data['id']);
                $ads_placement_id = $data['id'];
            }
            $feed_position = '';
            if (isset($data['feed_position']) && $data['placement_type'] == 'feed') {
                $feed_position = $data['feed_position'];
                if (!is_array($feed_position)) {
                    $feed_position = array($feed_position);
                }
                unset($data['feed_position']);
            }
            
            $user = AuthComponent::user();
            $data['user_id'] = $user['id'];
            $this->AdsPlacement->set($data);
            $this->_validateData($this->AdsPlacement);
            $this->AdsPlacement->save();
            if(!$ads_placement_id){
                $ads_placement_id =  $this->AdsPlacement->id;
            }
            if ($feed_position) {
               
                $this->AdsPlacementFeed->insertPlacementFeed($ads_placement_id, $feed_position);
            }
            $this->AdsPlacement->updateValueWidget();
            $this->Session->setFlash(__d('ads', 'Successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            $response['result'] = 1;
            echo json_encode($response);
            exit;
        }
    }

    public function loadAjaxCampaign() // load campaign by ajax widget
    {  
        $this->autoRender = false;
        if($this->request->is('post')){
            $view = new View($this,false);
            $view->layout=false;
            $data = $this->request->data;
            $this->loadModel('Ads.AdsCampaign');
            $this->loadModel('Ads.AdsReport');
            // count view
            $campaign = $this->AdsCampaign->getAdsCampaignDetail($data['id']);

            if ($campaign) {
                // update view count
                if ($campaign['AdsCampaign']['view_count'] < $campaign['AdsPlacement']['view_limit'] &&
                    $campaign['AdsCampaign']['click_count'] < $campaign['AdsPlacement']['click_limit']) {
                    $check = $this->AdsPlacement->_checkConditionCampaign($campaign);
                    $campaign_id = $campaign['AdsCampaign']['id'];
                    if ($check && $campaign['AdsCampaign']['item_status'] == 'active') {
                        if (!isset($_SESSION['Ads']['View'][$campaign_id])) {
                            $_SESSION['Ads']['View'][$campaign_id] = time();
                            $this->AdsReport->clear();
                            $this->AdsReport->_updateViewCount($campaign_id, 'view');
                        } elseif (time() - $_SESSION['Ads']['View'][$campaign_id] > ADS_SESSION_EXPIRE) {
                            unset($_SESSION['Ads']['View'][$campaign_id]);
                        }
                    }
                }
                $campaign = $campaign;
                $campaign['result'] = 1;
                $view->set('campaign', $campaign);
                $view->set('title_enable', $data['title_enable']);
                $view->set('ads_type', $data['ads_type']);
                $html = $view->render('Ads.Elements/Widget/ads_campaign');
                return $html;
            } else {
                return false;
            }
                
        }
        
    }
    
    
    

}