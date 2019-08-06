<?php

class StickersController extends StickerAppController {

    public $components = array('Paginator');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->admin_link = $this->request->base . '/admin/sticker/stickers/';
        $this->admin_url = '/admin/sticker/stickers/';
        $this->set('admin_link', $this->admin_link);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Sticker.Sticker');
        $this->loadModel('Sticker.StickerImage');
        $this->loadModel('Sticker.StickerCategory');
    }

    ////////////////////////////////////////////////////////backend////////////////////////////////////////////////////////
    function admin_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : array();

        $stickers = $this->Sticker->loadAdminPaging($this, $search);
        $this->set(array(
            'stickers' => $stickers,
            'search' => $search,
            'title_for_layout' => __d('sticker', 'Stickers')
        ));
    }

    public function admin_create($id = null)
    {
        $parent_id = !empty($this->request->query['sub']) ? $this->request->query['sub'] : 0;
        if (!empty($id) && !$this->Sticker->isStickerExist($id))
        {
            $this->_redirectError(__d('sticker', 'Sticker not found'), $this->admin_url);
        }
        else
        {
            $translate = array();
            if (!empty($id))
            {
                $stickerHelper = MooCore::getInstance()->getHelper('Sticker_Sticker');
                $sticker = $this->Sticker->getDetail($id);
                $sticker = $sticker['Sticker'];
                $sticker_images = $this->StickerImage->loadImages(array(
                    'sticker_id' => $id
                ));
                foreach($sticker_images as $k => $sticker_image)
                {
                    $sticker_images[$k]['StickerImage']['url'] = $stickerHelper->getStickerImage($sticker_image);
                }
                $translate = $this->Sticker->loadListTranslate($id);
            }
            else
            {
                $sticker = $this->Sticker->initFields();
                $sticker = $sticker['Sticker'];
                $sticker_images = array();
            }
            
            //category list
            $category_list = $this->StickerCategory->loadList();

            $this->set(array(
                'sticker' => $sticker,
                'sticker_images' => $sticker_images,
                'category_list' => $category_list,
                'translate' => $translate,
                'title_for_layout' => __d('sticker', 'Stickers')
            ));
        }
    }

    public function admin_save()
    {
        $this->autoRender = false;
        $data = $this->request->data;
        if ($this->Sticker->isStickerExist($data['id']))
        {
            $this->Sticker->id = $data['id'];
        }
        else
        {
            $data['ordering'] = $this->generateOrdering('Sticker', true);
        }
        $this->Sticker->set($data);
        $this->_validateData($this->Sticker);
        if(empty($data['image_filename']))
        {
            $this->_jsonError(__d('sticker', 'Please add at least an image'));
        }

        if ($this->Sticker->save())
        {
            $id = $this->Sticker->id;
            
            //save icon
            $this->save_icon($data['icon'], $id);
            
            //save image
            $this->save_image($data, $id);
            
            //save image for app
            $this->save_app_image($data, $id);

            //show message
            $redirect = $this->admin_link;
            if ($data['save_type'] == 1)
            {
                $redirect = $this->admin_link . 'create/' . $id;
            }
            $this->_jsonSuccess(__d('sticker', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }
        $this->_jsonError(__d('sticker', 'Something went wrong, please try again'));
    }
    
    private function save_icon($filename, $sticker_id)
    {
        if(!file_exists(STICKER_UPLOAD_PATH_TEMP . DS . $filename))
        {
            return;
        }
        App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
        $photo = PhpThumbFactory::create(STICKER_UPLOAD_PATH_TEMP . DS . $filename);

        //resize
        $path = STICKER_UPLOAD_PATH_STICKER.DS.$sticker_id;
        if(!is_dir($path))
        {
            $this->_prepareDir($path);
        }
        $path = $path.DS.$filename;
        $photo->adaptiveResize(STICKER_STICKER_ICON_WIDTH, STICKER_STICKER_ICON_HEIGHT)->save($path);
    }
    
    private function save_image($data, $sticker_id)
    {
        if(empty($data['image_filename']))
        {
            return;
        }
        $image_list = $this->StickerImage->loadImageList(array(
            'sticker_id' => $sticker_id
        ), 'id', 'id');
        $except_id = array();
        $sticker_dir = STICKER_UPLOAD_PATH_STICKER.DS.$sticker_id;
        if(!is_dir($sticker_dir))
        {
            $this->_prepareDir($sticker_dir);
        }
        
        foreach($data['image_filename'] as $k => $filename)
        {
            $source = STICKER_UPLOAD_PATH_TEMP . DS . $filename;
            $des = $sticker_dir.DS.$filename;
            
            
            if(file_exists($source))
            {
                rename($source, $des);
            }
            
            //save
            $this->StickerImage->create();
            if(!empty($data['image_id'][$k]))
            {
                $this->StickerImage->id = $data['image_id'][$k];
                $except_id[] = $data['image_id'][$k]; 
            }
            
            //image size
            list($img_width, $img_height, $img_type, $img_attr) = getimagesize($des);
            
            $this->StickerImage->save(array(
                'sticker_sticker_id' => $sticker_id,
                'sticker_category_id' => !empty($data['image_category_id'][$k]) ? $data['image_category_id'][$k] : "",
                'filename' => $filename,
                'width' => $img_width,
                'height' => $img_height,
                'block' => !empty($data['block'][$k]) ? $data['block'][$k] : 1,
                'quantity' => !empty($data['quantity'][$k]) ? $data['quantity'][$k] : 1,
                'enabled' => in_array($k, $data['image_enable']) ? 1 : 0,
                'ordering' => $k + 1,
                'animation_interval' => !empty($data['animation_interval']) ? $data['animation_interval'] : 0
            ));
        }
        foreach($image_list as $id)
        {
            if($except_id != null && !in_array($id, $except_id))
            {
                $this->StickerImage->delete($id);
            }
        }
    }
    
    private function save_app_image($data, $sticker_id)
    {
        if(empty($data['image_filename']))
        {
            return;
        }
        
        foreach($data['image_filename'] as $k => $filename)
        {
            //prepare file
            $source = STICKER_UPLOAD_PATH_STICKER.DS.$sticker_id.DS.$filename;
            $des = sprintf(STICKER_UPLOAD_PATH_STICKER_APP, $sticker_id);
            $block = $data['block'][$k];
            if(!file_exists($source))
            {
                continue;
            }
            if(!is_dir($des))
            {
                $this->_prepareDir($des);
            }
            $des .= DS.$filename;
            
            if($block == 1)
            {
                copy($source, $des);
                continue;
            }
            
            //calculate crop size
			$offset = 80;
			App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create($source);
			$photo->crop(0, 0, $offset, $offset);
			$photo->save($des, "PNG");
        }
    }
    
    public function admin_upload_image()
    {
        $this->autoRender = false;
        // save temp image
        $allowedExtensions = explode(',', STICKER_IMAGE_EXTENSION);
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(STICKER_UPLOAD_PATH_TEMP);
        if (!empty($result['success']))
        {
            $url = FULL_BASE_URL . $this->request->webroot . '/'.STICKER_UPLOAD_URL_TEMP . '/' . $result['filename'];
            $result['url'] = $url;
            
            //image size
            list($img_width, $img_height, $img_type, $img_attr) = getimagesize($url);
            $result['width'] = $img_width;
            $result['height'] = $img_height;
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }
    
    public function admin_upload_icon()
    {
        $this->autoRender = false;
        // save temp image
        $allowedExtensions = explode(',', STICKER_IMAGE_EXTENSION);
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(STICKER_UPLOAD_PATH_TEMP);
        if (!empty($result['success']))
        {
            $url = FULL_BASE_URL . $this->request->webroot . '/'.STICKER_UPLOAD_URL_TEMP . '/' . $result['filename'];
            $result['url'] = $url;
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    public function admin_enable()
    {
        $this->active($this->request->data, 1, 'enabled');
    }

    public function admin_disable()
    {
        $this->active($this->request->data, 0, 'enabled');
    }

    private function active($data, $value, $task)
    {
        if (!empty($data['cid']))
        {
            foreach ($data['cid'] as $id)
            {
                if ($this->Sticker->isStickerExist($id))
                {
                    $this->Sticker->activeField($id, $task, $value);
                }
            }
        }
        $this->_redirectSuccess(__d('sticker', 'Successfully updated'), $this->referer());
    }

    public function admin_ordering()
    {
        $data = $this->request->data;
        if (!empty($data['cid']))
        {
            foreach ($data['cid'] as $k => $id)
            {
                $this->Sticker->saveOrdering($id, $data['ordering'][$k]);
            }
        }
        $this->_redirectSuccess(__d('sticker', 'Successfully updated'), $this->referer());
    }

    public function admin_delete()
    {
        $data = $this->request->data;
        $redirect = $this->admin_url;
        if (!empty($data['cid']))
        {
            foreach ($data['cid'] as $id)
            {
                /*if ($this->Sticker->isContainActivity($id))
                {
                    $this->_redirectError(__d('sticker', 'Can not delete this sticker! Please delete related activites first.'), $this->referer());
                }
                else
                {*/
                    $this->Sticker->deleteSticker($id);
                //}
            }
        }
        $this->_redirectSuccess(__d('sticker', 'Successfully deleted'), $redirect);
    }
    
    public function admin_sample()
    {
        
    }

    ////////////////////////////////////////////////////////front end////////////////////////////////////////////////////////
    public function sticker_modal()
    {
        $item_type = !empty($this->request->query['item_type']) ? $this->request->query['item_type'] : "";
        $item_id = isset($this->request->query['item_id']) ? $this->request->query['item_id'] : "";
        $photo_theater = !empty($this->request->query['photo_theater']) ? $this->request->query['photo_theater'] : "";
        $stickers = $this->Sticker->loadSticker(array(
            'enabled' => 1
        ));
        $categories = $this->StickerCategory->loadAll(array(
            'enabled' => 1
        ));
        
        $this->set(array(
            'categories' => $categories,
            'stickers' => $stickers,
            'item_type' => $item_type,
            'item_id' => $item_id,
            'photo_theater' => $photo_theater
        ));
    }
    
    public function sticker_modal_images($id, $renderView = true)
    {
        $sticker_images = $this->StickerImage->loadImages(array(
            'enabled' => 1,
            'sticker_id' => $id
        ));
        
        $this->set(array(
            'sticker_images' => $sticker_images
        ));
        if($renderView)
        {
            $this->autoRender = false;
            $this->render('Sticker.Elements/list/sticker_animation_list');
        }
    }
    
    public function sticker_modal_search()
    {
        $categories = $this->StickerCategory->loadAll(array(
            'enabled' => 1
        ));
        
        $this->set(array(
            'categories' => $categories
        ));
        $this->render('Sticker.Elements/sticker_modal_search');
    }
    
    public function sticker_modal_recent($renderView = true)
    {
        $sticker_log = $this->Sticker->loadStickerLog(MooCore::getInstance()->getViewer(true));
        if(empty($sticker_log['StickerLog']['stickers']))
        {
            $sticker_images = array();
        }
        else
        {
            $sticker_images = $this->StickerImage->loadImages(array(
                'enabled' => 1,
                'ids' => $sticker_log['StickerLog']['stickers']
            ));
        }
        
        $this->set(array(
            'sticker_images' => $sticker_images
        ));
        if($renderView)
        {
            $this->render('Sticker.Elements/list/sticker_animation_list');
        }
    }
    
    public function search($renderView = true)
    {
        $sticker_images = array();
        if(!empty($this->request->query['keyword']))
        {
            $keyword = $this->request->query['keyword'];
            $sticker_cats = $this->StickerCategory->loadList(array(
                'enabled' => 1,
                'keyword' => $keyword
            ));
            if($sticker_cats != null)
            {
                $sticker_images = $this->StickerImage->loadImages(array(
                    'enabled' => 1,
                    'enabled_sticker' => 1,
                    'sticker_category_id' => array_keys($sticker_cats)
                ));
            }
        }
        
        $this->set(array(
            'sticker_images' => $sticker_images
        ));
        if($renderView)
        {
            $this->render('Sticker.Elements/list/sticker_animation_list');
        }
    }
}
