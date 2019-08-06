<?php 
App::uses('AdsAppModel', 'Ads.Model');
class AdsCampaign extends AdsAppModel{
    public $validationDomain = 'ads';
    public $mooFields = array('linkreport');
    public $validate = array(   
        'client_name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide client name'
        ),
        'email' =>   array(   
            'rule' => array('email'),
            'message' => 'Invalid email'
        ),
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide campaign name'
        ),	
        'ads_placement_id' =>   array(   
            'rule' => array('checkPlacementExist'),
            'message' => 'Please select placement'
        ),
         'start_date'=>array(
            'rule' => 'notBlank',
            'message' => 'Please provide planned start date'
        ),
        'ads_image' =>   array(   
            'rule' => array('validImageDimension'),
            'message' => 'Please upload banner or banner dimension is not valid'
        ),
        'link' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide banner link'
        )
       
    );
    
    public $belongsTo = array(
        'AdsPlacement'=> array(
            'className' => 'Ads.AdsPlacement',
            'foreignKey' => 'ads_placement_id',
            'dependent' => true
    ));
    
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);

    }
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
            if(isset($this->data['AdsCampaign']['start_date']) && !empty($this->data['AdsCampaign']['start_date']))
            {
                $start_hour = !empty($this->data['AdsCampaign']['start_hour']) ? $this->data['AdsCampaign']['start_hour'] : '00';
                $start_min = !empty($this->data['AdsCampaign']['start_min']) ? $this->data['AdsCampaign']['start_min'] : '00';
                $this->data['AdsCampaign']['set_date'] = date('Y-m-d H:i:s', strtotime($this->data['AdsCampaign']['start_date'].' '.$start_hour.':'.$start_min.':00'));
                if(!empty($this->data['AdsCampaign']['timezone']))
                {
                    $this->data['AdsCampaign']['start_date'] = $this->timeZoneConvert($this->data['AdsCampaign']['set_date'], $this->data['AdsCampaign']['timezone'], 'UTC');
                }
            }
            else if(empty($this->data['AdsCampaign']['id']))
            {
                $this->data['AdsCampaign']['timezone'] = '';

            }
    }
    
    public function timeZoneConvert($time, $fromTimezone, $toTimezone,$format = 'Y-m-d H:i:s') 
    {
       $from = new DateTimeZone($fromTimezone);
       $to = new DateTimeZone($toTimezone);
       $orgTime = new DateTime($time, $from);
       $toTime = new DateTime($orgTime->format("c"));
       $toTime->setTimezone($to);
       return $toTime->format($format);
   }
    
    public function checkPlacementExist($data)
    {
        $mAdsPlacement = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        return $mAdsPlacement->checkPlacementExist($data['ads_placement_id'], true);
    }
    
    public function checkAdsCampaignExist($id, $enable = null)
    {
        $conds = array(
            'AdsCampaign.id' => $id
        );
        if(is_bool($enable))
        {
            $conds['AdsCampaign.enable'] = $enable;
        }
        return $this->hasAny($conds);
    }
    
    public function validImageDimension($data)
    {
        $mAdsPlacement = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        $ads_placement = $mAdsPlacement->getAdsPlacementDetail($this->data['AdsCampaign']['ads_placement_id']);

        //check demension
        if(!empty($data['ads_image']) && file_exists('uploads' . DS . 'commercial'.DS.$data['ads_image']))
        {
            $image_info = getimagesize('uploads' . DS . 'commercial'.DS.$data['ads_image']);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            if(!empty($ads_placement) && $image_width == $ads_placement['AdsPlacement']['dimension_width'] && 
               $image_height == $ads_placement['AdsPlacement']['dimension_height'])
            {
                return true;
            }
        }
        return false;
    }

    public function getListRole()
    {
        $mRole = MooCore::getInstance()->getModel('Role');
        return $mRole->find('list'); 
    }
    
    public function getAdsPlacementList()
    {
        $mAdsPlacement = MooCore::getInstance()->getModel('Ads.AdsPlacement');
        return $mAdsPlacement->find('list', array(
            'conditions' => array(
                'AdsPlacement.enable' => 1
            ),
            'fields' => array('AdsPlacement.name')
        )); 
    }
    
    public function loadAdsCampaignPaging($obj, $keyword = '', $search_status = '', $ads_placement_id = '')
    {
        $cond = array();
        $joins = array();

        if($keyword != '')
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond[] = "(AdsCampaign.name LIKE '%$keyword%' OR AdsCampaign.email LIKE '%$keyword%' OR AdsPlacement.name LIKE '%$keyword%')";
        }
        if($search_status != '')
        {
            $cond['AdsCampaign.item_status'] = $search_status;
        }
        if($ads_placement_id != '')
        {
            $cond['AdsCampaign.ads_placement_id'] = $ads_placement_id;
            $cond['AdsCampaign.item_status'] = ADS_STATUS_ACTIVE;
        }
        $obj->Paginator->settings=array(
            'conditions' => $cond,
            'order' => array('AdsCampaign.id' => 'DESC'),
            'limit' => 10,
        );
        return $obj->paginate('AdsCampaign');
    }
    
    public function getAdsCampaignDetail($id)
    {
        return $this->findById($id);
    }
    
    public function parseAdsData($data)
    {
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                $data[$k]['AdsCampaign']['action'] = $this->getAdsCampaignAction($item['AdsCampaign']['item_status']);
                $data[$k]['AdsCampaign']['is_expired'] = $this->checkAdsExpired($item);
            }
        }
        return $data;
    }
    
    public function getAdsCampaignAction($status)
    {
		$this->action = array(
            'pending' => __d('ads', 'Pending'),
            'active' => __d('ads', 'Active'),
            'disable' => __d('ads', 'Disable'),
            'payment' => __d('ads', 'Send Payment Request'),
            'delete' => __d('ads', 'Delete')
        );
        $action = $this->action;
        switch($status)
        {
            case ADS_STATUS_PENDING:
                unset($action['pending']);
                unset($action['disable']);
                break;
            case ADS_STATUS_ACTIVE:
                unset($action['pending']);
                unset($action['active']);
                unset($action['payment']);
                unset($action['delete']);
                break;
            case ADS_STATUS_DISABLE:
                unset($action['pending']);
                unset($action['disable']);
                unset($action['delete']);
                break;
        }
        return $action;
    }


    public function checkAdsCampaignAllowAction($id, $action_value)
    {
        $ads_campaign = $this->findById($id);
        if($ads_campaign != null)
        {
            $action = $this->getAdsCampaignAction($ads_campaign['AdsCampaign']['item_status']);
        if($action_value == 'delete'){
            if($this->checkAdsExpired($ads_campaign)){
                $action['delete'] = 'delete';
            }
        }
            if(array_key_exists($action_value, $action))
            {
                return true;
            }
        }
        return false;
    }
    
    public function updateAdsCampaignStatus($id, $status)
    {
        $this->id = $id;
        return $this->save(array(
            'item_status' => $status
        ));
    }
    
    public function deleteAdsCampaignStatus($id)
    {
        $this->deleteAdsRelative($id);
        return $this->delete($id);
    }
    
    public function activeAdsCampaign($id, $active_now = false)
    {
        $adsHelper = MooCore::getInstance()->getHelper('Ads_Ads');
        $ads_campaign = $this->getAdsCampaignDetail($id);
        if($ads_campaign != null)
        {
            $ads_placement = $ads_campaign['AdsPlacement'];
            $ads_campaign = $ads_campaign['AdsCampaign'];
            
            $data = array(
                'AdsCampaign.item_status' => "'".ADS_STATUS_ACTIVE."'",
                'AdsCampaign.payment_status' => 1,
                'AdsCampaign.view_count' => '0',
                'AdsCampaign.click_count' => '0',
            );
            if(!empty($ads_campaign['set_date']) && !$active_now && !empty($ads_campaign['timezone']))
            {
                
                $set_end_date = $adsHelper->calculateAdsEndDate($ads_campaign['set_date'], $ads_placement['period']);
                $end_date = $adsHelper->calculateAdsEndDate($ads_campaign['start_date'], $ads_placement['period']);
                $data['AdsCampaign.set_end_date'] = "'".$set_end_date."'";
                $data['AdsCampaign.end_date'] = "'".$end_date."'";
            }
            else if(empty($ads_campaign['set_date']) || $active_now || empty($ads_campaign['timezone']))
            {
                $cur_date = date('Y-m-d H:i:s');
                $end_date = $adsHelper->calculateAdsEndDate($cur_date, $ads_placement['period']);
                $data['AdsCampaign.start_date'] = "'".$cur_date."'";
                $data['AdsCampaign.end_date'] = "'".$end_date."'";
                $data['AdsCampaign.set_date'] = "'".$cur_date."'";
            }

            if($this->updateAll($data, array(
                'AdsCampaign.id' => $id
            )))
            {
                $coMooMail = MooCore::getInstance()->getComponent('MooMail');
                $coMooMail->send($ads_campaign['email'], 'ad_activated', array(
                    'link_report' => Router::url('/', true ).'ads/report/'.base64_encode(base64_encode($ads_campaign['id'].'AdsPlugin')).'/'
                ));
            }
        }
    }
    
    public function loadExpiredAdsCampaign()
    {
        return $this->find('all', array(
            'conditions' => array(
                'AdsCampaign.item_status' => ADS_STATUS_ACTIVE,
                'AdsCampaign.payment_status' => 1,
                '((AdsPlacement.view_limit > 0 AND AdsCampaign.view_count >= AdsPlacement.view_limit) OR (AdsPlacement.click_limit > 0 AND AdsCampaign.click_count >= AdsPlacement.click_limit) OR UNIX_TIMESTAMP(\''.date('Y-m-d H:i:s').'\') > UNIX_TIMESTAMP(AdsCampaign.end_date))'
            )
        ));
    }
    
    public function loadAdsCampaignReport()
    {
        $interval = Configure::read('Ads.auto_report_will_send');
        if($interval == 'weekly')
        {
            $interval = 'WEEK';
        }
        else if($interval == 'monthly')
        {
            $interval = 'MONTH';
        }
        return $this->find('all', array(
            'conditions' => array(
                'AdsCampaign.item_status' => ADS_STATUS_ACTIVE,
                'AdsCampaign.payment_status' => 1,
                'AdsCampaign.view_count <= AdsPlacement.view_limit',
                'AdsCampaign.click_count <= AdsPlacement.click_limit',
                'UNIX_TIMESTAMP(\''.date('Y-m-d H:i:s').'\') <= UNIX_TIMESTAMP(AdsCampaign.end_date)',
                'UNIX_TIMESTAMP(DATE_ADD(IFNULL(AdsCampaign.last_date_report, AdsCampaign.start_date), INTERVAL 1 '.$interval.')) <= UNIX_TIMESTAMP(\''.date('Y-m-d H:i:s').'\')'
            )
        ));
    }
    
    public function getLinkReport($ads_campaign)
    {
        if(!empty($ads_campaign['id']))
        {
            return Router::url('/', true ).'ads/report/'.base64_encode(base64_encode($ads_campaign['id'].'AdsPlugin')).'/';
        }
        return false;
    }
    
    public function updateLastReportDate($id)
    {
        $this->id = $id;
        $this->save(array(
            'last_date_report' => date('Y-m-d H:i:s')
        ));
    }
    
    public function decodeAdCampaignId($code)
    {
        return base64_decode(base64_decode($code));
    }
    public function getAllRole()
    {
        $mRole = MooCore::getInstance()->getModel('Role');
        return $mRole->find('all'); 
    }
    
    public function getUnactivePaidAds()
    {
        return $this->find('all', array(
            'conditions' => array(
                'payment_status' => 1,
                'item_status' => ADS_STATUS_PENDING,
                '(start_date = NULL || UNIX_TIMESTAMP(\''.date('Y-m-d H:i:s').'\') >= UNIX_TIMESTAMP(start_date))'
            )
        ));
    }
    
    public function getRePaidAds()
    {
        return $this->find('all', array(
            'conditions' => array(
                'payment_status' => 1,
                'item_status != \''.ADS_STATUS_ACTIVE.'\'',
                'start_date' => 'NOT NULL',
                'end_date' => 'NOT NULL',
            )
        ));
    }
	
	public function getActivePaidAds()
    {
        return $this->find('all', array(
            'conditions' => array(
				'item_status' => ADS_STATUS_ACTIVE,
                'payment_status' => 1,
				'start_date NOT NULL',
                'end_date NOT NULL'
            )
        ));
    }
    
    public function checkUserExistByEmail($email) {
        $mUser = MooCore::getInstance()->getModel('User');
        $conds = array('User.email' => $email);
        return $mUser->hasAny($conds);
    }

    public function createMooAccount($email, $name, $password) {
        $mUser = MooCore::getInstance()->getModel('User');
        $mUser->create();
        return $mUser->save(array(
                    'email' => $email,
                    'name' => $name,
                    'password' => $password,
                    'gender' =>'Male'
        ));
    }

    public function countTotalAdsCamExist($user_id) {
            return $this->find('count',array('conditions'=>array('AdsCampaign.user_id'=>$user_id)));
    }

    public function loadAdsCampaignDetail($user_id,$offset=0){
        $order = array('AdsCampaign.id DESC');
        $conds = array('AdsCampaign.user_id'=>$user_id);
        
        return $this->find('all',array('conditions'=>$conds,'limit'=>ADS_LIMIT_LOAD_MY_ADS,'offset'=>$offset,'order'=>$order));
    }
    
        public function loadAdsByTitle($user_id,$keywork){
        $order = array('AdsCampaign.id DESC');
        $conds = array('AdsCampaign.user_id'=>$user_id,'AdsCampaign.ads_title LIKE'=>"%$keywork%");
        return $this->find('all',array('conditions'=>$conds,'order'=>$order));
        
    }
    public function checkCampaignPaymentStatus($id){
        $ads = $this->findById($id);
        if($ads['AdsCampaign']['payment_status'] != null){
            return $ads['AdsCampaign']['payment_status'];
        }
        return false;
    }
    
    public function deleteAdsCampaign($id){
           $this->deleteAdsRelative($id);
            return $this->delete($id);
    }
    
    public  function setStatusItemCampaign($id,$status){
           return $this->save(array(
                'id'=>$id,
                'is_hide'=>$status
            ));
    }
    
    public function checkAdsExpired($ad){
        $end_date = strtotime($ad['AdsCampaign']['end_date']);
        $cur_date = time();
        if($cur_date > $end_date){
            return true;
        }
        if($ad['AdsCampaign']['view_count']>=$ad['AdsPlacement']['view_limit']){
            return true;
        }
        if($ad['AdsCampaign']['click_count'] >= $ad['AdsPlacement']['click_limit']){
            return true;
        }
        return false;
    }
    
    public function deleteAdsRelative($id){
        // delete report
        $mReport = MooCore::getInstance()->getModel('Ads.AdsReport');
        $mReport->delete($id);
        // delete transaction
        $mTrans = MooCore::getInstance()->getModel('Ads.AdsTransaction');
        $mTrans->delete($id);
    }
    // for upgrade ads
    
    public function getDataForUpgrade(){
        return $this->find('all',array('fields'=>array('AdsCampaign.id','AdsCampaign.email','AdsCampaign.client_name')));
    }
    
    public function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    
    public function upgradeUserID($ads_id,$user_id){
        $this->create();
        $this->save(array(
            'id'=>$ads_id,
            'user_id'=>$user_id
        ));
    }
    
    public function adsCheckUserRoles($aco) {
        $viewer = MooCore::getInstance()->getViewer();
        /*if (!empty($viewer) && $viewer['Role']['is_admin']) {
            return true;
        }*/
        $params = $this->_getUserRoleParams();
        if (in_array($aco, $params)) {
            return true;
        }
        return false;
    }

    public function _getUser() {
     
        $uid = AuthComponent::user('id');
        $cuser = array();
        if (!empty($uid)) { // logged in users
            $userModal =  MooCore::getInstance()->getModel('User');
            $user = $userModal->findById($uid);

            $cuser = $user['User'];
            $cuser['Role'] = $user['Role'];
        }

        return $cuser;
    }

    public function _getUserRoleParams() {
        $cuser = $this->_getUser();

        if (!empty($cuser)) {
            $params = explode(',', $cuser['Role']['params']);
        } else {
            $params = Cache::read('guest_role');

            if (empty($params)) {
                $this->loadModel('Role');
                $guest_role = $this->Role->findById(ROLE_GUEST);
                $params = explode(',', $guest_role['Role']['params']);
            }
        }
        return $params;
    }

}
