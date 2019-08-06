<?php
App::uses('Widget','Controller/Widgets');

class featureDocumentWidget extends Widget {
    public function beforeRender(Controller $controller) {
		$uid = MooCore::getInstance()->getViewer(true);
		$documentModel = MooCore::getInstance()->getModel('Document.Document');	
		$params = array('user_id'=>$uid,'feature'=>1,'limit'=>$this->params['num_item_show']);
		$documents = $documentModel->getDocuments($params);
		$this->setData('documents', $documents);
    }
}