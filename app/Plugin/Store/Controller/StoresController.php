<?php

class StoresController extends StoreAppController
{

    public $components = array('Paginator');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->url = STORE_MANAGER_URL;
        $this->admin_url = $this->request->base . '/admin/store/stores/';
        $this->set('url', $this->url);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.Store');
        $this->loadModel('Store.StoreSetting');
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.StoreOrder');
        $this->loadModel('Store.StoreProductComment');
        $this->loadModel('Store.StoreCategory');
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.StoreProductAttribute');
        $this->loadModel('Store.StorePayment');
        $this->loadModel('Store.StorePackage');
        $this->loadModel('Store.StoreProductVideo');
        $this->loadModel('Store.StoreReview');
        $this->loadModel('User');
        $this->loadModel('Friend');
        $this->loadModel('Store.StoreBusiness');
    }

    ////////////////////////////////////////////////////////backend admin////////////////////////////////////////////////////////
    public function admin_index()
    {
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $stores = $this->Store->loadStorePaging($this, array('keyword' => $keyword));

        $this->set(array(
            'stores' => $stores,
            'keyword' => $keyword,
            'title_for_layout' => __d('store', 'Stores')
        ));
    }

    public function admin_enable()
    {
        $this->active($this->request->data, 1, 'enable');
    }

    public function admin_disable()
    {
        $this->active($this->request->data, 0, 'enable');
    }
    
    public function admin_featured_dialog($id)
    {
        $store = $this->Store->loadStoreDetail($id);
        $this->set(array(
            'id' => $id,
            'store' => $store
        ));
        $this->render('Store.Elements/feature_day_dialog');
    }
    
    public function admin_save_feature()
    {
        $data = $this->request->data;
        $id = $data['id'];
        if(!$this->Store->checkStoreExist($id, false))
        {
            $this->_jsonError(__d('store', 'Store not found'));
        }
        else if(!empty($data['unlimited_feature']))
        {
            if($this->Store->updateAll(array(
                'Store.featured' => 1,
                'Store.unlimited_feature' => 1,
                'Store.sent_expiration_email' => 1,
            ), array(
                'Store.id' => $id
            )))
            {
                //send notification
                $this->Store->sendFeaturedNotification($id, 1);
                
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
            if($this->Store->updateAll(array(
                'Store.featured' => 1,
                'Store.unlimited_feature' => 0,
                'Store.sent_expiration_email' => 0,
                'Store.feature_expiration_date' => "'" . $expiration_date . "'"
            ), array(
                'Store.id' => $id
            )))
            {
                //send notification
                $this->Store->sendFeaturedNotification($id, 1);
                
                $this->_jsonSuccess(__d("store", "Successfully set feature"), true);
            }
            $this->_jsonError(__d('store', 'Cannot set feature, please try again!'));
        }
    }

    /*public function admin_featured()
    {
        $this->active($this->request->data, 1, 'featured');
    }*/

    public function admin_unfeatured()
    {
        $this->active($this->request->data, 0, 'featured');
    }

    private function active($data, $value = 1, $task)
    {
        $count = 0;
        if (!empty($data['cid']))
        {
            foreach ($data['cid'] as $id)
            {
                if ($this->Store->checkStoreExist($id, false))
                {
                    $this->Store->activeField($id, $task, $value);

                    //send notification
                    if ($task == 'featured')
                    {
                        //send notification
                        $this->Store->sendFeaturedNotification($id, $value);
                    }
                    else if ($task == 'enable')
                    {
                        $store = $this->Store->loadStoreDetail($id);
                        if ($store != null)
                        {
                            $url = '/stores/manager';
                            switch ($value)
                            {
                                case 0:
                                    $this->Store->sendNotification($store['Store']['user_id'], $store['Store']['user_id'], 'disapprove_seller', $url, '', 'Store');
                                    break;
                                case 1:
                                    $this->Store->sendNotification($store['Store']['user_id'], $store['Store']['user_id'], 'approve_seller', $url, '', 'Store');
                                    break;
                            }
                        }
                    }
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }

    ////////////////////////////////////////////////////////backend////////////////////////////////////////////////////////
    public function manager_index()
    {
        $store = $this->Store->loadCurrentStore();

        //package
        $package = $this->StorePackage->findById(STORE_PACKAGE_FEATURED_STORE_ID);

        $this->set(array(
            'store' => $store,
            'package' => $package,
            'active_menu' => 'seller',
            'title_for_layout' => __d('store', 'Seller')
        ));
    }

    function manager_help()
    {
        $this->autoRender = false;
        return json_encode($this->StoreSetting->loadHelpData());
    }

    function manager_load_manager_menu()
    {
        $this->set(array(
            'dialog' => true
        ));
        $this->render('Store.Elements/manager_menu_dialog');
    }

    public function manager_delete()
    {
        $store = $this->Store->findByUserId(Configure::read('store.uid'));
        if ($store != null)
        {
            if ($this->Store->deleteStore($store['Store']['id']))
            {
                $this->_redirectSuccess(__d("store", "Seller has been deleted"), "/stores");
            }
        }
        $this->_redirectSuccess(__d("store", "Something went wrong, please try again"), "/stores/manager");
    }

    ////////////////////////////////////////////////////////frontend////////////////////////////////////////////////////////
    public function index($id_param = null)
    {
        $type = !empty($this->request->query["type"]) ? $this->request->query["type"] : "";
        $this->set(array(
            'allow_create_store' => $this->Store->storePermission(STORE_PERMISSION_CREATE_STORE),
            'default_sorting' => 0,
            'title_for_layout' => ''
        ));
        switch ($type)
        {
            case "my_orders":
                $this->set(array(
                    'title_for_layout' => __d('store', 'My Orders')
                ));
                $this->render('/Stores/my_orders');
                break;
            case "my_wishlist":
                $this->set(array(
                    'title_for_layout' => __d('store', 'My Wishlist')
                ));
                $this->render('/Stores/my_wishlist');
                break;
            case "my_files":
                $this->set(array(
                    'title_for_layout' => __d('store', 'My Files')
                ));
                $this->render('/Stores/my_files');
                break;
            default:
                $store_category_id = $this->getIdFromUrl($id_param);

                //breadcrumb
                $cat_paths = $this->StoreCategory->getPath($store_category_id);

                $this->set(array(
                    'store_category_id' => $store_category_id,
                    'select_store_category_id' => $store_category_id,
                    'cat_paths' => $cat_paths,
                    'title_for_layout' => isset($cat_paths[count($cat_paths) - 1]) ? $cat_paths[count($cat_paths) - 1]['StoreCategory']['name'] : ''
                ));
                $this->render('/Stores/products');
        }
    }
    
    public function sellers()
    {
		$this->set(array(
            'title_for_layout' => ''
        ));
        if (!Configure::read('Store.show_store_list'))
        {
            $this->_redirectError(__d("store", "This page cound not be found"), "/stores");
        }
        $this->render('/Stores/stores');
    }
    
    public function seller_products($id_param = null)
    {
		$this->set(array(
            'title_for_layout' => ''
        ));
        if (!Configure::read('Store.show_store_list'))
        {
            $this->_redirectError(__d("store", "This page cound not be found"), "/stores");
        }
        
        if (!Configure::read('Store.show_store_list'))
        {
            $this->_redirectError(__d("store", "This page cound not be found"), "/stores");
        }
        
        $store_id = $this->getIdFromUrl($id_param);
        if ($store_id > 0 && !$this->Store->checkStoreExist($store_id))
        {
            $this->_redirectError(__d('store', 'Store not found'), '/stores/sellers');
        }
        else
        {
            $store = $this->Store->loadStoreDetail($store_id);
            $this->set(array(
                'store_id' => $store_id,
                'store' => $store,
                'title_for_layout' => $store['Store']['name']
            ));
            $this->render('/Stores/products');
        }
    }

    public function load_store_list()
    {
        $data = $this->request->data;
        if($data['keyword'] != null && strlen($data['keyword']) <= 2)
        {
            $this->_jsonError(__d('store', 'Keyword must be greater than 3 characters'));
        }
        $data['enable'] = 1;
        $stores = $this->Store->loadStorePaging($this, $data, Configure::read('Store.store_store_per_page'), true);

        $this->set(array(
            'stores' => $stores,
        ));
        $this->render('Store.Elements/list/store_list');
    }

    public function create($id = null)
    {
        $this->_checkPermission(array('confirm' => true));
        if (Configure::read('store.uid') == 0)
        {
            $this->redirect('/users/member_login');
        }
        else if (!$this->Store->storePermission(STORE_PERMISSION_CREATE_STORE) && !$this->Store->hasStore(Configure::read('store.uid')))
        {
            $this->_redirectError(__d('store', 'You don\'t have permission to create seller.'), STORE_URL);
        }
        else if (empty($id) && $this->Store->hasStore(Configure::read('store.uid')))
        {
            $this->redirect(STORE_MANAGER_URL);
        }
        else
        {
            //load product
            if (!empty($id))
            {
                $store = $this->Store->loadCurrentStore();
            }
            else
            {
                $store = $this->Store->initFields();
            }
            
            if ($this->is_integrate_to_business) 
            {
                $uid = MooCore::getInstance()->getViewer(true);
                $listBusiness = $this->Store->loadMyBusinessList($uid);
                $this->set('listBusiness', $listBusiness);
            }

            $this->set(array(
                'store' => $store['Store'],
                'payments' => $this->StorePayment->getList(),
                'title_for_layout' => $id > 0 ? __d('store', 'Edit seller') : __d('store', 'Create Seller')
            ));
        }
    }

    function save()
    {
        $data = $this->request->data;
        $store = $this->Store->loadCurrentStore();
        if (Configure::read('store.uid') == null)
        {
            $this->_jsonError(__d('store', 'Please login to continue'));
        }
        else if (!$this->Store->storePermission(STORE_PERMISSION_CREATE_STORE))
        {
            $this->_jsonError(__d('store', 'You don\'t have permission to create seller.'));
        }
        else if ((empty($data['store_id']) && $this->Store->hasStore(Configure::read('store.uid'))) ||
                ((!empty($data['store_id']) && !empty($store['Store']) && $data['store_id'] != $store['Store']['id'])))
        {
            $this->_jsonError(__d('store', 'You are only able to create one seller account.'));
        }
        else
        {
            $data['user_id'] = Configure::read('store.uid');
            $isEdit = false;
            if ($data['store_id'] > 0)
            {
                $isEdit = true;
                $this->Store->id = $data['store_id'];
            }
            else
            {
                $data['enable'] = 0;
                if (Configure::read('Store.auto_approve_seller'))
                {
                    $data['enable'] = 1;
                }
            }
            
            //check business
            if ($this->is_integrate_to_business && empty($data['business_id'])) 
            {
                $this->_jsonError(__d('store', 'Please select business page'));
            }

            //check valid paypal email
            if (isset($data['paypal_email']) && $data['paypal_email'] == null)
            {
                $this->_jsonError(__d('store', 'Paypal email is required.'));
            }
            else if (!empty($data['paypal_email']) && !$this->Store->checkPaypalGatewayInfo())
            {
                $this->_jsonError(__d('store', 'Paypal config is empty, please contact site admin for more details.'));
            }
            else if(!empty($data['paypal_email']))
            {
                $paypal_type = Configure::read('Store.store_paypal_type');
                if($paypal_type == STORE_PAYPAL_TYPE_EXPRESS)
                {
                    $cPaypalExpress = MooCore::getInstance()->getComponent('Store.PaypalExpress');
                    if (!$cPaypalExpress->checkAccountExist($data['paypal_email'], $data['paypal_first_name'], $data['paypal_last_name']))
                    {
                        $this->_jsonError(__d('store', 'Paypal email does not exist.'));
                    }
                }
                else
                {
                    $cPaypalParallel = MooCore::getInstance()->getComponent('Store.PaypalParallel');
                    if (!$cPaypalParallel->checkAccountExist($data['paypal_email'], $data['paypal_first_name'], $data['paypal_last_name']))
                    {
                        $this->_jsonError(__d('store', 'Paypal email does not exist.'));
                    }
                }
            }
            if (empty($data['paypal_email']))
            {
                $data['paypal_email'] = '';
            }

            //store payment
            $data['payments'] = '';
            if (!empty($data['store_payment']))
            {
                $data['payments'] = implode(',', $data['store_payment']);
            }

            if (!empty($store) && $data['image'] == $store['Store']['image'])
            {
                unset($data['image']);
            }

            $this->Store->set($data);
            $this->_validateData($this->Store);
            if ($this->Store->save($data))
            {
                //send notification
                if (!$isEdit)
                {
                    $adminUser = $this->Store->getAdminUser();
                    $this->Store->sendNotification($adminUser['User']['id'], MooCore::getInstance()->getViewer(true), 'create_seller', '/admin/store/stores/?keyword=' . $data['name'], '', 'Store');
                }

                //show message
                if (empty($data['store_id']) && !Configure::read('Store.auto_approve_seller'))
                {
                    $this->_jsonSuccess(__d('store', 'You have successfully created seller, you can create products after this seller is approved.'), true, array(
                        'redirect' => STORE_MANAGER_URL
                    ));
                }
                else
                {
                    $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                        'redirect' => STORE_MANAGER_URL
                    ));
                }
            }
            else
            {
                $this->_jsonError(__d('store', 'Something went wrong, please try again'));
            }
        }
    }

    public function product($id, $quickview = false)
    {
        $user = $this->_getUser();
        $id = $this->getIdFromUrl($id);
        if ((empty($user) || (!empty($user['Role']) && !$user['Role']['is_admin'])) && !$this->StoreProduct->checkProductExist($id, true))
        {
            $this->_redirectError(__d('store', "Product not found", "Store"), STORE_URL);
        }
        else
        {
            $noPermission = false;
            if (!$this->Store->storePermission(STORE_PERMISSION_VIEW_PRODUCT_DETAIL))
            {
                if ($this->request->is('ajax'))
                {
                    echo "You don't have permission to view this page";
                    exit;
                }
                else
                {
                    $noPermission = true;
                }
            }

            //load detail
            $product = $this->StoreProduct->loadProductDetail($id, false, true);
            if ($product == null)
            {
                $this->_redirectError(__d('store', "Product not found", "Store"), STORE_URL);
            }

            //update views
            $this->StoreProduct->updateProductViews($id);

            //breadcrumb
            $cat_paths = $this->StoreCategory->getPath($product['StoreProduct']['store_category_id']);

            //attribute to buy
            $attributes = $this->StoreProductAttribute->loadProductAttributeToBuy($id);

            //check rated
            $rating = $this->StoreProductComment->getProductRateValue($id);

            //package
            $package = $this->StorePackage->findById(STORE_PACKAGE_FEATURED_STORE_ID);

            //load product video
            $product_videos = $this->StoreProductVideo->loadProductVideo($id, array(
                'enable' => 1,
                'limit' => Configure::read('Store.video_item_per_page'),
                'page' => 1
            ));

            MooCore::getInstance()->setSubject($product);
            $store_helper = MooCore::getInstance()->getHelper('Store_Store');

            //check reviewed
            $is_reviewed = $this->StoreReview->isReviewed($id);
            
            //related products
            $relatedProducts = $this->StoreProduct->relatedProducts($id);
            
            //are friend
            $are_friend = $this->Friend->areFriends($this->uid, $product['Store']['user_id']);
            
            //store user
            $store_user = $this->User->findById($product['Store']['user_id']);
            
            $this->set(array(
                'og_image' => !empty($product['StoreProductImage'][0]) ? $store_helper->getProductImage($product['StoreProductImage'][0], array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH)) : '',
                'noPermission' => $noPermission,
                'user' => $user,
                'product' => $product,
                'attributes' => $attributes,
                'cat_paths' => $cat_paths,
                'store_category_id' => $product['StoreProduct']['store_category_id'],
                'rating' => $rating,
                'package' => $package,
                'product_videos' => $product_videos,
                'is_reviewed' => $is_reviewed,
                'relatedProducts' => $relatedProducts,
                'store_user' => $store_user,
                'are_friend' => $are_friend,
                'allow_create_store' => $this->Store->storePermission(STORE_PERMISSION_CREATE_STORE),
                'title_for_layout' => !empty($product['StoreProduct']['meta_title']) ? $product['StoreProduct']['meta_title'] : $product['StoreProduct']['name'],
                'keyword_for_layout' => !empty($product['StoreProduct']['meta_keyword']) ? $product['StoreProduct']['meta_keyword'] : Configure::read('store.meta_keyword'),
                'description_for_layout' => !empty($product['StoreProduct']['meta_description']) ? $product['StoreProduct']['meta_description'] : Configure::read('store.meta_description')
            ));

            if ($quickview)
            {
                $this->render('Store.Elements/product_quickview');
            }
        }
    }

    function load_product_video($product_id)
    {
        if (!$this->request->is('ajax'))
        {
            $this->_redirectError(__d("store", "This page cound not be found"), "/stores");
        }
        $page = !empty($this->request->named['page']) ? $this->request->named['page'] : 1;

        //load product video
        $product_videos = $this->StoreProductVideo->loadProductVideo($product_id, array(
            'enable' => 1,
            'limit' => Configure::read('Store.video_item_per_page'),
            'page' => $page
        ));

        $this->set(array(
            'product_videos' => $product_videos,
            'product_id' => $product_id,
            'page' => $page
        ));
        $this->render('Store.Elements/list/videos_list');
    }

    function product_video_detail($id)
    {
        if (!$this->request->is('ajax') && !$this->isApp())
        {
            $this->_redirectError(__d("store", "This page cound not be found"), "/stores");
        }
        $video = $this->StoreProductVideo->loadVideoDetail(null, $id);
		
		$this->set(array(
            'video' => $video
        ));
		if($this->isApp())
		{
			$product = $this->StoreProduct->loadProductDetail($video['StoreProductVideo']['product_id'], false, true);
			$this->set(array(
				'video' => $video,
				'title_for_layout' => !empty($product['StoreProduct']['meta_title']) ? $product['StoreProduct']['meta_title'] : $product['StoreProduct']['name'],
				'keyword_for_layout' => !empty($product['StoreProduct']['meta_keyword']) ? $product['StoreProduct']['meta_keyword'] : Configure::read('store.meta_keyword'),
				'description_for_layout' => !empty($product['StoreProduct']['meta_description']) ? $product['StoreProduct']['meta_description'] : Configure::read('store.meta_description')
			));
		}
    }

    function share_product_content($product_id)
    {
        $this->autoRender = false;
        App::import('Model', 'Store.StoreProduct');
        $mProduct = new StoreProduct();
        MooCore::getInstance()->getModel('Store.StoreProduct');

        return $mProduct->loadProductDetail($product_id);
    }

    function create_store_content($store_id)
    {
        $this->autoRender = false;
        $mStore = MooCore::getInstance()->getModel('Store.Store');

        return $mStore->loadStoreDetail($store_id);
    }

    function load_category_dialog()
    {
        //all categories
        $storeCats = $this->StoreCategory->loadStoreCategoryList();

        $this->set(array(
            'storeCats' => $storeCats,
        ));
        $this->render('Store.Elements/category_dialog');
    }

    function load_shortcut()
    {
        $this->autoRender = false;
        $store = $this->Store->findByUserId(Configure::read('store.uid'));
        $this->set(array(
            'store' => $store
        ));
        $this->render('Store.Elements/menustore');
    }

    public function upload_image()
    {
        $this->autoRender = false;
        // save temp image
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);
        if (!empty($result['success']))
        {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);


            //resize
            $path = STORE_UPLOAD_DIR . DS . $result['filename'];
            $photo->adaptiveResize(STORE_PHOTO_THUMB_WIDTH, STORE_PHOTO_THUMB_HEIGHT)->save($path);
            $path = STORE_UPLOAD_DIR . DS . STORE_PHOTO_TINY_WIDTH . '_' . $result['filename'];
            $photo->adaptiveResize(STORE_PHOTO_TINY_WIDTH, STORE_PHOTO_TINY_HEIGHT)->save($path);

            $url = FULL_BASE_URL . $this->request->webroot . STORE_UPLOAD_DIR . DS . $result['filename'];
            $result['url'] = $url;
            $result['file'] = $path . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    public function check_exist_business_page()
    {
        if ($this->is_integrate_to_business)
        {
            $this->autoRender = false;
            $uid = MooCore::getInstance()->getViewer(true);

            if(!$this->Store->checkBusinessExist($uid))
            {
                $this->_jsonSuccess(__d('store', 'Deny'), null, array(
                    'text' => __d('store', 'Please create your business page first to be able to open seller account'),
                    'redirect' => '/businesses/create'
                ));
            }
            
            $this->_jsonError(__d('store', 'Allow'), null, array(
                'redirect' => '/stores/create'
            ));
        }
    }
    
    public function check_link_business_page($business_id)
    {
        $this->autoRender = false;
        $store_id = $this->request->data['store_id'];

        if($this->Store->checkBusinessLink($business_id, $store_id))
        {
            $this->_jsonError(__d('store', 'This business page already linked to a store, please choose another business page'));
        }
    }
}
