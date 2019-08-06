<?php
App::uses('Widget','Controller/Widgets');
class breadcrumbBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $controller_name = $controller->request->params['controller'];
            $action_name = $controller->request->params['action'];
            
            $this->setData('controller_name', $controller_name);
            $this->setData('action_name', $action_name);
        }
    }
}