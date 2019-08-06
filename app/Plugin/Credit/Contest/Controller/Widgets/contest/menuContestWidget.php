<?php

App::uses('Widget', 'Controller/Widgets');

class menuContestWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $categoryModel = $controller->loadModel('Category');
        $menus = $controller->request->params['named'];
        $params = 0;
        
        if (isset($menus['type'])) {
            $type = $menus['type'];
        }else{
            $type = 'active';
        }
        if (isset($menus['category'])) {
            $params = $menus['category'];
        }
        $uid = MooCore::getInstance()->getViewer(true);
        $categories = $controller->Category->getCategoriesList('Contest');
        $this->setData('categories', $categories);
        $this->setData('type', $type);
        $this->setData('params', $params);
        $this->setData('uid', $uid);
        
    }

}
