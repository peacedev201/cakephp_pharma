<?php
App::uses('Widget','Controller/Widgets');

class most_memberForumWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $num_item_show = $this->params['num_item_show'];
        $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');

        $members = $topicModel->getActiveMembers((int)$num_item_show);
        $this->setData('members', $members);
    }
}