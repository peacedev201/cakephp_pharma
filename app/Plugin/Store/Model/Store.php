<?php 
App::uses('StoreAppModel', 'Store.Model');
class Store extends StoreAppModel
{
    public $validationDomain = 'store';
    public $recursive = 1; 
    public $mooFields = array('href', 'plugin', 'type', 'url', 'thumb', 'privacy', 'title');
	public $validate = array( 
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide name'
        ),
        'email' =>   array(   
            'rule' => 'email',
            'message' => 'Invalid email'
        ),		
        'address' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide address'
        ),
        'phone' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide phone'
        ),
        'payments' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please select at least a payment'
        ),
    );
    
    public $actsAs = array(
        'Storage.Storage' => array(
            'type' => array('stores' => 'image'),
        ),
    );
    
    public $belongsTo = array(
        'User'=> array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );
    
    public function activeMenu($active)
    {
        $mCoreMenuItem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mCoreMenuItem->findByUrl('/stores');
        if($menu != null)
        {
            $mCoreMenuItem->id = $menu['CoreMenuItem']['id'];
            $mCoreMenuItem->save(array(
                'is_active' => $active
            ));
        }
    }
    
    public function getHref($store)
    {
        if(!empty($store['name']))
        {
            $request = Router::getRequest();
            return $request->base.'/stores/seller_products/'.Inflector::slug($store['name'],'-').'-'.$store['id'];
        }
        return false;
    }
    
    function getCurrentUser()
    {
        $mUser = MooCore::getInstance()->getModel('User');
        return $mUser->findById(Configure::read('store.uid'));
    }
    
    function checkStoreCategory($data)
    {
        $mStoreCategory = MooCore::getInstance()->getModel('Store.StoreCategory');
        if($data['store_category_id'] == 0)
        {
            return true;
        }
        return $mStoreCategory->isStoreCategoryExist($data['store_category_id']);
    }

    function validCreateStore($user_id)
    {
        $result = $this->hasAny(array(
            'Store.user_id' => $user_id
        ));
        if($result != null)
        {
            return false;
        }
        return true;
    }
    
    function validAliasName($alias, $except_store_id = null)
    {
        
        $cond = array(
            'Store.alias' => $alias
        );
        if((int)$except_store_id > 0)
        {
            $cond[] = 'Store.id != '.(int)$except_store_id;
        }
        $result = $this->find('first', array(
            'conditions' => $cond
        ));
        if($result != null)
        {
            return false;
        }
        return true;
    }
    
    function hasStore($user_id)
    {
        return $this->hasAny(array(
            'Store.user_id' => $user_id
        ));
    }
    
    function loadDefaultGlobalCurrency()
    {
        $mCurrency = MooCore::getInstance()->getModel('Store.Currency');
        
        return $mCurrency->findByIsDefault(1);
    }
    
    function checkStoreExist($id, $check_by_user = true)
    {
        $cond = array(
            'Store.id' => (int)$id 
        );
        return $this->hasAny($cond);
    }
    
    function loadStorePaging($obj, $params = array(), $item_per_page = 10, $block_user = false)
    {
        $cond = array();
        $joins = array();

        if(!empty($params['keyword']))
        {
            $keyword = str_replace("'", "\'", $params['keyword']);
            $cond[] = "Store.name LIKE '%$keyword%'";
        }
        if(!empty($params['enable']))
        {
            $cond["enable"] = $params['enable'];
        }
        //for block user
        if($block_user){
            $cond = $this->addBlockCondition($cond);
        }
        $obj->Paginator->settings=array(
            'conditions' => $cond,
            'order' => array('Store.id' => 'DESC'),
            'limit' => $item_per_page
        );
        return $obj->paginate('Store');
    }
    
    function updateStore($data)
    {
        return $this->updateAll(array(
            'Store.name' => "'".$data['name']."'",
            'Store.alias' => "'".$data['alias']."'",
            'Store.logo' => "'".$data['logo']."'",
            'Store.email' => "'".$data['email']."'"
        ), array(
            'Store.id' => Configure::read('store.store_id')
        ));
    }
    
    function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'Store.'.$task => $value
        ), array(
            'Store.id' => $id,
        ));
    }
    
    public function loadCurrentStore()
    {
        $mStoreBusiness = MooCore::getInstance()->getModel('Store.StoreBusiness');
        if ($mStoreBusiness->isIntegrateToBusiness()) 
        {
            $this->bindModel(array(
				'belongsTo' => array(
                    'Business' => array(
                        'className' => 'Business.Business',
                        'foreignKey' => 'business_id',
                        'dependent' => true
                    )
                )
			));
        }
        return $this->findByUserId(Configure::read('store.uid'));
    }
    
    public function loadStoreDetail($id)
    {
        $cond = array(
            'Store.id' => $id,
        );
        return $this->find('first', array(
            'conditions' => $cond
        ));
    }
    
    function createActivity($action, $item_id, $privacy, $item_type, $user_id, $content = '')
    {
        Configure::read();
        $mActivity = MooCore::getInstance()->getModel('Activity');
        return $mActivity->save(array(
            'type' => 'user',
            'action' => $action,
            'item_id' => $item_id,
            'privacy' => $privacy,
            'item_type' => $item_type,
            'content' => $content,
            'query' => 1,
            'params' => 'item',
            'plugin' => 'Store',
            'user_id' => $user_id,
            'share' => true,
        ));
    }
    
    function deleteStore($store_id)
    {
        $this->create();
        if($this->delete($store_id))
        {
            //delete products
            $mProduct = MooCore::getInstance()->getModel('StoreProduct');
            $products = $mProduct->find('list', array(
                'conditions' => array('StoreProduct.store_id' => $store_id),
                'fields' => array('StoreProduct.id')
            ));
            if($products != null)
            {
                foreach($products as $product_id)
                {
                    $mProduct->deleteProduct($product_id);
                }
            }

            //delete orders
            $mOrder = MooCore::getInstance()->getModel('StoreOrder');
            $orders = $mOrder->find('list', array(
                'conditions' => array('StoreOrder.store_id' => $store_id),
                'fields' => array('StoreOrder.id')
            ));
            if($orders != null)
            {
                foreach($orders as $order_id)
                {
                    $mOrder->delete($order_id);
                }
            }

            //delete producers
            $mProducer = MooCore::getInstance()->getModel('StoreProducer');
            $producers = $mProducer->find('list', array(
                'conditions' => array('StoreProducer.store_id' => $store_id),
                'fields' => array('StoreProducer.id')
            ));
            if($producers != null)
            {
                foreach($producers as $producer_id)
                {
                    $mProducer->delete($producer_id);
                }
            }
            
            //delete attributes
            $mAttribute = MooCore::getInstance()->getModel('StoreAttribute');
            $attributes = $mAttribute->find('list', array(
                'conditions' => array('StoreAttribute.store_id' => $store_id),
                'fields' => array('StoreAttribute.id')
            ));
            if($attributes != null)
            {
                foreach($attributes as $attribute_id)
                {
                    $mAttribute->delete($attribute_id);
                }
            }

            return true;
        }
        return false;
    }
    
    public function updateCounter($id, $field = 'comment_count',$conditions = '',$model = 'Comment') 
    {
        if(empty($conditions))
        {
            $conditions = array('Comment.type' => 'Store_Store', 'Comment.target_id' => $id);
        }
        parent::updateCounter($id, $field, $conditions, $model);
    }
    
    public function sendNotification($user_id, $sender_id, $action, $url, $params = null, $plugin = 'Store')
    {
        $data = array(
            'user_id' => $user_id,
            'sender_id' => $sender_id,
            'action' => $action,
            'url' => $url,
            'params' => $params,
            'plugin' => $plugin,
        );
        $mNotification = MooCore::getInstance()->getModel('Notification');
        $mNotification->create();
        $mNotification->save($data);
    }
    
    public function changeActivityPrivacy($privacy, $action, $item_type, $item_id = null, $parent_id = null)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $mActivity->create();
        $cond = array(
            'action' => $action,
            'item_type' => $item_type,
            'plugin' => 'Store',
        );
        if($item_id != null)
        {
            $cond['item_id'] = $item_id;
        }
        if($parent_id != null)
        {
            $cond['parent_id'] = $parent_id;
        }
        return $mActivity->updateAll(array(
            'Activity.privacy' => $privacy
        ), $cond);
    }
    
    public function getAdminUser()
    {
        $mUser = MooCore::getInstance()->getModel('User');
        return $mUser->find('first', array(
            'conditions' => array(
                'role_id' => 1,
                'approved' => 1
            )
        ));
    }
    
    public function storePermission($value)
    {
        $role = Configure::read('store.user_role');
        if(!empty($role))
        {
            if(!is_array($role) && $role == 'all')
            {
                return true;
            }
            if(($value == STORE_PERMISSION_BUY_PRODUCT || $value == STORE_PERMISSION_VIEW_PRODUCT_DETAIL) && 
                in_array(STORE_PERMISSION_BUY_PRODUCT, $role) && 
                !in_array(STORE_PERMISSION_VIEW_PRODUCT_DETAIL, $role))
            {
                return false;
            }
            if(in_array($value, $role))
            {
                return true;
            }
        }
        return false;
    }
	
	public function checkPaypalGatewayInfo()
	{
		$mGateway = MooCore::getInstance()->getModel('PaymentGateway.Gateway');
        $paypal_type = Configure::read('Store.store_paypal_type');
        if($paypal_type == STORE_PAYPAL_TYPE_EXPRESS)
        {
            $gateway = $mGateway->findByPlugin('PaypalExpress');
        }
        else
        {
            $gateway = $mGateway->findByPlugin('PaypalAdaptive');
        }
        if(!empty($gateway['Gateway']['config']))
        {
			return true;
		}
		return false;
	}
    
    public function featuredStores()
    {
        return $this->find('all', array(
            'conditions' => array(
                'Store.featured' => 1,
                'Store.enable' => 1
            ),
            'order' => array('Store.id DESC'),
            'limit' => Configure::read('Store.featured_stores')
        ));
    }
    
    public function sendFeaturedNotification($store_id, $value)
    {
        $store = $this->loadStoreDetail($store_id);
        if($store != null)
        {
            switch($value)
            {
                case 0:
                    $this->sendNotification($store['Store']['user_id'], $store['Store']['user_id'], 'unfeatured_store', $store['Store']['moo_url'], '', 'Store');
                    break;
                case 1:
                    $this->sendNotification($store['Store']['user_id'], $store['Store']['user_id'], 'featured_store', $store['Store']['moo_url'], '', 'Store');
                    break;
            }
        }
    }
    
    public function isFeaturedStore($store_id)
    {
        return $this->hasAny(array(
            'Store.id' => $store_id,
            'Store.featured' => 1
        ));
    }
    
    public function saveActivity($target_id, $action, $item_type, $item_id = 0, $items = null, $user_id = null, $content = null, $type = 'user')
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        if($user_id == null)
        {
            $user_id = MooCore::getInstance()->getViewer(true);
        }
        $share = 0;
        $data = array(
            'type' => $type,
            'target_id' => $target_id,
            'user_id' => $user_id,
            'action' => $action,
            'item_type' => $item_type,
            'item_id' => $item_id,
            'plugin' => 'Store',
            'share' => $share
        );
        if($items != null)
        {
            $data['items'] = $items;
        }
        if($content != null)
        {
            $content = str_replace('<script', '', $content);
            $content = str_replace('</script>', '', $content);
            $data['content'] = $content;
        }
        return $mActivity->save($data);
    }
    
    public function deleteActivity($target_id, $action, $item_type, $item_id = 0, $items = ' ')
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $cond = array(
            'Activity.type' => 'user',
            'Activity.target_id' => $target_id,
            'Activity.action' => $action,
            'Activity.plugin' => 'Store',
        );
        if($item_id > 0)
        {
            $cond['Activity.item_id'] = $item_id;
        }
        return $mActivity->deleteAll($cond);
    }
    
    public function checkBusinessExist($user_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        return $mBusiness->hasAny(array(
            'user_id' => $user_id
        ));
    }

    public function loadMyBusinessList($user_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
        
        //find all business id of current user who is set as admin
        $business_ids = $mBusinessAdmin->find('list', array(
            'conditions' => array(
                'BusinessAdmin.user_id' => $user_id
            ),
            'fields' => array('BusinessAdmin.business_id')
        ));
        
        //find my business
        $cond = array();
        $data = array();
        
        if($business_ids != null)
        {
            $business_ids = implode(',', $business_ids);
            $cond[] = '(Business.user_id = '.$user_id.' OR Business.id IN('.$business_ids.'))';
        }
        else
        {
            $cond['Business.user_id'] = $user_id;
        }
        
        $objBusinesses = $mBusiness->find('all', array(
            'conditions' => $cond,
            'order' => array('Business.id' => 'DESC'),
        ));


        if(!empty($objBusinesses)) {
            foreach($objBusinesses as $objBusiness) {
                $data[$objBusiness['Business']['id']] = $objBusiness['Business']['name'];
            }
        }
        return $data;
    }
    
    public function checkBusinessLink($business_id, $store_id)
    {
        $cond = array(
            'Store.business_id' => $business_id
        );
        if (!empty($store_id))
        {
            $store = $this->findById($store_id);
            if (!empty($store))
            {
                $cond['Store.business_id !='] = $store['Store']['business_id'];
            }
        }
        return $this->hasAny($cond);
    }
}