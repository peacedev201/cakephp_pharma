<?php
class AdsTransactionController extends AdsAppController {
    public $components = array('Paginator');
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->url = $this->request->base.'/ads/ads_transaction/';
        $this->admin_url = $this->request->base.'/admin/ads/ads_transaction/';
        $this->set('url', $this->url);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Ads.AdsCampaign');
        $this->loadModel('Ads.AdsPlacement');
        $this->loadModel('Ads.AdsTransaction');
    }
    
    ////////////////////////////////////////////backend////////////////////////////////////////////
    public function admin_index()
    {
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $ads_transactions = $this->AdsTransaction->loadAdsTransactionPaging($this, $keyword);

        $this->set(array(
            'ads_transactions' => $ads_transactions,
            'keyword' => $keyword,
            'title_for_layout' => __d('ads', 'Ad Transactions')
        ));
    }
}
