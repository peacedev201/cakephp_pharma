<?php
App::uses('Widget','Controller/Widgets');

class tagForumWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $controller->loadModel('Tag');
        $tags = $controller->Tag->getTags('Forum_Forum_Topic', Configure::read('core.popular_interval'), $this->params['num_item_show'],null, 'count desc' );

    	$this->setData("tags",$tags);
    }
}