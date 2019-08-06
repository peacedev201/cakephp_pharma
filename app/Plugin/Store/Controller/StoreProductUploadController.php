<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class StoreProductUploadController extends StoreAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->autoRender = false;
        $this->loadModel('Store.StoreProductImage');
    }

    public function slider() 
    {
        // save this picture to album
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) 
        {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $path = 'uploads'.DS.'sliders'.DS.$result['filename'];
            $url = FULL_BASE_URL.$this->request->webroot.'uploads/sliders/'.$result['filename'];
            $photo->adaptiveResize(SLIDER_PHOTO_WIDTH, SLIDER_PHOTO_HEIGHT)->save($path);
            
            $result['url'] = $url;
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function images() {
        $error = false;
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        
        //check demension
        $image_info = getimagesize($_FILES["qqfile"]["tmp_name"]);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if($image_width < PRODUCT_UPLOAD_MIN_WIDTH)
        {
            $result['success'] = 0;
            $result['message'] = sprintf(__d('store', 'Image width must be greater than %spx'), PRODUCT_UPLOAD_MIN_WIDTH);
            echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
            exit;
        }
 
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        $path = $this->StoreProductImage->createImagePath();
        
        if($path != null)
        {
            $image_url = $path['url'];
            $path_date = $path['path_date'];
            $path = $path['path'];

            $result = $uploader->handleUpload($path);

            if (!empty($result['success'])) {
                // resize image
                App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                $photo = PhpThumbFactory::create($path . DS . $result['filename']);

                $tiny_photo_path = $path . DS . PRODUCT_PHOTO_TINY_WIDTH.'_'.$result['filename'];
                $thumb_photo_path = $path . DS . PRODUCT_PHOTO_THUMB_WIDTH.'_'.$result['filename'];
                $large_photo_path = $path . DS . PRODUCT_PHOTO_LARGE_WIDTH.'_'.$result['filename'];
                $original_photo_path = $path . DS .$result['filename'];
                $photo->adaptiveResize(PRODUCT_PHOTO_TINY_WIDTH, PRODUCT_PHOTO_TINY_HEIGHT)->save($tiny_photo_path);
                $photo = PhpThumbFactory::create($path . DS . $result['filename']);
                $photo->adaptiveResize(PRODUCT_PHOTO_THUMB_WIDTH, PRODUCT_PHOTO_THUMB_HEIGHT)->save($thumb_photo_path);
                $photo = PhpThumbFactory::create($path . DS . $result['filename']);
                $photo->adaptiveResize(PRODUCT_PHOTO_LARGE_WIDTH, PRODUCT_PHOTO_LARGE_HEIGHT)->save($large_photo_path);
                if(file_exists($original_photo_path))
                {
                    unlink($original_photo_path);
                }
                
                $result['path'] = $path_date;
                $result['tiny'] = FULL_BASE_URL . $this->request->webroot.str_replace(DS, '/', $tiny_photo_path);
                $result['thumb'] = FULL_BASE_URL . $this->request->webroot.str_replace(DS, '/', $thumb_photo_path);
                $result['large'] = FULL_BASE_URL . $this->request->webroot.str_replace(DS, '/', $large_photo_path);
                $result['original'] = FULL_BASE_URL . $this->request->webroot.str_replace(DS, '/', $original_photo_path);
            }
        }
        else 
        {
            $result['success'] = 0;
        }

        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    public function files() {
        $extensions = Configure::read('Store.store_allow_digital_file_extensions');
        $allowedExtensions = explode(',', $extensions);
        
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        $result = $uploader->handleUpload(STORE_DIGITAL_PRODUCT_UPLOAD_DIR);
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    public function videos() {
        $extensions = Configure::read('Store.store_allow_video_extensions');
        $allowedExtensions = explode(',', $extensions);
        
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        $result = $uploader->handleUpload(STORE_VIDEO_UPLOAD_DIR);
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    /*private function _getPath()
    {
        $curYear = date('Y');
        $curMonthDate = date('m').'_'.date('d');
        $path = 'uploads' . DS . 'products';
        $path_year = $path.DS.$curYear;
        $path_month_date = $path.DS.$curYear.DS.$curMonthDate;
        $result = array(
            'path_date' => $curYear.'/'.$curMonthDate,
            'path' => $path_month_date,
            'url' => str_replace(DS, '/', $path_month_date)
        );
        if(!is_dir($path_month_date))
        {
            if(!is_dir($path))
            {
                $mask = umask(0);
                mkdir($path, 0777);
                umask($mask);
            }
            if(!is_dir($path_year))
            {
                $mask = umask(0);
                mkdir($path_year, 0777);
                umask($mask);
            }
            if(!is_dir($path_month_date))
            {
                $mask = umask(0);
                mkdir($path_month_date, 0777);
                umask($mask);
            }
            return $result;
        }
        else
        {
            return $result;
        }
        return array();
    }*/

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