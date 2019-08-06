<?php
App::uses('Widget','Controller/Widgets');

class recentContestWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$num_item_show = $this->params['num_item_show'];
    	$controller->loadModel('Contest.Contest');
    	$contests = $controller->Contest->getRecentContests($num_item_show);
    	if ($controller->request->is('androidApp') || $controller->request->is('iosApp'))
        {
            $controller->set('r_contests', $contests);
        }
        else
        {
            $this->setData('r_contests', $contests);
        }
		
    }
}