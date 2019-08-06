<?php
App::uses('Widget','Controller/Widgets');

class tagPollWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$poll = MooCore::getInstance()->getSubject();
    	$tags = array();
    	if ($poll)
    	{
    		$controller->loadModel("Tag");
    		$tags = $controller->Tag->getContentTags($poll['Poll']['id'], 'Poll_Poll');    		
    	}
    	$this->setData("tags",$tags);
    }
}