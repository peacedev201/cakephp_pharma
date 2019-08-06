<?php
App::uses('Widget','Controller/Widgets');

class menuStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mStore = MooCore::getInstance()->getModel('Store.Store');
            $allow_create_store = $mStore->storePermission(STORE_PERMISSION_CREATE_STORE);
            
            $this->setData('allow_create_store', $allow_create_store);
        }
    }
}