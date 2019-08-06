<?php
App::uses('Widget','Controller/Widgets');

class homeForumWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $forumCategoryModel = MooCore::getInstance()->getModel('Forum.ForumCategory');
        $forumTopicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
        $forumModel = MooCore::getInstance()->getModel('Forum.Forum');
        $cats = $forumCategoryModel->find('all',array(
            'conditions' => array(),
            'order' => array('ForumCategory.order asc')
        ));
        foreach($cats as &$cat)
        {
            $forumModel->unbindModel(array('belongsTo' => 'ForumCategory'));
            $cat['Forums'] = $forumModel->getForumByCatId($cat['ForumCategory']['id']);
            if(is_array($cat['Forums']))
            {
                foreach ($cat['Forums'] as &$forum)
                {
                    $forumTopicModel->unbindModel(array('belongsTo'=> array('Forum','LastPost')));
                    $forum['last_topic'] = $forumTopicModel->find('first',array(
                        'conditions' => array(
                            'ForumTopic.forum_id' => $forum['Forum']['id'],
                        ),
                        'order' => array('ForumTopic.created desc')
                    ));
                    if(!empty($forum['last_topic']))
                    {
                        if($forum['last_topic']['ForumTopic']['parent_id'] != 0)
                        {
                            $forumTopicModel->unbindModel(array('belongsTo'=> array('Forum','LastPost')));
                            $parent_topic = $forumTopicModel->find('first',array(
                                'conditions' => array(
                                    'ForumTopic.id' => $forum['last_topic']['ForumTopic']['parent_id']
                                ),
                                'fields' => array('ForumTopic.id','ForumTopic.title'),
                            ));
                            $forum['last_topic']['ForumTopic']['title'] = $parent_topic['ForumTopic']['title'];
                            $forum['last_topic']['ForumTopic']['id'] = $parent_topic['ForumTopic']['id'];
                            $forum['last_topic']['ForumTopic']['moo_href'] = $parent_topic['ForumTopic']['moo_href'];
                        }

                    }
                    $forumModel->unbindModel(array('belongsTo' => array('ForumCategory')));
                    $forum['subs'] = $forumModel->getSubForumByParentId($forum['Forum']['id']);
                }
            }
        }
        //debug($cats);
        $this->setData('cats',$cats);
        $this->setData('type','forum');
    }
}