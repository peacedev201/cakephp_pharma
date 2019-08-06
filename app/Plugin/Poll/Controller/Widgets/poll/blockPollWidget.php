<?php
App::uses('Widget','Controller/Widgets');

class blockPollWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid);
		$order_type = $this->params['order_type'];
		if ($order_type == 'popular')
		{
			$params['interval'] = Configure::read('core.popular_interval');
			$params['order'] = 'Poll.like_count desc';
		}
		elseif ($order_type == 'feature')
		{
			$params['feature'] = 1;
			$params['order'] = 'Poll.like_count desc';
		}
		else
		{
			$params['order'] = $order_type;
		}
		$polls = $pollModel->getPolls($params);
		$this->setData('polls', $polls);	
    }
}