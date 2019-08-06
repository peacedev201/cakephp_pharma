<?php
App::uses('Widget','Controller/Widgets');

class blockQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$questionModel = MooCore::getInstance()->getModel('Question.Question');
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid);
		$order_type = $this->params['order_type'];
		
		switch ($order_type) {
			case 'popular':
				$params['interval'] = Configure::read('core.popular_interval');
				$params['order'] = 'Question.view_count desc';
				break;
			case 'feature':
				$params['feature'] = 1;
				$params['order'] = 'Question.view_count desc';
				break;
			default:
				$params['order'] = $order_type;
				break;
		}
		$questions = $questionModel->getQuestions($params);
		$this->setData('questions', $questions);	
    }
}