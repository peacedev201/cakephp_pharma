<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionTag extends QuestionAppModel {
	public $mooFields = array('href','status');
	public $validationDomain = 'question';
	public $validate = array(
		'title' => 	array(
				'rule' => 'notBlank',
				'message' => 'Title is required',
		)
	);
	public function getHref($row)
	{
		$request = Router::getRequest();
		if (isset($row['id']))
			return $request->base.'/questions/index/all?keyword=['.$row['title'].']';
		
		return false;
	}
	
	public function getStatus($row)
	{
		switch ($row['status'])
		{
			case 0:
				return __d('question','Unapproved');
			case 1:
				return __d('question','Approved');
			case 2:
				return __d('question','Denied');
		}
	}
	
	public function updateCount($id,$up = true)
	{
		if ($up)
			$this->query("UPDATE $this->tablePrefix$this->table SET `count`=`count` + 1 WHERE id=" . intval($id));
		else
			$this->query("UPDATE $this->tablePrefix$this->table SET `count`=`count` - 1 WHERE id=" . intval($id));
	}
	
	public function delete($id = NULL, $cascade = true)
	{
		$tagMapModel = MooCore::getInstance()->getModel("Question.QuestionTagMap");
		$tagMapModel->deleteAll(array('QuestionTagMap.tag_id'=>$id));
		parent::delete($id);
	}
}
