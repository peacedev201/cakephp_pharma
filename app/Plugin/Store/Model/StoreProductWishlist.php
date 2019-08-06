<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProductWishlist extends StoreAppModel{
    public $recursive = 1; 
    public $belongsTo = array(
        'StoreProduct'=> array(
            'className' => 'Store.StoreProduct',
            'foreignKey' => 'product_id',
            'dependent' => true),
        'Store'=> array(
            'className' => 'Store',
            'foreignKey' => 'store_id',
            'dependent' => true)
    );
    
    public function isExistInWishlist($product_id)
    {
        return $this->hasAny(array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'product_id' => $product_id
        ));
    }
    
    public function addToWishlist($product_id)
    {
        $data = array(
            'user_id' => MooCore::getInstance()->getViewer(true),
            'product_id' => $product_id
        );
        return $this->save($data);
    }
    
    public function removeFromWishlist($product_id)
    {
        return $this->deleteAll(array(
            'StoreProductWishlist.user_id' => MooCore::getInstance()->getViewer(true),
            'StoreProductWishlist.product_id' => $product_id,
        ));
    }
    
    public function loadWishlist($obj)
    {
        $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');

        $obj->Paginator->settings = array(
            'conditions' => array(
                'StoreProductWishlist.user_id' => MooCore::getInstance()->getViewer(true),
            ),
            'order' => array('StoreProductWishlist.id' => 'DESC'),
            'fields' => array('StoreProductWishlist.product_id'),
            'limit' => Configure::read('Store.wishlist_item_per_page'),
        );
        $wishlist = $obj->paginate('StoreProductWishlist');
        $data = array();
        if($wishlist != null)
        {
            $list = array();
            foreach($wishlist as $item)
            {
                $list[] = $item['StoreProductWishlist']['product_id'];
            }
            $data = $mProduct->find('all', array(
                'conditions' => array(
                    'StoreProduct.id IN('.implode(',', $list).')'
                )
            ));
        }
        return $mProduct->parseProductData($data);
    }
    
    public function findListStoreWishlist()
    {
        $data = $this->find('all', array(
            'conditions' => array(
                'StoreProductWishlist.user_id' => MooCore::getInstance()->getViewer(true)
            ),
            'fields' => array('Store.id', 'Store.name'),
            'group' => array('StoreProductWishlist.store_id')
        ));
        $stores = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $item = $item['Store'];
                $stores[$item['id']] = $item['name'];
            }
        }
        return $stores;
    }
    
    public function totalMyWishlist()
    {
        return $this->find('count', array(
			'conditions' => array(
				'StoreProductWishlist.user_id' => MooCore::getInstance()->getViewer(true)
			)
        ));
    }
}