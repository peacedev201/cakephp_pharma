<?php
class QuestionUploadController extends QuestionAppController {
	public function beforeFilter() {
        parent::beforeFilter();
        $this->autoRender = false;
    }
	public function attachments($type) {
        $uid = MooCore::getInstance()->getViewer(true);
        if (!$uid)
        	die();
        	
        $extension = Configure::read('Question.question_filetype_allow');
    	$extension = explode(',', $extension);
    	
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($extension);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'questions'.DS.'attachments';

        $original_filename = $this->request->query['qqfile'];
        $ext = $this->_getExtension($original_filename);
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            if (in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
                
                $this->loadModel('Photo.Photo');

                $this->Photo->create();
                $this->Photo->set(array(
                    'target_id' => 0,
                	'type' => $type == 'Answer' ? 'QuestionAnswer' : 'Question',
                    'user_id' => $uid,
                    'thumbnail' => $path . DS . $result['filename']
                ));
                $this->Photo->save();

                $photo = $this->Photo->read();

                $view = new View($this);
                $mooHelper = $view->loadHelper('Moo');
                $result['thumb'] = $mooHelper->getImageUrl($photo, array('prefix' => '450'));
                $result['large'] = $mooHelper->getImageUrl($photo, array('prefix' => '1500'));
                $result['photo_id'] = $photo['Photo']['id'];
                $result['attachment_id'] = 0;
            }else {
                // save to db
                $this->loadModel('Question.QuestionAttachment');
                $this->QuestionAttachment->create();
                $this->QuestionAttachment->set(array('user_id' => $uid,
                    'filename' => $result['filename'],
                	'type' => $type,
                    'original_filename' => $original_filename,
                    'extension' => $ext
                ));
                $this->QuestionAttachment->save();

                $result['attachment_id'] = $this->QuestionAttachment->id;
                $result['original_filename'] = $original_filename;
            }
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
	public function _getExtension($filename = null) {
        $tmp = explode('.', $filename);
        $re = array_pop($tmp);
        return $re;
    }
}