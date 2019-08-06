<?php
App::uses('Widget','Controller/Widgets');

class latest_productsStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $latestProducts = $mProduct->latestProducts();
            
            //currency
            App::import('Model', 'Store.Store');
            $mStore = new Store();
            $currency = $mStore->loadDefaultGlobalCurrency();
            Configure::write('store.currency_symbol', $currency['Currency']['symbol']);
            Configure::write('store.currency_code', $currency['Currency']['currency_code']);
            
            $this->setData('latestProducts', $latestProducts);
        }
    }
}