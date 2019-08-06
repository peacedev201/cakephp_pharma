<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreTransaction extends StoreAppModel
{
    public $validationDomain = 'store';
    public $belongsTo = array('User', 'Store.StoreProduct', 'Store.StorePackage', 'Store.Store', 'Gateway');
	public $mooFields = array('href', 'plugin', 'type', 'url', 'thumb', 'privacy', 'title');

	public function getPlugin($product)
    {
        return "Store";
    }
	
    public function loadStoreTransaction($id = null)
    {
        if($id > 0)
        {
            return $this->findById($id);
        }
        return $this->find('all');
    }
    
    public function loadAdminPaging($obj, $search = null, $limit = 20)
    {
        $cond = array();
        if(!empty($search['keyword']))
        {
            $cond[] = "StoreProduct.name LIKE '%".$search['keyword']."%'";
        }
        if(!empty($search['status']))
        {
            $cond['StoreTransaction.status'] = $search['status'];
        }
        if(!empty($search['package']))
        {
            $cond['StoreTransaction.store_package_id'] = $search['package'];
        }
        $obj->Paginator->settings=array(
            'conditions' => $cond,
            'order' => array('StoreTransaction.id' => 'DESC'),
            'limit' => $limit,
        );
        return $obj->paginate('StoreTransaction');
    }
    
    public function isStoreTransactionExist($id, $enable = null)
    {
        $cond = array(
            'StoreTransaction.id' => $id
        );
        if(is_bool($enable))
        {
            $cond['StoreTransaction.enable'] = $enable;
        }
        return $this->hasAny($cond);
    }
    
	function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StoreTransaction.'.$task => $value
        ), array(
            'StoreTransaction.id' => $id,
        ));
    }
    
    public function getExpiredFeatureProducts()
    {
        $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        $mStoreProduct->unbindModel(array(
            'belongsTo' => array('Store.StoreProducer', 'User', 'Store.Store'),
            'hasMany' => array('StoreProductImage')
        ), true);
        
        $curdate = strtotime(date("Y-m-d H:i:s"));
        return $mStoreProduct->find("all", array(
            "conditions" => array(
                "StoreProduct.featured" => 1,
                "UNIX_TIMESTAMP(StoreProduct.feature_expiration_date) <= ".$curdate,
            )
        ));
    }
    
    public function getExpiredFeatureStores()
    {
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->unbindModel(array(
            'belongsTo' => array('User'),
        ), true);
        
        $curdate = strtotime(date("Y-m-d H:i:s"));
        return $mStore->find("all", array(
            "conditions" => array(
                "Store.featured" => 1,
                "UNIX_TIMESTAMP(Store.feature_expiration_date) <= ".$curdate,
            )
        ));
    }
    
    public function getFeaturedProductExpirationReminder()
    {
        $mStorePackage = MooCore::getInstance()->getModel('Store.StorePackage');
        $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        $mStoreProduct->unbindModel(array(
            'belongsTo' => array('Store.StoreProducer', 'User', 'Store.Store'),
            'hasMany' => array('StoreProductImage')
        ), true);
        
        $storePackage = $mStorePackage->findById(STORE_PACKAGE_FEATURED_PRODUCT_ID);
        $curdate = strtotime(date("Y-m-d H:i:s"));
        $reminder_days = $storePackage['StorePackage']['reminder'];
        $this->unbindModel(array(
            'belongsTo' => array('User', 'Store.StorePackage', 'Gateway')
        ));
        return $mStoreProduct->find("all", array(
            "conditions" => array(
                "StoreProduct.featured" => 1,
                "StoreProduct.sent_expiration_email" => 0,
                "UNIX_TIMESTAMP(DATE_SUB(StoreProduct.feature_expiration_date, INTERVAL $reminder_days DAY)) <= ".$curdate
            )
        ));
    }
    
    public function getFeaturedStoreExpirationReminder()
    {
        $mStorePackage = MooCore::getInstance()->getModel('Store.StorePackage');
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $mStore->unbindModel(array(
            'belongsTo' => array('User'),
        ), true);
        
        $storePackage = $mStorePackage->findById(STORE_PACKAGE_FEATURED_STORE_ID);
        $curdate = strtotime(date("Y-m-d H:i:s"));
        $reminder_days = $storePackage['StorePackage']['reminder'];
        $this->unbindModel(array(
            'belongsTo' => array('User', 'Store.StorePackage', 'Gateway')
        ));
        return $mStore->find("all", array(
            "conditions" => array(
                "Store.featured" => 1,
                "Store.sent_expiration_email" => 0,
                "UNIX_TIMESTAMP(DATE_SUB(Store.feature_expiration_date, INTERVAL $reminder_days DAY)) <= ".$curdate
            )
        ));
    }
    
    public function loadManagerPaging($obj, $search = null, $limit = 10)
    {
        $cond = array(
            'StoreTransaction.store_id' => Configure::read('store.store_id')
        );
        if(!empty($search['keyword']))
        {
            $cond[] = "StoreProduct.name LIKE '%".$search['keyword']."%'";
        }
        if(!empty($search['status']))
        {
            $cond['StoreTransaction.status'] = $search['status'];
        }
        if(!empty($search['package']))
        {
            $cond['StoreTransaction.store_package_id'] = $search['package'];
        }
        $obj->Paginator->settings=array(
            'conditions' => $cond,
            'order' => array('StoreTransaction.id' => 'DESC'),
            'limit' => $limit,
        );
        return $obj->paginate('StoreTransaction');
    }
    
    public function updateStatus($id, $status)
    {
        $this->updateAll(array(
            'StoreTransaction.status' => "'$status'"
        ), array(
            'StoreTransaction.id' => $id
        ));
    }
}