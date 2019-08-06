<?php
App::uses('Widget','Controller/Widgets');
class menu_detailBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $tab = getTabFromUrl($controller->request->url);
            $this->setData('tab', $tab);
        }
    }
}