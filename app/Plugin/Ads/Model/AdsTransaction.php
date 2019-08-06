<?php 
App::uses('AdsAppModel', 'Ads.Model');
class AdsTransaction extends AdsAppModel{
    public $mooFields = array('plugin');
    public $belongsTo = array(
        'AdsCampaign'=> array(
            'className' => 'Ads.AdsCampaign',
            'foreignKey' => 'ads_campaign_id',
            'dependent' => true
    ),
        'AdsPlacement'=> array(
            'className' => 'Ads.AdsPlacement',
            'foreignKey' => 'ads_placement_id',
            'dependent' => true
    ));
    
    public function loadAdsTransactionPaging($obj, $keyword = '')
    {
        $cond = array();
        $joins = array();

        if($keyword != '')
        {
            $cond[] = "(AdsTransaction.transaction_id LIKE '%$keyword%' OR AdsCampaign.email LIKE '%$keyword%')";
        }
        $obj->Paginator->settings=array(
            'conditions' => $cond,
            'order' => array('AdsTransaction.id' => 'DESC'),
            'limit' => 10,
        );
        return $obj->paginate('AdsTransaction');
    }
    
    public function checkPaypalConfig()
    {
		$email = Configure::read('Ads.ads_paypal_email');
        if (!empty($email))
        {
            return true;
        }
        return false;
    }

    public function payViaPaypal($data)
    {
        $paypal = MooCore::getInstance()->getComponent('Ads.Paypal');
        $paypal->setConfig();
        $params = array(	
            'item_name' => $data['item_name'],
            'item_number' => $data['item_id'],
            'amount' => $data['amount'],
            'currency_code' => $data['currency_code'],
            'notify_url' => Router::url('/', true ).'ads/notify_transaction',
            'return' => Router::url('/', true ).'ads/success_transaction',
            'cancel_return' => Router::url('/', true ).'ads/cancel_transaction',
            'custom' => $data['item_id'],
        ); 
        return $paypal->getUrl($params);
    }
    
    public function verifyTransaction()
    {
       $paypal = MooCore::getInstance()->getComponent('Ads.Paypal');
        $paypal->setConfig();
        list($status, $params) = $paypal->callback();
        if($status == 'Completed')
        {
            $ads_transaction = $this->findById($params['item_number']);
            if($ads_transaction != null && $ads_transaction['AdsTransaction']['status'] == ADS_TRANSACTION_PENDING)
            {

                $ads_transaction = $ads_transaction['AdsTransaction'];

                App::import('Model', 'Ads.AdsCampaign');
                $mAdsCampaign = new AdsCampaign();

                //update transaction
                $this->id = $ads_transaction['id'];
                $this->save(array(
                    'transaction_id' => !empty($params['txn_id']) ? $params['txn_id'] : '',
                    'status' => ADS_TRANSACTION_COMPLETED,
					'type' =>'paypal'
                ));

                //paid
                $mAdsCampaign->updateAll(array(
                    'AdsCampaign.payment_status' => 1,
                ), array(
                    'AdsCampaign.id' => $ads_transaction['ads_campaign_id']
                ));

                //active ads
                $mAdsCampaign->activeAdsCampaign($ads_transaction['ads_campaign_id']);
            }
        }
        
        
        

    }
    
    public function checkVerificationCodeExist($code)
    {
        return $this->hasAny(array(
            'AdsTransaction.verification_code' => $code,
            'AdsTransaction.status' => ADS_TRANSACTION_PENDING
        ));
    }
    
    public function createVerificationCode($id)
    {
        return md5($id.date('Y-m-d H:i:s'));
    }
    
    public function getTransactionByVerificationCode($code)
    {
        return $this->findByVerificationCode($code);
    }
    
    public function clearOldTransactionVerification($ads_campaign_id, $except_id = null)
    {
        $cond = array(
            'AdsTransaction.ads_campaign_id' => $ads_campaign_id
        );
        if($except_id != null)
        {
            $cond[] = 'AdsTransaction.id != '.$except_id;
        }
        $this->updateAll(array(
            'AdsTransaction.verification_code' => "''"
        ), $cond);
    }
    
    public function checkAdsTransitionExist($ads_campaign_id){
     $conds = array('AdsTransaction.ads_campaign_id'=>$ads_campaign_id,'AdsTransaction.verification_code <>'=>'');
     return $this->hasAny($conds);
    }
    
    public function getVerificationCode($ads_campaign_id){
         $conds = array('AdsTransaction.ads_campaign_id'=>$ads_campaign_id,'AdsTransaction.verification_code <>'=>'');
         $trans= $this->find('first',array('conditions'=>$conds));
         if(!empty($trans)){
             return $trans['AdsTransaction']['verification_code'];
         }
         return false;
         
    }
        public function delete($id = null, $cascade = true){
        $this->deleteAll(array('AdsTransaction.ads_campaign_id'=>$id));
    }
	    public function getWaitingTransactionForActive($ads_campaign_id){
          $conds = array('AdsTransaction.ads_campaign_id'=>$ads_campaign_id,'AdsTransaction.verification_code <>'=>'');
          $trans= $this->find('first',array('conditions'=>$conds));
          if(!empty($trans)){
             return $trans['AdsTransaction'];
         }
         return false;
    }
}