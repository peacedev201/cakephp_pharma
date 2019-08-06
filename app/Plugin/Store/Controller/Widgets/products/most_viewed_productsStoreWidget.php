<?php
App::uses('Widget','Controller/Widgets');

class most_viewed_productsStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $mostViewedProducts = $mProduct->mostViewProducts();
            
            //currency
            App::import('Model', 'Store.Store');
            $mStore = new Store();
            $currency = $mStore->loadDefaultGlobalCurrency();
            Configure::write('store.currency_symbol', $currency['Currency']['symbol']);
            Configure::write('store.currency_code', $currency['Currency']['currency_code']);
            
            $this->setData('mostViewedProducts', $mostViewedProducts);
        }
    }
}