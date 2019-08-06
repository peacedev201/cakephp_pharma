<?php
App::uses('Widget','Controller/Widgets');

class myPollWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid,'type'=>'my');
		$polls = $pollModel->getPolls($params);
		$this->setData('polls', $polls);
    }
}