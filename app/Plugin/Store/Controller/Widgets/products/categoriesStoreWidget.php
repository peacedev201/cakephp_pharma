<?php
App::uses('Widget','Controller/Widgets');

class categoriesStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            $mStoreCategory = MooCore::getInstance()->getModel('Store.StoreCategory');
            $storeCats = $mStoreCategory->loadStoreCategoryList();
            
            $this->setData('storeCats', $storeCats);
        }
    }
}