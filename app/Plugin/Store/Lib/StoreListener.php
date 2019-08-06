<?php

App::uses('CakeEventListener', 'Event');

class StoreListener implements CakeEventListener
{

    public function implementedEvents()
    {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'Controller.Comment.afterComment' => 'afterComment',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'Plugin.View.Api.Search' => 'apiSearch',
            'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
            'UserController.deleteUserContent' => 'deleteUserContent',
            'Controller.Home.adminIndex.Statistic' => 'statistic',
            'Controller.Search.setCommentViewLink' => 'setCommentViewLink',
            'Mail.Controller.Component.MooMailComponent.BeforeSend' => 'beforeSendMail',
            'StorageHelper.products.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.products.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.products.getFilePath' => 'storage_amazon_get_file_path',
            'StorageHelper.stores.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.stores.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.stores.getFilePath' => 'storage_amazon_get_file_path',
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
            'Plugin.Controller.Video.afterDeleteVideo' => 'afterDeleteVideo',
            
            'ApiHelper.renderAFeed.create_product' => 'apiFeedCreateProduct',
            'ApiHelper.renderAFeed.product_review' => 'apiFeedProductReview',
            'ApiHelper.renderAFeed.product_item_detail_share' => 'apiFeedProductItemDetailShare',
        );
    }

    public function beforeRender($event)
    {
		$user = MooCore::getInstance()->getViewer();
        if(Configure::read('Store.store_enabled') && (!Configure::read('core.site_offline') || ($user != null && $user['Role']['is_admin'])))
        {
            if (Configure::read('debug') == 0)
            {
                $min = "min.";
            }
            else
            {
                $min = "";
            }
            $e = $event->subject();
            if ((!empty($e->request->params['prefix']) && $e->request->params['prefix'] == 'manager') || ($e->request->params['controller'] == 'stores' && $e->request->params['action'] == 'create'))
            {
                $e->Helpers->MooRequirejs->addPath(array(
                    "store_store" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/store.{$min}js"),
                    "store_jquery_ui" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/jquery-ui.{$min}js"),
                    "store_manager" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/manager.{$min}js"),
                    "store_metismenu" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/metismenu.{$min}js"),
                ));
            }
            else
            {
                $e->Helpers->MooRequirejs->addPath(array(
                    "store_store" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/store.{$min}js"),
                    "store_jquery_ui" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/jquery-ui.{$min}js"),
                    "store_cloudzoom" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/cloudzoom.{$min}js"),
                    "store_jcarousel" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/jquery.jcarousel.{$min}js"),
                    "store_star_rating" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/star-rating.{$min}js"),
                    "store_accordion" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/accordion.js"),
                    "store_slick_slider" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/slick-slider.{$min}js"),
                    "store_fancybox" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/fancybox.{$min}js"),
                    "store_flex_slider" => $e->Helpers->MooRequirejs->assetUrlJS("Store.js/flex_slider.{$min}js"),
                ));

                $e->Helpers->MooRequirejs->addShim(array(
                    'store_cloudzoom' => array("deps" => array('jquery')),
                    'store_jcarousel' => array("deps" => array('jquery')),
                    'store_star_rating' => array("deps" => array('jquery')),
                    'store_accordion' => array("deps" => array('jquery')),
                    'store_flex_slider' => array("deps" => array('jquery')),
					'store_fancybox' => array("deps" => array('jquery')),
                ));
            }

            $e->addPhraseJs(array(
                "text_add_to_wishlist" => __d('store', 'Add to wishlist'),
                "text_remove_from_wishlist" => __d('store', 'Remove from wishlist'),
                "text_you_liked_this_product" => __d('store', 'You Liked This Product'),
                "text_like_this_product" => __d('store', 'Like This Product'),
                "text_confirm_clear_products" => __d('store', 'Are you sure you want to clear all products?'),
                "are_you_sure_you_want_to_delete" => __d('store', 'Are you sure you want to delete?'),
                "you_must_select_at_least_an_item" => __d('store', 'You must select at least an item'),
                "drag_or_click_here_to_upload_photo" => __d('store', 'Drag or click here to upload photo'),
                "text_confirm_remove_from_wishlist" => __d('store', 'Are you sure you want to remove this product?'),
                "text_confirm_delete_seller" => __d('store', 'All related data will be deleted. Are you sure you want to remove this seller?'),
                "text_please_select_product" => __d('store', 'Please select product'),
                "text_invalid_quantity" => __d('store', 'Invalid quantity'),
                "text_credits" => __d('store', 'credits'),
                "STORE_SHIPPING_FREE" => "free_shipping",
                "STORE_SHIPPING_PER_ITEM" => "per_item_shipping",
                "STORE_SHIPPING_PICKUP" => "pickup_from_store",
                "STORE_SHIPPING_FLAT" => "flat_shipping_rate",
                "STORE_SHIPPING_WEIGHT" => "weight_based_shipping",
                "STORE_PRODUCT_TYPE_REGULAR" => STORE_PRODUCT_TYPE_REGULAR,
                "STORE_PRODUCT_TYPE_DIGITAL" => STORE_PRODUCT_TYPE_DIGITAL,
                "STORE_PRODUCT_TYPE_LINK" => STORE_PRODUCT_TYPE_LINK,
                "drag_or_click_here_to_upload_file" => __d('store', 'Drag or click here to upload file'),
                "drag_or_click_here_to_upload_video" => __d('store', 'Drag or click here to upload video'),
                "store_allow_digital_file_extensions" => Configure::read('Store.store_allow_digital_file_extensions'),
                "store_allow_video_file_extensions" => Configure::read('Store.store_allow_video_extensions'),
                "text_added_to_cart" => __d('store', 'Added to cart'),
                "setting_credit_currency_exchange" => Configure::read('Credit.credit_currency_exchange'),
                "product_text_confirm_remove_review" => __d('store', 'Are you sure you want to delete your review?'),
                "product_text_no_reviews" => __d('store', 'No reviews found'),
                "open_business_page" => __d('store', 'Open business page now'),
            ));
            if ($e->request->params['controller'] != 'share' && empty($e->request->params['admin']) && !MooCore::getInstance()->isMobile(null))
            {
                $e->Helpers->Html->css(array(
                    'Store.menu_store',
                        ), array('block' => 'css', 'minify' => false));

                $e->Helpers->Html->scriptBlock(
                        "require(['jquery','store_store'], function($,store_store) {store_store.initShortcut();});", array(
                    'inline' => false,
                        )
                );
            }
            if (strtolower($e->theme) == 'mooapp')
            {
                $e->Helpers->Html->css(array(
                    'Store.star-rating',
                    'Store.store_widget'
                        ), array('block' => 'css', 'minify' => true));
            }
            if($e->request->params['plugin'] != 'store')
            {
                $e->Helpers->Html->scriptBlock(
                    "require(['jquery','store_store'], function($,store_store) {store_store.parseAjaxLink();});", 
                    array('inline' => false)
                );
            }
            $e->MooPopup->register('storeModal');
        }
    }

    //used to update comment count
    public function afterComment($event)
    {
        if (Configure::read('Store.store_enabled'))
        {
            $data = $event->data['data'];
            $target_id = isset($data['target_id']) ? $data['target_id'] : null;
            $type = isset($data['type']) ? $data['type'] : '';
            if ($type == 'Store_Store' && !empty($target_id))
            {
                $mStore = MooCore::getInstance()->getModel('Store.Store');
                Cache::clearGroup('store', 'store');
                $mStore->updateCounter($target_id);
            }
            if ($type == 'Store_Store_Product' && !empty($target_id))
            {
                $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
                Cache::clearGroup('store', 'store');
                $mProduct->updateCounter($target_id);
            }
        }
    }

    public function search($event)
    {
        $e = $event->subject();
        if (Configure::read('Store.store_enabled'))
        {
            $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $results = $mProduct->searchProduct($e->keyword);
            if($results != null)
            {
                $event->result['Store']['header'] = "Products";
                $event->result['Store']['icon_class'] = "shopping_cart";
                $event->result['Store']['view'] = "list/search_product_list";
                $event->result['Store']['notEmpty'] = 1;
                $e->set('products', $results);
            }
        }
    }

    public function suggestion($event)
    {
        if (Configure::read('Store.store_enabled'))
        {
            $e = $event->subject();
            $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $hStore = MooCore::getInstance()->getHelper('Store_Store');

            if (isset($event->data['type']) && $event->data['type'] == 'all')
            {
                $products = $mProduct->searchProduct($e->request->data['searchVal']);
                foreach ($products as $index => &$detail)
                {
                    $event->result['product'][$index]['id'] = $detail['StoreProduct']['id'];
                    $product_image = $detail['StoreProductImage'][0];
                    $url = $hStore->getProductImage($product_image, array(
                        'prefix' => PRODUCT_PHOTO_TINY_WIDTH
                    ));
                    $event->result['product'][$index]['img'] = $url;
                    $event->result['product'][$index]['title'] = html_entity_decode($detail['StoreProduct']['name'], ENT_QUOTES);
                    $event->result['product'][$index]['find_name'] = 'Find Product';
                    $event->result['product'][$index]['icon_class'] = 'shopping_cart';
                    $event->result['product'][$index]['view_link'] = 'stores/product/' . $detail['StoreProduct']['alias'] . '-';

                    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');

                    $utz = (!is_numeric(Configure::read('core.timezone')) ) ? Configure::read('core.timezone') : 'UTC';
                    $cuser = MooCore::getInstance()->getViewer();
                    // user timezone
                    if (!empty($cuser['User']['timezone']))
                    {
                        $utz = $cuser['User']['timezone'];
                    }

                    $event->result['product'][$index]['more_info'] = $mooHelper->getTime($detail['StoreProduct']['created'], Configure::read('core.date_format'), $utz);
                }
            }
            elseif (isset($event->data['type']) && ($event->data['type'] == 'store' || $event->data['type'] == 'product'))
            {
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $products = $mProduct->searchProduct($e->request->pass[1], $page);
                $e->set('products', $products);
                $e->set('result', 1);
                $e->set('more_url', '/search/suggestion/store/' . $e->request->pass[1] . '/page:' . ( $page + 1 ));
                $e->set('element_list_path', "Store.list/search_product_list");
            }
        }
    }

    public function apiSearch($event)
    {
        $view = $event->subject();
        $items = &$event->data['items'];
        $type = $event->data['type'];
        $viewer = MooCore::getInstance()->getViewer();
        $utz = $viewer['User']['timezone'];
        if ($type == 'Store' && !empty($view->viewVars['products']))
        {
            $helper = MooCore::getInstance()->getHelper('Store_Store');
            foreach ($view->viewVars['products'] as $item)
            {
                $productImage = !empty($item['StoreProductImage'][0]) ? $item['StoreProductImage'][0] : null;
                $items[] = array(
                    'id' => $item["StoreProduct"]['id'],
                    'url' => FULL_BASE_URL . $item['StoreProduct']['moo_href'],
                    'avatar' => $helper->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH)),
                    'owner_id' => $item["StoreProduct"]['user_id'],
                    'title_1' => $item["StoreProduct"]['moo_title'],
                    'title_2' => __('Posted by') . ' ' . html_entity_decode($view->Moo->getNameWithoutUrl($item['User'], false),ENT_QUOTES) . ' ' . $view->Moo->getTime($item["StoreProduct"]['created'], Configure::read('core.date_format'), $utz),
                    'created' => $item["StoreProduct"]['created'],
                    'type' => "product",
                    'type_title' => __d('store', 'Stores')
                );
            }
        }
    }

    public function hashtagEnable($event)
    {
        $event->result['store_products']['enable'] = true;
    }

    public function deleteUserContent($event)
    {
        MooCore::getInstance()->getModel('Store.Store');
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $store = $mStore->findByUserId($event->data['aUser']['User']['id']);
        if ($store != null)
        {
            $mStore->deleteStore($store['Store']['id']);
        }
    }

    public function statistic($event)
    {
        $request = Router::getRequest();
        $mProduct = MooCore::getInstance()->getModel("Store.StoreProduct");
        $mStore = MooCore::getInstance()->getModel("Store.Store");
        $event->result['statistics'][] = array(
            'item_count' => $mStore->find('count'),
            'ordering' => 9999,
            'name' => __d('store', 'Stores'),
            'href' => $request->base . '/admin/store/stores',
            'icon' => '<i class="fa fa-asterisk"></i>'
        );
        $event->result['statistics'][] = array(
            'item_count' => $mProduct->find('count'),
            'ordering' => 9999,
            'name' => __d('store', 'Products'),
            'href' => $request->base . '/admin/store/store_products',
            'icon' => '<i class="fa fa-cart-arrow-down"></i>'
        );
    }

    public function setCommentViewLink($event)
    {
        $request = Router::getRequest();
        $event->result['view_link'] = $request->base . '/stores/product/';
    }

    public function beforeSendMail($event)
    {
        $rParams = &$event->data['rParams'];
        $mail_template = $event->data['template'];
        if (!isset($rParams['template']) && !isset($rParams['layout']) && !empty($mail_template['type']) && $mail_template['type'] == 'store_order')
        {
            $rParams['template'] = 'Store.default';
            $rParams['layout'] = 'Store.default';
        }
    }

    /////////////////////////////////////amazon s3/////////////////////////////////////
    public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $type = $e->data['type'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];

        switch ($type)
        {
            case 'products':
                if ($e->data['thumb'])
                {
                    if ($e->data['prefix'] == 'files')
                    {
                        $url = FULL_BASE_LOCAL_URL . $request->webroot . STORE_DIGITAL_PRODUCT_UPLOAD_DIR . $thumb;
                    }
                    else if ($e->data['prefix'] == 'videos')
                    {
                        $url = FULL_BASE_LOCAL_URL . $request->webroot . STORE_VIDEO_UPLOAD_DIR . $thumb;
                    }
                    else
                    {
                        $url = FULL_BASE_LOCAL_URL . $request->webroot . PRODUCT_UPLOAD_DIR . $prefix . $thumb;
                    }
                }
                else
                {
                    $url = $v->getImage("store/images/noimage_product.png");
                }
                break;
            case 'stores':
                if ($e->data['thumb'])
                {
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . STORE_UPLOAD_DIR . '/' . $prefix . $thumb;
                }
                else
                {
                    $url = $v->getImage("store/images/noimage_store.png");
                }
                break;
        }
        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        switch ($type)
        {
            case 'products':
                if ($e->data['prefix'] == 'files')
                {
                    $e->result['url'] = $v->getAwsURL($e->data['oid'], "products", "files", $e->data['thumb']);
                }
                else if ($e->data['prefix'] == 'videos')
                {
                    $e->result['url'] = $v->getAwsURL($e->data['oid'], "products", "videos", $e->data['thumb']);
                }
                else
                {
                    $e->result['url'] = $v->getAwsURL($e->data['oid'], "products", $e->data['prefix'], $e->data['thumb']);
                }
                break;
            case 'stores':
                $e->result['url'] = $v->getAwsURL($e->data['oid'], "stores", $e->data['prefix'], $e->data['thumb']);
                break;
        }
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];
        $path = false;
        switch ($type)
        {
            case 'products':
                if (!empty($thumb))
                {
                    if ($e->data['name'] == 'files')
                    {
                        $path = WWW_ROOT . "uploads" . DS . "products" . DS . "files" . DS . $thumb;
                    }
                    else if ($e->data['name'] == 'videos')
                    {
                        $path = WWW_ROOT . "uploads" . DS . "products" . DS . "videos" . DS . $thumb;
                    }
                    else
                    {
                        $path = WWW_ROOT . "uploads" . DS . "products" . DS . $name . $thumb;
                    }
                }
                break;
            case 'stores':
                $path = WWW_ROOT . "uploads" . DS . "stores" . DS . $thumb;
                break;
        }
        $e->result['path'] = $path;
    }

    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStoreProductImage = MooCore::getInstance()->getModel('Store.StoreProductImage');
        $product_images = $mStoreProductImage->find('all', array(
            'conditions' => array("StoreProductImage.id > " => $v->getMaxTransferredItemId("products")),
            'limit' => 10,
            'order' => array('StoreProductImage.id'),
        ));

        if ($product_images != null)
        {
            foreach ($product_images as $product_image)
            {
                $product_image = $product_image['StoreProductImage'];
                if (!empty($product_image["filename"]))
                {
                    $v->transferObject($product_image['id'], 'products', PRODUCT_PHOTO_TINY_WIDTH, $product_image["filename"]);
                    $v->transferObject($product_image['id'], 'products', PRODUCT_PHOTO_THUMB_WIDTH, $product_image["filename"]);
                    $v->transferObject($product_image['id'], 'products', PRODUCT_PHOTO_LARGE_WIDTH, $product_image["filename"]);
                    $v->transferObject($product_image['id'], 'products', '', $product_image["filename"]);
                }
            }
        }

        $stores = $mStore->find('all', array(
            'conditions' => array("Store.id > " => $v->getMaxTransferredItemId("stores")),
            'limit' => 10,
            'order' => array('Store.id'),
        ));
        if ($stores != null)
        {
            foreach ($stores as $store)
            {
                $store = $store['Store'];
                if (!empty($store["image"]))
                {
                    $v->transferObject($store['id'], 'stores', '', $store["image"]);
                }
            }
        }
    }

    public function afterDeleteVideo($e)
    {
        if (!empty($e->data['item']['Video']['id']))
        {
            $mStoreProductVideo = MooCore::getInstance()->getModel('Store.StoreProductVideo');
            $mStoreProductVideo->deleteAllByVideoId($e->data['item']['Video']['id']);
        }
    }

    public function apiFeedCreateProduct($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$mStoreProduct = MooCore::getInstance()->getModel("Store.StoreProduct");
    	$product_item = $mStoreProduct->loadProductDetail($data['Activity']['item_id']);
        $product = $product_item['StoreProduct'];
    	
    	$title = __d('store', 'created a product');
        $titleHtml = $actorHtml.' '. __d('store', 'created a product');
            
    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Store_Store_Product',
                'id' => $product['id'],
                'url' => $this->appProductLink($product_item)."/?tab=1",
                'description' => $this->appProductDescription($product_item),
                'title' => $product['name'],
                'images' => array('850' => $this->appProductImage($product_item)),
            ),
            'target' => array()
    	);
    }
    
    public function apiFeedProductItemDetailShare($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$mStoreProduct = MooCore::getInstance()->getModel("Store.StoreProduct");
    	$product_item = $mStoreProduct->loadProductDetail($data['Activity']['parent_id']);
        $product = $product_item['StoreProduct'];
    	
    	$title = __d('store', 'shared a product');
        $titleHtml = $actorHtml.' '. __d('store', 'shared a product').$this->appShareToTarget($data);
            
    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Store_Store_Product',
                'id' => $product['id'],
                'url' => $this->appProductLink($product_item),
                'description' => $this->appProductDescription($product_item),
                'title' => $product['name'],
                'images' => array('850' => $this->appProductImage($product_item)),
            ),
            'target' => array(),
			'isActivityView' => true
    	);
    }
    
    public function apiFeedProductReview($e)
    {
        $data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];

    	$mStoreProduct = MooCore::getInstance()->getModel("Store.StoreProduct");
        $mStoreReview = MooCore::getInstance()->getModel('Store.StoreReview');
        
        $review = $mStoreReview->getReviewDetail($data['Activity']['item_id'], $data['Activity']['target_id']);
        $review_photos = $review['Photo'];
        $review = $review['StoreReview'];
        $product_item = $mStoreProduct->loadOnlyProduct($data['Activity']['target_id']);
        $product = $product_item['StoreProduct'];
        $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
    	
    	$title = __d('store', 'wrote a review');
        $titleHtml = $actorHtml.' '.__d('store', 'wrote a review for').' <a href="'.$product['moo_href'].'/?tab=3">'.$product['name'].'</a>';

    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Store_Store_Product',
                'id' => $review['id'],
                'url' => $this->appProductLink($product_item)."/?tab=3",
                'description' => $review['content'],
                'title' => '',
                'images' => array(),
            ),
            'target' => array(),
            'isActivityView' => true,
    	);
    }
    
    private function appProductLink($product)
    {
        if($product == null)
        {
            return "";
        }
        $product = $product['StoreProduct'];
        return FULL_BASE_URL . str_replace('?','',mb_convert_encoding($product['moo_href'], 'UTF-8', 'UTF-8'));
    }
    
    private function appProductDescription($product)
    {
        if($product == null)
        {
            return "";
        }
        $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
        $product_images = $product['StoreProductImage'];
        $product = $product['StoreProduct'];
        
        $html = "";
        $html .= __d('store', 'Price').' '.$storeHelper->formatMoney($product['new_price']);
        
        return $html;
    }
    
    private function appProductImage($product)
    {
        if($product == null)
        {
            return "";
        }
        $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
        $product_images = $product['StoreProductImage'];
        $product = $product['StoreProduct'];
        
        return $storeHelper->getProductImage(!empty($product_images[0]) ? $product_images[0] : "", array('prefix'=>'800'));
    }
    
    private function appShareToTarget($activity)
    {
        $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);
        $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);
        if($show_subject)
        {
            switch ($activity['Activity']['type'])
            {
                case "Group_Group":
                    $mGroup = MooCore::getInstance()->getModel('Group.Group');
                    $group = $mGroup->findById($activity['Activity']['target_id']);
                    return ' > <a href="'.$group['Group']['moo_href'].'">'.$group['Group']['name'].'</a>';
                    break;
                case "User":
                    if ($activity['Activity']['target_id'] > 0)
                    {
                        $mUser = MooCore::getInstance()->getModel('User.User');
                        $user = $mUser->findById($activity['Activity']['target_id']);
                        return ' > <a href="'.$user['User']['moo_href'].'">'.$user['User']['name'].'</a>';
                    }
                    break;
            }
        }
        return "";
    }
}
