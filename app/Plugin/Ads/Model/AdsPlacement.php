<?php 
App::uses('AdsAppModel', 'Ads.Model');
class AdsPlacement extends AdsAppModel{
    public $validationDomain = 'ads';
    public $belongsTo = array(
        'AdsPosition'=> array(
            'className' => 'Ads.AdsPosition',
            'foreignKey' => 'ads_position_id',
            'dependent' => true
    ));
    
   public $validate = array(
            'name' => array(
            'rule' => 'notBlank',
            'message' => 'Name is required'
        ),
       'dimension_width' => array(
           'rule1'=>array(
                'rule' => 'notBlank',
               'message' => 'Width is required'
           ),
            'rule2'=>array(
                'rule' => 'numeric',
               'message' => 'Width should be numeric'
           ),
           'rule3'=>array(
               'rule'=>array('checkWidthFeedType'),
               'message'=>'The minimum width for feed type placement is 800px'
           )
        ),
        'dimension_height' => array(
           'rule1'=>array(
                'rule' => 'notBlank',
               'message' => 'Height is required'
           ),
            'rule2'=>array(
                'rule' => 'numeric',
               'message' => 'Height should be numeric'
           )
        ),
        'price' => array(
           'rule1'=>array(
                'rule' => 'notBlank',
               'message' => 'Price is required'
           ),
            'rule2'=>array(
                'rule' => 'numeric',
               'message' => 'Price should be numeric'
           )
        ),
       
       'period'=>array(
           'rule1'=>array(
               'rule'=>'notBlank',
               'message'=>'Day is required'
           ),
           'rule2'=>array(
               'rule'=>array('checkValidateDays'),
               'message'=>'Invalid day'
           )
       ),
        'view_limit' => array(
           'rule1'=>array(
                'rule' => 'notBlank',
               'message' => 'View limit is required'
           ),
            'rule2'=>array(
                'rule' => 'numeric',
               'message' => 'View limit should be numeric'
           )
        ),
        'click_limit' => array(
           'rule1'=>array(
                'rule' => 'notBlank',
               'message' => 'Click limit  is required'
           ),
            'rule2'=>array(
                'rule' => 'numeric',
               'message' => 'Click limit should be numeric'
           )
        ),
        'total_ads' => array(
           'rule1'=>array(
                'rule' => 'notBlank',
               'message' => 'Ads apprears is required'
           ),
            'rule2'=>array(
                'rule' => 'numeric',
               'message' => 'Ads apprears should be numeric'
           )
        ),
       'number_of_ads' => array(
           'rule1'=>array(
                'rule' => 'notBlank',
               'message' => 'Number of ads  is required'
           ),
            'rule2'=>array(
                'rule' => 'numeric',
               'message' => 'Number of ads should be numeric'
           ),
           'rule3'=>array(
               'rule'=>array('compareAds','total_ads'),
               'message'=>'Number of ads must smaller than Ads apprears'
           )
        )

    );
   public function checkValidateDays($data){
       if(is_numeric($data['period'])){
           if($data['period'] > 0){
               return true;
           }else{
               return false;
           }
       }
       return false;
   }
   public function compareAds($data,$total_ads)
   {
       $value = array_values($data);
        $comparewithvalue = $value[0];
        return ($this->data[$this->name][$total_ads] >= $comparewithvalue);
    }
    public function checkWidthFeedType($data){
        $placement_type = $this->data[$this->name]['placement_type'];
        if($placement_type == 'feed'){
            if($data['dimension_width'] < 800){
                return false;
            }
        }
        return true;
    }
    public function getAdsPlacementList($all = false)
    {
        $mAdsPlacement = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        $data = $mAdsPlacement->find('list', array(
            'conditions' => array(
                'AdsPlacement.enable' => 1
            ),
            'fields' => array('AdsPlacement.name')
        ));
        if($data != null && !$all)
        {
            foreach($data as $id => $item)
            {
                if($this->checkPlacementReachLimit($id))
                {
                    unset($data[$id]);
                }
            }
            array_values($data);
        }
        return $data;
    }
    public function loadAdsPlacementList()
    {
        $mAdsPlacement = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        $data = $mAdsPlacement->find('list', array(
            'conditions' => array(
                'AdsPlacement.enable' => 1,
                'AdsPlacement.placement_type != '=>'feed'
            ),
            'fields' => array('AdsPlacement.name')
        ));
        return $data;
    }
    
    
    public function checkPlacementExist($id, $enable = null)
    {
        $conds = array(
            'AdsPlacement.id' => $id
        );
        if(is_bool($enable))
        {
            $conds['AdsPlacement.enable'] = $enable;
        }
        return $this->hasAny($conds);
    }
    
    public function getAdsPlacementDetail($id, $enable = null)
    {
        $conds = array(
            'AdsPlacement.id' => $id
        );
        if(is_bool($enable))
        {
            $conds['AdsPlacement.enable'] = $enable;
        }
        return $this->find('first', array(
            'conditions' => $conds
        ));
    }
    
    public function getAdsPlacements($enable = null)
    {
        $conds = array();
        if(is_bool($enable))
        {
            $conds['AdsPlacement.enable'] = $enable;
        }
        return $this->find('all', array(
            'conditions' => $conds
        ));
    }
    
    public function getAllAdsPlacements()
    {
        return $this->find('all');
    }
    
    public function update_status($key,$id) 
    {
        $this->save(array(
            'id'=>$id,
            'enable'=>$key
        ));
    }
    
    public function delete_ads_place($ads)
    {
         $mAdsCampaign = MooCore::getInstance()->getModel('Ads.AdsCampaign');
         $tmp = 0;
         foreach($ads as $ads_placement_id){
             $cond = array('AdsCampaign.ads_placement_id'=>$ads_placement_id);
             $check = $mAdsCampaign->hasAny($cond);
             if(!$check) {
                 $this->delete($ads_placement_id);
             }else{
                 $tmp+=1;
             }
         }
         if(!empty($tmp)){
             if($tmp > 1){
                 $_SESSION['Ads']['mss']['placement'] = __d('ads','some placements cannot  delete' );
             }else{
                 $_SESSION['Ads']['mss']['placement'] = __d('ads','Cannot  delete this placement');
             }
         }
    }
    
    public function getAllPlacementPosition() {
        $placementPositions = MooCore::getInstance()->getModel('Ads.AdsPosition');
        return $placementPositions->find('all');
    }
    
    public function getAdsPlacementById($id = null)
    {
        $this->bindModel(array(
            'hasMany'=>array(
                'AdsPlacementFeed'=>array(
                    'className'=>'AdsPlacementFeed',
                    'foreignKey' => 'ads_placement_id'
                )
            )
        ));
        $this->recursive = 1;
       return $this->findById($id);
    }
   
    public function updateValueWidget()
    {
        $coreBlockModel = MooCore::getInstance()->getModel('CoreBlock');
        $adsPlacementList = $this->loadAdsPlacementList();

        $advertisement_widget = $coreBlockModel->find('first', array('conditions' => array('CoreBlock.name' => ADS_WIDGET, 'CoreBlock.plugin' => 'Ads')));
        if ($advertisement_widget) {
            $params = $advertisement_widget['CoreBlock']['params'];
            $params = json_decode($params, true);
            $params[1]['value'] = $adsPlacementList;
            $params = json_encode($params);
            $coreBlockModel->id = $advertisement_widget['CoreBlock']['id'];
            $coreBlockModel->saveField('params', $params);
        }
    }
       public function afterDelete() {
           $this->updateValueWidget();
       }
    
    public function loadAdsCampaignsByPlacement($id=null,$placement=null) {
        $campaignModel = MooCore::getInstance()->getModel('Ads.AdsCampaign');
 
        $conds = array(
            'AdsCampaign.ads_placement_id'=>$id,
            'AdsCampaign.item_status' => 'active',
            '(AdsCampaign.view_count < '.$placement['view_limit'].' OR AdsPlacement.view_limit = 0)',
            '(AdsCampaign.click_count < '.$placement['click_limit'].' OR AdsPlacement.click_limit = 0)',
            'AdsCampaign.is_hide'=>0
        );

        $orders = array('AdsCampaign.view_count ASC');
        $adsCampaigns = $campaignModel->find('all', array('conditions' => $conds,'order'=>$orders));
        $newCampaigns = array();
        if ($adsCampaigns) {
            foreach ($adsCampaigns as $campaign) {
                $checkConditionCampaign = $this->_checkConditionCampaign($campaign);
                if ($checkConditionCampaign == true) {
                    $newCampaigns[] = $campaign;
                }
            }
        }
        return $newCampaigns;
    }
    public function _checkConditionCampaign($range){
        $user = MooCore::getInstance()->getViewer();
		$user = $user['User'];
        if(!$user){
            $mUser = MooCore::getInstance()->getModel('User');
            $user = $mUser->findById($user['id']);
            if($user){
                $user = $user['User'];
            }else{
                $user = array();
            }
        }
        $time_start = $range['AdsCampaign']['start_date'];
        $time_start = strtotime($time_start);
        $time = time();
        $endDate = strtotime($range['AdsCampaign']['end_date']);
        $curDate = strtotime(date('Y-m-d H:i:s'));
        if($curDate > $endDate){
            return false;
        }
        if($time_start >= $time){
            return false;
        }
        if(!empty($user)){
           $age = date_diff(date_create($user['birthday']), date_create('now'))->y;
        }else{
        
            $age = 0;
        }
        
        if(!empty($range['AdsCampaign']['age_from']) && !empty($user)){
            if($age <$range['AdsCampaign']['age_from'] ){
                return false;
            }
        }
        if(!empty($range['AdsCampaign']['age_to'])&&!empty($user)){
            if($age > $range['AdsCampaign']['age_to'] ){
                return false;
            }
        }
         if(!empty($range['AdsCampaign']['gender']) && !empty($user)){
             if(strtolower($range['AdsCampaign']['gender']) == 'male'){
                 $gender = 'male';
             }else{
                 $gender = 'female';
             }
            if(strtolower($user['gender']) != $gender){
                return false;
            }
        }
        if(!empty($range['AdsCampaign']['role_id'])){
            if(empty($user)){
                $userRole = 3;
            }else{
                 $userRole = $user['role_id'];
            }
            $exist = in_array($userRole, explode(',', $range['AdsCampaign']['role_id']));
            if($userRole != 1&& !$exist){
                return false;
            }
        }
        return true;
    }
    
    
            function calculateAdsEndDate($date, $period) {
        if ($date != '') {
             $date = date('Y-m-d H:i:s', strtotime($date . ' +'.$period.' day'));
        }
        return $date;
    }

     public function loadAdsPlacementPaging($obj, $keyword = '')
    {
        $cond = array();
        $joins = array();

        if($keyword != '')
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond[] = "(AdsPlacement.name LIKE '%$keyword%' OR AdsPlacement.placement_type LIKE '%$keyword%')";
        }
        $obj->Paginator->settings=array(
            'conditions' => $cond,
            'order' => array('AdsPlacement.id' => 'DESC'),
            'limit' => 10,
        );
        return $obj->paginate('AdsPlacement');
    }

    public function checkPlacementReachLimit($id)
    {
        $ads_placement = $this->findById($id);
        if($ads_placement != null)
        {
            $mAdsCampaign = MooCore::getInstance()->getModel('Ads.AdsCampaign');
            $total_ads_campaign = $mAdsCampaign->find('count', array(
                'conditions' => array(
                    'AdsCampaign.ads_placement_id' => $id,
                    'AdsCampaign.item_status' => ADS_STATUS_ACTIVE
                )
            ));
            if($total_ads_campaign < $ads_placement['AdsPlacement']['total_ads'])
            {
                return false;
            }
        }
        return true;
    }
    
    // for upgrade plugin
    public function getAllDataPeriod(){
        $data = $this->find('all',array('fields'=>array('AdsPlacement.id,AdsPlacement.period','AdsPlacement.updated')));
        return $data;
    }
    public function insertPeriodColum($id,$num_date){
        $this->create();
        $this->id = $id;
        $this->saveField(
            'period',$num_date
        );
    }
   public function getAllActiveFeedPlacements(){
        $conds = array(
            'AdsPlacement.placement_type'=>'feed',
            'AdsPlacement.enable'=>1
        );
        return $this->find('all',array('conditions'=>$conds));
    } 

}