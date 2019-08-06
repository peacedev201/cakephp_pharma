<?php
App::uses('Widget','Controller/Widgets');

class profileQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$subject = MooCore::getInstance()->getSubject();
		if (!$subject || !isset($subject['User']))
		{
			$this->setData('questions', array());
			return;
		} 
		$questionModel = MooCore::getInstance()->getModel('Question.Question');
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid,'owner_id'=>$subject['User']['id'],'type'=>'my');
		$questions = $questionModel->getQuestions($params);
		$this->setData('questions', $questions);
    }
}