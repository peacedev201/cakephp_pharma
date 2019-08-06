<?php
App::uses('Widget','Controller/Widgets');
class menuBusinessWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Business.business_enabled'))
        {
            $task = !empty($controller->request->params['task']) ? $controller->request->params['task'] : '';
            $this->setData('task', $task);
        }
    }
}