<?php
App::uses('Widget','Controller/Widgets');

class top_point_usersQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {    	
    	$num_item_show = $this->params['num_item_show'];
    	$userModel = MooCore::getInstance()->getModel("Question.QuestionUser");
    	$conditions = array('QuestionUser.total >'=> 0);
    	$conditions = $userModel->addBlockCondition($conditions);
    	$users = $userModel->find("all",array(
    			'conditions' => $conditions,
    			'limit' => $num_item_show,
    			'order' => array('QuestionUser.total DESC'),
    	));
    	$this->setData("users", $users);
    }
}