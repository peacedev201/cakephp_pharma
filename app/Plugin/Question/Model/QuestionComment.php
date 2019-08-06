<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionComment extends QuestionAppModel {
	public $belongsTo = array( 'User');
	public function afterSave($created, $options = array()) {
		parent::afterSave($created,$options);
		if ($created)
		{
			$id = $this->data['QuestionComment']['type_id'];
			$type = $this->data['QuestionComment']['type'];
			
			if ($type == 'Question')
				$this->query("UPDATE ".$this->tablePrefix."questions SET `comment_count`=`comment_count` + 1 WHERE id=" . intval($id));
			else
				$this->query("UPDATE ".$this->tablePrefix."question_answers SET `comment_count`=`comment_count` + 1 WHERE id=" . intval($id));
		}
	}
	
	public function beforeDelete($cascade = true) 
	{
		$comment = $this->findById($this->id);
		
		$type = $comment['QuestionComment']['type'];
		$id = $comment['QuestionComment']['type_id'];
			
		if ($type == 'Question')
			$this->query("UPDATE ".$this->tablePrefix."questions SET `comment_count`=`comment_count` - 1 WHERE id=" . intval($id));
		else
			$this->query("UPDATE ".$this->tablePrefix."question_answers SET `comment_count`=`comment_count` - 1 WHERE id=" . intval($id));
		
	}
	
	public function getComments($type,$id,$page = 1)
	{
		$conditions = array(
			'QuestionComment.type' => $type,
			'QuestionComment.type_id' => $id
		);
		$conditions = $this->addBlockCondition($conditions);
		return $this->find('all',array(
			'conditions' => $conditions,
			'limit' => Configure::read("Question.question_item_per_pages"),
			'page' => $page,
			'order' => 'QuestionComment.id'
		));
	}
	
	public function getCountComments($type,$id)
	{
		$conditions = array(
				'QuestionComment.type' => $type,
				'QuestionComment.type_id' => $id
		);
		$conditions = $this->addBlockCondition($conditions);
		return $this->find('count',array(
			'conditions' => $conditions,
		));
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
}
