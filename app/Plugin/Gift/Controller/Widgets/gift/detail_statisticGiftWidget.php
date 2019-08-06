<?php
App::uses('Widget','Controller/Widgets');
class detail_statisticGiftWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Gift.gift_enabled'))
        {
            $mGift = MooCore::getInstance()->getModel('Gift.Gift');
            $gift = $mGift->getGift($controller->request->params['pass'][0], true);
            
            $this->setData('gift', $gift);
        }
    }
}