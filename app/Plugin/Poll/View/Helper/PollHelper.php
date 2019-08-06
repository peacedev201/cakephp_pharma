<?php
App::uses('AppHelper', 'View/Helper');
class PollHelper extends AppHelper {	
	public $friend_list = array();
	public $helpers = array('Storage.Storage');
	
	public function getFriends($id)
	{
		if (!isset($this->friend_list[$id]))
		{
			$friendModel = MooCore::getInstance()->getModel('Friend');
			$this->friend_list[$id] = $friendModel->getFriends($id);
		}
		return $this->friend_list[$id];
	}
	
	public function getImage($item, $options = array()) {
		$prefix = '';
		if (isset($options['prefix'])) {
			if ($options['prefix'])
			{
				$prefix = $options['prefix'] . '_';
			}
			else
			{
				$prefix = '';
			}
		}
		
		return $this->Storage->getUrl($item['Poll']['id'], $prefix, $item['Poll']['thumbnail'], "polls");
	}
	
	public function checkSeeComment($poll,$uid)
	{
		if ($poll['Poll']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		return $this->checkPostStatus($poll,$uid);
	}
	
	public function checkPostStatus($poll,$uid)
	{
		if (!$uid)
			return false;		
		$friendModel = MooCore::getInstance()->getModel('Friend');
		if ($uid == $poll['Poll']['user_id'])
			return true;
			
		if ($poll['Poll']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		if ($poll['Poll']['privacy'] == PRIVACY_FRIENDS)
		{
			$areFriends = $friendModel->areFriends( $uid, $poll['Poll']['user_id'] );
			if ($areFriends)
				return true;
		}
		
		
		return false;
	}
	
	public function checkSeeActivity($poll,$uid)
	{
		return $this->checkPostStatus($poll,$uid);
	}	
	
	public function canEdit($item,$viewer)
	{
		if (!$viewer)
			return false;
		
		if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['Poll']['user_id'])
			return true;

		return false;
	}
	
	public function canDelete($item,$viewer)
	{
		return $this->canEdit($item, $viewer);
	}
	
	public function getEnable()
	{
		return Configure::read('Poll.poll_enabled');
	}
	
	public function getTagUnionsPoll($pollids)
	{
		return "SELECT i.id, i.title, '' as body, i.like_count, i.created, 'Poll_Poll' as moo_type, i.privacy, i.user_id
						 FROM " . Configure::read('core.prefix') . "polls i
						 WHERE i.id IN (" . implode(',', $pollids) . ")";
	}
	
	public function canEditAnswer($poll)
	{
		$answerModel = MooCore::getInstance()->getModel('Poll.PollAnswer');
		
		$count = $poll['Poll']['answer_count'];
		
		if ($count > 0)
			return false;
		
		return true;
	}
	
	public function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
	
	public function getItemSitemMap($name,$limit,$offset)
	{
		if (!MooCore::getInstance()->checkPermission(null, 'poll_view'))
			return null;
	
		$pollModel = MooCore::getInstance()->getModel("Poll.Poll");
		$polls = $pollModel->find('all',array(
				'conditions' => array('Poll.privacy'=>PRIVACY_PUBLIC),
				'limit' => $limit,
				'offset' => $offset
		));
			
		$urls = array();
		foreach ($polls as $poll)
		{
			$urls[] = FULL_BASE_URL.$poll['Poll']['moo_href'];
		}
			
		return $urls;
	}
}
