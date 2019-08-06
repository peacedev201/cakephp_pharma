<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionBadge extends QuestionAppModel {
	public $validationDomain = 'question';
	public $validate = array(
		'name' => 	array(
			'rule' => 'notBlank',
			'message' => 'Name is required',
		),
		'color' => 	array(
			'rule' => 'notBlank',
			'message' => 'Color is required',
		),
		'point' => 	array(
			'rule' => 'notBlank',
			'message' => 'Point is required',
		),
	);
	public function afterDelete()
	{
		Cache::clearGroup('question');
		parent::afterDelete();
	}
	
	public function afterSave($created, $options = array())
	{
		Cache::clearGroup('question');
		parent::afterSave($created, $options);
	}
}
