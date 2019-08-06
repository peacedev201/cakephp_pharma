<?php
App::uses('AdsAppModel', 'Ads.Model');
class AdsReport extends AdsAppModel{
    public function loadReport($id, $from_date, $to_date)
    {
        $data = $this->find('all', array(
            'conditions' => array(
                'ads_campaign_id' => $id,
                "DATE(created) >= '$from_date' AND DATE(created) <= '$to_date'"
            ),
            'fields' => array('id', 'type', 'SUM(male) AS male', 'SUM(famale) AS famale', 'SUM(`under_20`) AS under_20', 'SUM(`20_to_50`) AS 20_to_50', 'SUM(`above_50`) AS above_50', 'GROUP_CONCAT(`role_id`) AS role_id'),
            'group' => 'type'
        ));
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                $role_id = explode(',', $item[0]['role_id']);
                $data[$k][0]['role_id'] = array_count_values($role_id);
            }
        }
        return $data;
    }
    
    public function _updateViewCount($id,$type) {
        $campaignModel = MooCore::getInstance()->getModel('Ads.AdsCampaign');
        $condReport = array();
        if ($type == 'view') {
            $campaignModel->updateAll(
                    array('AdsCampaign.view_count' => 'AdsCampaign.view_count + 1'), array('AdsCampaign.id' => $id));
        }else{
              $campaignModel->updateAll(array('AdsCampaign.click_count' => 'AdsCampaign.click_count + 1'), array('AdsCampaign.id' => $id)
            );
        }
        $checkIsset = AuthComponent::user();
        if (isset($checkIsset)) {
            $user = AuthComponent::user();
            if (strtolower($user['gender']) == 'male') {
                $condReport['male'] = '1';
            } else if (strtolower($user['gender']) == 'female') {
                $condReport['famale'] = 1;
            }
            $condReport['role_id'] = $user['role_id'];
            $age = date_diff(date_create($user['birthday']), date_create('now'))->y;
            if ($age < 20) {
                $condReport['under_20'] = 1;
            } else if ($age <= 50) {
                $condReport['20_to_50'] = 1;
            } else {
                $condReport['above_50'] = 1;
            }
            $condReport['role_id'] = $user['role_id'];
        }else{
            $condReport['role_id'] = 3;
        }
        $condReport['type'] = $type;
        $condReport['ads_campaign_id'] = $id;
        $this->save($condReport);
        
    }
    public function delete($id = null, $cascade = true){
        $this->deleteAll(array('AdsReport.ads_campaign_id'=>$id));
    }

}