<?php

App::uses('Widget', 'Controller/Widgets');

class popularContestWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $num_item_show = $this->params['num_item_show'];
        $controller->loadModel('Contest.Contest');
        $contests = $controller->Contest->getPopularContests($num_item_show);
        if ($controller->request->is('androidApp') || $controller->request->is('iosApp')) {
            $controller->set('p_contests', $contests);
        } else {
            $this->setData('p_contests', $contests);
        }
    }

}
