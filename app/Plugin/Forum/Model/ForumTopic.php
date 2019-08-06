<?php
class ForumTopic extends ForumAppModel
{
    public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumb' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}forums{DS}{field}{DS}',
                'thumbnailSizes' => array(
                    'size' => array('50','100','150','150_square','200','300')
                )
            )
        ),
        'Storage.Storage' => array(
            'type'=>array(
                'forum_topic_thumb'=>'thumb',
            ),
        ),
        'Hashtag',
    );
    public $validationDomain = 'forum';
    public $order = 'ForumTopic.ping desc,ForumTopic.sort_date desc, ForumTopic.id desc';

    public $mooFields = array('title','href','plugin','type','url', 'thumb','privacy');

    public $belongsTo = array(
        'Forum.Forum','User',
        'LastPost'    => array(
            'className'     => 'Forum.ForumTopic',
            'foreignKey'    => 'last_reply_id'),
    );

    public $validate = array(
        'title' => 	array(
            'rule' => 'notBlank',
            'message' => 'Title is required'
        ),
        'description' => 	array(
            'rule' => 'notBlank',
            'message' => 'Description is required'
        )
    );

    public function beforeSave($options = array())
    {
        if(!empty($this->data['ForumTopic']['description']) && empty($this->data['ForumTopic']['parent_id'])) {
            $this->data['ForumTopic']['body'] = $this->data['ForumTopic']['description'];
        }
    }

    public function getTitle(&$row)
    {
        if (isset($row['title']))
        {
            $row['title'] = htmlspecialchars($row['title']);
            return $row['title'];
        }
        return '';
    }

    public function getHref($row)
    {
        $request = Router::getRequest();
        if (isset($row['id']))
            return $request->base.'/forums/topic/view/'.$row['id'].'/'.seoUrl($row['moo_title']);

        return false;
    }

    public function getThumb($row){
        return 'thumb';
    }

    public function getTopic($id){
        $topic = $this->find('first', array(
            'conditions' => array(
                'ForumTopic.id' => $id,
                'ForumTopic.parent_id' => 0,
            )
        ));
        return $topic;
    }

    public function getTopics( $cond = array(), $page = 1, $limit = RESULTS_LIMIT, $order = 'ForumTopic.id DESC' ){
        $cond['ForumTopic.parent_id'] = 0;
        $topics = $this->find('all', array('conditions' => $cond, 'limit' => $limit, 'page' => $page, 'order' => $order));
        return $topics;
    }

    public function deleteTopic( $topic, $update_counter = true)
    {
        $this->_doDeleteItem($topic);

        if($topic['ForumTopic']['parent_id']) { //delete a reply
            $this->updateParticipant($topic['ForumTopic']['parent_id']);
            $this->updateCounter($topic['ForumTopic']['parent_id'], 'count_reply', array('ForumTopic.parent_id' => $topic['ForumTopic']['parent_id']), 'ForumTopic');
            $this->_updateForumCounter($topic['ForumTopic']['forum_id'], false, true);
        }else {//delete a topic
            // delete activity
            $activityModel = MooCore::getInstance()->getModel('Activity');
            $parentActivity = $activityModel->find('list', array('fields' => array('Activity.id'), 'conditions' =>
                array('Activity.item_type' => 'Forum_Forum_Topic', 'Activity.item_id' => $topic['ForumTopic']['id'])));

            $activityModel->deleteAll(array('Activity.item_type' => 'Forum_Forum_Topic', 'Activity.item_id' => $topic['ForumTopic']['id']), true, true);

            // delete child activity
            $activityModel->deleteAll(array('Activity.item_type' => 'Forum_Forum_Topic', 'Activity.parent_id' => $parentActivity));

            //delete hashtag
            $tagModel = MooCore::getInstance()->getModel('Tag');
            $tagModel->deleteAll(array('Tag.type' => 'Forum_Forum_Topic', 'Tag.target_id' => $topic['ForumTopic']['id']));

            $forumFavoriteModel = MooCore::getInstance()->getModel('Forum.ForumFavorite');
            $forumFavoriteModel->deleteAll(array('ForumFavorite.target_id' => $topic['ForumTopic']['id']), true, true);

            $forumPinModel = MooCore::getInstance()->getModel('Forum.ForumPin');
            $forumPinModel->deleteAll(array('ForumPin.forum_topic_id' => $topic['ForumTopic']['id']), true, true);

            $forumSubscribeModel = MooCore::getInstance()->getModel('Forum.ForumSubscribe');
            $forumSubscribeModel->deleteAll(array('ForumSubscribe.target_id' => $topic['ForumTopic']['id'], 'ForumSubscribe.type' => 'Topic'), true, true);

            $forumFileModel = MooCore::getInstance()->getModel('Forum.ForumFile');
            $files = $forumFileModel->getFiles($topic['ForumTopic']['id']);
            foreach ($files as $file){
                $forumFileModel->deleteFile($file);
            }

            //delete all reply
            if (!$topic['ForumTopic']['parent_id']) {
                $replies = $this->find('all', array(
                    'conditions' => array('ForumTopic.parent_id' => $topic['ForumTopic']['id'])
                ));
                foreach ($replies as $reply){
                    $this->_doDeleteItem($reply);
                }
            }
            if($update_counter){
                $this->_updateForumCounter($topic['ForumTopic']['forum_id'], true);
            }
        }
    }

    private function _doDeleteItem($topic){
        $this->delete($topic['ForumTopic']['id']);

        $forumThankModel = MooCore::getInstance()->getModel('Forum.ForumThank');
        $forumThankModel->deleteAll(array('ForumThank.target_id' => $topic['ForumTopic']['id']), true, true);

        $forumTopicHistoryModel = MooCore::getInstance()->getModel('Forum.ForumTopicHistory');
        $forumTopicHistoryModel->deleteAll(array('ForumTopicHistory.target_id' => $topic['ForumTopic']['id']), true, true);
    }

    private function _updateForumCounter($forum_id, $up_all = false, $is_reply = false){
        $forumModel = MooCore::getInstance()->getModel('Forum.Forum');
        //update counter
        if($up_all){
            $count_types = array( 'count_reply','count_topic' );
        }else {
            if ($is_reply) {
                $count_types = array('count_reply');
            } else {
                $count_types = array('count_topic');
            }
        }

        $forum = $forumModel->findById($forum_id);

        if ($forum['Forum']['parent_id']) {
            $forum_ids = $forumModel->getListSubForum($forum['Forum']['parent_id']);
            $forum_ids[] = $forum['Forum']['parent_id'];
            foreach ($count_types as $count_type) {
                if ($count_type == 'count_reply') {
                    $cond = array('ForumTopic.parent_id <> 0', 'ForumTopic.forum_id' => $forum_ids);
                    $cond1 = array('ForumTopic.forum_id' => $forum_id, 'ForumTopic.parent_id <> 0');
                } else {
                    $cond = array('ForumTopic.parent_id' => 0, 'ForumTopic.forum_id' => $forum_ids);
                    $cond1 = array('ForumTopic.forum_id' => $forum_id, 'ForumTopic.parent_id' => 0);
                }

                $forumModel->updateCounter($forum['Forum']['parent_id'], $count_type, $cond, 'ForumTopic');
                $forumModel->updateCounter($forum_id, $count_type, $cond1, 'ForumTopic');
            }
        } else {
            $forum_ids = $forumModel->getListSubForum($forum_id);
            $forum_ids[] = $forum_id;
            foreach ($count_types as $count_type) {
                if ($count_type == 'count_reply') {
                    $cond = array('ForumTopic.parent_id <> 0', 'ForumTopic.forum_id' => $forum_ids);
                } else {
                    $cond = array('ForumTopic.parent_id' => 0, 'ForumTopic.forum_id' => $forum_ids);
                }

                $forumModel->updateCounter($forum_id, $count_type, $cond, 'ForumTopic');
            }
        }
    }

    public function afterDelete() {
        // delete attached images in topic
        $photoModel = MooCore::getInstance()->getModel('Photo.Photo');
        $photos = $photoModel->find('all', array('conditions' => array('Photo.type' => 'ForumTopic',
            'Photo.target_id' => $this->id)));
        foreach ($photos as $p){
            $photoModel->delete($p['Photo']['id']);
        }
    }

    public function isActive($id){
        $topic = $this->findById($id);
        if($topic['ForumTopic']['status']){
            return true;
        }
        return false;
    }

    public function updateLastReply($id, $topic_id){
        $this->id = $id;
        $this->saveField('last_reply_id', $topic_id);
    }

    public function getRecentTopics($limit = RESULTS_LIMIT) {
        $cond = array(
            'ForumTopic.status' => 1,
            'ForumTopic.parent_id' => 0,
        );
        $topics = $this->find('all', array('conditions' => $cond, 'limit' => $limit, 'order' => 'ForumTopic.created DESC'));
        return $topics;
    }

    public function getMostPopular($limit = RESULTS_LIMIT) {
        $cond = array(
            'ForumTopic.status' => 1,
            'ForumTopic.parent_id' => 0,
        );
        $topics = $this->find('all', array(
            'conditions' => $cond,
            'limit' => $limit,
            'order' => 'ForumTopic.count_reply DESC'
        ));

        return $topics;
    }

    public function getMostView($limit = RESULTS_LIMIT) {
        $cond = array(
            'ForumTopic.status' => 1,
            'ForumTopic.parent_id' => 0,
        );
        $topics = $this->find('all', array('conditions' => $cond, 'limit' => $limit, 'order' => 'ForumTopic.count_view DESC'));

        return $topics;
    }

    public function getActiveMembers($limit = RESULTS_LIMIT) {
        $results = $this->find('all', array('conditions' => array(
            ),
            'fields' => array('User.*','COUNT(*) AS total','ForumTopic.user_id', 'SUM(ForumTopic.parent_id = 0) AS topic', 'SUM(ForumTopic.parent_id != 0) AS reply'),
            'group' => 'ForumTopic.user_id',
            'limit' => $limit,
            'order' => 'total DESC'));

        return $results;
    }

    public function updateParticipant($topic_id){
        $participant = $this->find('count', array('conditions' => array('ForumTopic.parent_id' => $topic_id), 'group' => 'ForumTopic.user_id'));
        $this->id = $topic_id;
        $this->saveField('count_user', $participant);
    }

    public function updateSortDate($topic_id, $date){
        $this->id = $topic_id;
        $this->saveField('sort_date', $date);
    }

    public function getTotalUserTopic($uid){
        $count = $this->find('count', array(
            'conditions' => array(
                'ForumTopic.user_id' => $uid,
                'ForumTopic.parent_id' => 0,
            )
        ));
        return $count;
    }

    public function getTopicHashtags($qid, $limit = RESULTS_LIMIT,$page = 1){
        $cond = array(
            'ForumTopic.id' => $qid,

        );

        //get topics of active user
        $cond['User.active'] = 1;
        $topics = $this->find( 'all', array( 'conditions' => $this->addBlockCondition($cond), 'limit' => $limit, 'page' => $page ) );

        return $topics;
    }
}