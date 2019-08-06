<?php
App::uses('Widget','Controller/Widgets');

class rankCreditWidget extends Widget {
    public function beforeRender(Controller $controller) {
        App::import('Credit.Model', 'CreditBalances');
        $balance = new CreditBalances();
        $type = MooCore::getInstance()->getSubjectType();
        $subject = MooCore::getInstance()->getSubject();
        if ($type != 'User')
            $uid = MooCore::getInstance()->getViewer(true);
        else
            $uid = $subject['User']['id'];

        $item = $balance->getBalancesUser($uid);
        if ($controller->isApp())
        {
            $controller->set('item', $item);
            $controller->set('uid', $uid);
            $controller->set('subject_type', $type);
        }
        else
        {
            $this->setData('item', $item);
            $this->setData('uid', $uid);
            $this->setData('subject_type', $type);
        }
    }
}
