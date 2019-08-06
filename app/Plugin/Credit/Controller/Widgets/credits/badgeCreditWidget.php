<?php
App::uses('Widget','Controller/Widgets');

class badgeCreditWidget extends Widget {
    public function beforeRender(Controller $controller) {
        App::import('Credit.Model', 'CreditBalances');
        $balance = new CreditBalances();
        $uid = MooCore::getInstance()->getViewer(true);
        $type = MooCore::getInstance()->getSubjectType();
        $subject = MooCore::getInstance()->getSubject();

        if ($type != 'User')
            $user_id = $uid;
        else
            $user_id = $subject['User']['id'];

        $item = $balance->getBalancesUser($user_id);
        if(!$item)
        {
            return;
        }
        App::import('Credit.Model', 'CreditRanks');
        $rank_model = new CreditRanks();
        $next_rank = null;
        if ($type != 'User' || $uid == $subject['User']['id'])
            $next_rank = $rank_model->getNextRank($item['CreditBalances']['current_credit']);


        $now_rank = $rank_model->getNowRank($item['CreditBalances']['current_credit']);

        $width_rank = 0;
        if(!empty($next_rank) && !empty($item)){
            $width_rank = ($item['CreditBalances']['current_credit'] / $next_rank['CreditRanks']['credit']) * 100;
        }
        if($width_rank > 100)
        {
            $width_rank = 100;
        }
        if($width_rank < 0)
        {
            $width_rank = 0;
        }
        if ($controller->isApp())
        {
            $controller->set('item', $item);
            $controller->set('now_rank', $now_rank);
            $controller->set('next_rank', $next_rank);
            $controller->set('width_rank', $width_rank);
        }
        else
        {
            $this->setData('item', $item);
            $this->setData('now_rank', $now_rank);
            $this->setData('next_rank', $next_rank);
            $this->setData('width_rank', $width_rank);
        }
    }
}