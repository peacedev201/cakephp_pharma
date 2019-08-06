<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreDigitalProduct extends StoreAppModel
{
    public $validationDomain = 'store';
    
    public function isBoughtDigitalProduct($product_id, $user_id)
    {
        $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        if($mProduct->hasAny(array(
            'StoreProduct.id' => $product_id,
            'StoreProduct.price' => '0.00',
        )))
        {
            return true;
        }
        return $this->hasAny(array(
            'StoreDigitalProduct.user_id' => $user_id,
            'StoreDigitalProduct.store_product_id' => $product_id,
        ));
    }
    
    public function removeDigitalProduct($product_id, $user_id)
    {
        $this->deleteAll(array(
            'StoreDigitalProduct.user_id' => $user_id,
            'StoreDigitalProduct.store_product_id' => $product_id,
        ));
    }

    public function loadMyFiles($obj)
    {
        $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');

        $obj->Paginator->settings = array(
            'conditions' => array(
                'StoreDigitalProduct.user_id' => MooCore::getInstance()->getViewer(true),
            ),
            'order' => array('StoreDigitalProduct.id' => 'DESC'),
            'fields' => array('StoreDigitalProduct.store_product_id'),
            'limit' => Configure::read('Store.my_files_item_per_page'),
        );
        $products = $obj->paginate('StoreDigitalProduct');
        $data = array();
        if($products != null)
        {
            $list = array();
            foreach($products as $item)
            {
                $list[] = $item['StoreDigitalProduct']['store_product_id'];
            }
            $data = $mProduct->find('all', array(
                'conditions' => array(
                    'StoreProduct.id IN('.implode(',', $list).')'
                )
            ));
        }
        return $mProduct->parseProductData($data);
    }
}