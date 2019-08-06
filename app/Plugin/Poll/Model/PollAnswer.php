<?php

App::uses('PollAppModel','Poll.Model');
class PollAnswer extends PollAppModel {
	public $belongsTo = array( 'User');
	
    public function getAnswerByPollId($id)
    {
    	return $this->find('all',array(
    		'conditions' => array(
				'PollAnswer.poll_id' => $id		
    		)
    	));
    }
    public function deleteByPollId($id)
    {
    	$this->deleteAll(array('PollAnswer.poll_id' => $id), false);
    }
    
    public function afterSave($created, $options = array()) 
    {
    	if ($created)
    	{
    		$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
    		$itemModel->updateTotalUser($this->data['PollAnswer']['item_id']);
    		
    		$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
    		$pollModel->updateCountAnswer($this->data['PollAnswer']['poll_id']);
    		
    		$activityModel = MooCore::getInstance()->getModel("Activity");
    		$activity = $activityModel->find('first', array('conditions' => array(
    				'Activity.item_type' => 'Poll_Poll',
    				'Activity.item_id' => $this->data['PollAnswer']['poll_id'],
    		)));
    		
    		if ($activity)
    		{
    			$activityModel->id = $activity['Activity']['id'];
    			$activityModel->save(array('Activity.item_type'=>'Poll_Poll'));
    		}
    	}
    }
    
    public function removeAnswerByPollId($poll_id,$uid)
    {
    	$answer = $this->find('first',array(
    			'conditions' => array(
    					'PollAnswer.poll_id' => $poll_id,
    					'PollAnswer.user_id' => $uid
    			)
    	));
    	
    	if ($answer)
    	{
    		$this->delete($answer['PollAnswer']['id']);
    	}
    }
    
    public function beforeDelete($cascade = true) 
    {
    	$item = $this->findById($this->id);
    	if ($item)
    	{
	    	$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
	    	$itemModel->updateTotalUser($item['PollAnswer']['item_id'],0);
	    	
	    	$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
	    	$pollModel->updateCountAnswer($item['PollAnswer']['poll_id']);
    	}
    }
    
    public function checkAnswer($item_id,$uid)
    {
    	if (!$uid)
    		return false;
    	
    	$answer = $this->find('first',array(
    		'conditions' => array(
				'PollAnswer.item_id' => $item_id,
				'PollAnswer.user_id' => $uid
			)
    	));
    	
    	return $answer;
    }
    
    public function getAnswers($item_id, $params)
    {
    	$conditions = array(
    		'PollAnswer.item_id' => $item_id
    	);
    	
    	if (isset($params['user_ids']) && is_array($params['user_ids']))
    	{
    		$conditions['PollAnswer.user_id'] = $params['user_ids'];
    	}
    	$page = null;
    	if (isset($params['page']) && $params['page'])
    	{
    		$page = $params['page'];
    	}
    	
    	if ($page)
    	{
	    	$limit = 20;
	    	if (isset($params['limit']) && $params['limit'])
	    	{
	    		$limit = $params['limit'];
	    	}
	    	
	    	return $this->find('all',array(
	    		'conditions' => $conditions,
    			'limit' => $limit,
    			'page' => $page,
	    	));
    	}
    	
    	return $this->find('all',array(
    		'conditions' => $conditions
    	));
    }
}
