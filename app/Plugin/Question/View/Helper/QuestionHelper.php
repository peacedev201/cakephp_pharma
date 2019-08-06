<?php
App::uses('AppHelper', 'View/Helper');
class QuestionHelper extends AppHelper {
	public $_permissions;
	public $helpers = array('Storage.Storage');
	public $categories = null;
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View,$settings);
		$this->_permissions = array(
			'create_new_tag' => __d('question','Create new tag'),
			'leave_comment' => __d('question','Leave comment'),
			'vote_up' => __d('question','Vote up'),
			'vote_down' => __d('question','Vote down'),
		);
	}
	
	public function canEdit($item,$viewer)
	{
		if (!$viewer)
			return false;
	
		if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['Question']['user_id'])
			return true;

		return false;
	}
	
	public function canEditAnswer($item,$viewer)
	{
		if (!$viewer)
			return false;
	
		if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['QuestionAnswer']['user_id'])
			return true;

		return false;
	}
	
	public function canMarkBestAnswer($question,$answer,$viewer)
	{
		if (!$this->canEdit($question, $viewer))
		{
			return false;
		}
		
		if ($viewer['Role']['is_admin'])
			return true;
		
		if ($viewer['User']['id'] == $answer['QuestionAnswer']['user_id'])
		{
			return false;
		}
		
		return true;
	}
	
	public function canAnswer($question,$uid)
	{
		if (!$uid)
			return false;
		
		$number = Configure::read("Question.question_number_max_answer");
		if (!$number)
			return true;
		
		$answerModel = MooCore::getInstance()->getModel("Question.QuestionAnswer");
		$count = $answerModel->getCountAnswerByUser($question['Question']['id'],$uid);
		if ($count >= $number)
			return false;
		
		return true;
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
		
		return $this->Storage->getUrl($item['Question']['id'], $prefix, $item['Question']['thumbnail'], "questions");
	}
	
	public function getEnable()
	{
		return Configure::read('Question.question_enabled');
	}
	
	public function canEditComment($comment,$viewer)
	{
		if (!$viewer)
			return false;
		
		if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $comment['QuestionComment']['user_id'])
			return true;
	
		return false;
	}
	
	public function getAllBadges()
	{
		$badges = Cache::read('all_badge', 'question');
		if (!$badges)
		{
			$badgeModal = MooCore::getInstance()->getModel('Question.QuestionBadge');
			$badges = $badgeModal->find('all',array(
					'order' => 'QuestionBadge.point DESC'
			));
				
			Cache::write('all_badge', $badges,'question');
		}
		foreach ($badges as &$badge)
		{
			$permissions = array();
			$data = explode(',',$badge['QuestionBadge']['permission']);
			foreach ($this->_permissions as $key => $permission)
			{
				$tmp = array("text"=>$permission,'check'=>false);
				if (in_array($key, $data))
				{
					$tmp["check"] = true; 
				}
				$permissions[] = $tmp;
			}
			$badge['permissions'] = $permissions;
		}
		
		return $badges;
	}
	
	public function getHtmlBadge($user_id)
	{
		$questionUserModal = MooCore::getInstance()->getModel('Question.QuestionUser');
		$qUser = $questionUserModal->getUser($user_id);
		
		$current = $this->getCurrentBadges($qUser); 
		
		if (!$current)
			return '';
		
		return '<span style="color:'.$current['QuestionBadge']['text_color'].';background-color:'.$current['QuestionBadge']['background_color'].';" class="user-badge">'.$current['QuestionBadge']['name'].'</span>';
	}
	
	public function getCurrentBadges($qUser)
	{
		$badges = $this->getAllBadges();
		$current = null;
		foreach ($badges as $badge)
		{
			if ($qUser['QuestionUser']['total'] >= $badge['QuestionBadge']['point'])
			{
				$current = $badge;
				break;
			}
		}
		
		return $current;
	}
	
	public function getListPoints()
	{
		$result = array();
		$result[] = array(
			'text' => __d('question','Ask a question'),
			'point' => "+".Configure::read('Question.question_point_create_question')
		);
		$result[] = array(
				'text' => __d('question','Answer a question'),
				'point' => "+".Configure::read('Question.question_point_create_answer')
		);		
		$result[] = array(
			'text' => __d('question','Question received a vote up'),
			'point' => "+".Configure::read('Question.question_point_vote_question')
		);
		$result[] = array(
			'text' => __d('question','Answer received a vote up'),
			'point' => "+".Configure::read('Question.question_point_vote_answer')
		);
		$result[] = array(
			'text' => __d('question','Have your answer selected as the best answer'),
			'point' => "+".Configure::read('Question.question_point_vote_best_answer')
		);
		$result[] = array(
				'text' => __d('question','Answer received a vote down'),
				'point' => "-".Configure::read('Question.question_point_vote_question')
		);
		$result[] = array(
				'text' => __d('question','Question received a vote down'),
				'point' => "-".Configure::read('Question.question_point_vote_answer')
		);
		return $result;
	}
	
	public function can($type,$viewer)
	{
		if (!$viewer)
			return QUESTION_CAN_ERROR_LOGIN;
		
		$allbadges = $this->getAllBadges();
		if (!count($allbadges))
			return QUESTION_CAN_ERROR_NONE;
		
		if ($viewer['Role']['is_admin'])
		{
			return QUESTION_CAN_ERROR_NONE;
		}
		
		$questionUserModal = MooCore::getInstance()->getModel('Question.QuestionUser');
		$qUser = $questionUserModal->getUser($viewer['User']['id']);
		$current = $this->getCurrentBadges($qUser);
		
		if ($current)
		{
			$permissions = explode(',', $current['QuestionBadge']['permission']);
			if (in_array($type, $permissions))
				return QUESTION_CAN_ERROR_NONE;
		}
		return QUESTION_CAN_ERROR_POINT;		
	}
	
	public function getItemSitemMap($name,$limit,$offset)
	{
		if (!MooCore::getInstance()->checkPermission(null, 'question_view'))
			return null;
	
		$questionModel = MooCore::getInstance()->getModel("Question.Question");
		$questions = $questionModel->find('all',array(
				'conditions' => array('Question.privacy'=>PRIVACY_PUBLIC),
				'limit' => $limit,
				'offset' => $offset
		));
			
		$urls = array();
		foreach ($questions as $question)
		{
			$urls[] = FULL_BASE_URL.$question['Question']['moo_href'];
		}
			
		return $urls;
	}
	
	public function getCategoryName($id)
	{
		if (!$this->categories)
		{
			$categoryModel = MooCore::getInstance()->getModel('Category');
			$this->categories = $categoryModel->getCategoriesList('Question');
		}
		if (isset($this->categories[$id]))
		{
			return $this->categories[$id];
		}
		
		return '';
	}
}
