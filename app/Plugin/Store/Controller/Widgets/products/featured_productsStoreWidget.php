<?php
App::uses('Widget','Controller/Widgets');

class featured_productsStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
            $featuredProducts = $mProduct->featuredProducts();
            
            //currency
            $mStore = MooCore::getInstance()->getModel('Store.Store');
            $currency = $mStore->loadDefaultGlobalCurrency();
            Configure::write('store.currency_symbol', $currency['Currency']['symbol']);
            Configure::write('store.currency_code', $currency['Currency']['currency_code']);
            
            $this->setData('featuredProducts', $featuredProducts);
        }
    }
}