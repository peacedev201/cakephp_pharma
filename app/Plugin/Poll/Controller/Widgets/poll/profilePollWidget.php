<?php
App::uses('Widget','Controller/Widgets');

class profilePollWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$subject = MooCore::getInstance()->getSubject();
		if (!$subject || !isset($subject['User']))
		{
			$this->setData('polls', array());
			return;
		} 
		$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
		$helper = MooCore::getInstance()->getHelper('Poll_Poll');	
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid,'owner_id'=>$subject['User']['id'],'type'=>'my');
		$polls = $pollModel->getPolls($params);
		$this->setData('polls', $polls);
    }
}