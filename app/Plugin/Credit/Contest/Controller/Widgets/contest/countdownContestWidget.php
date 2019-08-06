<?php

App::uses('Widget', 'Controller/Widgets');

class countdownContestWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $contest = MooCore::getInstance()->getSubject();
        $uid = MooCore::getInstance()->getViewer(true);
        $this->setData('contest', $contest);
    }

}
