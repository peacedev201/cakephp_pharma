<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionPointHistory extends QuestionAppModel {
	public function afterSave($created, $options = array()) {
		parent::afterSave($created,$options);
		if ($created)
		{
			$to_user = $this->data['QuestionPointHistory']['user_id'];
			$point = $this->data['QuestionPointHistory']['point'];
			$type = $this->data['QuestionPointHistory']['type'];
			$questionUserModel = MooCore::getInstance()->getModel('Question.QuestionUser');
			$user = $questionUserModel->getUser($to_user, true);
			$total = $user['QuestionUser']['total'] + $point;
			$total_best = $user['QuestionUser']['total_best_answer'];
			if ($type == 'Best_Answer')
			{
				$total_best++;
			}
			
			$questionUserModel->id = $user['QuestionUser']['id'];
			$questionUserModel->save(array(
				'total' => $total,
				'total_best_answer' => $total_best
			));
		}
	}
	
	public function beforeDelete($cascade = true) 
	{
		$history = $this->findById($this->id);
		$to_user = $history['QuestionPointHistory']['user_id'];
		$point = $history['QuestionPointHistory']['point'];
		$type = $history['QuestionPointHistory']['type'];
		$questionUserModel = MooCore::getInstance()->getModel('Question.QuestionUser');
		$user = $questionUserModel->getUser($to_user, true);
		$total = $user['QuestionUser']['total'] - $point;
		$total_best = $user['QuestionUser']['total_best_answer'];
		if ($type == 'Best_Answer')
		{
			$total_best--;
		}
		
		$questionUserModel->id = $user['QuestionUser']['id'];
		$questionUserModel->save(array(
			'total' => $total,
			'total_best_answer' => $total_best
		));
		
	}
}
