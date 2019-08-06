<?php
class ForumTopicsController extends ForumAppController
{
    public $components = array('Paginator');

    private function _checkForumPermission($forum){
        $authorized = true;
        $msg = '';
        $return_url = '';
        $viewer = MooCore::getInstance()->getViewer();

        $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
        $is_moderator = $forumHelper->checkModerator($viewer,$forum);

        if($is_moderator){
            return true;
        }

        if(!$forum['Forum']['status']  && !$viewer['Role']['is_admin']){
            $authorized = false;
            $msg = __d('forum','This forum has been locked');
        }

        if (!$authorized) {
            if (empty($this->layout)) {
                $this->autoRender = false;
                echo $msg;
            } else {
                if ($this->request->is('ajax')) {
                    $this->_jsonError($msg);
                } else {
                    if (!empty($msg)) {
                        $this->Session->setFlash($msg, 'default', array('class' => 'error-message'));
                    }

                    $this->redirect('/pages/no-permission' . $return_url);
                }
            }
            exit;
        }
    }

    private function _checkTopicPermission($id){
        $authorized = true;
        $msg = '';
        $return_url = '';
        $viewer = MooCore::getInstance()->getViewer();

        $topic = $this->ForumTopic->findById($id);
        if(empty($topic)){
            $authorized = false;
        }

        if (!empty($viewer) && $viewer['Role']['is_admin']) {
            return true;
        }
        if (!empty($viewer) && $viewer['User']['id'] == $topic['ForumTopic']['user_id']) {
            return true;
        }

        $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
        $is_moderator = $forumHelper->checkModerator($viewer,array('Forum'=>$topic['Forum']));

        if($is_moderator){
            return true;
        }

        if(!empty($topic) && $topic['ForumTopic']['status'] ){
            $is_active = true;
        }else{
            $is_active = false;
        }

        if(!$is_active){
            $authorized = false;
            $msg = __d('forum','This topic has been locked');
        }

        if (!$authorized) {
            if (empty($this->layout)) {
                $this->autoRender = false;
                echo $msg;
            } else {
                if ($this->request->is('ajax')) {
                    $this->_jsonError($msg);
                } else {
                    if (!empty($msg)) {
                        $this->Session->setFlash($msg, 'default', array('class' => 'error-message'));
                    }

                    $this->redirect('/pages/no-permission' . $return_url);
                }
            }
            exit;
        }
    }

    public function index($type = 'topic', $user_id = null){
        $uid = $this->Auth->user('id');
        $cond = array();
        $scope = array();
        $topics = array();

        if ( !empty( $this->request->named['keyword'] ) && $type != 'replies')
        {
            $keyword = $this->request->named['keyword'];

            $cond['OR'] = array(
                'ForumTopic.title LIKE' => '%'.$keyword.'%',
                'ForumTopic.search_desc LIKE' => '%'.$keyword.'%'
            );
        }
        $cond['ForumTopic.parent_id'] = 0;
        switch ($type){
            case 'topic':
                $title = __d('forum','All topics');
                break;
            case 'started':
                $cond['ForumTopic.user_id'] = $user_id ? $user_id : $uid;
                $title = __d('forum', 'Forum Topics Started');
                break;
            case 'favorite':
                $this->loadModel('Forum.ForumFavorite');
                $topic_ids = $this->ForumFavorite->getTopicList($uid);
                $cond['ForumTopic.id'] = $topic_ids;
                $title = __d('forum', 'Favorite Forum Topics');
                break;
            case 'replies':
                unset($cond['ForumTopic.parent_id']);
                $cond['ForumTopic.parent_id <>'] = 0;
                $cond['ForumTopic.user_id'] = $uid;

                $this->ForumTopic->bindModel(array
                (
                    'belongsTo' => array
                    (
                        'ParentTopic' => array
                        (
                            'className'     => 'Forum.ForumTopic',
                            'foreignKey' => false,
                            'conditions' => array
                            (
                                'ForumTopic.parent_id = ParentTopic.id'
                            )
                        )
                    )
                ));

                if ( !empty( $this->request->named['keyword'] ))
                {
                    $keyword = $this->request->named['keyword'];

                    $cond['ForumTopic.search_desc LIKE'] = '%'.$keyword.'%';
                }
                $limit = Configure::read('Forum.forum_number_reply_per_page');
                $title = __d('forum', 'Forum Replies Created');
                break;
            case 'subscribe':
                $this->loadModel('Forum.ForumSubscribe');
                $this->loadModel('Forum.Forum');
                $topic_ids = $this->ForumSubscribe->getUserItemList($uid);
                $forum_ids = $this->ForumSubscribe->getUserItemList($uid, 'Forum');
                $forums = $this->Forum->find('all', array('conditions' => array('Forum.id' => $forum_ids)));

                $cond['ForumTopic.id'] = $topic_ids;
                $this->set('forums', $forums);
                break;
        }

        if($type != 'signature') {
            $scope['conditions'] = $cond;
            $scope['limit'] = isset($limit) ? $limit : Configure::read('Forum.forum_number_topic_per_page');
            $scope['paramType'] = 'querystring';
            $this->Paginator->settings = $scope;

            $this->ForumTopic->order = 'ForumTopic.id desc';

            $topics = $this->Paginator->paginate('ForumTopic');
        }

        $this->set(compact('topics', 'keyword', 'title'));
        $this->set('type',$type);
        $this->set('title_for_layout', '');

        if($type == 'signature'){
            $this->render('signature');
        }
    }

    public function browse($type = 'my', $user_id = 0){
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $uid = $user_id ? $user_id : $this->Auth->user('id');
        $cond['ForumTopic.user_id'] = $uid;
        $limit = Configure::read('Forum.forum_number_topic_per_page');
        $topics = $this->ForumTopic->getTopics($cond, $page, $limit);
        $more_topics = $this->ForumTopic->getTopics($cond, $page+1, $limit);

        $more_result = 0;
        if (!empty($more_topics))
            $more_result = 1;

        $title = __d('forum', 'My Forum Topics');

        $this->set('topics', $topics);
        $this->set('title', $title);
        $this->set('type', $type);
        $this->set('more_result', $more_result);
        $this->set('more_url', '/forums/topic/browse/' . $type . '/'. $user_id. '/page:' . ( $page + 1 ) ) ;

        if($page > 1) {
            $this->render('/Elements/lists/topic_list');
        }

    }

    public function view($id = null)
    {
        $this->loadModel('Forum.ForumTopicReply');
        $this->loadModel('Forum.ForumSubscribe');
        $this->loadModel('Forum.ForumFavorite');

        $id = intval($id);
        $topic = $this->ForumTopic->findById($id);

        $this->_checkExistence($topic);
        if($topic['ForumTopic']['parent_id'] != 0){ //is reply, redirect to reply view page
            $this->redirect('/forums/topic/view/'.$topic['ForumTopic']['parent_id'].'/reply_id:'.$id);
        }

        $this->checkAco($topic,'forum_view');
        $this->checkPermissionForum($topic['ForumTopic']['forum_id']);
        $uid = $this->Auth->user('id');

        MooCore::getInstance()->setSubject($topic);

        //get replies
        $scope = array();
        $cond = array(
            'ForumTopic.parent_id' => $topic['ForumTopic']['id']
        );
        if ( !empty( $this->request->named['keyword'] ))
        {
            $keyword = $this->request->named['keyword'];
            $cond['ForumTopic.search_desc LIKE'] = '%'.$keyword.'%';
        }else{
            $keyword = '';
        }

        if ( !empty( $this->request->named['reply_id'] ))
        {
            $reply_id = $this->request->named['reply_id'];
            $this->set('reply_id', $reply_id);

            $cond['ForumTopic.id'] = $reply_id;
        }

        $scope['order']['ForumTopic.id'] = 'desc';
        $scope['conditions'] = $cond;
        $scope['limit'] = Configure::read('Forum.forum_number_reply_per_page');
        $scope['paramType'] = 'querystring';
        $this->Paginator->settings = $scope;

        $replies = $this->Paginator->paginate( 'ForumTopic');

        $is_subscribe = $this->ForumSubscribe->isSubscribe($uid, $id, 'Topic');
        $is_favorite = $this->ForumFavorite->isFavorite($uid, $id);
        $last_reply = $this->ForumTopic->findById($topic['ForumTopic']['last_reply_id']);

        //tags
        $this->loadModel('Tag');
        $tags = $this->Tag->getContentTags($id,'Forum_Forum_Topic');
        $this->set('tags', $tags);

        // get files
        $this->loadModel('Forum.ForumFile');
        $files = $this->ForumFile->getFiles($id);
        $this->set('files', $files);

        //SEO
        $this->set('title_for_layout', htmlspecialchars($topic['ForumTopic']['title']));

        $description = $this->getDescriptionForMeta($topic['ForumTopic']['description']);
        if ($description) {
            $this->set('description_for_layout', $description);
            if (count($tags))
            {
                $tags = implode(",", $tags).' ';
            }
            else
            {
                $tags = '';
            }
            $this->set('mooPageKeyword', $this->getKeywordsForMeta($tags.$description));
        }

        // set og:image
        if ($topic['ForumTopic']['thumb']) {
            $helper = MooCore::getInstance()->getHelper('Forum_Forum');
            $this->set('og_image', $helper->getTopicImage($topic, array('prefix' => '200')));

        }

        //update viewed
        if($uid != $topic['ForumTopic']['user_id']) {
            $this->ForumTopic->increaseCounter($id, 'count_view');
        }

        // get reply thanks
        if ( !empty( $uid ) )
        {
            $this->loadModel('Forum.ForumThank');

            $reply_thanks = $this->ForumThank->getReplyThanks( $uid, $id );
            $this->set('reply_thanks', $reply_thanks);
        }

        $this->set('topic', $topic);
        $this->set('is_subscribe', $is_subscribe);
        $this->set('is_favorite', $is_favorite);
        $this->set('last_reply', $last_reply);
        $this->set('replies', $replies);
        $this->set('keyword', $keyword);
    }

    public function create($forum_id = 0, $topic_id = 0){
        $this->loadModel('Forum.ForumSubscribe');
        $this->loadModel('Forum.Forum');
        $forum_id = intval($forum_id);
        $topic_id = intval($topic_id);
        $tags = '';
        $forum = $this->Forum->findById($forum_id);
        $this->_checkExistence($forum);

        $this->checkAco($forum,'forum_create');
        $this->_checkForumPermission($forum);

        if(!empty($topic_id)){
            $this->loadModel('Tag');
            $topic = $this->ForumTopic->findById($topic_id);

            // get files
            $this->loadModel('Forum.ForumFile');
            $files = $this->ForumFile->getFiles($topic_id);
            $this->set('files', $files);

            $tags = $this->Tag->getContentTags($topic_id,'Forum_Forum_Topic');
            if (count($tags))
            {
                $tags = implode(",", $tags).' ';
            }
        }else{
            $topic = $this->ForumTopic->initFields();
        }

        $uid = $this->Auth->user('id');
        $is_subscribe = $this->ForumSubscribe->isSubscribe($uid, $topic_id, 'Topic');
        if($topic_id == 0 || ($topic['ForumTopic']['parent_id'] == '0' && $topic_id != 0))
        {
            $this->set('topic_status',true);
        }

        $this->set('forum', $forum);
        $this->set('is_subscribe', $is_subscribe);
        $this->set('tags', $tags);
        $this->set('topic',$topic);
        $this->set('id',$topic_id);
        $this->set('forum_id',$forum_id);
    }

    public function save(){
        $this->_checkPermission(array('confirm' => true));
        $this->autoRender = false;
        if($this->request->data){
            $this->loadModel('Tag');
            $this->loadModel('Forum.Forum');

            $helperForum = MooCore::getInstance()->getHelper('Forum_Forum');
            $uid = $this->Auth->user('id');
            $data = $this->request->data;
            $is_edit = false;

            if(!empty($data['id'])){
                $old_item = $this->ForumTopic->findById($data['id']);
                $data['user_edited'] = $uid;
                $is_edit = true;
            }else{
                $data['user_id'] = $uid;
            }

            $this->ForumTopic->set($data);
            $this->_validateData( $this->ForumTopic );

            $data['search_desc'] = strip_tags(html_entity_decode($helperForum->stripBBCode($data['description'])));
            $this->ForumTopic->set($data);

            // check captcha
            if ($helperForum->isCreateTopicRecaptchaEnabled())
            {
                App::import('Vendor', 'recaptchalib');
                $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
                $reCaptcha = new ReCaptcha($recaptcha_privatekey);
                $resp = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
                );

                if ($resp != null && !$resp->success) {
                    $response['result'] = 0;
                    $response['message'] = __d('forum','Invalid security code');

                    echo json_encode($response);
                    exit;
                }

            }

            if ( $this->ForumTopic->save() ) {
                $item = $this->ForumTopic->read();
                if($is_edit) {
                    $this->loadModel('Forum.ForumTopicHistory');
                    $this->ForumTopicHistory->save(array(
                        'user_id' => $old_item['ForumTopic']['user_edited'] ? $old_item['ForumTopic']['user_edited'] : $old_item['ForumTopic']['user_id'],
                        'content' => $old_item['ForumTopic']['description'],
                        'target_id' => $old_item['ForumTopic']['id'],
                        'created' => $old_item['ForumTopic']['modified'],
                    ));
                }

                $this->Tag->saveTags($this->request->data['tags'],  $this->ForumTopic->id, 'Forum_Forum_Topic');

                //update counter
                $forum = $this->Forum->findById($data['forum_id']);
                if($forum['Forum']['parent_id']){
                    $forum_ids = $this->Forum->getListSubForum($forum['Forum']['parent_id']);
                    $forum_ids[] = $forum['Forum']['parent_id'];
                    $cond = array('ForumTopic.parent_id' => 0, 'ForumTopic.forum_id' => $forum_ids);

                    $this->Forum->updateCounter($forum['Forum']['parent_id'], 'count_topic', $cond, 'ForumTopic');
                    $this->Forum->updateCounter($data['forum_id'], 'count_topic', array('ForumTopic.forum_id' => $data['forum_id'], 'ForumTopic.parent_id' => 0), 'ForumTopic');
                }else{
                    $forum_ids = $this->Forum->getListSubForum($data['forum_id']);
                    $forum_ids[] = $data['forum_id'];
                    $cond = array('ForumTopic.parent_id' => 0, 'ForumTopic.forum_id' => $forum_ids);

                    $this->Forum->updateCounter($data['forum_id'], 'count_topic', $cond, 'ForumTopic');
                }

                //update last topic id
                if($data['forum_id']) {
                    $this->Forum->updateLastTopic($data['forum_id'], $this->ForumTopic->id);
                }

                //subscribe
                $this->_subscribe($this->ForumTopic->id, $data['subscribe']);

                //save files
                $this->loadModel('Forum.ForumFile');
                $fileList = explode(',', $this->request->data['new_files']);
                $originalFileList = explode(',', $this->request->data['new_original_files']);
                $i=0;
                foreach ($fileList as $fileItem){
                    if(!empty($originalFileList[$i]))
                    {
                        $file = $this->ForumFile->find('first', array('conditions'=>array('ForumFile.file_name' => $fileItem)));
                        if(empty($file)) {
                            $this->ForumFile->create();

                            $this->ForumFile->set(array(
                                'target_id' => $this->ForumTopic->id,
                                'file_name' => $fileItem,
                                'download_url' => $originalFileList[$i],
                            ));
                            $this->ForumFile->save();
                        }
                    }
                    $i++;
                }

                if(!$is_edit){
                    $this->loadModel('Activity');
                    $this->Activity->save(array('type' => 'user',
                        'action' => 'forum_topic_create',
                        'user_id' => $uid,
                        'item_type' => 'Forum_Forum_Topic',
                        'item_id' => $item['ForumTopic']['id'],
                        'query' => 1,
                        'plugin' => 'Forum',
                        'params' => 'item',
                        'share' => 1,
                        'privacy' => PRIVACY_EVERYONE
                    ));

                    $this->_sendNotifications($item);
                }

                // update Topic item_id for photo thumbnail
                if (!empty($this->request->data['forum_topic_photo_ids'])) {
                    $photos = explode(',', $this->request->data['forum_topic_photo_ids']);
                    if (count($photos))
                    {
                        $this->loadModel('Photo.Photo');
                        // Hacking for cdn
                        $result = $this->Photo->find("all",array(
                            'recursive'=>1,
                            'conditions' =>array(
                                'Photo.type' => 'ForumTopic',
                                'Photo.user_id' => $uid,
                                'Photo.id' => $photos
                            )));
                        if($result){
                            $view = new View($this);
                            $mooHelper = $view->loadHelper('Moo');
                            foreach ($result as $iPhoto){
                                $iPhoto["Photo"]['moo_thumb'] = 'thumbnail';
                                $mooHelper->getImageUrl($iPhoto, array('prefix' => '450'));
                                $mooHelper->getImageUrl($iPhoto, array('prefix' => '1500'));
                            }
                            // End hacking
                            $this->Photo->updateAll(array('Photo.target_id' => $item['ForumTopic']['id']), array(
                                'Photo.type' => 'ForumTopic',
                                'Photo.user_id' => $uid,
                                'Photo.id' => $photos
                            ));
                        }

                    }
                }

                $response['result'] = 1;
                $page = '?page='.$this->request->data['page'];
                if($this->isApp())
                    $page.= "&";
                if($item['ForumTopic']['parent_id'] != '0')
                    $response['redirect'] = $this->request->base.'/forums/topic/view/'.$item['ForumTopic']['parent_id'].$page;
                else
                    $response['redirect'] = $this->request->base.'/forums/topic/view/'.$this->ForumTopic->id .$page;
                echo json_encode($response);
                exit;
            }
        }
    }

    public function upload_avatar()
    {
        $uid = MooCore::getInstance()->getViewer(true);

        if (!$uid)
            return;

        // save this picture to album
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        die();
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

    public function pin($id = null){
        if(!Configure::read('Forum.forum_enable_user_pin_topic')){
            $this->_checkPermission( array( 'admin' => true ) );
        }
    	$this->_checkPermission( array( 'confirm' => true ) );
    	$user = MooCore::getInstance()->getViewer();
    	$topic = $this->ForumTopic->findById($id);
    	$this->_checkExistence($topic);
    	$forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
    	$is_moderator = $forumHelper->checkModerator($user,array('Forum'=>$topic['Forum']));
    	if ( ($topic['ForumTopic']['user_id'] != $user['User']['id']) && !$is_moderator)
    	{
    		$this->_checkExistence(null);
    	}
    	$this->set('is_moderator',$is_moderator);
    	$this->set('topic',$topic);
    }

    public function ajax_thank($id = null, $parent_id = 0){
        $this->loadModel('Forum.ForumThank');

        $id = intval($id);
        $this->autoRender = false;
        $this->_checkPermission( array( 'confirm' => true ) );
        $uid = $this->Auth->user('id');

        // check to see if user already thank this item
        $thank = $this->ForumThank->getUserThank( $id, $uid );

        if(!empty($thank)){
            $this->ForumThank->delete( $thank['ForumThank']['id'] );
        }else{
            $data = array('target_id' => $id, 'user_id' => $uid, 'parent_id' => $parent_id);
            $this->ForumThank->save($data);

            // send notification to author
            $this->ForumTopic->bindModel(array
            (
                'belongsTo' => array
                (
                    'ParentTopic' => array
                    (
                        'className'     => 'Forum.ForumTopic',
                        'foreignKey' => false,
                        'conditions' => array
                        (
                            'ForumTopic.parent_id = ParentTopic.id'
                        )
                    )
                )
            ));
            $item = $this->ForumTopic->findById($id);

            $this->loadModel('Notification');
            if($item['User']['id'] != $uid) {
                $this->Notification->record(array('recipients' => $item['User']['id'],
                    'sender_id' => $uid,
                    'action' => 'thank_reply',
                    'url' => '/forums/topic/view/'.$item['ForumTopic']['parent_id'].'/reply_id:' . $id,
                    'params' => !empty($item['ParentTopic']['moo_title']) ? $item['ParentTopic']['moo_title'] : $item['ForumTopic']['moo_title'],
                    'plugin' => 'Forum'
                ));
            }
        }
        $this->ForumTopic->updateCounter($id, 'count_thank', array('ForumThank.target_id' => $id), 'ForumThank');
        $re = array('thank_count' => $this->ForumThank->getCountThanks($id));
        echo json_encode($re);
    }

    public function ajax_show( $id = null )
    {
        $this->loadModel('Forum.ForumThank');
        $id = intval($id);
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $count = 0;

        $users = $this->ForumThank->getThanks( $id, RESULTS_LIMIT, $page );
        $count = $this->ForumThank->getCountThanks($id);

        $this->set( 'users', $users );
        $this->set('page', $page);
        $this->set('count',$count);
        $this->set('more_url', '/forums/topic/ajax_show/' . $id . '/page:' . ( $page + 1 ) );

        $this->render('/Elements/ajax/user_overlay_thank');
    }

    public function ajax_invite($topic_id = null) {
        $topic_id = intval($topic_id);
        $this->_checkPermission(array('confirm' => true));

        $this->set('topic_id', $topic_id);
    }

    private function _subscribe($id = null, $sub = 1){
        $this->loadModel('Forum.ForumSubscribe');
        $uid = $this->Auth->user('id');
        $subscribe_id = $this->ForumSubscribe->isSubscribe($uid, $id);
        if(!$subscribe_id && $sub == 1) {
            $this->ForumSubscribe->set(array(
                'target_id' => $id,
                'user_id' => $uid,
                'type' => 'Topic',
            ));
            $this->ForumSubscribe->save();
        }else if($sub == 0 && $subscribe_id){
            $this->ForumSubscribe->delete($subscribe_id);
        }
    }

    public function subscribe($id = null){
        $this->_checkPermission(array('confirm' => true));
        $this->autoRender = false;
        $this->loadModel('Forum.ForumSubscribe');
        $uid = $this->Auth->user('id');
        $subscribe_id = $this->ForumSubscribe->isSubscribe($uid, $id);
        if(!$subscribe_id) {
            $this->ForumSubscribe->set(array(
                'target_id' => $id,
                'user_id' => $uid,
                'type' => 'Topic',
            ));
            if ($this->ForumSubscribe->save()) {
                $response['result'] = 1;
                echo json_encode($response);
                exit;

            }
        }else{
            $this->ForumSubscribe->delete($subscribe_id);
            $response['result'] = 2;
            echo json_encode($response);
            exit;
        }
    }

    public function favorite($id = null){
        $this->_checkPermission(array('confirm' => true));
        $this->autoRender = false;
        $this->loadModel('Forum.ForumFavorite');
        $uid = $this->Auth->user('id');
        $favorite_id = $this->ForumFavorite->isFavorite($uid, $id);
        if(!$favorite_id) {
            $this->ForumFavorite->set(array(
                'target_id' => $id,
                'user_id' => $uid,
                'type' => 'Topic',
            ));
            if ($this->ForumFavorite->save()) {
                $response['result'] = 1;
                echo json_encode($response);
                exit;
            }
        }else{
            $this->ForumFavorite->delete($favorite_id);
            $response['result'] = 2;
            echo json_encode($response);
            exit;
        }
    }

    public function ajax_sendInvite() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));
        $cuser = $this->_getUser();
        $this->loadModel('Forum.ForumSubscribe');
        $topic = $this->ForumTopic->findById($this->request->data['topic_id']);

        if ($this->request->data['invite_type_topic'] == 1)
        {
            if (!empty($this->request->data['friends'])) {
                $friends = explode(',', $this->request->data['friends']);

                $this->loadModel('Notification');
                $this->Notification->record(array('recipients' => $friends,
                    'sender_id' => $cuser['id'],
                    'action' => 'forum_topic_invite',
                    'url' => '/forums/topic/view/' . $this->request->data['topic_id'],
                    'params' => h($topic['ForumTopic']['title']),
                    'plugin' => 'Forum'
                ));
            } else {
                return $this->_jsonError(__d('forum', 'Recipient is required'));
            }
        }
        else
        {
            if (!empty($this->request->data['emails'])) {
                // check captcha
                $checkRecaptcha = MooCore::getInstance()->isRecaptchaEnabled();
                $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
                $is_mobile = $this->viewVars['isMobile'];
                if ( $checkRecaptcha && !$is_mobile)
                {
                    App::import('Vendor', 'recaptchalib');
                    $reCaptcha = new ReCaptcha($recaptcha_privatekey);
                    $resp = $reCaptcha->verifyResponse(
                        $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
                    );

                    if ($resp != null && !$resp->success) {
                        return	$this->_jsonError(__d('forum', 'Invalid security code'));
                    }
                }
                $emails = explode(',', $this->request->data['emails']);

                $i = 1;


                foreach ($emails as $email) {
                    $invite_checksum = uniqid();
                    if ($i <= 10) {
                        if (Validation::email(trim($email))) {
                            $ssl_mode = Configure::read('core.ssl_mode');
                            $http = (!empty($ssl_mode)) ? 'https' :  'http';
                            $this->MooMail->send(trim($email),'forum_topic_invite_none_member',
                                array(
                                    'topic_title' => $topic['ForumTopic']['moo_title'],
                                    'link' => $http.'://'.$_SERVER['SERVER_NAME'].$topic['ForumTopic']['moo_href'].'/'.$invite_checksum,
                                    'email' => trim($email),
                                    'sender_title' => $cuser['name'],
                                    'sender_link' => $http.'://'.$_SERVER['SERVER_NAME'].$cuser['moo_href'],
                                )
                            );
                        }
                    }
                    $i++;
                }
            }
            else
            {
                return	$this->_jsonError(__d('forum', 'Recipient is required'));
            }
        }

        $response = array();
        $response['result'] = 1;
        $response['msg'] = __d('forum', 'Your invitations have been sent.') . ' <a href="javascript:void(0)" onclick="$(\'#themeModal .modal-content\').load(\''.$this->request->base.'/forums/topic/ajax_invite/'.$this->request->data['topic_id'].'\');">' . __d('forum', 'Invite more friends') . '</a>';
        echo json_encode($response);
    }

    public function save_reply(){
        $this->_checkPermission(array('confirm' => true));
        $this->_checkTopicPermission($this->request->data['parent_id']);
        $this->autoRender = false;
        if($this->request->data){
            $this->loadModel('Tag');

            $helperForum = MooCore::getInstance()->getHelper('Forum_Forum');
            $uid = $this->Auth->user('id');
            $data = $this->request->data;
            $is_edit = false;

            if(!empty($data['id'])){
                $reply = $this->ForumTopic->findById($data['id']);
                $data['user_edited'] = $uid;
                $is_edit = true;
            }else{
                $data['user_id'] = $uid;
            }

            $this->ForumTopic->set($data);
            $this->_validateData( $this->ForumTopic );

            $data['search_desc'] = strip_tags(html_entity_decode($helperForum->stripBBCode($data['description'])));
            $this->ForumTopic->set($data);

            // check captcha
            if ($helperForum->isReplyRecaptchaEnabled())
            {
                App::import('Vendor', 'recaptchalib');
                $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
                $reCaptcha = new ReCaptcha($recaptcha_privatekey);
                $resp = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
                );

                if ($resp != null && !$resp->success) {
                    $response['result'] = 0;
                    $response['message'] = __d('forum','Invalid security code');

                    echo json_encode($response);
                    exit;
                }

            }

            if ( $this->ForumTopic->save() ) {
                $item = $this->ForumTopic->read();
                if(!empty($reply)) {
                    $this->loadModel('Forum.ForumTopicHistory');
                    $this->ForumTopicHistory->save(array(
                        'user_id' => $reply['ForumTopic']['user_edited'] ? $reply['ForumTopic']['user_edited'] : $reply['ForumTopic']['user_id'],
                        'content' => $reply['ForumTopic']['description'],
                        'target_id' => $reply['ForumTopic']['id'],
                        'created' => $reply['ForumTopic']['modified'],
                    ));
                }

                $this->ForumTopic->updateCounter($data['parent_id'], 'count_reply', array('ForumTopic.parent_id' => $data['parent_id']), 'ForumTopic');
                $this->ForumTopic->updateSortDate($data['parent_id'], date("Y-m-d H:i:s"));

                $this->loadModel('Forum.Forum');
                if($data['forum_id']) {
                    //update counter
                    $forum = $this->Forum->findById($data['forum_id']);
                    if($forum['Forum']['parent_id']){
                        $forum_ids = $this->Forum->getListSubForum($forum['Forum']['parent_id']);
                        $forum_ids[] = $forum['Forum']['parent_id'];
                        $cond = array('ForumTopic.parent_id <> 0', 'ForumTopic.forum_id' => $forum_ids);

                        $this->Forum->updateCounter($forum['Forum']['parent_id'], 'count_reply', $cond, 'ForumTopic');
                        $this->Forum->updateCounter($data['forum_id'], 'count_reply', array('ForumTopic.forum_id' => $data['forum_id'], 'ForumTopic.parent_id <> 0'), 'ForumTopic');
                    }else{
                        $forum_ids = $this->Forum->getListSubForum($data['forum_id']);
                        $forum_ids[] = $data['forum_id'];
                        $cond = array('ForumTopic.parent_id <> 0', 'ForumTopic.forum_id' => $forum_ids);

                        $this->Forum->updateCounter($data['forum_id'], 'count_reply', $cond, 'ForumTopic');
                    }
                }

                if(!$is_edit) {
                    //update last reply id & participant
                    if ($data['parent_id']) {
                        $this->ForumTopic->updateLastReply($data['parent_id'], $item['ForumTopic']['id']);
                        $this->ForumTopic->updateParticipant($data['parent_id']);

                    }

                }

                //subscribe
                if(isset($data['subscribe'])) {
                    $this->_subscribe($data['parent_id'], $data['subscribe']);
                }

                //save files
                $this->loadModel('Forum.ForumFile');
                $fileList = explode(',', $this->request->data['new_files']);
                $originalFileList = explode(',', $this->request->data['new_original_files']);
                $i=0;
                foreach ($fileList as $fileItem){
                    if(!empty($originalFileList[$i]))
                    {
                        $file = $this->ForumFile->find('first', array('conditions'=>array('ForumFile.file_name' => $fileItem)));
                        if(empty($file)) {
                            $this->ForumFile->create();

                            $this->ForumFile->set(array(
                                'target_id' => $item['ForumTopic']['id'],
                                'file_name' => $fileItem,
                                'download_url' => $originalFileList[$i],
                            ));
                            $this->ForumFile->save();
                        }
                    }
                    $i++;
                }

                // update Topic item_id for photo thumbnail
                if (!empty($this->request->data['forum_topic_photo_ids'])) {
                    $photos = explode(',', $this->request->data['forum_topic_photo_ids']);
                    if (count($photos))
                    {
                        $this->loadModel('Photo.Photo');
                        // Hacking for cdn
                        $result = $this->Photo->find("all",array(
                            'recursive'=>1,
                            'conditions' =>array(
                                'Photo.type' => 'ForumTopic',
                                'Photo.user_id' => $uid,
                                'Photo.id' => $photos
                            )));
                        if($result){
                            $view = new View($this);
                            $mooHelper = $view->loadHelper('Moo');
                            foreach ($result as $iPhoto){
                                $iPhoto["Photo"]['moo_thumb'] = 'thumbnail';
                                $mooHelper->getImageUrl($iPhoto, array('prefix' => '450'));
                                $mooHelper->getImageUrl($iPhoto, array('prefix' => '1500'));
                            }
                            // End hacking
                            $this->Photo->updateAll(array('Photo.target_id' => $item['ForumTopic']['id']), array(
                                'Photo.type' => 'ForumTopic',
                                'Photo.user_id' => $uid,
                                'Photo.id' => $photos
                            ));
                        }

                    }
                }

                if(!$is_edit){
                    //add feed
                    $this->loadModel("Activity");
                    $activity = $this->Activity->find('first', array('conditions' => array(
                        'Activity.action' => 'forum_topic_reply',
                        'Activity.item_id' => $item['ForumTopic']['parent_id'],
                        'Activity.user_id' => $uid,
                    )));
                    $this->Activity->clear();
                    $data_feed = array(
                        'type'=>'user',
                        'user_id' => $uid,
                        'action' => 'forum_topic_reply',
                        'item_type' => 'Forum_Forum_Topic',
                        'item_id' => $item['ForumTopic']['parent_id'],
                        'target_id' => $item['ForumTopic']['id'],
                        'params'=> 'no-comments',
                        'plugin' => 'Forum',
                        'privacy' => PRIVACY_EVERYONE,
                        'share' => 0,
                    );
                    if(!empty($activity)){
                        $this->Activity->id = $activity['Activity']['id'];
                        $data_feed['created'] = date('Y-m-d H:i:s');
                    }
                    $this->Activity->save($data_feed);

                    //notification
                    $this->_sendReplyNotifications($item);
                }

                $response['result'] = 1;
                $response['redirect'] = $this->request->base.'/forums/topic/view/'.$data['parent_id'];
                if ($this->isApp())
                {
                    $response['redirect'] .= '?app_no_tab=1';
                }
                echo json_encode($response);
                exit;
            }
        }
        $response['result'] = 0;
        $response['message'] = __d('forum','An error has occurred');
        echo json_encode($response);
        exit;
    }

    private function _sendReplyNotifications($topic) {
        if(!empty($topic)) {
            $this->loadModel('Notification');
            $this->loadModel("UserBlock");
            $this->loadModel("User");
            $this->loadModel("Forum.ForumSubscribe");

            $uid = $this->Auth->user('id');
            $block_users = $this->UserBlock->getBlockedUsers($topic['ForumTopic']['user_id']);
            $users = $this->ForumSubscribe->getUsersList($topic['ForumTopic']['parent_id'], 'Topic');
            $parent_topic = $this->ForumTopic->findById($topic['ForumTopic']['parent_id']);
            $user_mentions = $this->getUserMention($topic['ForumTopic']['description']);
            $user_mentions = array_diff($user_mentions,$users);
            //send notification to user subscribe
            if (!empty($users)) {
                foreach ($users as $user_id) {
                    if (!in_array($user_id, $block_users) && $user_id != $uid) {
                        if ($this->User->checkSettingNotification($user_id, 'reply_forum_topic')) {
                            $noti = $this->Notification->find( 'first', array( 'conditions' => array( 'Notification.user_id' => $user_id,
                                'Notification.sender_id' => $uid,
                                'Notification.action' => 'reply_topic',
                                'Notification.url LIKE' => '/forums/topic/view/'.$parent_topic['ForumTopic']['id'].'%',
                                'Notification.read' => 0
                            ) ));

                           if(empty($noti)){
                                $this->Notification->record(array('recipients' => $user_id,
                                    'sender_id' => $uid,
                                    'action' => 'reply_topic',
                                    'url' => '/forums/topic/view/' . $parent_topic['ForumTopic']['id'] . '/' . seoUrl($parent_topic['ForumTopic']['moo_title']) . '/reply_id:' . $topic['ForumTopic']['id'],
                                    'params' => $parent_topic['ForumTopic']['moo_title'],
                                    'plugin' => 'Forum'
                                ));
                            }
                        }
                    }
                }
            }

            //send notification to user mentioned
            if (!empty($user_mentions)) {
                $i = 0;
                foreach ($user_mentions as $user_id) {
                    if($i == Configure::read('Forum.forum_limit_notification_mention'))
                    {
                        break;
                    }
                    if (!in_array($user_id, $block_users) && $user_id != $topic['ForumTopic']['user_id']) {
                        if ($this->User->checkSettingNotification($user_id, 'reply_forum_topic')) {
                            $noti = $this->Notification->find( 'first', array( 'conditions' => array( 'Notification.user_id' => $user_id,
                                'Notification.sender_id' => $uid,
                                'Notification.action' => 'tag_member_reply_topic',
                                'Notification.url LIKE' => $parent_topic['ForumTopic']['moo_href'].'%',
                                'Notification.read' => 0
                            ) ));

                            if(empty($noti)){
                                $this->Notification->record(array('recipients' => $user_id,
                                    'sender_id' => $uid,
                                    'action' => 'tag_member_reply_topic',
                                    'url' => $parent_topic['ForumTopic']['moo_href'] . '/reply_id:' . $topic['ForumTopic']['id'],
                                    'params' => $parent_topic['ForumTopic']['moo_title'],
                                    'plugin' => 'Forum'
                                ));
                            }
                        }
                    }
                    $i++;
                }
            }
        }
    }

    private function _sendNotifications($topic) {
        if(!empty($topic)) {
            $this->loadModel('Notification');
            $this->loadModel("UserBlock");
            $this->loadModel("User");
            $this->loadModel("Forum.ForumSubscribe");
            $this->loadModel("Forum.Forum");

            $uid = $this->Auth->user('id');
            $block_users = $this->UserBlock->getBlockedUsers($topic['ForumTopic']['user_id']);
            $users = $this->ForumSubscribe->getUsersList($topic['ForumTopic']['forum_id'], 'Forum');
            $forum = $this->Forum->findById($topic['ForumTopic']['forum_id']);
            $user_mentions = $this->getUserMention($topic['ForumTopic']['description']);
            $user_mentions = array_diff($user_mentions,$users);

            if (!empty($users)) {
                foreach ($users as $user_id) {
                    if (!in_array($user_id, $block_users) && $user_id != $topic['ForumTopic']['user_id']) {
                        if ($this->User->checkSettingNotification($user_id, 'create_forum_topic')) {
                            $this->Notification->record(array('recipients' => $user_id,
                                'sender_id' => $uid,
                                'action' => 'create_topic',
                                'url' => '/forums/topic/view/'.$topic['ForumTopic']['id'].'/'.seoUrl($topic['ForumTopic']['moo_title']),
                                'params' => $forum['Forum']['moo_title'],
                                'plugin' => 'Forum'
                            ));
                        }
                    }
                }
            }

            //send notification to user mentioned
            if (!empty($user_mentions)) {
                $i = 0;
                foreach ($user_mentions as $user_id) {
                    if($i == Configure::read('Forum.forum_limit_notification_mention'))
                    {
                        break;
                    }
                    if (!in_array($user_id, $block_users) && $user_id != $topic['ForumTopic']['user_id']) {
                        if ($this->User->checkSettingNotification($user_id, 'create_forum_topic')) {
                            $this->Notification->record(array('recipients' => $user_id,
                                'sender_id' => $uid,
                                'action' => 'tag_member_create_topic',
                                'url' => $topic['ForumTopic']['moo_href'],
                                'params' => $topic['ForumTopic']['title'],
                                'plugin' => 'Forum'
                            ));
                        }
                    }
                    $i++;
                }
            }
        }
    }

    public function delete($id = null, $topic_id = 0){
        $id = intval($id);
        $topic = $this->ForumTopic->findById($id);
        $this->_checkExistence($topic);

        $allow_ids = array($topic['User']['id']);

        $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
        $moderators = $forumHelper->getModeratorFromForum(array('Forum' => $topic['Forum']));
        $allow_ids = array_merge($moderators, $allow_ids);

        $this->_checkPermission(array('admins' => $allow_ids));

        $this->ForumTopic->deleteTopic($topic);
        $cakeEvent = new CakeEvent('Plugin.Controller.Forum.afterDeleteForumTopic', $this, array('item' => $topic));
        $this->getEventManager()->dispatch($cakeEvent);

        $this->Session->setFlash(__d('forum','Entry has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if($topic_id){
            $this->redirect('/forums/topic/view/'.$topic_id);
        }else{
            $this->redirect('/forums/topic');
        }
    }

    public function reply($id = 0){
        $this->_checkPermission(array('confirm' => true));
        $reply = $this->ForumTopic->findById($id);
        $this->_checkExistence($reply);

        if(!empty($reply)){
            $this->loadModel('Tag');
            if(!empty($reply['ForumTopic']['description']))
            {
                $helperForum = MooCore::getInstance()->getHelper('Forum_Forum');
                $reply['ForumTopic']['description'] = $helperForum->parseTagMember($reply['ForumTopic']['description']);
            }
            // get files
            $this->loadModel('Forum.ForumFile');
            $files = $this->ForumFile->getFiles($id);
            $this->set('files', $files);

            //check subscribe
            $this->loadModel('Forum.ForumSubscribe');
            $uid = $this->Auth->user('id');
            $is_subscribe = $this->ForumSubscribe->isSubscribe($uid, $reply['ForumTopic']['parent_id'], 'Topic');

        }else{
            $reply = $this->ForumTopic->initFields();
        }

        $this->set('reply',$reply);
        $this->set('id',$id);
        $this->set('is_subscribe', isset($is_subscribe) ? $is_subscribe : false);
    }

    public function ajax_show_history($target_id) {
        $this->loadModel('Forum.ForumTopicHistory');
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        $histories = $this->ForumTopicHistory->getHistory($target_id, $page);
        $this->set('page', $page);
        $this->set('histories', $histories);
        $this->set('historiesCount', $this->ForumTopicHistory->getHistoryCount($target_id));
        $this->set('more_url', '/forums/topic/ajax_show_history/' . $target_id . '/page:' . ( $page + 1 ));
    }

    public function upload()
    {
        $helper = MooCore::getInstance()->getHelper('Forum_Forum');
        $this->_checkPermission(array('confirm' => true));
        $allowedExtensions = $helper->support_extention;

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'forums'.DS.'files';

        $original_filename = $this->request->query['qqfile'];
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            $result['document_file'] = $result['filename'];
            $result['original_filename'] = $original_filename;
        }

        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        die();
    }

    public function delete_file($file_name = null, $id = null)
    {
        $this->_checkPermission(array('confirm' => true));
        if ($file_name)
        {
            $dir = APP.'webroot'.DIRECTORY_SEPARATOR.'uploads' . DIRECTORY_SEPARATOR . 'forums'.DIRECTORY_SEPARATOR.'files';
            @unlink($dir.DIRECTORY_SEPARATOR.$file_name);

            if($id) {
                $this->loadModel('Forum.ForumFile');
                $this->ForumFile->delete($id);
            }
        }
        die();
    }

    public function lock($id = 0){
        $this->_checkPermission(array('confirm' => true));
        $this->_checkTopicPermission($id);
        $this->autoRender = false;

        $topic = $this->ForumTopic->findById($id);
        $response['result'] = 0;

        if(!empty($topic)){
            if($topic['ForumTopic']['status']){
               $data = array(
                   'id' => $id,
                   'status' => 0
               );
                $response['result'] = 2;
            }else{
                $data = array(
                    'id' => $id,
                    'status' => 1
                );
                $response['result'] = 1;
            }
            $this->ForumTopic->set($data);
            $this->ForumTopic->save();
        }
        echo json_encode($response);
    }

    public function signature(){
        $this->_checkPermission(array('confirm' => true));
        if($this->request->is('post')){
            $this->loadModel('User');
            $uid = $this->Auth->user('id');
            $data = array(
                'id' => $uid,
                'signature' => $this->request->data['signature'],
                'show_signature' => $this->request->data['show_signature'],
            );
            if($this->User->save($data)){
                echo json_encode(array('result' => 1));exit;
            }else{
                echo json_encode(array('result' => 0, 'message' => __d('forum', 'An error has occurred')));exit;
            }
        }
    }

    public function search($keyword='', $type = ''){
        $uid = $this->Auth->user('id');
        $cond = array();
        $scope = array();
        if(empty($keyword)){
            $this->redirect('/forums/topic');
        }
        $keyword = urldecode(urldecode($keyword));
        if ($type == 'hashtag')
        {
            $this->loadModel('Tag');
            $tag = h($keyword);
            $tags = $this->Tag->find('all', array('conditions' => array(
                'Tag.type' => 'Forum_Forum_Topic',
                'Tag.tag' => $tag
            )));
            $topic_ids = Hash::combine($tags, '{n}.Tag.id', '{n}.Tag.target_id');

            $this->loadModel('Hashtag');
            $items = $this->Hashtag->find('all',array(
                'conditions' => array(
                    'hashtags LIKE "%'.$keyword.'%"'
                )
            ));
            $topic_ids = array_merge($topic_ids,Hash::combine($items, '{n}.Hashtag.id', '{n}.Hashtag.item_id'));

            $cond['ForumTopic.id'] = $topic_ids;
            $keyword = '#'.$keyword;
        }else{
            $cond['OR'] = array(
                array('ForumTopic.title LIKE' => '%'.$keyword.'%', 'ForumTopic.parent_id = 0'),
                array('ForumTopic.search_desc LIKE' => '%'.$keyword.'%'),
            );
        }
        
        $this->ForumTopic->bindModel(array
        (
            'belongsTo' => array
            (
                'ParentTopic' => array
                (
                    'className'     => 'Forum.ForumTopic',
                    'foreignKey' => false,
                    'conditions' => array
                    (
                        'ForumTopic.parent_id = ParentTopic.id'
                    )
                )
            )
        ));

        $scope['order']['ForumTopic.id'] = 'desc';
        $scope['conditions'] = $cond;
        $scope['limit'] = Configure::read('Forum.forum_number_topic_per_page');
        $scope['paramType'] = 'querystring';
        $this->Paginator->settings = $scope;

        $topics = $this->Paginator->paginate( 'ForumTopic');

        $this->set(compact('topics', 'keyword'));
        $this->set('type',$type);
    }

    public function get_quote($id)
    {
        $this->autoRender = false;
        $data['content'] = '';
        if($id != null)
        {
            $this->ForumTopic->unbindModel(array('belongsTo'=> array('Forum','LastPost')));
            $topic = $this->ForumTopic->find('first',array(
                'conditions' => array('ForumTopic.id' => $id),
            ));
            if(!empty($topic))
            {
                $data['content'] = "[quote={$topic['ForumTopic']['user_id']};{$topic['ForumTopic']['id']}]{$this->delete_quote($topic['ForumTopic']['description'])}[/quote]";
            }
        }
        return json_encode($data);
    }

    private function delete_quote($text)
    {
        $pattern = '#\[quote=(.+)](\r\n)?(.+?)\[/quote]#si';
        $text = preg_replace($pattern,'',$text);
        return $text;
    }

    public function ajax_tag_member()
    {

    }

    public function get_tag_users()
    {
        $this->autoRender = false;
        $tag_member = $this->request->data['tag-member'];
        if(!empty($tag_member))
        {

            $order = 'FIND_IN_SET(User.id,\''.$tag_member.'\')';
            $users = $this->getUsersForTag(1,array('FIND_IN_SET(User.id,\''.$tag_member.'\')'), $order);
            $result['content'] = "";
            foreach($users as $user)
            {
                $result['content'] .= "[user={$user['User']['id']}]{$user['User']['name']}[/user] ";
            }
            return json_encode($result);
        }
    }

    private function getUsersForTag( $page = 1, $conditions = null, $order = 'User.id desc', $limit = RESULTS_LIMIT )
    {
        if ( empty( $conditions ) )
            $conditions = array( 'User.active' => 1 );
        $this->loadModel('User');
        $conditions = $this->User->addBlockCondition($conditions);
        $this->User->unbindModel(array('belongsTo' => array('ProfileType','Role')));
        $users = $this->User->find('all', array( 'conditions' => $conditions,
            'limit' 		=> $limit,
            'page'  		=> $page,
            'order' 		=> $order,
            'fields'        => array('User.id','User.name')
        )	);
        return $users;
    }

    private function getUserMention($text)
    {
        $pat = '#\[user=([0-9]+)](\r\n)?(.+?)\[/user]#si';
        preg_match_all($pat,$text,$users);
        return array_unique($users[1]);
    }
}