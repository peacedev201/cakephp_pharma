<?php
App::uses('Widget','Controller/Widgets');

class voteContestWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$subject = MooCore::getInstance()->getSubject();
        $num_item_show = $this->params['num_item_show'];
        $controller->loadModel('Contest.ContestEntry');
        $entries = $controller->ContestEntry->getTopVotes($subject, $num_item_show);
    	if ($controller->request->is('androidApp') || $controller->request->is('iosApp'))
        {
            $controller->set('entries', $entries);
        }
        else
        {
            $this->setData('entries', $entries);
        }
    }
}