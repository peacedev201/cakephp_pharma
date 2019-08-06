<?php
App::uses('Widget','Controller/Widgets');

class topContestWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$num_item_show = $this->params['num_item_show'];
    	$controller->loadModel('Contest.Contest');
    	$contests = $controller->Contest->getTopContests($num_item_show);
    	if ($controller->request->is('androidApp') || $controller->request->is('iosApp'))
        {
            $controller->set('t_contests', $contests);
        }
        else
        {
            $this->setData('t_contests', $contests);
        }
		
    }
}