<?php
App::uses('Widget','Controller/Widgets');
class featuredBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        $task = !empty($controller->request->params['task']) ? $controller->request->params['task'] : '';
        if(Configure::read('Business.business_enabled') && $task == '')
        {
            $num_item_show = Configure::read('Business.business_number_of_featured_items');
            $controller->loadModel('Business.Business');
            $featured_businesses = $controller->Business->getFeaturedBusinesses($num_item_show);
            if ($controller->request->is('androidApp') || $controller->request->is('iosApp'))
            {
                $controller->set('featured_businesses', $featured_businesses);
            }
            else
            {
                $this->setData('featured_businesses', $featured_businesses);
            }
        }
    }
}