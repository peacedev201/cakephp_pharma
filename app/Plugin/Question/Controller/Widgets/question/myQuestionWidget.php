<?php
App::uses('Widget','Controller/Widgets');

class myQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$questionModel = MooCore::getInstance()->getModel('Question.Question');
		$uid = MooCore::getInstance()->getViewer(true);
		$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid,'type'=>'my');
		$questions = $questionModel->getQuestions($params);
		$this->setData('questions', $questions);
    }
}