<?php
App::uses('Widget','Controller/Widgets');

class tagsQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$num_item_show = $this->params['num_item_show'];
    	$tagModel = MooCore::getInstance()->getModel("Question.QuestionTag");
    	$tags = $tagModel->find("all",array(
    		'conditions' => array('QuestionTag.status'=>1),
    		'limit' => $num_item_show,
    		'order' => array('QuestionTag.count DESC'),
    	));
    	$this->setData("tags", $tags);
    }
}