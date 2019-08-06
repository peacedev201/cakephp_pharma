<?php
App::uses('Widget','Controller/Widgets');

class browse_tagsQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {    	
    	$controller->loadModel("Question.QuestionTag");
		$tags = $controller->QuestionTag->find("all",array(
			'conditions' => array('QuestionTag.status'=>1),
			'order' => array('QuestionTag.title')
		));
		
		$groups = array();
		foreach ($tags as $tag)
		{
			$groups[$tag['QuestionTag']['title'][0]][] = $tag;
		}
		
		if ($controller->request->is('androidApp') || $controller->request->is('iosApp'))
		{
			$controller->set("groups", $groups);
		}
		else
		{
			$this->setData("groups",$groups);
		}
    }
}