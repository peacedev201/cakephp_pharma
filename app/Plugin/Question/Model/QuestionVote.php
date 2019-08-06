<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionVote extends QuestionAppModel {
	public function afterSave($created, $options = array()) {
		parent::afterSave($created,$options);
		if ($created)
		{
			$questionPointHistoryModel = MooCore::getInstance()->getModel('Question.QuestionPointHistory');
			$type = $this->data['QuestionVote']['type'];
			$vote = $this->data['QuestionVote']['vote'];
			$type_id = $this->data['QuestionVote']['type_id'];
			
			$object = $this->getTypeModel($type,$type_id);
			$data = array(
				'type' => $type,
				'type_id' => $type_id,
				'from_user_id' => $this->data['QuestionVote']['user_id'],
				'user_id' => $object[key($object)]['user_id']
			);
			
			$point = 1;
			$table = '';
			switch ($this->data['QuestionVote']['type']) {
				case 'Question': 
					$point = Configure::read('Question.question_point_vote_question');	
					$table = 'questions';
					break;
				case 'Answer':
					$point = Configure::read('Question.question_point_vote_answer');
					$table = 'question_answers';
					break;
			}
			$data['point'] = $point*($vote ? 1 : -1);
			$questionPointHistoryModel->save($data);
			
			if ($vote)
			{
				$this->query("UPDATE ".$this->tablePrefix."$table SET `vote_count`=`vote_count` + 1 WHERE id=" . intval($type_id));
			}
			else
			{
				$this->query("UPDATE ".$this->tablePrefix."$table SET `vote_count`=`vote_count` - 1 WHERE id=" . intval($type_id));
			}
		}
	}
	
	public function beforeDelete($cascade = true) 
	{
		$vote = $this->findById($this->id);		
		
		$type = $vote['QuestionVote']['type'];
		$type_id = $vote['QuestionVote']['type_id'];
		
		$object = $this->getTypeModel($type,$type_id);
		
		$from_user_id = $vote['QuestionVote']['user_id'];
		$user_id = $object[key($object)]['user_id'];
		
		$questionPointHistoryModel = MooCore::getInstance()->getModel('Question.QuestionPointHistory');
		$histories = $questionPointHistoryModel->find('all',array(
			'conditions' => array(
				'QuestionPointHistory.type' => $type,
				'QuestionPointHistory.type_id' => $type_id,
				'QuestionPointHistory.user_id' => $user_id,
				'QuestionPointHistory.from_user_id' => $from_user_id,
			)
		));
		
		foreach ($histories as $history)
		{
			$questionPointHistoryModel->delete($history['QuestionPointHistory']['id']);
		}
		
		$table = '';
		switch ($vote['QuestionVote']['type']) {
			case 'Question':				
				$table = 'questions';
				break;
			case 'Answer':				
				$table = 'question_answers';
				break;
		}
		
		if ($vote['QuestionVote']['vote'])
		{
			$this->query("UPDATE ".$this->tablePrefix."$table SET `vote_count`=`vote_count` - 1 WHERE id=" . intval($type_id));
		}
		else
		{
			$this->query("UPDATE ".$this->tablePrefix."$table SET `vote_count`=`vote_count` + 1 WHERE id=" . intval($type_id));
		}
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
		}
		return $result;
	}
	
	public function checkVote($type,$type_id,$user_id,$up = 1)
	{
		if (!$user_id)
			return false;
		
		return $this->find('first',array(
    		'conditions' => array(
				'QuestionVote.type' => $type,
    			'QuestionVote.type_id' => $type_id,
				'QuestionVote.user_id' => $user_id,
    			'QuestionVote.vote' => $up
			)
    	));
		
	}
}
