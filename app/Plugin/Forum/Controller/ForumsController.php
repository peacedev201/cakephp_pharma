<?php
App::uses('ForumBBcode','Forum.Lib');
class ForumsController extends ForumAppController
{
    public $components = array('Paginator');
    /**
     * Admin
     */

    /**
     * Show list categories and forums
     */
    public function admin_index()
    {
        $this->loadModel('Forum.ForumCategory');
        $categories = $this->ForumCategory->find('all',array(
            'order' => 'ForumCategory.order asc'
        ));
        $this->set('categories', $categories);
        $allMod = $this->Forum->getAllMod();
        $this->set('mods',$allMod);
        $this->set('title_for_layout', __d('forum','Forums'));
    }

    public function admin_sub($idForum = null)
    {
        if($idForum!= null)
        {
            $forumParent = $this->Forum->findById($idForum);
            $this->set('forumParent', $forumParent);
            $allMod = $this->Forum->getAllMod();
            $this->set('mods',$allMod);
            $this->set('title_for_layout', __d('forum','Forums'));
        }
    }


    /**
     * @param null $id forum id to delete
     */
    public function admin_delete_confirm()
    {
        $this->autoRender = false;
        $id = $this->request->data['id'];
        if (!empty($id)) {
            $forum = $this->Forum->find('first', array(
                'conditions' => array('Forum.id' => $id)
            ));
            if (!empty($forum)) {
                //Delete forum parent_id = 0
                if ($forum['Forum']['parent_id'] == 0) {
                    $findsubs = $this->Forum->find('count', array(
                        'conditions' => array('Forum.parent_id' => $id),
                    ));
                    if ($findsubs > 0) {
                        $response['result'] = 0;
                        $response['message'] = __d('forum', 'Please delete sub-forum first.');
                        echo json_encode($response);
                        exit();
                    } else {
                        //confirm before delete
                        $response['result'] = 1;
                        $response['id'] = $id;
                        $response['message'] = __d('forum', 'Are you sure you want to delete? All of topics inside of this forum will also be deleted');
                        echo json_encode($response);
                        exit();
                    }
                } //Delete forum parent_id != 0
                else {
                    //confirm before delete
                    $response['result'] = 1;
                    $response['id'] = $id;
                    $response['message'] = __d('forum', 'Are you sure you want to delete? All of topics inside of this forum will also be deleted');
                    echo json_encode($response);
                    exit();
                }
            } else exit();
        }
    }

    /**
     * @param null $id forum
     */
    public function admin_delete($id = null)
    {
        try {
            //delete content
            $this->loadModel('Forum.ForumTopic');
            $topics = $this->ForumTopic->find('all', array(
                'conditions' => array('ForumTopic.forum_id' => $id, 'ForumTopic.parent_id' => 0)
            ));
            $total = count($topics);
            $i = 1;
            foreach ($topics as $topic){
                if($i == $total){
                    $this->ForumTopic->deleteTopic($topic, true);
                }else{
                    $this->ForumTopic->deleteTopic($topic, false);
                }
            }

            $this->loadModel('Forum.ForumSubscribe');
            $this->ForumSubscribe->deleteAll(array('ForumSubscribe.target_id' => $id, 'ForumSubscribe.type' => 'Forum'), true, true);
            //end delete content
            $this->Forum->delete($id);
            $this->Session->setFlash( __d('forum','Forum has been deleted') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $response['result'] = 1;
            echo json_encode($response);
            exit();
        } catch (Exception $e) {
            $response['result'] = 0;
            $response['message'] = $e;
            echo json_encode($response);
            exit();
        }

    }

    public function admin_create($cat_id = null, $id = null, $flag_sub = false,$parent_id = 0)
    {
        $bIsEdit = false;
        $forum = $this->Forum->initFields();
        if($flag_sub == true)
        {
            $this->set('flag_sub',$flag_sub);
            $forumId = $cat_id;
            $parents = $this->Forum->find('all', array(
                'conditions' => array(
                    'Forum.category_id' => $forumId,
                    'Forum.parent_id' => 0
                ),
                'fields' => 'Forum.id,Forum.name,Forum.category_id'
            ));
            $this->set('parent_id',$parent_id);
            unset($forumId);
        }
        else{
            $this->loadModel('Forum.ForumCategory');
            $parents = $this->ForumCategory->find('all', array(
                'fields' => 'ForumCategory.id, ForumCategory.name'
            ));
        }
        if (!empty($id)) {
            $bIsEdit = true;
            $forum = $this->Forum->find('first', array(
                'conditions' => array(
                    'Forum.id' => $id
                ),
            ));
            if(!empty($forum['Forum']['moderator']))
            {
                $this->loadModel('User');
                $friends = $this->User->getUsers(1,array('FIND_IN_SET(User.id,\''.$forum['Forum']['moderator'].'\')'));
                $friend_options = array();
                foreach ($friends as $friend)
                    $friend_options[] = array( 'id' => $friend['User']['id'], 'name' => $friend['User']['name'], 'avatar' => $friend['User']['avatar'] );
                $this->set('friends',json_encode( $friend_options ));
            }
            else
                $this->set('friends',null);
        }
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');
        $this->set('roles', $roles);
        $this->set('cat_id', $cat_id);
        $this->set('parents', $parents);
        $this->set('bIsEdit', $bIsEdit);
        $this->set('forum', $forum);

    }
    public function admin_create_sub($parent_id = null, $id = null)
    {
        $this->admin_create($parent_id, $id,true);
        //$this->autoRender = false;
    }
    public function admin_save_forum()
    {
        $this->autoRender = false;
        $bIsEdit = false;
        $mods = explode(',',$this->request->data['moderator']);
        if(!empty($this->data['id']))
        {
            $this->Forum->id = $this->request->data['id'];
            $forum = $this->Forum->findById($this->request->data['id']);
            $bIsEdit = true;
            if(empty($this->request->data['thumb']))
            {
                unset($this->request->data['thumb']);
            }
            if((isset($this->request->data['parent_id']) && $this->request->data['parent_id'] != $forum['Forum']['parent_id'])  || $forum['Forum']['category_id'] != $this->request->data['category_id'])
            {
                $parent_id = (isset($this->request->data['parent_id']))? $this->request->data['parent_id'] : 0;
                $this->request->data['order'] = $this->Forum->genOrder($parent_id,$this->request->data['category_id']);
            }
            $mods = array_diff($mods,explode(',',$forum['Forum']['moderator']));
        }
        else
        {
            $parent_id = (isset($this->request->data['parent_id']))? $this->request->data['parent_id'] : 0;
            $this->request->data['order'] = $this->Forum->genOrder($parent_id,$this->request->data['category_id']);
        }
        if(empty($this->request->data['name'])){
            $response['result'] = 0;
            $response['message'] = __d('forum','Forum title can not empty');
            echo json_encode($response);
            exit();
        }
        if(empty($this->request->data['description'])){
            $response['result'] = 0;
            $response['message'] = __d('forum','Description can not empty');
            echo json_encode($response);
            exit();
        }
        if($bIsEdit == false && empty($this->request->data['thumb']))
        {
            $response['result'] = 0;
            $response['message'] = __d('forum','Please select icon for forum');
            echo json_encode($response);
            exit();
        }
        if(!isset($_POST['permissions']) && $this->request->data['everyone'] == '0')
        {
            $this->request->data['permission'] = null;
        }
        else
        {
            $this->request->data['permission'] = (empty( $this->request->data['everyone'] )) ? implode(',', $_POST['permissions']) : '';
        }
        $data = $this->request->data;
        $this->Forum->set($data);
        if($this->Forum->save()){
            if (!empty($this->request->data['moderator']))
            {
                $this->loadModel('Forum.ForumSubscribe');
                $moderators = explode(',',$this->request->data['moderator']);
                foreach ($moderators as $mod_id){
                    $subscribe_id = $this->ForumSubscribe->isSubscribe($mod_id, $this->Forum->id, 'Forum');
                    if(!$subscribe_id) {
                        $this->ForumSubscribe->clear();
                        $this->ForumSubscribe->set(array(
                            'target_id' => $this->Forum->id,
                            'user_id' => $mod_id,
                            'type' => 'Forum',
                        ));
                        $this->ForumSubscribe->save();
                    }
                }
            }
        }
        $this->Session->setFlash(__d('forum','Forum has been successfully saved'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));
        if(!empty($mods))
        {
            $uid = $this->Auth->user('id');
            $this->loadModel('Notification');
            foreach ($mods as $mod) {
                $this->Notification->record(array('recipients' => $mod,
                    'sender_id' => $uid,
                    'action' => 'add_moderator',
                    'url' => '/forums/view/' . $this->Forum->id,
                    'params' => $this->request->data['name'],
                    'plugin' => 'Forum'
                ));
            }
        }
        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_moveup($idForum,$parent_id,$cat_id,$order_current)
    {
        $this->autoRender = false;
        $neighbors = $this->Forum->find('neighbors', array(
            'order' => 'Forum.order asc',
            'conditions' => array(
                'Forum.category_id' => $cat_id,
                'Forum.parent_id' => $parent_id,
                //'Forum.id' => $idForum,
            ),
            'field' => 'Forum.order', 'value' => $order_current,
        ));
        if(!empty($neighbors['prev']))
        {
            $order_prev = $neighbors['prev']['Forum']['order'];
            $this->Forum->setOrder($neighbors['prev']['Forum']['id'],$order_current);
            $this->Forum->setOrder($idForum,$order_prev);
        }
        if($parent_id != 0)
            $this->redirect(array('action' => 'admin_sub',$parent_id));
        else
            $this->redirect(array('action' => 'admin_index'));
    }



    public function admin_remove_mod($forumID = null, $userID = null)
    {
        $this->autoRender = false;
        if(!empty($forumID) && !empty($userID))
        {
            $forum = $this->Forum->findById($forumID);
            $array_moderator = array_diff(str_getcsv($forum['Forum']['moderator']), array($userID));
            $data['moderator'] = implode(',', $array_moderator); ;
            $this->Forum->id = $forum['Forum']['id'];
            $this->Forum->set($data);
            $this->Forum->save();
            if($forum['Forum']['parent_id'] != 0)
                $this->redirect(array('action' => 'admin_sub',$forum['Forum']['parent_id']));
            else
                $this->redirect(array('action' => 'admin_index'));
        }
    }

    public function test()
    {
        //var_dump($this->Forum->getAllMod());
        $image = getcwd(). "\\uploads\\tmp\\cbacfb084723d28cbb7cac68a4627d17.png";
        var_dump(getimagesize($image));
    }
    /**
     * End Admin
     */


    public function index()
    {
        $this->loadModel('Forum.ForumCategory');
        $this->loadModel('Forum.ForumTopic');
        $cats = $this->ForumCategory->find('all',array(
            'conditions' => array(),
            'order' => array('ForumCategory.order asc')
        ));
        foreach($cats as &$cat)
        {
            $this->Forum->unbindModel(array('belongsTo' => 'ForumCategory'));
            $cat['Forums'] = $this->Forum->getForumByCatId($cat['ForumCategory']['id']);
            if(is_array($cat['Forums']))
            {
                foreach ($cat['Forums'] as &$forum)
                {
                    $this->ForumTopic->unbindModel(array('belongsTo'=> array('Forum','LastPost')));
                    $forum['last_topic'] = $this->ForumTopic->findById($forum['Forum']['last_topic_id']);
                    if(!empty($forum['last_topic']))
                    {
                        if($forum['last_topic']['ForumTopic']['parent_id'] != 0)
                        {
                            $this->ForumTopic->unbindModel(array('belongsTo'=> array('Forum','LastPost')));
                            $parent_topic = $this->ForumTopic->find('first',array(
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
                    $this->Forum->unbindModel(array('belongsTo' => array('ForumCategory')));
                    $forum['subs'] = $this->Forum->getSubForumByParentId($forum['Forum']['id']);
                }
            }
        }
        $this->set('title_for_layout', '');
        $this->set('cats',$cats);
        $this->set('type','forum');
    }

    public function view($id = null){
        $this->loadModel('Forum.ForumTopic');
        $this->loadModel('Forum.ForumSubscribe');

        $uid = $this->Auth->user('id');
        $id = intval($id);
        $forum = $this->Forum->findById($id);
        $this->_checkExistence($forum);
        $this->checkPermissionForum($id);
        $cond = array(
            'ForumTopic.parent_id' => 0,
            'ForumTopic.forum_id' => $id,
        );

        if ( !empty( $this->request->named['keyword'] ))
        {
            $keyword = $this->request->named['keyword'];
            $cond['OR'] = array(
                'ForumTopic.title LIKE' => '%' . $keyword . '%',
                'ForumTopic.search_desc LIKE' => '%' . $keyword . '%',
            );
        }

        if ( !empty( $this->request->named['hashtag'] ))
        {
            $this->loadModel('Tag');
            $keyword = $this->request->named['hashtag'];
            $tag = h(urldecode($keyword));
            $tags = $this->Tag->find('all', array('conditions' => array(
                'Tag.type' => 'Forum_Forum_Topic',
                'Tag.tag' => $tag
            )));
            $topic_ids = Hash::combine($tags, '{n}.Tag.id', '{n}.Tag.target_id');
            $cond['ForumTopic.id'] = $topic_ids;
            $keyword = '#'.$keyword;
        }

        $scope = array();
        $scope['conditions'] = $cond;
        $scope['limit'] = Configure::read('Forum.forum_number_topic_per_page');
        $scope['paramType'] = 'querystring';
        $this->Paginator->settings = $scope;

        $topics = $this->Paginator->paginate( 'ForumTopic');
        $sub_forums = $this->Forum->getSubForumByParentId($id);
        //moderator
        $mods = array();
        $moderators = array();
        if(!empty($forum['Forum']['moderator'])){
            $mods = explode(',',$forum['Forum']['moderator']);
        }
        if ($forum['Forum']['parent_id']) {
            $forum_parent = $this->Forum->findById($forum['Forum']['parent_id']);
            if (!empty($forum_parent['Forum']['moderator']))
            {
                $mods = array_merge($mods,explode(',',$forum_parent['Forum']['moderator']));
            }
        }
        if(!empty($mods)){
            $this->loadModel('User');
            $moderators = $this->User->find('all', array(
                'conditions' => array('User.id' => $mods)
            ));
        }

        //SEO
        $this->set('title_for_layout', htmlspecialchars($forum['Forum']['moo_title']));
        $description = $this->getDescriptionForMeta($forum['Forum']['description']);
        if ($description) {
            $this->set('description_for_layout', $description);
            $this->set('mooPageKeyword', $this->getKeywordsForMeta($description));
        }

        // set og:image
        if ($forum['Forum']['thumb']) {
            $helper = MooCore::getInstance()->getHelper('Forum_Forum');
            $this->set('og_image', $helper->getIconForum($forum));

        }

        $last_topic = $this->ForumTopic->findById($forum['Forum']['last_topic_id']);
        $is_subscribe = $this->ForumSubscribe->isSubscribe($uid, $id, 'Forum');
        $is_view_forum = 1;
        $this->set(compact('topics', 'keyword', 'forum', 'is_subscribe', 'last_topic','sub_forums', 'moderators','is_view_forum'));
    }

    public function subscribe($id = null){
        $this->autoRender = false;
        $this->loadModel('Forum.ForumSubscribe');
        $uid = $this->Auth->user('id');
        $subscribe_id = $this->ForumSubscribe->isSubscribe($uid, $id, 'Forum');
        if(!$subscribe_id) {
            $this->ForumSubscribe->set(array(
                'target_id' => $id,
                'user_id' => $uid,
                'type' => 'Forum',
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
    public function admin_set_status_forum($id)
    {
        //check permission
        $this->Forum->id = $id;
        $data['status'] = 1;
        $this->Forum->set($data);
        $this->Forum->save();
        $this->Session->setFlash(__d('forum','Forum has been successfully opened'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));
        $this->redirect(array('action' => 'admin_index'));
    }

    public function testbbcode()
    {
        $helper = MooCore::getInstance()->getHelper('Forum_Forum');
        $bbcode = '[quote=1]text bold[/quote] [quote=2]text bold[/quote]';
        echo $helper->bbcodetohtml($bbcode,true);
        $this->autoRender = false;



    }

}