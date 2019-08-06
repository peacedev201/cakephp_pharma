<?php
App::uses('Widget','Controller/Widgets');

class profile_infoQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$subject = MooCore::getInstance()->getSubject();
    	$type = MooCore::getInstance()->getSubjectType();
		if (!$subject || $type != 'User')
		{
			$this->setData('question_user', null);
			return;
		} 
		$questionUserModel = MooCore::getInstance()->getModel('Question.QuestionUser');
		$question_user = $questionUserModel->getUser($subject['User']['id']);
		$this->setData('question_user', $question_user);
    }
}