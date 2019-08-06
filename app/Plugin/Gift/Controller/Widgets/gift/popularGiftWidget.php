<?php
App::uses('Widget','Controller/Widgets');
class popularGiftWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Gift.gift_enabled'))
        {
            $mGift = MooCore::getInstance()->getModel('Gift.Gift');
            $gifts = $mGift->getPupularGifts();
            $this->setData('gifts', $gifts);
        }
    }
}