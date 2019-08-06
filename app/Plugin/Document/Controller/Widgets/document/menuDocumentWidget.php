<?php
App::uses('Widget','Controller/Widgets');

class menuDocumentWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$documentModel = MooCore::getInstance()->getModel('Document.Document');
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
		$categories = $documentModel->getCateogries(array('user_id'=>$uid));
		$this->setData('categories', $categories);
		$this->setData('type', $type);
		$this->setData('params', $params);
		$this->setData('uid', $uid);
    }
}