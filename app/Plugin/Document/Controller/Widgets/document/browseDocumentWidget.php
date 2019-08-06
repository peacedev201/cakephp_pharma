<?php
App::uses('Widget','Controller/Widgets');

class browseDocumentWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$uid = MooCore::getInstance()->getViewer(true);
		$documentModel = MooCore::getInstance()->getModel('Document.Document');
		$page = 1;
		if (isset($controller->request->params['named']['page']))
			$page = $controller->request->params['named']['page'];
		$params = array_merge($controller->request->params['named'],array('user_id'=>$uid));
		$documents = $documentModel->getDocuments($params); 
		
		$total = $documentModel->getTotalDocuments($params);
		$limit = Configure::read('Document.document_item_per_pages');
		$is_view_more = (($page - 1) * $limit  + count($documents)) < $total;
		
		$type = 'all';
		if (isset($controller->request->params['named']['type']))
			$type = $controller->request->params['named']['type'];
			
		if (isset($controller->request->params['named']['category']))
			$type = 'category/'.$controller->request->params['named']['category'];
		
		$url_more = '/documents/browse/'.$type.'/page:2';
		if ($controller->request->is('androidApp') || $controller->request->is('iosApp'))
		{
			$controller->set('documents', $documents);
			$controller->set('url_more', $url_more);
			$controller->set('type', $type);
			$controller->set('is_view_more', $is_view_more);
			$controller->set('uid', $uid);
		}
		else
		{
			$this->setData('documents', $documents);
			$this->setData('url_more', $url_more);
			$this->setData('is_view_more', $is_view_more);
			$this->setData('uid', $uid);
		}
		
    }
}