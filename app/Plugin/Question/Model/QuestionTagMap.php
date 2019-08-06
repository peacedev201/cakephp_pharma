<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionTagMap extends QuestionAppModel {
	public $belongsTo = array(
        'QuestionTag' => array(
    		'className'=> 'Question.QuestionTag',
			'foreignKey' => 'tag_id'
   	));
   	
	public function getTag($question_id)
	{
		$conditions = array('QuestionTagMap.question_id'=>$question_id);
		return $this->find('all',array('conditions'=>$conditions));
	}
	
	public function afterSave($created,$options = array())
	{
		if ($created)
		{
			$tagModel = MooCore::getInstance()->getModel("Question.QuestionTag");
			$tagModel->updateCount($this->data['QuestionTagMap']['tag_id']);
		}
	}
	
	public function beforeDelete($cascade = true)
	{
		$item = $this->findById($this->id);
		if ($item)
		{
			$tagModel = MooCore::getInstance()->getModel("Question.QuestionTag");
			$tagModel->updateCount($item['QuestionTagMap']['tag_id'], false);
		}
	}
}
