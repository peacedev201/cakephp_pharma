<?php
App::uses('Widget','Controller/Widgets');

class searchStoreWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Store.store_enabled'))
        {
            
        }
    }
}