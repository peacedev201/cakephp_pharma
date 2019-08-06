<?php
App::uses('Widget','Controller/Widgets');
class nav_giftGiftWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Gift.gift_enabled'))
        {
            $mGiftCategory = MooCore::getInstance()->getModel('Gift.GiftCategory');
            $aCategories = $mGiftCategory->getCategories();
            
            $loadAjax = true;
            if($controller->request->params['action'] == 'view' || $controller->request->params['action'] == 'create')
            {
                $loadAjax = false;
            }
            
            $this->setData('aCategories', $aCategories);
            $this->setData('url_gift', '/gifts');
            $this->setData('loadAjax', $loadAjax);
        }
    }
}