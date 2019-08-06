<?php
App::uses('Widget','Controller/Widgets');

class featured_storesStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mStore = MooCore::getInstance()->getModel('Store.Store');
            $featuredStores = $mStore->featuredStores();
            $this->setData('featuredStores', $featuredStores);
        }
    }
}