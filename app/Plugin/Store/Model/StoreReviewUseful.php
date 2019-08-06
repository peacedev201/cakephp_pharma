<?php
class StoreReviewUseful extends StoreAppModel 
{
    public function isSetUseful($review_id)
    {
        $cond = array(
            'StoreReviewUseful.store_review_id' => $review_id,
            'StoreReviewUseful.user_id' => MooCore::getInstance()->getViewer(true)
        );
        return $this->hasAny($cond);
    }
    
    public function deleteUseful($review_id)
    {
        $mStoreReview = MooCore::getInstance()->getModel('Store.StoreReview');
        if($this->deleteAll(array(
            'StoreReviewUseful.user_id' => MooCore::getInstance()->getViewer(true),
            'StoreReviewUseful.store_review_id' => $review_id
        )))
        {
            return $mStoreReview->updateUsefulCounter($review_id);
        }
        return 0;
    }
    
    public function addUseful($review_id)
    {
        $mStoreReview = MooCore::getInstance()->getModel('Store.StoreReview');
        if($this->save(array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'store_review_id' => $review_id
        )))
        {
            return $mStoreReview->updateUsefulCounter($review_id);
        }
        return 0;
    }
    
    public function getUserLike($review_id, $page)
    {
        $this->bindModel(array(
            'belongsTo' => array('User')
        ));
        return $this->find('all', array(
            'conditions' => array(
                'StoreReviewUseful.store_review_id' => $review_id
            ),
            'limit' => RESULTS_LIMIT,
            'page' => $page,
            'fields' => array('User.*')
        ));
    }
    
    public function totalUserLike($review_id)
    {
        return $this->find('count', array(
            'conditions' => array(
                'StoreReviewUseful.store_review_id' => $review_id
            )
        ));
    }
}