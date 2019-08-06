<?php

class FaqSettingsController extends FaqAppController {

    public $components = array('QuickSettings');

    public function admin_index($id = null) {
        $this->set('title_for_layout', __d('faq', 'F.A.Q Settings'));
        $this->QuickSettings->run($this, array("Faq"), $id);
        if (CakeSession::check('Message.flash')) {
            $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $menu = $menuModel->findByUrl('/faqs');
            if ($menu) {
                $menuModel->id = $menu['CoreMenuItem']['id'];
                $menuModel->save(array('is_active' => Configure::read('Faq.faq_enabled')));
            }
            Cache::clearGroup('menu', 'menu');
        }
    }

    public function upload_background($max_width = null, $max_height = null) {
        $this->autoRender = false;
        // save this picture to album

        $path = 'uploads' . DS . 'faqs';
        $url = 'uploads/faqs/';

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        //check demension
        if ($max_width != null && $max_height != null) {
            $image_info = getimagesize($_FILES["qqfile"]["tmp_name"]);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            if ($image_width != $max_width || $image_height != $max_height) {
                $result['success'] = 0;
                $result['message'] = sprintf(__d('addonsstore', 'Banner dimension must be %sx%s pixel!'), $max_width, $max_height);
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
                exit;
            }
        }

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            $result['url'] = $url;
            $result['path'] = '/uploads/faqs/' . $result['filename'];
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

}