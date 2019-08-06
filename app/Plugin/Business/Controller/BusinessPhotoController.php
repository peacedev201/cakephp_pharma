<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessPhotoController extends BusinessAppController {

    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->url_dashboard = $this->request->base.'/businesses/dashboard/';
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessAdmin');
        $this->loadModel('Business.BusinessPhoto');
        $this->loadModel('Business.BusinessFollow');
        $this->loadModel('Photo.Photo');
    }
    
    public function save_photos()
    {
        $data = $this->request->data;
        $business = $this->Business->getOnlyBusiness($data['business_id']);
        if(!$this->Business->permission($data['business_id'], BUSINESS_PERMISSION_MANAGE_PHOTO, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        /*else if(empty($data['photo_filename']))
        {
            $this->_jsonError(__d('business', 'Please upload photo'));
        }*/
        else
        {
            if($business['Business']['album_id'] == null || !$this->Business->checkAlbumExist($business['Business']['album_id']))
            {
                $album_id = $this->Business->createAlbum($business['Business']['name'], $business['Business']['user_id']);
                $this->Business->updateBusinessAlbum($business['Business']['id'], $album_id);
            }
            
            $this->save_photo_item($data, $data['business_id']);
            
            //update business photo counter
            $this->BusinessPhoto->updateBusinessPhotoCounter($data['business_id']);
            
            //send notification to followers
            if($business != null && $business['Business']['status'] == BUSINESS_STATUS_APPROVED)
            {
                $this->Business->sendBusinessNotification(
                    $business['Business']['id'], 
                    'business_add_photos', 
                    MooCore::getInstance()->getViewer(true),
                    $business['Business']['moo_url']
                );
            }

            $this->_jsonSuccess(__d('business', 'Photos have been saved'), true, array(
                'location' => $this->url_dashboard.'business_photos/'.$data['business_id']
            ));
        }
    }
    
    private function save_photo_item($data, $business_id)
    {
        $business = $this->Business->getOnlyBusiness($business_id);
        $business_package = $this->Business->getBusinessPackage($business_id);

        //delete
        if(!empty($data['photo_delete_id']))
        {
            $this->BusinessPhoto->deletePhotoList($data['photo_delete_id']);
        }
        
        //update
        $total_photos = 0;
        if(!empty($data['photo_caption_exist']))
        {
            foreach($data['photo_caption_exist'] as $photo_id => $caption)
            {
                if((int)$photo_id > 0)
                {
                    $enable = 0;
                    $total_photos++;
                    if($total_photos <= ($business_package['photo_number']))
                    {
                        $enable = 1;
                    }
                    $this->BusinessPhoto->updateAll(array(
                        'BusinessPhoto.caption' => "'".str_replace("'", "&#8216;", $caption)."'",
                    ), array(
                        'BusinessPhoto.id' => $photo_id
                    ));
                }
            }
        }
        
        //new
        if(!empty($data['photo_filename']))
        {
            $photo_ids = array();
            foreach($data['photo_filename'] as $k => $filename)
            {
                $enable = 0;
                if($k <= ($business_package['photo_number'] - 1 - $total_photos))
                {
                    $enable = 1;
                }
                $this->BusinessPhoto->create();
                $this->BusinessPhoto->set(array(
                    'target_id' => $business['Business']['album_id'],
                    'type' => 'Photo_Album',
                    'user_id' => $business['Business']['user_id'],
                    'thumbnail' => $filename,
                    'caption' => $data['photo_caption'][$k],
                ));
                $this->BusinessPhoto->save();
                /*if($k <= ($business_package['photo_number'] - 1 - $total_photos))
                {
                    $photo_ids[] = $this->BusinessPhoto->id;
                }*/
                $photo_ids[] = $this->BusinessPhoto->id;
            }
            if($photo_ids != null)
            {
                $this->BusinessPhoto->savePhotoActivity($business_id, $photo_ids);
            }
        }
        
        //update album photo counter
        $this->BusinessPhoto->updateAlbumPhotoCounter($business['Business']['album_id']);
    }
    
    public function delete_photo()
    {
        $data = $this->request->data;
        if(!$this->Business->isBusinessExist($data['business_id'], MooCore::getInstance()->getViewer(true)))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if(empty($data['delete_photo']))
        {
            $this->_jsonError(__d('business', 'Please select at least a photo'));
        }
        else
        {
            foreach($data['delete_photo'] as $business_photo_id)
            {
                $this->BusinessPhoto->deletePhoto($business_photo_id);
            }
            $this->_jsonSuccess(__d('business', 'Seleted photos have been deleted'), true, array(
                'location' => $this->url_dashboard.'business_photos/'.$data['business_id']
            ));
        }
    }
    
    public function load_business_photos($business_id)
    {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $photos = $this->BusinessPhoto->getPhotos($business_id, $page);
        
        $this->set(array(
            'photos' => $photos,
            'more_photo_url' => '/business_photo/load_business_photos/'.$business_id.'/page:'.($page + 1),
        ));
        $this->render('/Elements/lists/photos_list');
    }
    
    public function upload_cover_photo($business_id)
    {
        $this->autoRender = false;
        $allowedExtensions = explode(',', BUSINESS_EXT_PHOTO);
        App::import('Vendor', 'qqFileUploader');
        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        if (!file_exists( BUSINESS_COVER_FILE_PATH ))
        {
            mkdir( BUSINESS_COVER_FILE_PATH, 0755, true );
            file_put_contents( WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . 'index.html', '' );
        }

        $result = $uploader->handleUpload(BUSINESS_COVER_FILE_PATH);

        if (!empty($result['success'])) 
        {
            //resize image
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create(BUSINESS_COVER_FILE_URL.$result['filename']);
            $photo->adaptiveResize(BUSINESS_COVER_IMAGE_WIDTH, BUSINESS_COVER_IMAGE_HEIGHT)->save(BUSINESS_COVER_FILE_PATH.'/'.BUSINESS_COVER_IMAGE_WIDTH.'_'.$result['filename']);

            $result['thumb'] = $this->request->base.'/'.BUSINESS_COVER_FILE_URL.$result['filename'];
            $result['cover'] = $this->request->base.'/'.BUSINESS_COVER_FILE_URL . BUSINESS_COVER_IMAGE_WIDTH . '_' . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    public function save_cover()
    {
        $this->autoRender = false;
        $uid = $this->Auth->user('id');
        $business_id = $_POST['id'];

        if (!$uid || !$business_id || !$_POST['image_name'])
            return;

        $cUser = $this->_getUser();
        if(($cUser != null && $cUser['Role']['is_admin']) || 
            $this->Business->isBusinessOwner($business_id) ||
            $this->BusinessAdmin->isBusinessAdmin($business_id, MooCore::getInstance()->getViewer(true)))
        {

            $thumbname = $_POST['image_name'];

            if ($_POST['w'] && $_POST['h'])
            {
                $ext = $this->_getExtension($_POST['image_name']);
                $thumbname = md5(microtime()) . '.' . $ext;

                App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

                $thumb = PhpThumbFactory::create(BUSINESS_COVER_FILE_URL.$_POST['image_name'], array('jpegQuality' => 100));
                $thumbloc = WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . BUSINESS_COVER_IMAGE_WIDTH . '_' .$thumbname;

                $current_dimension = $thumb->getCurrentDimensions();
                $ratio_w = $current_dimension['width'] / $_POST['jcrop_width'] ;
                $ratio_h = $current_dimension['height'] / $_POST['jcrop_height'] ;

                $_POST['w'] = $_POST['w'] * $ratio_w;
                $_POST['x'] = $_POST['x'] * $ratio_w;
                $_POST['h'] = $_POST['h'] * $ratio_h;
                $_POST['y'] = $_POST['y'] * $ratio_h;

                $thumb->crop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h'])->resize(BUSINESS_COVER_IMAGE_WIDTH, BUSINESS_COVER_IMAGE_HEIGHT)->save($thumbloc);

                $file_old = WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . $_POST['image_name'];
                $file_old_cover = WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . BUSINESS_COVER_IMAGE_WIDTH . '_' .$_POST['image_name'];

                if (file_exists($file_old) && file_exists($file_old_cover))
                {
                    unlink($file_old_cover);
                    rename($file_old, WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . $thumbname);
                }
            }

            //delete old file
            $business = $this->Business->findById($business_id);
            if ($business['Business']['cover'] && $business['Business']['cover'] != $thumbname && file_exists(WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . $business['Business']['cover']))            
            {
                unlink(WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . $business['Business']['cover']);
                unlink(WWW_ROOT . BUSINESS_COVER_FILE_PATH . DS . BUSINESS_COVER_IMAGE_WIDTH . '_' . $business['Business']['cover']);
            }

            $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
            $objectModel->destroy($business_id,'business_covers');

            //save cover to database
            $this->Business->id = $business_id;
            $this->Business->save( array( 'cover' => $thumbname ) );

            $result['thumb'] =$this->request->base.'/'.BUSINESS_COVER_FILE_URL . BUSINESS_COVER_IMAGE_WIDTH . '_' . $thumbname;

            echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        } else {
            $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
        }
    }

    public function change_default_photo($id)
    {
        $cUser = $this->_getUser();
        if(($cUser != null && $cUser['Role']['is_admin']) || 
            $this->Business->isBusinessOwner($id) ||
            $this->BusinessAdmin->isBusinessAdmin($id, MooCore::getInstance()->getViewer(true)))
        {
            $business = $this->Business->findById($id);
            if(!empty($business)){
                $this->Business->removeCoverFile($business['Business']);
            }

            $this->autoRender = false;
            $this->Business->id = $id;
            if ($this->Business->save( array( 'cover' => null )))
            {
                $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
                $result['thumb'] = $businessHelper->defaultCoverUrl();
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
            }
        } else {
            $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
        }
    }

    public function remove_image_null()
    {
        $this->autoRender = false;
        $data = $this->request->data;
        if ($data['filename'])
        {
            unlink(WWW_ROOT . DS . BUSINESS_COVER_FILE_PATH . DS . $data['filename']);
            unlink(WWW_ROOT . DS . BUSINESS_COVER_FILE_PATH . DS . BUSINESS_COVER_IMAGE_WIDTH . '_' .$data['filename']);
        }
    }

    public function _getExtension($filename = null)
	{
        $this->autoRender = false;
		$tmp = explode('.', $filename);
		$re = array_pop($tmp);
		return $re;
	}
}
