<?php

class CreditUploadController extends CreditAppController {

    public function avatar() {
	    $this->autoRender = false;
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(WWW_ROOT . $path);

        if (!empty($result['success'])) {
            // resize image
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            $result['file_url'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file_path'] = $path . DS . $result['filename'];
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

    public function upload($id, $save_original = 0) {
        $uid = $this->Session->read('uid');

        if (!$id || !$uid)
            return;

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';
        $this->_prepareDir($path);
        $result = $uploader->handleUpload(WWW_ROOT . $path);

        if (!empty($result['success'])) {
            // resize image
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            $photo = PhpThumbFactory::create($path . DS . $result['filename']);


            if ($save_original) {
                $original_photo = $url . $result['filename'];
                $medium_photo = 'm_' . $result['filename'];
            } else {
                $original_photo = '';
                $medium_photo = $result['filename'];
            }

            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $medium_photo);

            $result['photo'] = $path . DS . $medium_photo;
        }

        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
}

?>
