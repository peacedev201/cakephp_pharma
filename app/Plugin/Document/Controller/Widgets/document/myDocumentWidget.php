<?php
App::uses('Widget','Controller/Widgets');

class myDocumentWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$documentModel = MooCore::getInstance()->getModel('Document.Document');
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid,'type'=>'my');
		$documents = $documentModel->getDocuments($params);
		$this->setData('documents', $documents);
    }
}