<?php

App::uses('AppHelper', 'View/Helper');

class AdsHelper extends AppHelper {

    function formatMoney($money, $period = null) {
        $money = Configure::read('Ads.currency_symbol') . $money;
        if ($period != null) {
            $day = '';
            if ($period < 2) {
                $day = __d('ads', 'day');
            } else {
                $day = __d('ads', 'days');
            }
            $money .= '/' . $period . ' ' . $day;
        }
        return $money;
    }

    function calculateAdsEndDate($date, $period, $format = 'Y-m-d H:i') {
        if ($date != '') {
            $date = date($format, strtotime($date . ' +' . $period . ' day'));
        }
        return $date;
    }

    public function onSuccessful($item, $data = array(), $price = 0, $recurring = false, $admin = 0) {
        if (!empty($item['AdsTransaction'])) {
            $ads_transaction = $item['AdsTransaction'];
            $ads_campaign = $item['AdsCampaign'];
            $mAdsTransaction = MooCore::getInstance()->getModel('Ads.AdsTransaction');
            App::import('Model', 'Ads.AdsCampaign');
            ;
            $mAdsCampaign = new AdsCampaign();

            //update transaction
            $mAdsTransaction->id = $ads_transaction['id'];
            $mAdsTransaction->save(array(
                'transaction_id' => (isset($item['transaction_id']) && !empty($item['transaction_id']))?$item['transaction_id']:$ads_transaction['id'],
                'status' => ADS_TRANSACTION_COMPLETED,
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

    public function getGmtByTimezone($timezone) {
        if ($timezone != null) {
            return $this->getStandardOffsetUTC($timezone);
        }
        return __d('ads', 'System Time');
    }

    public function getStandardOffsetUTC($timezone) {
        if ($timezone == 'UTC') {
            return 'GMT';
        } else {
            $timezone = new DateTimeZone($timezone);
            $transitions = array_slice($timezone->getTransitions(), -3, null, true);

            foreach (array_reverse($transitions, true) as $transition) {
                if ($transition['isdst'] == 1) {
                    continue;
                }

                return sprintf('GMT%+3d', $transition['offset'] / 3600);
            }

            return false;
        }
    }
    
    public  function getParamsPayment($item){
        $adsTransaction = $item['AdsTransaction'];
        $url = Router::url('/', true);
        $first_amount = 0;
        $currency = Configure::read('Config.currency');
        $params = array(
            'cancel_url' => $url . 'ads/cancel',
            'return_url' => $url . 'ads/success',
            'currency' => $currency['Currency']['currency_code'],
            'description' => __d('ads',"Payment ads"),
            'type' => 'Ads_Ads_Transaction',
            'id' => $adsTransaction['id'],
            'amount' => $adsTransaction['price'],
        );

        return $params;
    }
   public function onFailure($item, $data){
        $adsModal = MooCore::getInstance()->getModel('Ads.AdsTransaction');

        $data = array(
            'status' => 'failed'
        );
        $adsModal->id = $item['AdsTransaction']['id'];
        $adsModal->save($data);
   }


}
