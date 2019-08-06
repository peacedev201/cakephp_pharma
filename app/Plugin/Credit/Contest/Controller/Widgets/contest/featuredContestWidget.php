<?php

App::uses('Widget', 'Controller/Widgets');

class featuredContestWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $num_item_show = $this->params['num_item_show'];
        $controller->loadModel('Contest.Contest');
        $contests = $controller->Contest->getFeaturedContests($num_item_show);
        if ($controller->request->is('androidApp') || $controller->request->is('iosApp')) {
            $controller->set('f_contests', $contests);
        } else {
            $this->setData('f_contests', $contests);
        }
    }

}
