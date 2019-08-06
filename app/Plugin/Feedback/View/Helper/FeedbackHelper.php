<?php
App::uses('AppHelper', 'View/Helper');
class FeedbackHelper extends AppHelper {
        public $helpers = array('Storage.Storage');
	public function checkPostStatus($feedback,$uid)
	{
		if (!$uid)
			return false;		
		$friendModel = MooCore::getInstance()->getModel('Friend');
		if ($uid == $feedback['Feedback']['user_id'])
			return true;
			
		if ($feedback['Feedback']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		if ($feedback['Feedback']['privacy'] == PRIVACY_FRIENDS)
		{
			$areFriends = $friendModel->areFriends( $uid, $feedback['Feedback']['user_id'] );
			if ($areFriends)
				return true;
		}
		
		
		return false;
	}
	
	public function getEnable()
	{
		return Configure::read('Feedback.feedback_enabled');
	}
	
	public function checkSeeComment($feedback,$uid)
	{
		if ($feedback['Feedback']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		return $this->checkPostStatus($feedback,$uid);
	}
	
	public function getTagUnionsFeedback($feedbackids)
	{
		return "SELECT i.id, i.title, i.description, i.like_count, i.created, 'Feedback_Feedback' as moo_type, i.privacy, i.user_id
						 FROM " . Configure::read('core.prefix') . "feedbacks i
						 WHERE i.id IN (" . implode(',', $feedbackids) . ")";
	}

	public function getImage($item, $options)
    {
        $prefix = (isset($options['prefix'])) ? $options['prefix'] . '_' : '';
        return $this->Storage->getUrl($item['id'], $prefix, $item['image_url'], "feedbacks");

	}

	public function getImageForApp($item, $options = array()) {
		$prefix = '111_';
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
		
		$item_img = '';
		if (!empty($item['FeedbackImage']))
		{
			$item_img = $item['FeedbackImage'][0]['image_url'];
		}

		return $this->Storage->getUrl($item['Feedback']['id'], $prefix, $item_img, "feedbacks");		
	}

	public function getImageForSuggestion($id, $options = null)
    {
		$prefix = (isset($options['prefix'])) ? $options['prefix'] . '_' : '';
		$mFeedbackImage = MooCore::getInstance()->getModel("Feedback.FeedbackImage");
		$feedback_image = $mFeedbackImage->findByFeedbackId($id);
		$url = '';
		$img_id = 0;
		if (!empty($feedback_image))
		{
			$feedback_image = $feedback_image['FeedbackImage'];
			$url = $feedback_image['image_url'];
			$img_id = $feedback_image['id'];
		}
		return $this->Storage->getUrl($img_id, $prefix, $url, "feedbacks");

	}
}
