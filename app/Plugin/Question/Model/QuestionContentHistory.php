<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionContentHistory extends QuestionAppModel {
	public $belongsTo = array( 'User' );
	
	public $order = 'QuestionContentHistory.created ASC';
	
	public function getHistoryCount($type,$target_id)
	{
		$cond = array(
			'type' => $type,
			'type_id' => $target_id
		);
		
		$count = $this->find('count', array('conditions' => $cond));
            	
        return $count;
	}
	
	public function getLastHistory($type,$target_id)
	{
		$cond = array(
			'type' => $type,
			'type_id' => $target_id
		);
		
		$history = $this->find('first', array(
			'conditions' => $cond,
	        'order' => array('QuestionContentHistory.id' => 'desc'),
			'limit' => 1
	    ));
	    
	    return $history;
	}
	
	public function getText($type,$target_id)
	{
		$history = $this->getLastHistory($type, $target_id);		
		$text = __d('question','Edited');
		if ($history)
		{			
			$target = $this->getTypeModel($type, $target_id);
			if ($target[key($target)]['user_id'] != $history['QuestionContentHistory']['user_id'])
			{
				$text.= ' '.__('by').' '.$history['User']['name']; 
			}	
		}
				
		return $text;
	}
	
	public function getTypeModel($type,$id)
	{
		$result = null;
		switch ($type) {
			case 'Question':
				$questionModel = MooCore::getInstance()->getModel('Question.Question');
				$result = $questionModel->findById($id);
				break;
			case 'Answer':
				$questionAnswerModel = MooCore::getInstance()->getModel('Question.QuestionAnswer');
				$result = $questionAnswerModel->findById($id);
				break;
			case 'Comment':
				$questionCommentModel = MooCore::getInstance()->getModel('Question.QuestionComment');
				$result = $questionCommentModel->findById($id);
				break;
		}
		return $result;
	}
}
