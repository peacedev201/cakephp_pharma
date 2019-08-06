<?php
App::uses('Widget','Controller/Widgets');

class profileDocumentWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$subject = MooCore::getInstance()->getSubject();
		if (!$subject || !isset($subject['User']))
		{
			$this->setData('documents', array());
			return;
		} 
		$documentModel = MooCore::getInstance()->getModel('Document.Document');
		$helper = MooCore::getInstance()->getHelper('Document_Document');	
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid,'owner_id'=>$subject['User']['id'],'type'=>'my');
		$documents = $documentModel->getDocuments($params);
		$this->setData('documents', $documents);
    }
}