<?php
App::uses('Widget','Controller/Widgets');
class top_categoriesBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $mBusinessCategory = MooCore::getInstance()->getModel('Business.BusinessCategory');

            $business_categories = $mBusinessCategory->getTopCategories();

            $this->setData('business_categories', $business_categories);
        }
    }
}