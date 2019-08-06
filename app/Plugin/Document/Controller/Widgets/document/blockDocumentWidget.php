<?php
App::uses('Widget','Controller/Widgets');

class blockDocumentWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$documentModel = MooCore::getInstance()->getModel('Document.Document');
		$helper = MooCore::getInstance()->getHelper('Document_Document');	
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid);
		$order_type = $this->params['order_type'];
		if ($order_type == 'feature')
		{
			$params['feature'] = true;
		}	
		elseif ($order_type == 'popular')
		{
			$params['interval'] = Configure::read('core.popular_interval');
			$params['order'] = 'Document.like_count desc';
		}
		else
		{
			$params['order'] = $order_type;
		}
		$documents = $documentModel->getDocuments($params);
		$this->setData('documents', $documents);	
    }
}