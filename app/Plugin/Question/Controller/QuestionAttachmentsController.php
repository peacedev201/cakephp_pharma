<?php

class QuestionAttachmentsController extends AppController {
	
	public function download($id = null)
    {
    	$this->_checkPermission( array('aco' => 'question_view'));
    	
        $id = intval($id);       
        $this->loadModel('Question.QuestionAttachment');
            
        $attachment = $this->QuestionAttachment->findById($id);
        $this->_checkExistence($attachment);       
        
        // update counter
        $this->QuestionAttachment->increaseCounter($id, 'downloads');
        
        $this->viewClass = 'Media';
        $extension = end(explode('.', $attachment['QuestionAttachment']['original_filename']));
        $name = str_replace('.'.$extension, '', $attachment['QuestionAttachment']['original_filename']);        
        // Download app/outside_webroot_dir/example.zip        
        $params = array(
        		'id'        => $attachment['QuestionAttachment']['filename'],
        		'name'      => $name,
        		'download'  => true,
        		'extension' => $extension,
        		'path'      => APP . 'webroot' . DS . 'uploads' . DS .'questions'. DS . 'attachments' . DS
        );
        $this->set($params);
    }
}

?>
