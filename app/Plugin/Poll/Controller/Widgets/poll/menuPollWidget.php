<?php
App::uses('Widget','Controller/Widgets');

class menuPollWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
		$menus = $controller->request->params['named'];
		$type = '';
		$params = 0;
		if (isset($menus['type']))
		{
			$type = $menus['type'];
		}
		if (isset($menus['category']))
		{
			$params = $menus['category'];
		}
		$uid = MooCore::getInstance()->getViewer(true);
		$categories = $pollModel->getCateogries(array('user_id'=>$uid));
		$this->setData('categories', $categories);
		$this->setData('type', $type);
		$this->setData('params', $params);
		$this->setData('uid', $uid);
    }
}