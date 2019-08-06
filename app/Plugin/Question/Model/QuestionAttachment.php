<?php

App::uses('QuestionAppModel','Question.Model');
class QuestionAttachment extends QuestionAppModel {
	public $mooFields = array('title','href','url');
	public function getTitle(&$row)
	{
		return $row['original_filename'];
	}
	public function getHref($row)
	{
		$request = Router::getRequest();
    	if (isset($row['id']))
    		return $request->base.'/question/question_attachments/download/'.$row['id'];
    		
    	return false;
	}
	public function getAttachments($type, $id )
	{
		$attachments = $this->find('all', array('conditions' => array('type'=>$type,'id' => $id)));
		return $attachments;
	}
		
	public function deleteAttachment( $attachment )
	{
		if ( file_exists(WWW_ROOT . 'uploads' . DS . 'question_attachments' . DS . $attachment['QuestionAttachment']['filename']) )
			unlink(WWW_ROOT . 'uploads' . DS . 'question_attachments' . DS . $attachment['QuestionAttachment']['filename']);
			
		$this->delete( $attachment['QuestionAttachment']['id'] );
	}
}
 