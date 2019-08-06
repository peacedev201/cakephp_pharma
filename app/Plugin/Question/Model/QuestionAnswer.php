<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionAnswer extends QuestionAppModel {
	public $belongsTo = array( 'User');
	public function afterSave($created, $options = array()) {
		parent::afterSave($created,$options);
		if ($created)
		{
			$question_id = $this->data['QuestionAnswer']['question_id'];
			$userModel = MooCore::getInstance()->getModel('Question.QuestionUser');
			$user = $userModel->getUser($this->data['QuestionAnswer']['user_id']);
			
			$this->query("UPDATE ".$this->tablePrefix."questions SET `answer_count`=`answer_count` + 1 WHERE id=" . intval($question_id));
			$this->query("UPDATE ".$this->tablePrefix."question_users SET `total_answer`=`total_answer` + 1 WHERE user_id=" . intval($this->data['QuestionAnswer']['user_id']));
		}
	}
	
	public function beforeDelete($cascade = true) 
	{
		$answer = $this->findById($this->id);		
		//delete comment
		if ($answer['QuestionAnswer']['comment_count'])
		{
			$commentModel = MooCore::getInstance()->getModel("Question.QuestionComment");
			$commentModel->deleteAll(array('QuestionComment.type' => "QuestionAnswer","QuestionComment.type_id"=>$answer['QuestionAnswer']['user_id']), false);
		}
		
		$this->query("UPDATE ".$this->tablePrefix."questions SET `answer_count`=`answer_count` - 1 WHERE id=" . intval($answer['QuestionAnswer']['question_id']));
		$this->query("UPDATE ".$this->tablePrefix."question_users SET `total_answer`=`total_answer` - 1 WHERE user_id=" . intval($answer['QuestionAnswer']['user_id']));
		
		//delete vote
		$voteModel = MooCore::getInstance()->getModel("Question.QuestionVote");
		$votes = $voteModel->find("all",array(
			"conditions"=>array("QuestionVote.type_id"=>$answer['QuestionAnswer']['id'],"QuestionVote.type"=>"Answer")
		));
		foreach ($votes as $vote)
		{
			$voteModel->delete($vote['QuestionVote']['id']);
		}
		
		//delete best answer
		$questionPointHistoryModel = MooCore::getInstance()->getModel('Question.QuestionPointHistory');
		if ($answer['QuestionAnswer']['best_answers'])
		{			
			$history = $questionPointHistoryModel->find('first',array(
    			'conditions'=>array(
	    			'type'=>'Best_Answer',
	    			'type_id'=>$answer['QuestionAnswer']['id']
    			)
    		));
			if ($history)
			{
				$questionPointHistoryModel->delete($history['QuestionPointHistory']['id']);
				$this->query("UPDATE ".$this->tablePrefix."questions SET `has_best_answers`= 0 WHERE id=" . intval($answer['QuestionAnswer']['question_id']));
			}
		}
		
		//delete point history		
		$history = $questionPointHistoryModel->find('first',array(
			'conditions' => array(
				'QuestionPointHistory.type' => 'Create_Answer',
				'QuestionPointHistory.type_id' => $answer['QuestionAnswer']['id'],
			)
		));
		if ($history)
			$questionPointHistoryModel->delete($history['QuestionPointHistory']['id']);
		
		//delete attach
    	$attachModel = MooCore::getInstance()->getModel("Question.QuestionAttachment");
    	$attachs = $attachModel->getAttachments('Answer',$answer['QuestionAnswer']['user_id']);
    	
    	foreach ($attachs as $attach)
    	{
    		$attachModel->deleteAttachment($attach);
    	}
    	
    	$this->id = $answer['QuestionAnswer']['id'];
	}
	
	public function getCountAnswerByUser($question_id,$uid)
	{
		$conditions = array(
			'QuestionAnswer.question_id' => $question_id,
			'QuestionAnswer.user_id' => $uid
		);
		
		$num = $this->find('count',array(
			'conditions' => $conditions
		));
		
		return $num;
	}
}
