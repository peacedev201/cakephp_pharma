<?php

App::uses('PollAppModel','Poll.Model');
class PollItem extends PollAppModel {
	public $mooFields = array('title');
	
	public function getTitle(&$row)
	{
		if (isset($row['name']))
		{
			$row['name'] = htmlspecialchars($row['name']);
			return $row['name'];
		}
		return '';
	}
	
    public function getItemByPollId($id)
    {
    	return $this->find('all',array(
    		'conditions' => array(
				'PollItem.poll_id' => $id		
    		)
    	));
    }
    
    public function deleteByPollId($id)
    {
    	$this->deleteAll(array('PollItem.poll_id' => $id), false);
    }
    
    public function updateTotalUser($id,$type = 1)
    {
    	if ($type)
    	{
    		$this->query("UPDATE $this->tablePrefix$this->table SET total_user=total_user+1 WHERE id=" . intval($id));
    	}
    	else
    	{
    		$this->query("UPDATE $this->tablePrefix$this->table SET total_user=total_user-1 WHERE id=" . intval($id));
    	}
    }
    
    public function getItemsMin($poll_id)
    {
    	$items = $this->find('all',array(
    			'conditions' => array(
    					'PollItem.poll_id' => $poll_id
    			),
    			'order' => array('PollItem.total_user DESC','PollItem.id')
    	));
    	
    	return $items;
    }
    
    public function getItems($poll_id,$uid , $max_item = 0)
    {
    	$answers = $this->find('all',array(
			'conditions' => array(
				'PollItem.poll_id' => $poll_id
			),
    		'order' => array('PollItem.total_user DESC','PollItem.id')
		));
    	
    	$helper = MooCore::getInstance()->getHelper('Poll_Poll');
    	$friends = $helper->getFriends($uid);    	
    	
    	$answerModel = MooCore::getInstance()->getModel('Poll.PollAnswer');
    	$max_answer = 0;
    	foreach ($answers as $i=>&$answer)
    	{
    		if ($answer['PollItem']['total_user'] > $max_answer)
    			$max_answer = $answer['PollItem']['total_user'];
    		
    		if ($max_item && $i > $max_item - 1)
    		{
    			break;
    		}
    		
    		$answer['PollItem']['mark_check'] = $answerModel->checkAnswer($answer['PollItem']['id'],$uid);
    		if ($answer['PollItem']['mark_check'])
    		{
    			$friends[] = $uid;    			
    		}
    		
    		$users = $answerModel->getAnswers($answer['PollItem']['id'],array('user_ids'=>$friends));
    		$tmp = array();
    		$tmp_other_user = array();
    		if (count($users))
    		{
    			foreach ($users as $user)
    			{
    				if ($uid == $user['User']['id'])
    				{
    					$tmp[] = $user;
    				}
    				else
    				{
    					$tmp_other_user[] = $user;
    				}
    			}
    		}
    		$tmp = array_merge($tmp,$tmp_other_user);
    		$answer['PollItem']['FriendsAnswer'] = $tmp;
    		
    	}
    	return array('max_answer'=>$max_answer,'result' => $answers);
    }
}
