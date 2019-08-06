<?php

class StickerCategoriesController extends StickerAppController {

    public $components = array('Paginator');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->admin_link = $this->request->base . '/admin/sticker/categories/';
        $this->admin_url = '/admin/sticker/categories/';
        $this->set('admin_link', $this->admin_link);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Sticker.StickerCategory');
    }

    ////////////////////////////////////////////////////////backend admin////////////////////////////////////////////////////////
    function admin_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : array();

        $categories = $this->StickerCategory->loadAdminPaging($this, $search);
        $this->set(array(
            'categories' => $categories,
            'search' => $search,
            'title_for_layout' => __d('sticker', 'Categories')
        ));
    }

    public function admin_create($id = null)
    {
        $parent_id = !empty($this->request->query['sub']) ? $this->request->query['sub'] : 0;
        if (!empty($id) && !$this->StickerCategory->isStickerCategoryExist($id))
        {
            $this->_redirectError(__d('sticker', 'Category not found'), $this->admin_url);
        }
        else
        {
            $translate = array();
            if (!empty($id))
            {
                $category = $this->StickerCategory->getDetail($id);
                $category = $category['StickerCategory'];
                $translate = $this->StickerCategory->loadListTranslate($id);
            }
            else
            {
                $category = $this->StickerCategory->initFields();
                $category = $category['StickerCategory'];
            }

            $this->set(array(
                'category' => $category,
                'translate' => $translate,
                'title_for_layout' => __d('sticker', 'Categories')
            ));
        }
    }

    public function admin_save()
    {
        $this->autoRender = false;
        $data = $this->request->data;
        if ($this->StickerCategory->isStickerCategoryExist($data['id']))
        {
            $this->StickerCategory->id = $data['id'];
        }
        else
        {
            $data['ordering'] = $this->generateOrdering('StickerCategory', true);
        }
        $this->StickerCategory->set($data);
        $this->_validateData($this->StickerCategory);

        if ($this->StickerCategory->save())
        {
            $id = $this->StickerCategory->id;
            
            //save icon
            $this->save_icon($data['icon']);

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
    
    private function save_icon($filename)
    {
        if(file_exists(STICKER_UPLOAD_PATH_TEMP . DS . $filename))
        {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create(STICKER_UPLOAD_PATH_TEMP . DS . $filename);

            //resize
            $path = STICKER_UPLOAD_PATH_CATEGORY.DS.$filename;
            $photo->adaptiveResize(STICKER_CATEGORY_ICON_WIDTH, STICKER_CATEGORY_ICON_HEIGHT)->save($path);
        }
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
                if ($this->StickerCategory->isStickerCategoryExist($id))
                {
                    $this->StickerCategory->activeField($id, $task, $value);
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
                $this->StickerCategory->saveOrdering($id, $data['ordering'][$k]);
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
                $this->StickerCategory->delete($id);
            }
        }
        $this->_redirectSuccess(__d('sticker', 'Successfully deleted'), $redirect);
    }

}
