<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionUser extends QuestionAppModel {
	public $users = array();
	public $belongsTo = array( 'User' );
	
	public function getUser($user_id, $reset = false)
	{
		if (!$reset)
		{
			if (isset($this->users[$user_id]))
				return $this->users[$user_id];
		}
		
		$user = $this->findByUserId($user_id);
		if (!$user)
		{
			$this->clear();
			$this->save(array(
		        'user_id' => $user_id	        
		    ));
		    $user = $this->findById($this->id);
		}
		$this->users[$user_id] = $user;
		return $user;
	}
}
