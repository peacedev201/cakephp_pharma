<?php
App::uses('Widget','Controller/Widgets');

class seller_infoStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $mStore = MooCore::getInstance()->getModel('Store.Store');
            if(!empty($controller->request->params['pass'][0]))
            {
                if(isset($controller->request->params['action']) && $controller->request->params['action'] == "seller_products")
                {
                    $store_id = $this->getIdFromUrl($controller->request->params['pass'][0]);
                    $store = $mStore->loadStoreDetail($store_id);
                    $this->setData('seller', $store['Store']);
                }
                else
                {
                    $product_id = $this->getIdFromUrl($controller->request->params['pass'][0]);
                    $product = $mProduct->loadProductDetail($product_id);
                    $this->setData('seller', $product['Store']);
                }
            }
        }
    }
    
    protected function getIdFromUrl($url)
    {
        if($url != null)
        {
            $url = explode('-', $url);
            return $url[count($url) - 1];
        }
        return '';
    }
}