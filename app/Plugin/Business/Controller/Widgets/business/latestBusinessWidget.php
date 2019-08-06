<?php
App::uses('Widget','Controller/Widgets');
class latestBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            list($show_distance, $businesses) = $mBusiness->getBusinessPaging($controller);
            $this->setData('businesses', $businesses);
        }
    }
}