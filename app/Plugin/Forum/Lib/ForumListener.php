<?php
App::uses('CakeEventListener', 'Event');

class ForumListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'User.NotificationSettings.View' => 'notification',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'profile.afterRenderMenu'=> 'profileAfterRenderMenu',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'welcomeBox.afterRenderMenu' => 'welcomeBoxAfterRenderMenu',
//            'UserController.deleteUserContent' => 'deleteUserContent',
//            'Plugin.View.Api.Search' => 'apiSearch',

            'StorageHelper.forum_topic_thumb.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.forum_topic_thumb.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.forum_topic_thumb.getFilePath' => 'storage_amazon_get_file_path',

            'StorageHelper.forum_files.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.forum_files.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.forum_files.getFilePath' => 'storage_amazon_get_file_path',

            'StorageHelper.forum_thumb.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.forum_thumb.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.forum_thumb.getFilePath' => 'storage_amazon_get_file_path',

            'StorageHelper.forum_category_thumb.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.forum_category_thumb.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.forum_category_thumb.getFilePath' => 'storage_amazon_get_file_path',

            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
            'StorageAmazon.forum_topic_files.putObject.success' => 'storage_amazon_topic_files_put_success_callback',
            'StorageAmazon.photos.putObject.success.ForumTopic' => 'storage_amazon_photo_put_success_callback',

//            'ApiHelper.renderAFeed.forum_topic_create' => 'exportTopicCreate',
//            'ApiHelper.renderAFeed.forum_topic_item_detail_share' => 'exportTopicItemDetailShare',
//            'ApiHelper.renderAFeed.forum_topic_reply' => 'exportTopicReply',

            'Controller.Home.adminIndex.Statistic' => 'statistic',
        );
    }


    public function exportTopicCreate($e)
    {
        $data = $e->data['data'];
        $actorHtml = $e->data['actorHtml'];

        $topicModel = MooCore::getInstance()->getModel("Forum.ForumTopic");
        $topic = $topicModel->findById($data['Activity']['item_id']);
        $helper = MooCore::getInstance()->getHelper('Forum_Forum');
        $moohelper = MooCore::getInstance()->getHelper('Core_Moo');

        list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
        if(!empty($title_tmp)){
            $title =  $title_tmp['title'];
            $titleHtml = $title_tmp['titleHtml'];
        }else{
            $title = __d('forum','created a forum topic');
            $titleHtml = $actorHtml . ' ' . __d('forum','created a forum topic');
        }
        $e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Forum_Forum_Topic',
                'id' => $topic['ForumTopic']['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($topic['ForumTopic']['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $helper->bbcodetohtml($moohelper->cleanHtml($e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $topic['ForumTopic']['description'])), 200, array('eclipse' => '')), Configure::read('Forum.forum_enable_hashtag'))),true),
                'title' => h($topic['ForumTopic']['moo_title']),
                'images' => array('850'=>$helper->getTopicImage($topic,array('prefix'=>''))),
            ),
            'target' => $target,
        );
    }

    public function exportTopicItemDetailShare($e)
    {
        $data = $e->data['data'];
        $actorHtml = $e->data['actorHtml'];

        $topicModel = MooCore::getInstance()->getModel("Forum.ForumTopic");
        $topic = $topicModel->findById($data['Activity']['parent_id']);
        $helper = MooCore::getInstance()->getHelper('Forum_Forum');
        $moohelper = MooCore::getInstance()->getHelper('Core_Moo');

        $target = array();

        if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
        {
            $title = $data['User']['name'] . ' ' . __d('forum',"shared %s's forum topic", $topic['User']['name']);
            $titleHtml = $actorHtml . ' ' . __d('forum',"shared %s's forum topic", $e->subject()->Html->link($topic['User']['name'], FULL_BASE_URL . $topic['User']['moo_href']));
            $target = array(
                'url' => FULL_BASE_URL . $topic['User']['moo_href'],
                'id' => $topic['User']['id'],
                'name' => $topic['User']['name'],
                'type' => 'User',
            );
        }

        list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml,true);
        if(!empty($title_tmp)){
            $title .=  $title_tmp['title'];
            $titleHtml .= $title_tmp['titleHtml'];
        }

        $e->result['result'] = array(
            'type' => 'share',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Forum_Forum_Topic',
                'id' => $topic['ForumTopic']['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($topic['ForumTopic']['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $helper->bbcodetohtml($moohelper->cleanHtml($e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $topic['ForumTopic']['description'])), 200, array('eclipse' => '')), Configure::read('Forum.forum_enable_hashtag'))),true),
                'title' => h($topic['ForumTopic']['moo_title']),
                'images' => array('850'=>$helper->getTopicImage($topic,array('prefix'=>''))),
            ),
            'target' => $target,
        );
    }

    public function exportTopicReply($e)
    {
        $data = $e->data['data'];
        $actorHtml = $e->data['actorHtml'];

        $topicModel = MooCore::getInstance()->getModel("Forum.ForumTopic");
        $topic = $topicModel->findById($data['Activity']['item_id']);

        list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
        $title = __d('forum','reply a forum topic');
        $titleHtml = $actorHtml . ' ' . __d('forum','just replied on %s\'s topic: %s', '<a href="'.$topic['User']['moo_href'].'">'.$topic['User']['name'].'</a>','<a href="'.$topic['ForumTopic']['moo_href'].'/reply_id:'.$data['Activity']['target_id'].'">'.$topic['ForumTopic']['moo_title'].'</a>');

        $e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
            ),
            'target' => $target,
        );
    }

    public function storage_amazon_topic_files_put_success_callback($e)
    {
        $path = $e->data['path'];
        if (Configure::read('Storage.storage_amazon_delete_image_after_adding') == "1")
        {   //CakeLog::write('storage', $path);
            if ($path)
            {
                $file = new File($path);
                $file->delete();
                $file->close();
            }
        }
    }

    public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $type = $e->data['type'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];
        $url = '';

        switch ($type)
        {
            case 'forum_topic_thumb':
                if ($thumb) {
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/forums/thumb/'.$oid.'/'. $prefix . $thumb;
                } else {
                    $url = $v->getImage("forum/img/noimage/topic.png");
                }
                break;
            case 'forum_files':
                $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/forums/files/'. $thumb;
                break;
            case 'forum_thumb':
                $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/forums/forum_icons/thumb/'.$oid.'/'. $prefix . $thumb;
                break;
            case 'forum_category_thumb':
                if ($thumb) {
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/forums/category_icons/thumb/' . $oid . '/' . $prefix . $thumb;
                } else {
                    $url = $v->getImage("forum/img/noimage/category.png");
                }
                break;
        }

        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        $e->result['url'] = $v->getAwsURL($e->data['oid'], $type, $e->data['prefix'], $e->data['thumb']);
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];

        $path = false;
        switch ($type)
        {
            case 'forum_topic_thumb':
                if (!empty($thumb)) {
                    $path = WWW_ROOT . "uploads" . DS . "forums". DS . "thumb" . DS . $objectId . DS . $name . $thumb;
                }
                break;
            case 'forum_files':
                $path = WWW_ROOT . "uploads" . DS . "forums". DS. "files" . DS . $thumb;
                break;
            case 'forum_thumb':
                $path = WWW_ROOT . "uploads" . DS . "forums". DS. "forum_icons" . DS. "thumb" . DS . $objectId . DS . $name . $thumb;
                break;
            case 'forum_category_thumb':
                $path = WWW_ROOT . "uploads" . DS . "forums". DS. "category_icons" . DS. "thumb" . DS . $objectId . DS . $name . $thumb;
                break;
        }

        $e->result['path'] = $path;
    }

    public function storage_task_transfer($e)
    {
        $v = $e->subject();

        $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
        $topics = $topicModel->find('all', array(
                'conditions' => array("ForumTopic.id > " => $v->getMaxTransferredItemId("forum_topic_thumb")),
                'limit' => 10,
                'order' => array('ForumTopic.id'),
            )
        );
        if($topics){
            foreach($topics as $topic){
                if(!empty($topic["ForumTopic"]['thumb'])) {
                    $v->transferObject($topic["ForumTopic"]['id'], "forum_topic_thumb", '', $topic["ForumTopic"]['thumb']);
                }
            }
        }

        $fileModel = MooCore::getInstance()->getModel('Forum.ForumFile');
        $files = $fileModel->find('all', array(
                'conditions' => array("ForumFile.id > " => $v->getMaxTransferredItemId("forum_files")),
                'limit' => 10,
                'order' => array('ForumFile.id'),
            )
        );
        if($files){
            foreach($files as $file){
                if(!empty($file["ForumFile"]['file_name'])) {
                    $v->transferObject($file["ForumFile"]['id'], "forum_files", '', $file["ForumFile"]['file_name']);
                }
            }
        }

        $forumModel = MooCore::getInstance()->getModel('Forum.Forum');
        $forums = $forumModel->find('all', array(
                'conditions' => array("Forum.id > " => $v->getMaxTransferredItemId("forum_thumb")),
                'limit' => 10,
                'order' => array('Forum.id'),
            )
        );
        if($forums){
            foreach($forums as $forum){
                if(!empty($forum["Forum"]['thumb'])) {
                    $v->transferObject($forum["Forum"]['id'], "forum_thumb", '', $forum["Forum"]['thumb']);
                }
            }
        }

        $forumCategoryModel = MooCore::getInstance()->getModel('Forum.ForumCategory');
        $categories = $forumCategoryModel->find('all', array(
                'conditions' => array("ForumCategory.id > " => $v->getMaxTransferredItemId("forum_category_thumb")),
                'limit' => 10,
                'order' => array('ForumCategory.id'),
            )
        );
        if($categories){
            foreach($categories as $category){
                if(!empty($category["ForumCategory"]['thumb'])) {
                    $v->transferObject($category["ForumCategory"]['id'], "forum_category_thumb", '', $category["ForumCategory"]['thumb']);
                }
            }
        }
    }

    public function storage_amazon_photo_put_success_callback($e){
        $photo = $e->data['photo'];
        $path= $e->data['path'];
        $url= $e->data['url'];
        if (Configure::read('Storage.storage_cloudfront_enable') == "1"){
            $url = rtrim(Configure::read('Storage.storage_cloudfront_cdn_mapping'),"/")."/".$e->data['key'];
        }
        $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
        $topicModel->clear();
        $topic = $topicModel->find("first",array(
            'conditions' => array("ForumTopic.id"=>$photo['Photo']['target_id']),
        ));
        if($topic){
            $findMe = str_replace(WWW_ROOT,"",$path);
            $isReplaced = false;
            $regexp = "[\s[^>]*url=(\[??)([^\] >]*?)\\1[^>]*]";
            if(preg_match_all("/$regexp/siU", $topic['ForumTopic']['description'], $matches)) {
                foreach ($matches[2] as $match){
                    if(strpos($match, $findMe) !== false){
                        $isReplaced = true;
                        $topic['ForumTopic']['description'] = str_replace($match,$url,$topic['ForumTopic']['description']);
                    }
                }
            }
            $regexp = "[\s[^>]*img](\[??)([^\[ >]*?)\\1[^>]*]";
            if(preg_match_all("/$regexp/siU", $topic['ForumTopic']['description'], $matches)) {
                foreach ($matches[2] as $match){
                    if(strpos($match, $findMe) !== false){
                        $isReplaced = true;
                        $topic['ForumTopic']['description'] = str_replace($match,$url,$topic['ForumTopic']['description']);
                    }
                }
            }
            if($isReplaced){
                $topicModel->clear();
                unset($topicModel->validate['title']);
                $topicModel->save($topic);
            }
        }

    }

    public function beforeRender($event)
    {
        if(Configure::read('Forum.forum_enabled')){
            $e = $event->subject();
            $e->Helpers->Html->css( array(
                'Forum.main',
                'global/typehead/bootstrap-tagsinput.css',
            ),
                array('block' => 'css')
            );

            if ($e->theme == 'mooApp') {
                $e->Helpers->Html->css( array(
                    'Forum.main-app'
                ),
                    array('block' => 'css')
                );
            }

            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }
            $e->Helpers->MooRequirejs->addPath(array(
                "mooForum"=>$e->Helpers->MooRequirejs->assetUrlJS("Forum.js/main.{$min}js"),
                "mooViewForum"=>$e->Helpers->MooRequirejs->assetUrlJS("Forum.js/view.{$min}js"),
            ));

            $e->addPhraseJs(array(
                'subscribe' => __d('forum', "Subscribe"),
                'subscribed' => __d('forum', "Unsubscribe"),
                'drag_photo' => __d('forum', "Drag or click here to upload photo"),
                'drag_file' => __d('forum', "Drag or click here to upload files"),
                'delete' => __d('forum', "Delete"),
                'lock' => __d('forum', "Lock"),
                'open' => __d('forum', "Open"),
                'this_forum_is_marked_as_locked_for_new_topic' => __d('forum', "This forum is marked as locked for new topic"),
                'are_you_sure_you_want_to_unpin_this_topic' => __d('forum', "Are you sure you wan to unpin this topic?"),
                'day_numeric' => __d('forum','Day only allow numbers'),
                'day_required' => __d('forum','Day is required'),
                'you_must_select_at_least_an_item' => __d('forum', 'You must select at least an item'),
                'are_you_sure_you_want_to_delete' => __d('forum', 'Are you sure you want to delete?'),
            ));
        }
    }

    public function notification($event)
    {
        $e = $event->subject();
        if(!Configure::read('Forum.forum_enabled'))
        {
            return;
        }

        echo $e->element('Forum.setting');
    }

    public function search($event)
    {
        if(Configure::read('Forum.forum_enabled')){
            $e = $event->subject();

            $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
            $cond = array('ForumTopic.title LIKE' => '%'.$e->keyword.'%');
            $results = $topicModel->getTopics($cond, 1,4);

            if(isset($e->plugin) && $e->plugin == 'Forum')
            {
                $e->set('forum_topics', $results);
                $e->render("Forum.Elements/lists/topic_list_m");
                $e->set('no_list_id',true);
            }
            else
            {
                $event->result['Forum']['header'] = __d('forum',"Forum Topic");
                $event->result['Forum']['icon_class'] = "forum";
                $event->result['Forum']['view'] = "lists/topic_list_m";
                $e->set('no_list_id',true);
                if(!empty($results))
                    $event->result['Forum']['notEmpty'] = 1;
                $e->set('forum_topics', $results);
            }
        }
    }

    public function suggestion($event)
    {
        if(Configure::read('Forum.forum_enabled')){
            $e = $event->subject();
            $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');

            $cond = array('ForumTopic.title LIKE' => '%'.$event->data['searchVal'].'%');

            $event->result['forum']['header'] = __d('forum',"Forum");
            $event->result['forum']['icon_class'] = 'forum';

            if(isset($event->data['type']) && $event->data['type'] == 'forum')
            {
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $topics = $topicModel->getTopics($cond, $page);
                $topics_next = $topicModel->getTopics($cond, $page+1);

                $e->set('topics', $topics);
                $e->set('result',1);
                $e->set('no_list_id',true);

                if ($topics_next && count($topics_next))
                    $e->set('is_view_more',true);

                $e->set('url_more','/search/suggestion/forum/'.$e->params['pass'][1]. '/page:' . ( $page + 1 ));
                if($page > 1){
                    $e->set('element_list_path',"Forum.lists/topic_list");
                }else{
                    $e->set('element_list_path',"Forum.lists/topic_list_m");
                }
            }
            if(isset($event->data['type']) && $event->data['type'] == 'all')
            {
                $event->result['forum'] = null;
                $topics = $topicModel->getTopics($cond, 1);
                $helper = MooCore::getInstance()->getHelper('Forum_Forum');
                $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');

                if(!empty($topics)){
                    $event->result['forum'] = array(__d('forum','Forum'));
                    foreach($topics as $index=>$detail){
                        $index++;
                        $event->result['forum'][$index]['id'] = $detail['ForumTopic']['id'];
                        if(!empty($detail['ForumTopic']['thumb']))
                        {
                            $event->result['forum'][$index]['img'] = $helper->getTopicImage($detail,array('prefix' => '50'));

                        }
                        else
                        {
                            $event->result['forum'][$index]['img'] = '/forum/img/noimage/topic.png';
                        }

                        $event->result['forum'][$index]['title'] = $detail['ForumTopic']['title'];
                        $event->result['forum'][$index]['find_name'] = __d('forum','Find Forum Topic');
                        $event->result['forum'][$index]['icon_class'] = 'book';
                        $event->result['forum'][$index]['view_link'] = 'forums/topic/view/';

                        if(!empty($detail['User']['id'])){
                            $username = $mooHelper->getNameWithoutUrl($detail['User'], false);
                        }else{
                            $username = __d('forum','Deleted Account');
                        }

                        $event->result['forum'][$index]['more_info'] = __d('forum','Posted by') . ' ' . $username . ' ' . $mooHelper->getTime( $detail['ForumTopic']['created'], Configure::read('core.date_format'), $e->viewVars['utz'] );
                    }
                }
            }
        }
    }

    public function hashtags($event)
    {
        $enable = Configure::read('Forum.forum_enable_hashtag');
        $topics = array();
        $e = $event->subject();
        App::import('Model', 'Forum.ForumTopic');
        App::import('Model', 'Tag');
        $this->Tag = new Tag();
        $this->ForumTopic = new ForumTopic();
        $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

        if ($enable) {
            if (isset($event->data['type']) && $event->data['type'] == 'forum_topics') {
                $topics = $this->ForumTopic->getTopicHashtags($event->data['item_ids'], RESULTS_LIMIT, $page);
            }
            $table_name = $this->ForumTopic->table;
            if (isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name])) {
                $topics = $this->ForumTopic->getTopicHashtags($event->data['item_groups'][$table_name], 5);
            }
        }

        // get tagged item
        $tag = h(urldecode($event->data['search_keyword']));
        $tags = $this->Tag->find('all', array('conditions' => array(
            'Tag.type' => 'Forum_Forum_Topic',
            'Tag.tag' => $tag
        )));
        $topic_ids = Hash::combine($tags, '{n}.Tag.id', '{n}.Tag.target_id');

        $items = $this->ForumTopic->find('all', array('conditions' => $this->ForumTopic->addBlockCondition(array(
            'ForumTopic.id' => $topic_ids
        )),
            'limit' => RESULTS_LIMIT,
            'page' => $page
        ));

        $topics = array_merge($topics, $items);

        //only display 5 items on All Search Result page
        if (isset($event->data['type']) && $event->data['type'] == 'all') {
            $topics = array_slice($topics, 0, 5);
        }
        $topics = array_map("unserialize", array_unique(array_map("serialize", $topics)));
        if (!empty($topics)) {
            $event->result['forum_topics']['header'] = __d('forum','Forums');
            $event->result['forum_topics']['icon_class'] = 'forum';
            $event->result['forum_topics']['view'] = "Forum.lists/topic_list_m";
            if (isset($event->data['type']) && $event->data['type'] == 'forum_topics') {
                $e->set('result', 1);
                $e->set('more_url', '/search/hashtags/' . $e->params['pass'][0] . '/forum_topics/page:' . ($page + 1));
                $e->set('element_list_path', "Forum.lists/topic_list_m");
            }
            $e->set('topics', $topics);
        }
    }

    public function hashtags_filter($event)
    {

        $e = $event->subject();
        App::import('Model', 'Forum.ForumTopic');
        $this->ForumTopic = new ForumTopic();

        if (isset($event->data['type']) && $event->data['type'] == 'forum_topics') {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $topics = $this->ForumTopic->getTopicHashtags($event->data['item_ids'], RESULTS_LIMIT, $page);
            $e->set('topics', $topics);
            $e->set('result', 1);
            $e->set('more_url', '/search/hashtags/' . $e->params['pass'][0] . '/forum_topics/page:' . ($page + 1));
            $e->set('element_list_path', "Forum.lists/topic_list_m");
        }
        $table_name = $this->ForumTopic->table;
        if (isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name])) {
            $event->result['topics'] = null;

            $topics = $this->ForumTopic->getTopicHashtags($event->data['item_groups'][$table_name], 5);

            if (!empty($topics)) {
                $event->result['forum_topics']['header'] = __d('forum','Forums');
                $event->result['forum_topics']['icon_class'] = 'forum';
                $event->result['forum_topics']['view'] = "Forum.lists/topic_list_m";
                $e->set('topics', $topics);
            }
        }
    }

    public function profileAfterRenderMenu($event)
    {
        $view = $event->subject();
        $uid = MooCore::getInstance()->getViewer(true);
        $subject = MooCore::getInstance()->getSubject();
        if($subject){
            $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
            $total = $topicModel->getTotalUserTopic($subject['User']['id']);
            echo $view->element('menu_profile',array('count'=>$total),array('plugin'=>'Forum'));
        }
    }

    public function statistic($event)
    {
        $request = Router::getRequest();
        $model = MooCore::getInstance()->getModel("Forum.ForumTopic");
        $event->result['statistics'][] = array(
            'item_count' => $model->find('count',array(
                'conditions' => array(
                    'ForumTopic.parent_id' => 0
                ),
            )),
            'ordering' => 9999,
            'name' => __d('forum','Forum\'s Topic'),
            'href' => $request->base.'/forums/topic',
            'icon' => '<i class="material-icons">forum</i>'
        );
    }

    public function welcomeBoxAfterRenderMenu($event)
    {
        $view = $event->subject();
        $uid = MooCore::getInstance()->getViewer(true);
        if(Configure::read('Forum.forum_enabled') && $uid){
            $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
            $total = $topicModel->getTotalUserTopic($uid);
            echo $view->element('menu_welcome',array('count'=>$total),array('plugin'=>'Forum'));
        }
    }

    public function deleteUserContent($event)
    {
        $topicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
        $topics = $topicModel->find('all', array(
            'conditions' => array(
                'ForumTopic.user_id' => $event->data['aUser']['User']['id'],
                'ForumTopic.parent_id' => 0,
            )
        ));
        foreach ($topics as $topic) {
            $topicModel->deleteTopic($topic);
        }

        $replies = $topicModel->find('all', array(
            'conditions' => array(
                'ForumTopic.user_id' => $event->data['aUser']['User']['id'],
                'ForumTopic.parent_id <>' => 0,
            )
        ));
        foreach ($replies as $reply) {
            $topicModel->deleteTopic($reply);
        }
    }

    public function apiSearch($event)
    {
        $view = $event->subject();
        $items = &$event->data['items'];
        $type = $event->data['type'];
        $viewer = MooCore::getInstance()->getViewer();
        $utz = $viewer['User']['timezone'];
        if ($type == 'Forum' && isset($view->viewVars['forum_topics']) && count($view->viewVars['forum_topics']))
        {
            $helper = MooCore::getInstance()->getHelper('Forum_Forum');
            foreach ($view->viewVars['forum_topics'] as $item){

                if(!empty($item['User']['id'])){
                    $username = $view->Moo->getNameWithoutUrl($item['User'], false);
                }else{
                    $username = __d('forum','Deleted Account');
                }


                $items[] = array(
                    'id' => $item["ForumTopic"]['id'],
                    'url' => FULL_BASE_URL.$item['ForumTopic']['moo_href'],
                    'avatar' =>  $helper->getTopicImage($item, array('prefix' => '150_square')),
                    'owner_id' => $item["ForumTopic"]['user_id'],
                    'title_1' => $item["ForumTopic"]['moo_title'],
                    'title_2' => __( 'Posted by') . ' ' . $username . ' ' .$view->Moo->getTime( $item["ForumTopic"]['created'], Configure::read('core.date_format'), $utz ),
                    'created' => $item["ForumTopic"]['created'],
                    'type' => "Forum",
                    'type_title' => __d('forum',"Forum")
                );
            }
        }
    }
}