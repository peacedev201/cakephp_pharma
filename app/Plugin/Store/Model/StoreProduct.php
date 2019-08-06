<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProduct extends StoreAppModel{
    public $validationDomain = 'store';
    public $recursive = 1; 
    public $mooFields = array('href', 'plugin', 'type', 'url', 'thumb', 'privacy', 'title');
    public $hasMany = array(
        'StoreProductImage'=> array(
            'className' => 'StoreProductImage',
            'foreignKey' => 'product_id',
            'dependent' => true
    ));
    
    public $belongsTo = array(
        'Store'=> array(
            'className' => 'Store.Store',
            'foreignKey' => 'store_id',
            'dependent' => true
    ),'User'=> array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
    ),'StoreProducer'=> array(
            'className' => 'Store.StoreProducer',
            'foreignKey' => 'producer_id',
            'conditions' => array('StoreProducer.enable' => 1),
            'dependent' => true
    ));
    
    public $validate = array(   
        'product_code' =>   array(
            array(
                'rule' => 'notBlank',
                'message' => 'Please provide product code'
            ),
            array(   
                'rule' => array('validProductCode'),
                'message' => 'This code is already exist'
            ),	
        ),
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide name'
        ),
        /*'alias' =>   array(   
            'rule' => array('validAliasName'),
            'message' => 'This name is already exist'
        ),*/
        'store_category_id' =>   array(   
            'rule' => array('checkStoreCategory'),
            'message' => 'Please select a category'
        ),
    );
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreProduct']['store_id'] = Configure::read('store.store_id');
        $this->data['StoreProduct']['user_id'] = Configure::read('store.uid');
        if(!empty($this->data['StoreProduct']['promotion_start']))
        {
            $this->data['StoreProduct']['promotion_start'] = date('Y-m-d', strtotime($this->data['StoreProduct']['promotion_start']));
        }
        if(!empty($this->data['StoreProduct']['promotion_end']))
        {
            $this->data['StoreProduct']['promotion_end'] = date('Y-m-d', strtotime($this->data['StoreProduct']['promotion_end']));
        }
    }
    
    public function getHref($product)
    {
        if(!empty($product['alias']))
        {
            $request = Router::getRequest();
            return $request->base.'/stores/product/'.$product['alias'].'-'.$product['id'];
        }
        return false;
    }
    
    public function getUrl($product)
    {
        if(!empty($product['alias']))
        {
            $request = Router::getRequest();
            return '/stores/product/'.$product['alias'].'-'.$product['id'];
        }
        return false;
    }
    
    public function getTitle(&$product)
    {
        if(!empty($product['name']))
        {
            return $product['name'];
        }
        return '';
    }
    
    public function getType($product)
    {
        return 'Store_Store_Product';
    }
    
    function validAliasName($alias, $except_id)
    {
        $cond = array(
            'StoreProduct.store_id' => Configure::read('store.store_id'),
            'StoreProduct.alias' => $alias,
        );
        if(!empty($this->data['StoreProduct']['id']) && $this->data['StoreProduct']['id'] > 0)
        {
            $cond[] = 'StoreProduct.id != '.$this->data['StoreProduct']['id'];
        }
        $result = $this->hasAny($cond);
        if($result != null)
        {
            return false;
        }
        return true;
    }
    
    function validProductCode($product_code, $except_id)
    {
        $cond = array(
            'StoreProduct.store_id' => Configure::read('store.store_id'),
            'StoreProduct.product_code' => $product_code,
        );
        if(!empty($this->data['StoreProduct']['id']) && $this->data['StoreProduct']['id'] > 0)
        {
            $cond[] = 'StoreProduct.id != '.$this->data['StoreProduct']['id'];
        }
        $result = $this->hasAny($cond);
        if($result != null)
        {
            return false;
        }
        return true;
    }
    
    function loadManagerPaging($obj, $search = array(), $limit = 20, $except_id = null, $all = false)
    {
        //load main image
        $this->bindModel(array(
            'hasMany' => array('StoreProductImage' => array(
                'className' => 'StoreProductImage',
                'foreignKey' => 'product_id',
                'conditions' => array('is_main' => 1),
                'dependent' => true
            ))
        ));
        
        //load data
        $cond = array();
        if(!$all)
        {
            $cond = array(
                'StoreProduct.store_id' => Configure::read('store.store_id')
            );
        }
        if(!empty($search['product_id']))
        {
            $cond["StoreProduct.id"] = $search['product_id'];
        }
        if(!empty($search['keyword']) && !empty($search['search_type']))
        {
            $keyword = trim($search['keyword']);
            switch($search['search_type'])
            {
                case 1:
                    $cond[] = "StoreProduct.name LIKE '%".$keyword."%'";
                    break;
                case 2:
                    $cond[] = "StoreProduct.product_code LIKE '%".$keyword."%'";
                    break;
            }
        }
        if(!empty($search['search_options']) && $search['search_options'] > 0)
        {
            switch ($search['search_options'])
            {
                case 1:
                    $cond["StoreProduct.approve"] = 1;
                    break;
                case 2:
                    $cond["StoreProduct.approve"] = 0;
                    break;
                case 3:
                    $cond["StoreProduct.featured"] = 1;
                    break;
                case 4:
                    $cond["StoreProduct.featured"] = 0;
                    break;
            }
        }
        if(!empty($search['store_category_id']) && $search['store_category_id'] > 0)
        {
            $cond["StoreProduct.store_category_id"] = $search['store_category_id'];
        }
        if($except_id != null)
        {
            $cond[] = "StoreProduct.id NOT IN($except_id)";
        }

        try
        {
            $obj->Paginator->settings=array(
                'conditions' => $cond,
                'order' => array('StoreProduct.id' => 'DESC'),
                'limit' => $limit,
            );
            $data = $obj->paginate('StoreProduct');
            return $data;
        } 
        catch (Exception $ex) {
            return null;
        }
    }
    
    function checkStoreCategory($id)
    {
        $mStoreCategory = MooCore::getInstance()->getModel('Store.StoreCategory');
        
        return $mStoreCategory->isStoreCategoryExist($id);
    }
    
    function checkProductExist($id, $enable = '', $store_id = '')
    {
        $cond = array(
            'StoreProduct.id' => (int)$id 
        );
        if($enable != '')
        {
            $cond['StoreProduct.enable'] = $enable;
            $cond['StoreProduct.approve'] = $enable;
            $cond['Store.enable'] = 1;
        }
        if($store_id != '')
        {
            $cond['StoreProduct.store_id'] = $store_id;
        }
        
        //for block user
        $cond = $this->addBlockCondition($cond);
        
        $data = $this->find('first', array(
            'conditions' => $cond,
            'fields' => array('StoreProduct.id')
        ));
        if($data != null)
        {
            return true;
        }
        return false;
    }

    function activeField($id, $task, $value, $no_store_id = false)
    {
        $cond = array(
            'StoreProduct.id' => $id,
        ); 
        if(!$no_store_id)
        {
            $cond['StoreProduct.store_id'] = Configure::read('store.store_id');
        }
        $this->create();
        $this->updateAll(array(
            'StoreProduct.'.$task => $value
        ), $cond);
    }
    
    function deleteProduct($id, $store_id = '')
    {
        $mStoreProductImage = MooCore::getInstance()->getModel("Store.StoreProductImage");
        $cond = array(
            'StoreProduct.id' => $id
        );
        if($store_id != '')
        {
            $cond['StoreProduct.store_id'] = $store_id;
        }
        
        //find all product image
        //$productImages = $mStoreProductImage->loadProductImage($id);
        
        if($this->deleteAll($cond))
        {
            $this->deleteProductNewsFeed($id);

            //delete wishlist
            $mStoreProductWishlist = MooCore::getInstance()->getModel("Store.StoreProductWishlist");
            $mStoreProductWishlist->deleteAll(array(
                "StoreProductWishlist.product_id" => $id
            ));

            //delete report
            $mStoreProductReport = MooCore::getInstance()->getModel("Store.StoreProductReport");
            $mStoreProductReport->deleteAll(array(
                "StoreProductReport.product_id" => $id
            ));
            
            //unlink product image
            /*if($productImages != null)
            {
                foreach($productImages as $productImage)
                {
                    $productImage = $productImage['StoreProductImage'];
                    $mStoreProductImage->unlinkAllProdiuctImage($productImage['path'], $productImage['filename']);
                }
            }*/
        }
    }
    
    public function deleteProductNewsFeed($product_id)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        
        //delete child
        /*$activities = $mActivity->find('list', array(
            'conditions' => array(
                'Activity.action' => 'product_share',
                'Activity.item_type' => 'Store_Store_Product',
                'Activity.plugin' => 'Store',
                'Activity.item_id' => $product_id,
            ),
            'fields' => array('Activity.parent_id')
        ));
        
        if($activities != null)
        {
            $mActivity->deleteAll(array(
                'Activity.action' => 'product_share',
                'Activity.item_type' => 'Store_Store_Product',
                'Activity.plugin' => 'Store',
                'Activity.id IN('.implode(',', $activities).')',
            ));
        }*/
        
        $mActivity->deleteAll(array(
            'Activity.action' => 'product_share',
            'Activity.item_type' => 'Store_Store_Product',
            'Activity.plugin' => 'Store',
            'Activity.parent_id' => $product_id,
        ));
        
        //delete child
        $activities = $mActivity->find('list', array(
            'conditions' => array(
                'Activity.action' => 'create_product',
                'Activity.item_type' => 'Store_Store_Product',
                'Activity.plugin' => 'Store',
                'Activity.item_id' => $product_id,
            ),
            'fields' => array('Activity.id')
        ));
        
        if($activities != null)
        {
            $mActivity->deleteAll(array(
                'Activity.action' => 'create_product_share',
                'Activity.item_type' => 'Store_Store_Product',
                'Activity.plugin' => 'Store',
                'Activity.parent_id IN('.implode(',', $activities).')',
            ));
        }
        
        $mActivity->deleteAll(array(
            'Activity.action' => 'create_product',
            'Activity.item_type' => 'Store_Store_Product',
            'Activity.plugin' => 'Store',
            'Activity.item_id' => $product_id,
        ));
    }
    
    function loadProductList($obj, $params = array())
    {
        $mProductAttribute = MooCore::getInstance()->getModel('Store.StoreProductAttribute');
        $mStoreSetting = MooCore::getInstance()->getModel('Store.StoreSetting');
        
        //load main image
        $this->bindModel(array(
            'hasMany' => array('StoreProductImage' => array(
                'className' => 'StoreProductImage',
                'foreignKey' => 'product_id',
                'conditions' => array('is_main' => 1, 'enable' => 1),
                'dependent' => true
            ))
        ));
        
        $this->unbindModel(array(
            'belongsTo' => array('Store.StoreProducer', 'User')
        ), true);
        
        //load data
        $cond = array(
            'StoreProduct.enable' => 1,
            'StoreProduct.approve' => 1,
            'Store.enable' => 1
        );
        if(!empty($params['keyword']))
        {
            $keyword = str_replace("'", "\'", $params['keyword']);
            $cond[] = "StoreProduct.name LIKE '%".$params['keyword']."%'";
        }
        
        //sort
        $sort = array('StoreProduct.featured' => 'DESC', 'StoreProduct.id' => 'DESC');
        if(!empty($params['sortby']))
        {
            switch($params['sortby'])
            {
                case PRODUCT_SORT_MOST_RECENT;
                    $sort = array('StoreProduct.featured' => 'DESC', 'StoreProduct.id' => 'DESC');
                    break;
                case PRODUCT_SORT_NAME_ASC;
                    $sort = array('StoreProduct.name' => 'ASC');
                    break;
                case PRODUCT_SORT_NAME_DESC;
                    $sort = array('StoreProduct.name' => 'DESC');
                    break;
                case PRODUCT_SORT_PRICE_ASC;
                    $sort = array('StoreProduct.price' => 'ASC');
                    break;
                case PRODUCT_SORT_PRICE_DESC;
                    $sort = array('StoreProduct.price' => 'DESC');
                    break;
                case PRODUCT_SORT_RATING_ASC;
                    $sort = array('StoreProduct.rating' => 'ASC');
                    break;
                case PRODUCT_SORT_RATING_DESC;
                    $sort = array('StoreProduct.rating' => 'DESC');
                    break;
            }
        }
        
        //price range
        /*if($price_from != null)
        {
            $cond[] = 'StoreProduct.price >= ABS('.$price_from.')';
        }
        if($price_to != null)
        {
            $cond[] = 'StoreProduct.price <= ABS('.$price_to.')';
        }
        
        //attributes
        if($attribute_ids != null)
        {
            $product_ids = $mProductAttribute->find('list', array(
                'conditions' => array(
                    'StoreProductAttribute.attribute_id IN('.$attribute_ids.')',
                    'StoreProductAttribute.force_to_buy' => 0
                ),
                'group' => array('StoreProductAttribute.product_id'),
                'fields' => array('StoreProductAttribute.product_id')
            ));
            if($product_ids != null)
            {
                $product_ids = implode(',', $product_ids);
                $cond[] = 'StoreProduct.id IN('.$product_ids.')';
            }
            else 
            {
                $cond['StoreProduct.id'] = 0;
            }
        }*/

        if(isset($params['store_category_id']) && $params['store_category_id'] > 0)
        {
            $mStoreCategory = MooCore::getInstance()->getModel('Store.StoreCategory');
            $cat = $mStoreCategory->findById($params['store_category_id']);
            $catIds = $mStoreCategory->find('list', array(
                'conditions' => array(
                    'StoreCategory.lft > '.$cat['StoreCategory']['lft'],
                    'StoreCategory.rght < '.$cat['StoreCategory']['rght'],
                ),
                'fields' => array(
                    'StoreCategory.id'
                ))
            );
            $catIds[] = $params['store_category_id'];
            $cond[] = "StoreProduct.store_category_id IN(".implode(',', $catIds).")";
        }   
        
        //filter by store
        if(isset($params['store_id']) && $params['store_id'] > 0)
        {
            $cond['StoreProduct.store_id'] = $params['store_id'];
        }
        
        //filter by product type
        if(!empty($params['product_type']))
        {
            $cond['StoreProduct.product_type'] = $params['product_type'];
        }
        
        //filter by business id
        if(!empty($params['business_id']))
        {
            $cond['Store.business_id'] = $params['business_id'];
        }
        
        //for block user
        $cond = $this->addBlockCondition($cond);
        
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'order' => $sort,
            'limit' => Configure::read('Store.channel_product_per_page')
        );
        $data = $obj->paginate('StoreProduct');
        $data = $this->parseProductData($data);
        return $data;
    }
    
    function searchProduct($keyword = null, $page = 1)
    {
        //load main image
        $this->bindModel(array(
            'hasMany' => array('StoreProductImage' => array(
                'className' => 'StoreProductImage',
                'foreignKey' => 'product_id',
                'conditions' => array('is_main' => 1),
                'dependent' => true
            ))
        ));
        
        //load data
        $cond = array(
            'StoreProduct.enable' => 1
        );
        if($keyword != null)
        {
            $cond[] = "StoreProduct.name LIKE '%".$keyword."%'";
        }
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => array('StoreProduct.id' => 'DESC'),
            'limit' => 5,
            'page' => $page
        ));
        $data = $this->parseProductData($data);
        return $data;
    }
    
    public function parseProductData($products = null, $single_product = null, $image_by_ordering = false)
    {
        $mProductWishlist = MooCore::getInstance()->getModel('Store.StoreProductWishlist');
        $mProductAttribute = MooCore::getInstance()->getModel('Store.StoreProductAttribute');
        $mLike = MooCore::getInstance()->getModel('Like');
        $mStoreSetting = MooCore::getInstance()->getModel('Store.StoreSetting');

        if($products != null || $single_product != null)
        {
            if($single_product != null)
            {
                $products[0] = $single_product;
            }
            foreach($products as $k => $product)
            {
                $product_images = !empty($product['StoreProductImage']) ? $product['StoreProductImage'] : null;
                $product = $product['StoreProduct'];
                $products[$k]['StoreProduct']['old_price'] = $products[$k]['StoreProduct']['new_price'] = $product['price'];
                if($product['allow_promotion'] && !empty($product['promotion_price']))
                {
                    if((!empty($product['promotion_start']) && !empty($product['promotion_end']) && strtotime($product['promotion_start']) <= strtotime(date('m/d/Y')) && strtotime($product['promotion_end']) >= strtotime(date('m/d/Y'))) || 
                       (!empty($product['promotion_start']) && empty($product['promotion_start']) && strtotime($product['promotion_start']) <= strtotime(date('m/d/Y'))) ||
                       (!empty($product['promotion_end']) && empty($product['promotion_end']) && strtotime($product['promotion_end']) >= strtotime(date('m/d/Y'))) ||
                       (empty($product['promotion_start']) && empty($product['promotion_end'])))
                    {
                        $products[$k]['StoreProduct']['new_price'] = $product['promotion_price'];
                    }
                    else
                    {
                        $products[$k]['StoreProduct']['allow_promotion'] = 0;
                    }
                }
                else
                {
                    $products[$k]['StoreProduct']['allow_promotion'] = 0;
                }
                //check exist in wishlist
                $products[$k]['StoreProduct']['in_wishlist'] = false;
                if($mProductWishlist->isExistInWishlist($product['id']))
                {
                    $products[$k]['StoreProduct']['in_wishlist'] = true;
                }
                
                //share url
                $products[$k]['StoreProduct']['url_share'] = Router::url('/', true).'share/ajax_share/Store_Store_Product/id:'.$product['id'].'/type:product_item_detail_share';
                
                //rating percentage
                $products[$k]['StoreProduct']['rating_percentage'] = $this->parseRatingPercentage($product['rating']);
                
                //has attribute to buy
                $products[$k]['StoreProduct']['attribute_to_buy'] = $mProductAttribute->hasAttributeToBuy($product['id']);
                
                //product image
                if($product_images != null)
                {
                    if($image_by_ordering)
                    {
                        usort($product_images, function($a,$b) {
                            return $a['ordering'] - $b['ordering'];
                        });
                    }
                    else 
                    {
                        usort($product_images, function($a,$b) {
                            return $b['is_main'] - $a['is_main'];
                        });
                    }
                }
                $products[$k]['StoreProductImage'] = $product_images;
                
                //check liked
                $products[$k]['StoreProduct']['is_liked'] = $mLike->hasAny(array(
                    'Like.user_id' => Configure::read('store.uid'),
                    'Like.target_id' => $product['id'],
                    'Like.type' => 'Store_Store_Product'
                ));
            }
            if($single_product != null)
            {
                $products = $products[0];
            }
        }
        return $products;
    }

    public function loadProductDetail($id, $original = false, $image_by_ordering = false, $store_id = '', $enable = '', $approve = '', $all_imaage = false)
    {
        if(!$all_imaage)
        {
            $this->bindModel(array(
                'hasMany' => array('StoreProductImage' => array(
                    'className' => 'StoreProductImage',
                    'foreignKey' => 'product_id',
                    'conditions' => array('enable' => 1),
                    'dependent' => true
                ))
            ));
        }
        $cond = array(
            'StoreProduct.id' => $id,
            'Store.enable' => 1
        );
        if($store_id != '')
        {
            $cond['StoreProduct.store_id'] = $store_id;
        }
        if($enable != '')
        {
            $cond['StoreProduct.enable'] = $enable;
        }
        if($approve != '')
        {
            $cond['StoreProduct.approve'] = $approve;
        }
        
        //for block user
        $cond = $this->addBlockCondition($cond);
        
        $data = $this->find('first', array(
            'conditions' => $cond
        ));
        if($original)
        {
            return $data;
        }
        return $this->parseProductData(null, $data, $image_by_ordering);
    }
    
    function loadProductByListId($ids)
    {
        if($ids != null)
        {
            return $this->find('all', array(
                'conditions' => array(
                    'StoreProduct.id IN('.implode(',', $ids).')'
                )
            ));
        }
        return null;
    }
    
    public function mostViewProducts()
    {
        $products = $this->find('all', array(
            'conditions' => array(
                'StoreProduct.enable' => 1,
                'StoreProduct.approve' => 1,
                'Store.enable' => 1,
            ),
            'order' => array('StoreProduct.views DESC'),
            'limit' => Configure::read('Store.most_viewed_products')
        ));
        return $this->parseProductData($products);
    }
    
    public function latestProducts()
    {
        $products = $this->find('all', array(
            'conditions' => array(
                'StoreProduct.enable' => 1,
                'StoreProduct.approve' => 1,
                'Store.enable' => 1
            ),
            'order' => array('StoreProduct.id DESC'),
            'limit' => Configure::read('Store.latest_products')
        ));
        return $this->parseProductData($products);
    }
    
    public function saleProducts()
    {
        $products = $this->find('all', array(
            'conditions' => array(
                'StoreProduct.allow_promotion = 1 AND '
                .'((UNIX_TIMESTAMP(promotion_start) <= UNIX_TIMESTAMP(CURDATE()) AND UNIX_TIMESTAMP(CURDATE()) <= UNIX_TIMESTAMP(promotion_end)) OR '
                .'(IFNULL(promotion_end, 0) = 0 && UNIX_TIMESTAMP(promotion_start) <= UNIX_TIMESTAMP(CURDATE())) OR '
                .'(IFNULL(promotion_start, 0) = 0 && UNIX_TIMESTAMP(CURDATE()) <= UNIX_TIMESTAMP(promotion_end)))',
                'StoreProduct.enable' => 1,
                'StoreProduct.approve' => 1,
                'Store.enable' => 1
            ),
            'order' => array('StoreProduct.id DESC'),
            'limit' => Configure::read('Store.sale_products')
        ));
        return $this->parseProductData($products);
    }
    
    public function randomProducts()
    {
        $products = $this->find('all', array(
            'conditions' => array(
                'StoreProduct.enable' => 1,
                'StoreProduct.approve' => 1,
                'Store.enable' => 1
            ),
            'order' => array('rand()'),
            'limit' => 5
        ));
        return $this->parseProductData($products);
    }
    
    public function relatedProducts($product_id)
    {
        $product = $this->findById($product_id);
        $products = $this->find('all', array(
            'conditions' => array(
                'StoreProduct.store_category_id' => $product['StoreProduct']['store_category_id'],
                'StoreProduct.id != '.$product_id,
                'StoreProduct.enable' => 1,
                'StoreProduct.approve' => 1,
                'Store.enable' => 1
            ),
            'order' => array('StoreProduct.id DESC'),
            'limit' => Configure::read('Store.related_products')
        ));
        return $this->parseProductData($products);
    }
    
    public function featuredProducts()
    {
        $products = $this->find('all', array(
            'conditions' => array(
                'StoreProduct.enable' => 1,
                'StoreProduct.approve' => 1,
                'StoreProduct.featured' => 1,
                'Store.enable' => 1
            ),
            'order' => array('StoreProduct.id DESC'),
            'limit' => Configure::read('Store.featured_products')
        ));
        return $this->parseProductData($products);
    }
    
    public function updateProductViews($product_id)
    {
        $this->updateAll(array(
            'views' => '(views + 1)'
        ), array(
            'StoreProduct.store_id' => Configure::read('store.store_id'),
            'StoreProduct.id' => $product_id
        ));
    }
    
    function updateProductRating($product_id)
    {
        $mProductComment = MooCore::getInstance()->getModel('Store.StoreProductComment');
        
        $productComment = $mProductComment->find('all', array(
            'conditions' => array(
                'StoreProductComment.enable' => 1,
                'StoreProductComment.product_id' => $product_id
            ),
            'fields' => array('SUM(StoreProductComment.rating) AS total_score', 'COUNT(StoreProductComment.rating) AS total_vote')
        ));
        
        if($productComment != null)
        {
            $productComment = $productComment[0][0];
            $rating = 0;
            if($productComment['total_vote'] > 0)
            {
                $rating = round($productComment['total_score'] / $productComment['total_vote'], 2);
            }
            $this->updateAll(array(
                'StoreProduct.rating' => $rating,
                'StoreProduct.rating_count' => $productComment['total_vote']
            ), array(
                'StoreProduct.id' => $product_id
            ));
        }
    }
    
    function totalSiteProducts()
    {
        return $this->find('count', array(
            'conditions' => array(
                'StoreProduct.store_id' => Configure::read('store.store_id')
            )
        ));
    }
    
    function shareProduct($data)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        return $mActivity->save(array(
            'type' => 'user',
            'action' => 'product_share',
            'item_id' => $data['product_id'],
            'privacy' => $data['privacy'],
            'item_type' => 'Store_Store_Product',
            'content' => $data['content'],
            'query' => 1,
            'params' => 'item',
            'plugin' => 'Store',
            'user_id' => Configure::read('store.uid'),
            'share' => true,
        ));
    }
    
    function createProductActivity($data)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        return $mActivity->save(array(
            'type' => 'user',
            'action' => 'create_product',
            'item_id' => $data['product_id'],
            'privacy' => $data['privacy'],
            'item_type' => 'Store_Store_Product',
            'content' => $data['content'],
            'query' => 1,
            'params' => 'item',
            'plugin' => 'Store',
            'user_id' => Configure::read('store.uid'),
            'share' => true,
        ));
    }
    
    function changeProductActivityPrivacy($product_id, $privacy)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->changeActivityPrivacy($privacy, 'create_product', 'Store_Store_Product', $product_id);
        $mStore->changeActivityPrivacy($privacy, 'product_share', 'Store_Store_Product', null, $product_id);
    }
    
    function getStoreIdByProductId($product_id)
    {
        $product = $this->find('first', array(
            'conditions' => array(
                'StoreProduct.id' => $product_id
            ),
            'fields' => array('StoreProduct.store_id')
        ));
        if($product != null)
        {
            return $product['StoreProduct']['store_id'];
        }
        return '';
    }
    
    public function updateCounter($id, $field = 'comment_count',$conditions = '',$model = 'Comment') 
    {
        if(empty($conditions))
        {
            $conditions = array('Comment.type' => 'Store_Store_Product', 'Comment.target_id' => $id);
        }
        parent::updateCounter($id, $field, $conditions, $model);
    }
    
    public function sendApproveNotification($product_id, $value)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $product = $this->loadProductDetail($product_id);
        if($product != null)
        {
            $url = '/stores/manager/products/?keyword='.$product['StoreProduct']['product_code'].'&search_type=2';
            switch($value)
            {
                case 0:
                    $mStore->sendNotification($product['Store']['user_id'], $product['Store']['user_id'], 'disapprove_product', $url, '', 'Store');
                    break;
                case 1:
                    $mStore->sendNotification($product['Store']['user_id'], $product['Store']['user_id'], 'approve_product', $url, '', 'Store');
                    break;
            }
        }
    }
    
    public function sendFeaturedNotification($product_id, $value)
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $product = $this->loadProductDetail($product_id);
        if($product != null)
        {
            switch($value)
            {
                case 0:
                    $mStore->sendNotification($product['Store']['user_id'], $product['Store']['user_id'], 'unfeatured_product', $product['StoreProduct']['moo_url'], '', 'Store');
                    break;
                case 1:
                    $mStore->sendNotification($product['Store']['user_id'], $product['Store']['user_id'], 'featured_product', $product['StoreProduct']['moo_url'], '', 'Store');
                    break;
            }
        }
    }
    
    public function loadOnlyProduct($id)
    {
        $this->unbindModel(array(
            'belongsTo' => array('Store.StoreProducer', 'User', 'Store.Store'),
            'hasMany' => array('StoreProductImage')
        ), true);
        $product = $this->findById($id);
        return $this->parseProductData(null, $product);
    }
    
    public function isFeaturedProduct($product_id)
    {
        return $this->hasAny(array(
            'StoreProduct.id' => $product_id,
            'StoreProduct.featured' => 1
        ));
    }
    
    public function sameProductsImages($product_id)
    {
        //find image name of product
        $mStoreProductImage = MooCore::getInstance()->getModel('Store.StoreProductImage');
        $image_list = $mStoreProductImage->find('list', array(
            'conditions' => array(
                'StoreProductImage.product_id' => $product_id
            ),
            'fields' => array('StoreProductImage.id', 'StoreProductImage.filename')
        ));
        if($image_list == null)
        {
            return null;
        }
        
        $product_list = $mStoreProductImage->find('list', array(
            'conditions' => array(
                'StoreProductImage.product_id != '.$product_id,
                'StoreProductImage.filename' => $image_list
            ),
            'fields' => array('StoreProductImage.id', 'StoreProductImage.product_id'),
            'group' => array('StoreProductImage.product_id')
        ));

        $products = $this->find('all', array(
            'conditions' => array(
                'StoreProduct.id != '.$product_id,
                'StoreProduct.id' => $product_list,
                'StoreProduct.enable' => 1,
                'StoreProduct.approve' => 1,
                'Store.enable' => 1,
            ),
            'order' => array('StoreProduct.id DESC'),
            'limit' => Configure::read('Store.related_products')
        ));
        return $this->parseProductData($products);
    }
}