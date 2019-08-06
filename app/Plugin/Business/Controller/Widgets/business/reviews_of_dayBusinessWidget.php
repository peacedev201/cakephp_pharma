<?php
App::uses('Widget','Controller/Widgets');
class reviews_of_dayBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
            $business_reviews = $mBusinessReview->getReviewOfDay();
            $this->setData('business_reviews', $business_reviews);
        }
    }
}