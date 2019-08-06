<?php
App::uses('Widget','Controller/Widgets');
class ratingBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
            list($business_id, $item_id) = getIdFromUrl($controller->request->params['pass'][0]);

            $user_can_create_review = true;
            $mUser = MooCore::getInstance()->getModel('User');
            $cuser = $mUser->findById($controller->Auth->user('id'));
            if ((Configure::read('core.approve_users') && !empty($cuser['User']) && !$cuser['User']['approved']) || 
                (!empty($cuser['User']) && Configure::read('core.email_validation') && !$cuser['User']['confirmed'])) {
                $user_can_create_review = false;
            }
            
            $this->setData('user_can_create_review', $user_can_create_review);
            $this->setData('can_create_review', $mBusiness->permission($business_id, 'can_create_review'));
        }
    }
}