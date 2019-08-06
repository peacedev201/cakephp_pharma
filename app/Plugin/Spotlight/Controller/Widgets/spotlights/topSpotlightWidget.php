<?php
App::uses('Widget','Controller/Widgets');

class topSpotlightWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$controller->loadModel('Spotlight.SpotlightUser');
        $canJoin = $controller->SpotlightUser->checkCanJoinSpotlight();
        $topUser = $controller->SpotlightUser->getTopSpotlight();

        $acos = $controller->_getUserRoleParams();
        if (!in_array('spotlight_use', $acos)) {
            $canJoin = 1;
        }
    	$this->setData('topSpotlight',array('users'=>$topUser));
        $this->setData('canJoin',$canJoin);

        $this->setData('content_id', $this->params['content_id']);

    }
}