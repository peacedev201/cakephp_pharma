<?php
App::uses('Widget','Controller/Widgets');
class popular_locationsBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
            $business_locations = $mBusinessLocation->getPopularLocations();

            $this->setData('business_locations', $business_locations);
        }
    }
}