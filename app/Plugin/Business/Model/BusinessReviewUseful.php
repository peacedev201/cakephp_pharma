<?php
class BusinessReviewUseful extends BusinessAppModel 
{
    public function isSetUseful($review_id)
    {
        $cond = array(
            'BusinessReviewUseful.business_review_id' => $review_id,
            'BusinessReviewUseful.user_id' => MooCore::getInstance()->getViewer(true)
        );
        return $this->hasAny($cond);
    }
    
    public function deleteUseful($review_id)
    {
        $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
        if($this->deleteAll(array(
            'BusinessReviewUseful.user_id' => MooCore::getInstance()->getViewer(true),
            'BusinessReviewUseful.business_review_id' => $review_id
        )))
        {
            return $mBusinessReview->updateUsefulCounter($review_id);
        }
        return 0;
    }
    
    public function addUseful($review_id)
    {
        $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
        if($this->save(array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'business_review_id' => $review_id
        )))
        {
            return $mBusinessReview->updateUsefulCounter($review_id);
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
                'BusinessReviewUseful.business_review_id' => $review_id
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
                'BusinessReviewUseful.business_review_id' => $review_id
            )
        ));
    }
}