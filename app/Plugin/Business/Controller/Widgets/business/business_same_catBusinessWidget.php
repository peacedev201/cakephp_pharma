<?php
App::uses('Widget','Controller/Widgets');
class business_same_catBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            list($business_id, $item_id, $seoname) = getIdFromUrl($controller->request->params['pass'][0]);
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $businesses = $mBusiness->getBusinessSameCategories($business_id);
            $this->setData('businesses', $businesses);
        }
    }
}