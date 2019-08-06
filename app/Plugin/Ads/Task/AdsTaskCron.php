<?php
App::import('Cron.Task','CronTaskAbstract');
class AdsTaskCron extends CronTaskAbstract
{
	public function __construct( $task,$cron = null)  	
	{  		
		parent::__construct($task,$cron);
        $this->AdsCampaign = MooCore::getInstance()->getModel('Ads.AdsCampaign');
        $this->MooMail = MooCore::getInstance()->getComponent('MooMail');
  	}

    public function execute()
    {
        $this->active_ads();
    	$this->check_expire();
        $this->send_report();
    }
    
    private function active_ads()
    {
        $ads_campaigns = $this->AdsCampaign->getUnactivePaidAds();
        $ads_campaigns_repaid = $this->AdsCampaign->getRePaidAds();
        $ads_campaigns = array_merge($ads_campaigns, $ads_campaigns_repaid);
        if($ads_campaigns != null)
        {
            foreach($ads_campaigns as $ads_campaign)
            {
                $ads_campaign = $ads_campaign['AdsCampaign'];
                if($this->AdsCampaign->activeAdsCampaign($ads_campaign['id']))
                {
                    $coMooMail = MooCore::getInstance()->getComponent('MooMail');
                    $coMooMail->send($ads_campaign['email'], 'ad_activated', array(
                        'link_report' => Router::url('/', true ).'ads/report/'.base64_encode(base64_encode($ads_campaign['id'].'AdsPlugin')).'/'
                    ));
                }
            }
        }
    }

    private function check_expire()
    {
        $this->autoRender = false;
        $ads_campaigns = $this->AdsCampaign->loadExpiredAdsCampaign();
        if($ads_campaigns != null)
        {
            date_default_timezone_set(Configure::read('core.timezone'));
            $cur_date = date('Y-m-d H:i');
            foreach($ads_campaigns as $ads_campaign)
            {
                $ads_placement = $ads_campaign['AdsPlacement'];
                $ads_campaign = $ads_campaign['AdsCampaign'];
                if($this->AdsCampaign->updateAdsCampaignStatus($ads_campaign['id'], ADS_STATUS_DISABLE))
                {
                    //get reason
                    $expire_reason = '';
                    if($ads_campaign['view_count'] >= $ads_placement['view_limit'])
                    {
                        $expire_reason = 'reached views limitation';
                    }
                    else if($ads_campaign['click_count'] >= $ads_placement['click_limit'])
                    {
                        $expire_reason = 'reached clicks limitation';
                    }
                    else
                    {
                        $expire_reason = 'expired';
                    }
                    
                    //send email
                    $this->MooMail->send($ads_campaign['email'], 'user_ads_expired', array( 
                        'expire_reason' => $expire_reason,
                        'link_contact' => Router::url('/', true ).'home/contact',
                        'link_report' => $ads_campaign['moo_linkreport'],
                    ));
                }
            }
        }
    }
    
    private function send_report()
    {
        $this->autoRender = false;
        $ads_campaigns = $this->AdsCampaign->loadAdsCampaignReport();
        if($ads_campaigns != null)
        {
            $interval = Configure::read('Ads.auto_report_will_send');
            if($interval == 'weekly')
            {
                $interval = 'week';
            }
            else if($interval == 'monthly')
            {
                $interval = 'month';
            }

            foreach($ads_campaigns as $ads_campaign)
            {
                $ads_campaign = $ads_campaign['AdsCampaign'];
                $last_report_date = empty($ads_campaign['last_date_report']) ? $ads_campaign['start_date'] : $ads_campaign['last_date_report'];
                $report_from = date('m-d-Y', strtotime($last_report_date));
                $report_to = date('m-d-Y', strtotime($last_report_date.' +1 '.$interval));

                $this->AdsCampaign->updateLastReportDate($ads_campaign['id']);
                
                //send email
                $this->MooMail->send($ads_campaign['email'], 'user_ads_report', array( 
                    'report_from' => $report_from,
                    'report_to' => $report_to,
                    'link_report' => $ads_campaign['moo_linkreport'].$report_from.'/'.$report_to,
                ));
            }
        }
    }
}