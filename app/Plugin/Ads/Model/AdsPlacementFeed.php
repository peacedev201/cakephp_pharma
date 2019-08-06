<?php
App::uses('AdsAppModel', 'Ads.Model');
class AdsPlacementFeed extends AdsAppModel{
      public $belongsTo = array(
        'AdsPlacement'=> array(
            'className' => 'Ads.AdsPlacement',
            'foreignKey' => 'ads_placement_id',
            'dependent' => true
    ));
    public function deleteByPlacementId($id){
        $is_exist =  $this->hasAny(array(
            'AdsPlacementFeed.ads_placement_id' => $id,
        ));
        if($is_exist){
            $this->deleteAll(array('AdsPlacementFeed.ads_placement_id'=>$id)); 
        }
    }
    
    public function insertPlacementFeed($placement_id,$feeds){
       foreach($feeds as $position){
           $this->clear();
           $this->save(array(
               'ads_placement_id'=>$placement_id,
               'feed_position'=>$position
           ));
       }
    }
    
    public function getAllFeedByPlacementId($placement_id){
        $conds = array('AdsPlacementFeed.ads_placement_id'=>$placement_id);
        return $this->find('all',array('conditions'=>$conds));
    }
    
    public function getPlacementByFeedPosition($position){
        $conds = array(
            'AdsPlacementFeed.feed_position'=>$position,
            'AdsPlacement.enable'=>1,
            'AdsPlacement.placement_type'=>'feed'
        );
        return $this->find('all',array('conditions'=>$conds));
    }
}