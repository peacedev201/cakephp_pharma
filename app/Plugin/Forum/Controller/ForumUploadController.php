<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class ForumUploadController extends ForumAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->autoRender = false;
    }

    public function icon($size) {
        $uid = $this->Auth->user('id');

        if (!$uid)
            return;

        // save this picture to album
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = MooCore::getInstance()->_getPhotoAllowedExtension();

        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            $imageForGetSize = $path . DS . $result['filename'];
            $image = getcwd() . "/" . $imageForGetSize;
            $info = getimagesize($image);
            //var_dump($info);
            if(!empty($info))
            {
                if($info[0] != $info[1] || $info[0] != $size)
                {
                    $result['success'] = false;
                    $result['error'] = __d('forum','Please upload image size %s x %s',$size,$size);
                    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);                    
                    exit();
                }
            }
            else
            {
                $result['success'] = false;
                $result['error'] = __d('forum',"Can not get size from image");
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
                exit();
            }
            
            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file_path'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function attachments() {
        $uid = $this->Auth->user('id');

        $allowedExtensions = MooCore::getInstance()->_getFileAllowedExtension();

        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'attachments';
        $url = 'uploads/attachments';

        $original_filename = $this->request->query['qqfile'];
        $ext = $this->_getExtension($original_filename);

        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            if (in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {

                $this->loadModel('Photo.Photo');

                $this->Photo->create();
                $this->Photo->set(array(
                    'target_id' => 0,
                    'type' => 'ForumTopic',
                    'user_id' => $uid,
                    'thumbnail' => $path . DS . $result['filename']
                ));
                $this->Photo->save();

                $photo = $this->Photo->read();

                $view = new View($this);
                $mooHelper = $view->loadHelper('Moo');
                $result['thumb'] = $mooHelper->getImageUrl($photo, array('prefix' => '450'),true);
                $result['large'] = $mooHelper->getImageUrl($photo, array('prefix' => '1500'),true);
                $result['attachment_id'] = 0;
                $result['photo_id'] = $photo['Photo']['id'];
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

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

}

?>