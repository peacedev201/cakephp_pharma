<?php
App::uses('Widget','Controller/Widgets');

class tagDocumentWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$document = MooCore::getInstance()->getSubject();
    	$tags = array();
    	if ($document)
    	{
    		$controller->loadModel("Tag");
    		$tags = $controller->Tag->getContentTags($document['Document']['id'], 'Document_Document');    		
    	}
    	$this->setData("tags",$tags);
    }
}