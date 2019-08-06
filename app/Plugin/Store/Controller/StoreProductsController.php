<?php 
class StoreProductsController extends StoreAppController
{	
    public $components = array('Paginator');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->url = STORE_MANAGER_URL.'products/';
        $this->set('url', $this->url);
        $this->admin_url = $this->request->base.'/admin/store/store_products/';
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.StoreCategory');
        $this->loadModel('Store.StoreProducer');
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.StoreProductWishlist');
        $this->loadModel('Store.StoreProductComment');
        $this->loadModel('Store.StoreProductImage');
        $this->loadModel('Store.StoreProductAttribute');
        $this->loadModel('Store.StorePackage');
        $this->loadModel('Store.StoreDigitalProduct');
        $this->loadModel('Store.StoreProductVideo');
        $this->loadModel('Video.Video');
        $this->loadModel('Category');
        $this->loadModel('Photo.Photo');
    }
    
    ////////////////////////////////////////////////////////admin////////////////////////////////////////////////////////
    public function admin_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : null;

        //load products
        $products = $this->StoreProduct->loadManagerPaging($this, $search, 20, null, true);
        
        //load product categories
        $storeCats = $this->StoreCategory->loadStoreCategoryTree();

        $this->set(array(
            'title_for_layout' => __d('store', 'Products'),
            'products' => $products,
            'search' => $search,
            'storeCats' => $storeCats,
            'active_menu' => 'manage_products'
        ));
    }
    
    public function admin_approve()
    {
        $this->admin_active($this->request->data, 1, 'approve');
    }
    
    public function admin_disapprove()
    {
        $this->admin_active($this->request->data, 0, 'approve');
    }
    
    /*public function admin_featured()
    {
        $this->admin_active($this->request->data, 1, 'featured');
    }*/
    
    public function admin_featured_dialog($id)
    {
        $store_product = $this->StoreProduct->loadProductDetail($id);
        $this->set(array(
            'id' => $id,
            'store_product' => $store_product
        ));
        $this->render('Store.Elements/feature_day_dialog');
    }
    
    public function admin_save_feature()
    {
        $data = $this->request->data;
        $id = $data['id'];
        if(!$this->StoreProduct->checkProductExist($id))
        {
            $this->_jsonError(__d('store', 'StoreProduct not found'));
        }
        else if(!empty($data['unlimited_feature']))
        {
            if($this->StoreProduct->updateAll(array(
                'StoreProduct.featured' => 1,
                'StoreProduct.unlimited_feature' => 1,
                'StoreProduct.sent_expiration_email' => 1,
            ), array(
                'StoreProduct.id' => $id
            )))
            {
                //send notification
                $this->StoreProduct->sendFeaturedNotification($id, 1);
                
                $this->_jsonSuccess(__d("store", "Successfully set feature"), true);
            }
            $this->_jsonError(__d('store', 'Cannot set feature, please try again!'));
        }
        else if(empty ($data['feature_day']))
        {
            $this->_jsonError(__d('store', 'Day is required'));
        }
        else
        {
            $expiration_date = date('Y-m-d h:i:s', strtotime(date('Y-m-d H:i:s').' + '.$data['feature_day'].' day'));
            if($this->StoreProduct->updateAll(array(
                'StoreProduct.featured' => 1,
                'StoreProduct.unlimited_feature' => 0,
                'StoreProduct.sent_expiration_email' => 0,
                'StoreProduct.feature_expiration_date' => "'" . $expiration_date . "'"
            ), array(
                'StoreProduct.id' => $id
            )))
            {
                //send notification
                $this->StoreProduct->sendFeaturedNotification($id, 1);
                
                $this->_jsonSuccess(__d("store", "Successfully set feature"), true);
            }
            $this->_jsonError(__d('store', 'Cannot set feature, please try again!'));
        }
    }
    
    public function admin_unfeatured()
    {
        $this->admin_active($this->request->data, 0, 'featured');
    }
    
    private function admin_active($data, $value = 1, $task)
    {
        $count = 0;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreProduct->checkProductExist($id))
                {
                    $this->StoreProduct->activeField($id, $task, $value, true);
                    
                    if($task == 'featured')
                    {
                        //send notification
                        $this->StoreProduct->sendFeaturedNotification($id, $value);
                    }
                    else if($task == 'approve')
                    {
                        switch($value)
                        {
                            case 0:
                                $this->StoreProduct->changeProductActivityPrivacy($id, PRIVACY_ME);
                                break;
                            case 1:
                                $this->StoreProduct->changeProductActivityPrivacy($id, PRIVACY_PUBLIC);
                                break;
                        }
                        
                        //send notification
                        $this->StoreProduct->sendApproveNotification($id, $value);
                    }
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function admin_delete()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreProduct->checkProductExist($id))
                {
                    $this->StoreProduct->deleteProduct($id);
                }
                else 
                {
                    $this->_redirectError(__d('store', 'Product not found'), $this->referer());
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully deleted'), $this->referer());
    }
    
    ////////////////////////////////////////////////////////backend////////////////////////////////////////////////////////
    public function manager_index()
    {
        //search
        $search = !empty($this->request->query) ? $this->request->query : null;
        
        //load products
        $products = $this->StoreProduct->loadManagerPaging($this, $search);
        
        //load product categories
        $storeCats = $this->StoreCategory->loadStoreCategoryTree();
        
        //feature product package
        $package = $this->StorePackage->loadStorePackage(1);

        $this->set(array(
            'products' => $products,
            'package' => $package,
            'search' => $search,
            'storeCats' => $storeCats,
            'active_menu' => 'manage_products',
            'title_for_layout' => __d('store', "Manage Products")
        ));
    }
    
    public function manager_create($id = null)
    {
        if(!empty($id) && !$this->StoreProduct->checkProductExist($id))
        {
            $this->_redirectError(__d('store', 'Product not found'), $this->url.'manage');
        }
        else 
        {
            //load product
            if (!empty($id))
            {
                $product = $this->StoreProduct->loadProductDetail($id, true, false, '', '', '', true);
                $productImages = $product['StoreProductImage'];
                usort($productImages, function($a, $b) {
                    return $a['ordering'] - $b['ordering'];
                });
                $product = $product['StoreProduct'];
                
                //load product video
                $productVideos = $this->StoreProductVideo->loadProductVideo($id);
				
				//cat path
                $cat_path = $this->StoreCategory->getCategoryPath($product['store_category_id']);
            }
            else 
            {
                $product = $this->StoreProduct->initFields();
                $product = $product['StoreProduct'];
                $productImages = $productVideos = $cat_path = null;
            }

            //load producer
            $producers = $this->StoreProducer->loadListProducer();
            
            $this->set(array(
                'producers' => $producers,
                'product' => $product,
                'storeCats' => $this->StoreCategory->loadStoreCategoryTree(),
                'productImages' => $productImages,
                'productVideos' => $productVideos,
				'cat_path' => $cat_path,
                'active_menu' => 'create_product',
                'title_for_layout' => !empty($id) ? __d('store', "Edit Product") : __d('store', "Create Product")
            ));
        }
    }
    
    function manager_save()
    {
        $data = $this->request->data;
        $data_images = !empty($data['images']) ? $data['images'] : null;
        $data_video = !empty($data['videos']) ? $data['videos'] : null;
        unset($data['images']);
        $isEdit = false;

        if((int)$data['id'] > 0 && $this->StoreProduct->checkProductExist($data['id']))
        {
            $this->StoreProduct->id = $data['id'];
            $isEdit = true;
        }

        $data['price'] = !empty($data['price']) ? $data['price'] : 0;
        $data['alias'] = !empty($data['name']) ? Inflector::slug(strtolower(trim($data['name'])), '-') : '';
        if(!$isEdit)
        {
            $data['approve'] = 0;
            if(Configure::read('Store.auto_approve_product'))
            {
                $data['approve'] = 1;
            }
        }
        $this->StoreProduct->set($data);
        $this->_validateData($this->StoreProduct);
        
        if($data['product_type'] == STORE_PRODUCT_TYPE_DIGITAL && empty($data['digital_file']))
        {
            $this->_jsonError(__d('store', 'Please upload file'));
        }
        else if($data['product_type'] == STORE_PRODUCT_TYPE_LINK && empty($data['product_link']))
        {
            $this->_jsonError(__d('store', 'Please input product link'));
        }
        
        if($this->StoreProduct->save($data))
        {
            //get product id
            $product_id = $this->StoreProduct->id;
            
            //save product images
            $this->saveProductImages($product_id, $data_images, $data['alias']);
            
            //save product videos
            $this->saveProductVideos($product_id, $data_video);
            
            //clear old attributes
            $this->StoreProductAttribute->deleteByProductId($product_id);
            
            //save attributes
            if(!empty($data['attribute_id']))
            {
                $this->saveAttributes($product_id, $data);
            }
            
            //save attribute force to buy
            if(!empty($data['attribute_buy']))
            {
                $this->saveAttributes($product_id, $data['attribute_buy'], 1);
            }
            
            //show activity
            if(!$isEdit)
            {
                $activityPrivacy = PRIVACY_EVERYONE;
                if(!Configure::read('Store.auto_approve_product'))
                {
                    $activityPrivacy = PRIVACY_ME;
                }
                $this->StoreProduct->createProductActivity(array(
                    'product_id' => $this->StoreProduct->id,
                    'privacy' => $activityPrivacy,
                    'content' => ''
                ));
            }

            //show message
            $redirect = $this->url.'';
            if($data['save_type'] == 1)
            {
                $redirect = $this->url.'create/'.$product_id;
            }
            if(!$isEdit && !Configure::read('Store.auto_approve_product'))
            {
                $this->_jsonSuccess(__d('store', 'Successfully saved. Your product will be published after approved.'), true, array(
                    'location' => $redirect
                ));
            }
            else 
            {
                $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                    'location' => $redirect
                ));
            }
        }
        else 
        {
            $this->_jsonError(__d('store', 'Something went wrong, please try again'));
        }
    }
    
    private function saveProductImages($product_id, $data, $product_alias)
    {
        $cond = array(
            'StoreProductImage.product_id' => $product_id
        );

        if(!empty($data['image_id']))
        {
			$except_ids = array();
            foreach($data['image_id'] as $image_id)
            {
                if(!empty($image_id))
                {
                    $except_ids[] = $image_id;
                }
            }
			
            if($except_ids != null)
            {
                $cond[] = 'StoreProductImage.id NOT IN('. implode(',', $except_ids).')';
            }
        }
        
        $this->StoreProductImage->deleteAll($cond);
        if(!empty($data))
        {
            $count = -1;
            foreach($data['filename'] as $k => $filename)
            {
                $count++;
                $enable = $is_main = 0;
                $path = $data['path'][$k];
                
                //main image
                if(isset($data['is_main']) && $data['is_main'] == $k)
                {
                    $is_main = 1;
                }
                else if(empty($data['is_main']) && $count == 0)
                {
                    $is_main = 1;
                }
                
                //enable
                if(!empty($data['enable']) && in_array($k, $data['enable']))
                {
                    $enable = 1;
                }
                
                //save
                if(!in_array($data['image_id'][$k], $except_ids))
                {
                    $this->StoreProductImage->create();
                    $this->StoreProductImage->save(array(
                        'product_id' => $product_id,
                        'path' => $path,
                        'filename' => $filename,
                        'is_main' => $is_main,
                        'enable' => $enable,
                        'ordering' => $count
                    ));

                    $new_filename = $product_alias.'_'.$this->StoreProductImage->id;
                    $new_filename = $this->StoreProductImage->renameImage($filename, $path, $new_filename);
                    $this->StoreProductImage->updateAll(array(
                        'filename' => "'".$new_filename."'"
                    ), array(
                        'StoreProductImage.id' => $this->StoreProductImage->id
                    ));
                }
                else
                {
                    $this->StoreProductImage->create();
                    $this->StoreProductImage->id = $data['image_id'][$k];
                    $this->StoreProductImage->save(array(
                        'is_main' => $is_main,
                        'enable' => $enable,
                        'ordering' => $count
                    ));
                }
            }
        }
    }
    
    private function saveProductVideos($product_id, $data)
    {
        $cond = array(
            'StoreProductVideo.product_id' => $product_id
        );

        $except_ids = array();
        if(!empty($data['product_video_id']))
        {
            foreach($data['product_video_id'] as $video_id)
            {
                if(!empty($video_id))
                {
                    $except_ids[] = $video_id;
                }
            }
			
            if($except_ids != null)
            {
                $cond[] = 'StoreProductVideo.id NOT IN('. implode(',', $except_ids).')';
            }
        }
        
        $this->StoreProductVideo->deleteAll($cond);
        if(!empty($data['video_id']))
        {
            $count = 0;
            foreach($data['video_id'] as $k => $video_id)
            {		
                $count++;
                $enable = 0;
                
                //enable
                if(!empty($data['enable']) && in_array($k, $data['enable']))
                {
                    $enable = 1;
                }
                
                //save
                $this->StoreProductVideo->create();
                if(in_array($data['product_video_id'][$k], $except_ids))
                {
                    $this->StoreProductVideo->id = $data['product_video_id'][$k];
                    $this->StoreProductVideo->save(array(
                        'enable' => $enable,
                        'ordering' => $count
                    ));
                }
                else
                {
                    $this->StoreProductVideo->save(array(
                        'product_id' => $product_id,
                        'video_id' => $video_id,
                        'enable' => $enable,
                        'ordering' => $count
                    ));
                }
            }
        }
    }
    
    private function saveAttributes($product_id, $data, $buy = 0)
    {
        if(!empty($data['attribute_id']))
        {
            foreach($data['attribute_id'] as $k => $attribute_id)
            {
                $this->StoreProductAttribute->create();
                $this->StoreProductAttribute->saveAll(array(
                    'product_id' => $product_id,
                    'attribute_id' => $attribute_id,
                    'force_to_buy' => 1,
                    'plus' => !empty($data['plus'][$k]) ? $data['plus'][$k] : 0,
                    'attribute_price' => !empty($data['attribute_price'][$k]) ? $data['attribute_price'][$k] : 0
                ));
            }
        }
    }

    public function manager_ordering()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $k => $id)
            {
                $this->StoreProduct->saveOrdering($id, $data['ordering'][$k]);
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function manager_delete()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreProduct->checkProductExist($id, '', Configure::read('store.store_id')))
                {
                    $this->StoreProduct->deleteProduct($id, Configure::read('store.store_id'));
                }
                else 
                {
                    $this->_redirectError(__d('store', 'Product not found'), $this->referer());
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully deleted'), $this->referer());
    }
    
    public function manager_enable()
    {
        $this->active($this->request->data, 1, 'enable');
    }
    
    public function manager_disable()
    {
        $this->active($this->request->data, 0, 'enable');
    }
    
    public function manager_out_of_stock()
    {
        $this->active($this->request->data, 1, 'out_of_stock');
    }
    
    public function manager_unout_of_stock()
    {
        $this->active($this->request->data, 0, 'out_of_stock');
    }
    
    public function manager_product_short_list()
    {
            //search
        $search = !empty($this->request->data) ? $this->request->data : null;
        $except_id = !empty($search_data['except_id']) ? $search_data['except_id'] : '';

        //load products
        $products = $this->StoreProduct->loadManagerPaging($this, $search, 10, $except_id);

        $this->set(array(
            'products' => $products,
            'search' => $search,
        ));
        $this->render('Store.Elements/list/product_short_list');
    }
    
    public function manager_create_video()
    {
    }
    
    public function manager_video_validate()
    {
        $this->autoRender = false;
        $this->_checkPermission( array( 'confirm' => true ) );
        
        $video = $this->Video->fetchVideo( $this->request->data['source'], $this->request->data['url'] );

        if ( isset($video['errorMsg']) && $video['errorMsg'] ){
            echo json_encode(array('error'=>'<span style="color:red">' . $video['errorMsg'] . '</span>'));
        }
        if ( empty( $video ) ){
            echo json_encode(array('error' => '<span style="color:red">' . __('Invalid URL. Please try again') . '</span>' ) );
        }
    }
    
    public function manager_video_fetch($id = null)
    {
        $video = $this->Video->fetchVideo($this->request->data['source'], $this->request->data['url']);
        if (empty($video)) 
        {
            $this->_jsonError(__d('store', 'Invalid URL. Please try again'));
        }
        else 
        {
            //load site categories
            $role_id = $this->_getUserRoleId();
            $categories = $this->Category->getCategoriesList( 'Video', $role_id);

            $this->set(array(
                'video' => $video,
                'categories' => $categories
            ));
        }
    }
    
    public function manager_save_video()
    {
        $videoHelper = MooCore::getInstance()->getHelper('Video_Video');
        $data = $this->request->data;
        $data['user_id'] = MooCore::getInstance()->getViewer(true);
        $isEdit = false;

        if((int)$data['id'] > 0 && $this->Video->hasAny(array(
            'Video.id' => $data['id']
        )))
        {
            $this->Video->id = $data['id'];
            $isEdit = true;
        }

        $this->Video->set($data);
        $this->_validateData($this->Video);
        
        if($this->Video->save($data))
        {
            //get id
            $video_id = $this->Video->id;
            $video = $this->StoreProductVideo->loadVideoDetail($video_id);
            
            $this->_jsonSuccess(__d('store', 'Successfully saved'), false, array(
                'video' => !empty($video) ? $video['Video'] : array()
            ));
        }
        else 
        {
            $this->_jsonError(__d('store', 'Something went wrong, please try again'));
        }
    }
    
    public function manager_select_video()
    {
        //search
        $data = !empty($this->request->data) ? $this->request->data : null;

        //load products
        $videos = $this->StoreProductVideo->loadVideoPaging($this, array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'keyword' => !empty($data['keyword']) ? $data['keyword'] : '',
            'except_id' => !empty($data['except_id']) ? $data['except_id'] : ''
        ), 10);

        $this->set(array(
            'videos' => $videos,
            'search' => $data,
        ));
        $this->render('Store.Elements/list/video_short_list');
    }
    
    public function manager_select_image()
    {
        //search
        $data = !empty($this->request->data) ? $this->request->data : null;

        //load products
        $images = $this->StoreProductImage->loadImagePaging($this, array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'keyword' => !empty($data['keyword']) ? $data['keyword'] : '',
            'except_id' => !empty($data['except_id']) ? $data['except_id'] : ''
        ), 10);

        $this->set(array(
            'images' => $images,
            'search' => $data,
        ));
        $this->render('Store.Elements/list/image_short_list');
    }

    ////////////////////////////////////frontend////////////////////////////////////
    private function active($data, $value, $task)
    {
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreProduct->checkProductExist($id))
                {
                    $this->StoreProduct->activeField($id, $task, $value);
                }
            }
        }
        $this->Session->setFlash(__d('store', 'Successfully updated'));
        $this->redirect($this->referer());
    }
    
    public function wishlist()
    {
        $storeWishlist = $this->StoreProductWishlist->findListStoreWishlist();
        
        $this->set(array(
            'storeWishlist' => $storeWishlist,
        ));
    }

    function add_rating()
    {
        $data = $this->request->data;
        $data['user_id'] = Configure::read('store.uid');
        if(Configure::read('store.uid') < 1)
        {
            $this->_jsonError(__d('store', 'Please login to continue'));
        }
        else if(empty($data['product_id']) || !$this->StoreProduct->checkProductExist($data['product_id']))
        {
            $this->_jsonError(__d('store', 'Product not found'));
        }
        else if($this->StoreProductComment->checkRated($data['product_id']))
        {
            $this->_jsonError(__d('store', 'You have already rated for this product.'));
        }
        else 
        {
            $data['enable'] = 1;
            $this->StoreProductComment->set($data);
            $this->_validateData($this->StoreProductComment);

            if($this->StoreProductComment->saveComment($data))
            {
                $this->StoreProduct->updateProductRating($data['product_id']);
                $this->_jsonSuccess(__d('store', 'Thank you for your rating'));
            }
            $this->_jsonError(__d('store', 'Something went wrong, please try again.'));
        }
    }
    
    function show_share_product()
    {
        if(Configure::read('store.uid') == 0)
        {
            $this->_jsonError(__d('store', "Please login to add this product to wishlist", "Store"), false, array('redirect' => $this->request->base.'/users/member_login'));
        }
            
        $product_id = $this->request->data['product_id'];
        $product = $this->StoreProduct->loadProductDetail($product_id);
        if($product == null)
        {
            $this->_jsonError(__d('store', 'Product not found'));
        }
        else
        {
            $privacy = array(
                PRIVACY_EVERYONE => __d('store', 'Everyone'),
                PRIVACY_FRIENDS => __d('store', 'Friend only')
            );
            $this->set(array(
                'product' => $product,
                'privacy' => $privacy
            ));
        }
    }
    
    function share_product()
    {
        $data = $this->request->data;
        if(empty($data['product_id']) || !$this->StoreProduct->checkProductExist($data['product_id'], 1))
        {
            $this->_jsonError(__d('store', 'Product not found'));
        }
        else if($this->StoreProduct->shareProduct($data))
        {
            $this->_jsonSuccess();
        }
        $this->_jsonError(__d('store', 'Something went wrong, please try again.'));
    }

    public function load_product_list()
    {
        $data = $this->request->query;
        if(!empty($data['keyword']) && strlen($data['keyword']) <= 2)
        {
            $this->_jsonError(__d('store', 'Keyword must be greater than 3 characters'));
        }
        
        $products = $this->StoreProduct->loadProductList($this, $data);
        $this->set(array(
            'products' => $products,
            'view' => $data['view']
        ));

        $this->render('Store.Elements/list/product_list');
    }
    
    public function related_products($product_id)
    {
        return $this->StoreProduct->relatedProducts($product_id);
    }
    
    public function download_product($product_id)
    {
        $product = $this->StoreProduct->loadProductDetail($product_id);
        if(empty($product_id) || $product == null)
        {
            $this->_redirectError(__d('store', 'Product not found'), '/stores');
        }
        else if($product['StoreProduct']['user_id'] != MooCore::getInstance()->getViewer(true) && !$this->StoreDigitalProduct->isBoughtDigitalProduct($product_id, MooCore::getInstance()->getViewer(true)))
        {
            $this->_redirectError(__d('store', "You don't have permission"), '/stores');
        }
        else if($this->isApp())
        {
            $this->_redirectError(__d('store', "Please download or view link of this product on web"), '/stores');
        }
        else
        {
            $product = $product['StoreProduct'];
            if($product['product_type'] == STORE_PRODUCT_TYPE_DIGITAL)
            {
                $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
                
                $file = $storeHelper->getDigitalFile($product);
                $path_parts = pathinfo($file);
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename= ".$path_parts['filename'].".".$path_parts['extension']);
                header("Content-Transfer-Encoding: binary");
                readfile($file);
                exit;

            }
            else if($product['product_type'] == STORE_PRODUCT_TYPE_LINK)
            {
                $this->redirect($product['product_link']);
            }
            else
            {
                $this->_redirectError(__d('store', "Product type not found"), '/stores');
            }
        }
    }
    
    public function load_my_files()
    {
        //wishlist
        $products = $this->StoreDigitalProduct->loadMyFiles($this);

        $this->set(array(
            'products' => $products,
        ));
        $this->render('Store.Elements/list/my_files_list');
    }
}