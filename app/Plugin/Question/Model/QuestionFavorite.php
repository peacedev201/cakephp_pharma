<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionFavorite extends QuestionAppModel {
	public $belongsTo = array( 'User');
	public function afterSave($created, $options = array()) {
		parent::afterSave($created,$options);
		if ($created)
		{
			$question_id = $this->data['QuestionFavorite']['question_id'];
			
			$this->query("UPDATE ".$this->tablePrefix."questions SET `favorite_count`=`favorite_count` + 1 WHERE id=" . intval($question_id));
		}
	}
	public function beforeDelete($cascade = true)
	{
		$favorite = $this->findById($this->id);
	
		$this->query("UPDATE ".$this->tablePrefix."questions SET `favorite_count`=`favorite_count` - 1 WHERE id=" . intval($favorite['QuestionFavorite']['question_id']));
	}
	
	public function checkFavorite($question_id,$uid)
	{		
		if (!$uid)
			return false;
	
		return $this->find('first',array(
			'conditions' => array(
				'QuestionFavorite.user_id' => $uid,
				'QuestionFavorite.question_id' => $question_id,
			)
		));
	}
}
