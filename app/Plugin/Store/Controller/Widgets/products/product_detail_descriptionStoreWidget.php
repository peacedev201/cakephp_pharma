<?php
App::uses('Widget','Controller/Widgets');

class product_detail_descriptionStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $product_id = $this->getIdFromUrl($controller->request->params['pass'][0]);
            $product = $mProduct->loadProductDetail($product_id);
            
            $this->setData('product', $product);
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