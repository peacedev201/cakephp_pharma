<?php
App::uses('Widget','Controller/Widgets');
class people_checkinBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $mBusinessCheckin = MooCore::getInstance()->getModel('Business.BusinessCheckin');
            list($business_id) = getIdFromUrl($controller->request->params['pass'][0]);
            
            $mBusiness->updateCheckinCounter($business_id);

            //people list
            $users = $mBusinessCheckin->getPeopleCheckin($business_id, 1, 8);

            //business
            $business = $mBusiness->getOnlyBusiness($business_id);

            $this->setData('users', $users);
            $this->setData('business', $business);
        }
    }
}