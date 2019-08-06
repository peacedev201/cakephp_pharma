<?php 
class ReactionsController extends ReactionAppController{

    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);

    }

    public function beforeFilter()
    {
        parent::beforeFilter();
        //$this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('Reaction.Reaction');
    }

    public function admin_index()
    {
    }
    public function index()
    {
    }

    public function ajax_add($type = null, $id = null, $reaction = null, $isResponse = 'json')
    {
        $id = intval($id);
        $this->autoRender = false;
        $this->_checkPermission( array( 'confirm' => true ) );
        $this->loadModel("User");
        $this->loadModel("Like");

        $reactionList = array(
            REACTION_LIKE => __d('reaction','Like'),
            REACTION_LOVE => __d('reaction','Love'),
            REACTION_HAHA => __d('reaction','Haha'),
            REACTION_WOW => __d('reaction','Wow'),
            REACTION_SAD => __d('reaction','Sad'),
            REACTION_ANGRY => __d('reaction','Angry'),
            REACTION_COOL => __d('reaction','Cool'),
            REACTION_CONFUSED => __d('reaction','Confused')
        );

        $reactionClassList = array(
            REACTION_LIKE => 'like',
            REACTION_LOVE => 'love',
            REACTION_HAHA => 'haha',
            REACTION_WOW => 'wow',
            REACTION_SAD => 'sad',
            REACTION_ANGRY => 'angry',
            REACTION_COOL => 'cool',
            REACTION_CONFUSED => 'confused'
        );

        $uid = $this->Auth->user('id');

        if ($type == 'activity') {
            $this->loadModel('Activity');
            $activity = $this->Activity->findById($id);
        }

        list($plugin, $model) = mooPluginSplit($type);

        if ($plugin)
            $this->loadModel( $plugin.'.'.$model );
        else
            $this->loadModel( $model );

        $item = $this->$model->findById( $id );
        $this->_checkExistence( $item );
        $like_item = array();

        // clear cache item
        switch ( $type )
        {
            case APP_PHOTO:
                Cache::delete('photo.photo_view_'.$id, 'photo');
                break;
            default:
                break;
        }

        // check to see if user already liked this item
        $like = $this->Like->getUserLike( $id, $uid, $type );
        $this->$model->id = $id;
        if ( !empty( $like ) ) // user already thumb up/down this item
        {
            if ( $like['Like']['reaction'] != $reaction )
            {
                $this->Like->id = $like['Like']['id'];
                $this->Like->save( array( 'thumb_up' => 1, 'reaction' => $reaction ) );
                $like_item = $this->Like->read();
                if ( $reaction ) // user thumbed down before
                {
                    $this->$model->updateCounter($id, 'like_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 1), 'Like');
                    $this->$model->updateCounter($id, 'dislike_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 0), 'Like');

                    if(!empty($activity))
                        $this->updatePhotoLike($activity,$reaction);

                }
                else
                {
                    $this->$model->updateCounter($id, 'like_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 1), 'Like');
                    $this->$model->updateCounter($id, 'dislike_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 0), 'Like');
                    if(!empty($activity))
                        $this->updatePhotoLike($activity,$reaction);

                }

                //------------------------------------------------
                //send
                // do not send notification when user like comment
                /*if ( !in_array( $type, array('core_activity_comment', 'comment') ) )
                {
                    // send notification to author
                    if ( $uid != $item['User']['id'] )
                    {
                        switch ( $type )
                        {
                            case 'Photo_Photo':
                                //$action = ($reaction == REACTION_LIKE) ? 'photo_like' : 'photo_reaction';
                                //$params = $this->getReactionNotificationIcon($reaction);
                                $action = 'photo_reaction';
                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'photo_like',
                                    'text' => ''
                                ));
                                break;

                            case 'activity':
                                //$action = ($reaction == REACTION_LIKE) ? 'activity_like' : 'activity_reaction';
                                //$params = $this->getReactionNotificationIcon($reaction);
                                $action = 'activity_reaction';
                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'activity_like',
                                    'text' => ''
                                ));
                                break;

                            case 'core_activity_comment':
                                //$action = ($reaction == REACTION_LIKE) ? 'item_like' : 'item_reaction';
                                //$params = $this->getReactionNotificationIcon($reaction);
                                $action = 'item_reaction';
                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'item_like',
                                    'text' => ''
                                ));
                                break;

                            default:
                                //$action = ($reaction == REACTION_LIKE) ? 'item_like' : 'item_reaction';
                                $action = 'item_reaction';
                                $param_text = isset($item[$model]['title']) ? h($item[$model]['title']) : '';

                                if (empty($param_text)){
                                    $param_text = isset($item[$model]['moo_title']) ? h($item[$model]['moo_title']) : '';
                                }
                                //$params .= $this->getReactionNotificationIcon($reaction);

                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'item_like',
                                    'text' => $param_text
                                ));
                        }

                        if ( !empty( $item[$model]['group_id'] ) ) // group topic / video
                        {
                            $url = '/groups/view/' . $item[$model]['group_id'] . '/' . $type . '_id:' . $id;
                        }
                        elseif ( $type == 'activity' ) // activity
                        {
                            $url = '/users/view/' . $item['User']['id'] . '/activity_id:' . $id;
                        }
                        else
                        {
                            $url = isset($item[key($item)]['moo_url']) ? $item[key($item)]['moo_url'] : '';

                            if ( $type == 'Photo_Photo' ){
                                $url .= '#content';
                            }
                        }

                        if ($this->User->checkSettingNotification($item['User']['id'],'like_item')) {
                            $notificationStopModel = MooCore::getInstance()->getModel('NotificationStop');
                            if (!$notificationStopModel->isNotificationStop($id, $type, $item['User']['id'])) {
                                $this->loadModel('Notification');
                                $notification = $this->Notification->find('first',array(
                                    'conditions' => array(
                                        'user_id' => $item['User']['id'],
                                        'sender_id' => $uid,
                                        'url' => $url,
                                        'plugin' => 'reaction'
                                    )
                                ));

                                if(!empty($notification)){
                                    $this->Notification->delete($notification['Notification']['id']);
                                    $this->Notification->clear();
                                    //$this->Notification->saveAll(array('id' => $notification['Notification']['id'], 'created' => date('Y-m-d H:i:s'), 'read' => 0, 'params' => $params));
                                }

                                $this->Notification->record(array('recipients' => $item['User']['id'],
                                    'sender_id' => $uid,
                                    'action' => $action,
                                    'url' => $url,
                                    'params' => $params,
                                    'plugin' => 'reaction'
                                    //'plugin' => ($reaction == REACTION_LIKE) ? '' : 'reaction'
                                ));
                            }
                        }
                    }
                }*/
                //------------------------------------------------
            }
            else // remove the entry
            {
                $this->Like->delete( $like['Like']['id'] );
                if (!empty($activity)) {
                    $this->updatePhotoLike($activity,$reaction, 1,true);
                }
                if ( $reaction )
                {
                    $this->$model->updateCounter($id, 'like_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 1), 'Like');

                }
                else
                {
                    $this->$model->updateCounter($id, 'dislike_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 0), 'Like');
                }

            }
        }
        else
        {
            $data = array('type' => $type, 'target_id' => $id, 'user_id' => $uid, 'thumb_up' => 1, 'reaction' => $reaction);
            $this->Like->save($data);
            $like_item = $this->Like->read();
            if ( $reaction )
            {
                $this->$model->updateCounter($id, 'like_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 1), 'Like');

                //user like activity photo with 1 photo
                if(!empty($activity))
                    $this->updatePhotoLike($activity,$reaction);

                // do not send notification when user like comment
                if ( !in_array( $type, array('core_activity_comment', 'comment') ) )
                {
                    // send notification to author
                    if ( $uid != $item['User']['id'] )
                    {
                        switch ( $type )
                        {
                            case 'Photo_Photo':
                                //$action = ($reaction == REACTION_LIKE) ? 'photo_like' : 'photo_reaction';
                                //$params = $this->getReactionNotificationIcon($reaction);
                                $action = 'photo_reaction';
                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'photo_like',
                                    'text' => ''
                                ));
                                break;

                            case 'activity':
                                //$action = ($reaction == REACTION_LIKE) ? 'activity_like' : 'activity_reaction';
                                //$params = $this->getReactionNotificationIcon($reaction);
                                $action = 'activity_reaction';
                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'activity_like',
                                    'text' => ''
                                ));
                                break;

                            case 'core_activity_comment':
                                //$action = ($reaction == REACTION_LIKE) ? 'item_like' : 'item_reaction';
                                //$params = $this->getReactionNotificationIcon($reaction);
                                $action = 'item_reaction';
                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'item_like',
                                    'text' => ''
                                ));
                                break;

                            default:
                                //$action = ($reaction == REACTION_LIKE) ? 'item_like' : 'item_reaction';
                                //$params = isset($item[$model]['title']) ? h($item[$model]['title']) : '';
                                $action = 'item_reaction';
                                $param_text = isset($item[$model]['title']) ? h($item[$model]['title']) : '';

                                if (empty($param_text)){
                                    $param_text = isset($item[$model]['moo_title']) ? h($item[$model]['moo_title']) : '';
                                }
                                //$params .= $this->getReactionNotificationIcon($reaction);
                                $params = serialize(array(
                                    'reaction' => $reaction,
                                    'core_action' => 'item_like',
                                    'text' => $param_text
                                ));
                        }

                        if ( !empty( $item[$model]['group_id'] ) ) // group topic / video
                        {
                            $url = '/groups/view/' . $item[$model]['group_id'] . '/' . $type . '_id:' . $id;
                        }
                        elseif ( $type == 'activity' ) // activity
                        {
                            $url = '/users/view/' . $item['User']['id'] . '/activity_id:' . $id;
                        }
                        else
                        {
                            $url = isset($item[key($item)]['moo_url']) ? $item[key($item)]['moo_url'] : '';

                            if ( $type == 'Photo_Photo' ){
                                $url .= '#content';
                            }
                        }

                        if ($this->User->checkSettingNotification($item['User']['id'],'like_item')) {
                            $notificationStopModel = MooCore::getInstance()->getModel('NotificationStop');
                            if (!$notificationStopModel->isNotificationStop($id, $type, $item['User']['id'])) {
                                $this->loadModel('Notification');
                                $this->Notification->record(array('recipients' => $item['User']['id'],
                                    'sender_id' => $uid,
                                    'action' => $action,
                                    'url' => $url,
                                    'params' => $params,
                                    'plugin' => 'reaction'
                                    //'plugin' => ($reaction == REACTION_LIKE) ? '' : 'reaction'
                                ));
                            }
                        }
                    }
                }
            }
            else
            {
                $this->$model->updateCounter($id, 'dislike_count', array('Like.type' => $type, 'Like.target_id' => $id, 'Like.thumb_up' => 0), 'Like');

                //user like activity photo with 1 photo
                if(!empty($activity))
                    $this->updatePhotoLike($activity,$reaction, 0);
            }
        }

        //$item = $this->$model->findById( $id );

        //Save Reaction
        $newReaction = $this->Reaction->updateReactionCounter($type, $id);

        $myLike = $this->Like->find( 'list', array( 'conditions' => array(
            'user_id' => $uid,
            'type' => $type,
            'target_id' => $id
        ),
            'fields' => array( 'Like.target_id', 'Like.reaction' )
        ) );

        $react_index = intval(!empty($myLike[$id]) ? $myLike[$id] : 1);

        // $item = $this->$model->findById( $id );
        $re = array(
            //'like_count' => $this->Like->getBlockLikeCount($id,$type),
            //'dislike_count' => $this->Like->getBlockLikeCount($id,$type,0),
            'is_like' => !empty($myLike[$id]) ? true : false,
            'reaction' => $react_index,
            'label' => $reactionList[$react_index],
            'ele_class' => $reactionClassList[$react_index]
        );

        $re = array_merge(array(
            'total_count' => $newReaction['Reaction']['total_count'],
            'like_count' => $newReaction['Reaction']['like_count'],
            'love_count' => $newReaction['Reaction']['love_count'],
            'haha_count' => $newReaction['Reaction']['haha_count'],
            'wow_count' => $newReaction['Reaction']['wow_count'],
            'sad_count' => $newReaction['Reaction']['sad_count'],
            'angry_count' => $newReaction['Reaction']['angry_count'],
            'cool_count' => $newReaction['Reaction']['cool_count'],
            'confused_count' => $newReaction['Reaction']['confused_count']
        ), $re);

        $like_current = $this->Like->getUserLike( $id, $uid, $type );

        if ($type == 'Photo_Photo')
        {
            $this->loadModel('Activity');
            $activity = $this->Activity->find('first',array(
                'conditions' => array(
                    'OR'=> array(
                        array('item_type' => 'Photo_Album','action'=>'wall_post','items'=>$id),
                        array('item_type' => 'Photo_Photo', 'action' => 'photos_add','items'=>$id),
                    )
                )
            ));
            if ($activity)
            {
                $this->Like->deleteAll(array('Like.type' => 'activity','Like.user_id'=>$uid,'Like.target_id'=>$activity['Activity']['id']), false);

                if ($like_current)
                {
                    $likeModel = MooCore::getInstance()->getModel('Like');
                    $likeModel->Behaviors->detach('Notification');
                    $likeModel->save(array(
                        'type' => 'activity',
                        'user_id' => $uid,
                        'target_id' => $activity['Activity']['id'],
                        'thumb_up' => $like_current['Like']['thumb_up'],
                        'reaction' => $reaction
                    ));
                }

                $this->Activity->updateCounter($activity['Activity']['id'], 'like_count', array('Like.type' => 'activity', 'Like.target_id' => $activity['Activity']['id'], 'Like.thumb_up' => 1), 'Like');
                $this->Activity->updateCounter($activity['Activity']['id'], 'dislike_count', array('Like.type' => 'activity', 'Like.target_id' => $activity['Activity']['id'], 'Like.thumb_up' => 0), 'Like');

                $this->Reaction->updateReactionCounter('activity', $activity['Activity']['id']);
            }
        }
        $cakeEvent = new CakeEvent('Controller.Like.afterLike', $this, array('aLike' => $like_item));
        $this->getEventManager()->dispatch($cakeEvent);

        if($isResponse == 'array'){
            return $re;
        }else{
            echo json_encode($re);
            return '';
        }
    }

    public function ajax_show($objectType = null, $id = null, $reaction_type = false)
    {
        $id = intval($id);
        $reaction_type = intval($reaction_type);
        if($objectType == 'photo_comment'){
            $objectType = 'comment';
        }
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $limit = RESULTS_LIMIT;

        $users = $this->Reaction->getUserReactions( $id, $objectType, $reaction_type, $limit, $page );
        $count = $this->Reaction->getCountByReactionItem( $id, $objectType, $reaction_type);

        $react = $this->Reaction->getCountReaction($objectType, $id);

        $this->set( 'users', $users );
        $this->set('page', $page);
        $this->set('count',$count);
        $this->set('reaction', $react);
        $this->set('type', $objectType);
        $this->set('id', $id);

        $countTabActive = 0;
        $tempActive = -1;
        if($react['like_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_LIKE;
        }
        if($react['love_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_LOVE;
        }
        if($react['haha_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_HAHA;
        }
        if($react['wow_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_WOW;
        }
        if($react['sad_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_SAD;
        }
        if($react['angry_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_ANGRY;
        }
        if($react['cool_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_COOL;
        }
        if($react['confused_count'] > 0){
            $countTabActive++;
            $tempActive = REACTION_CONFUSED;
        }

        $this->set('active_reaction', ($countTabActive <= 1) ? $tempActive : $reaction_type);

        $this->set('showTabAll', ($countTabActive > 1) ? true : false );

        $this->set('limit', $limit );
        $this->set('isApp', $this->isApp() );

        $token = '';
        if(isset($this->request->query['access_token'])){
            $token = '?access_token='.$this->request->query['access_token'];
        }

        $this->set('more_url', '/reactions/ajax_show_more/' . $objectType . '/' . $id . '/'.$reaction_type.'/page:' . ( $page + 1 ) .$token );

        $this->render('/Elements/ajax/reaction_user_overlay_like');
    }

    public function ajax_show_more($objectType = null, $id = null, $reaction_type = false)
    {
        $id = intval($id);
        $reaction_type = intval($reaction_type);
        if($objectType == 'photo_comment'){
            $objectType = 'comment';
        }
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $limit = RESULTS_LIMIT;

        $users = $this->Reaction->getUserReactions( $id, $objectType, $reaction_type, $limit, $page );
        $count = $this->Reaction->getCountByReactionItem( $id, $objectType, $reaction_type);

        $this->set( 'users', $users );
        $this->set('page', $page);
        $this->set('count',$count);
        $this->set('active_reaction', $reaction_type);

        $this->set('limit', $limit );

        $token = '';
        if(isset($this->request->query['access_token'])){
            $token = '?access_token='.$this->request->query['access_token'];
        }

        $this->set('more_url', '/reactions/ajax_show_more/' . $objectType . '/' . $id . '/'.$reaction_type.'/page:' . ( $page + 1 ).$token );

        $this->render('/Elements/ajax/reaction_user_overlay_like_more');
    }

    public function updatePhotoLike($activity = null, $reaction = 1, $thumb = 1, $deleteLike = false) {
        $uid = $this->Auth->user('id');
        if  (!empty($activity)) {
            $item_type = $activity['Activity']['item_type'];

            if (
                ($item_type == 'Photo_Album' && $activity['Activity']['action'] == 'wall_post')
                || ($item_type == 'Photo_Photo' && $activity['Activity']['action'] == 'photos_add')
            )
            {
                $photo_id = explode(',',$activity['Activity']['items']);
                if (count($photo_id) == 1) {
                    $this->loadModel('Photo.Photo');
                    $data_like = array('type' => 'Photo_Photo', 'target_id' => $photo_id[0] , 'user_id' => $uid, 'thumb_up' => $thumb, 'reaction' => $reaction);

                    $like_id = false;
                    $like = $this->Like->findByTargetIdAndType($photo_id[0],'Photo_Photo');
                    //$like = $this->Like->getUserLike( $photo_id[0], $uid, 'Photo_Photo' );
                    if(!empty($like))
                        $like_id = $like['Like']['id'];

                    if ($deleteLike && $like_id) {
                        $this->Like->delete($like_id);
                    } else {
                        $this->Like->create();
                        if($like_id)
                            $this->Like->id = $like_id;
                        $this->Like->save($data_like);
                    }

                    $this->Photo->updateCounter($photo_id[0], 'like_count', array('Like.type' => 'Photo_Photo', 'Like.target_id' => $photo_id[0], 'Like.thumb_up' => 1), 'Like');
                    $this->Photo->updateCounter($photo_id[0], 'dislike_count', array('Like.type' => 'Photo_Photo', 'Like.target_id' => $photo_id[0], 'Like.thumb_up' => 0), 'Like');

                    $this->loadModel('Reaction.Reaction');
                    $this->Reaction->updateReactionCounter('Photo_Photo', $photo_id[0]);
                }
            }
        }
    }
}