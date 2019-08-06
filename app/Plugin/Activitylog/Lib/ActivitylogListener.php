<?php
App::uses('CakeEventListener', 'Event');

class ActivitylogListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'View.Elements.User.headerProfile.beforeRenderSectionMenu' => 'beforeRenderSectionMenu',
            'profile.afterRenderMenu' => 'afterRenderMenu',
            'Elements.profilenav' => 'profilenav',
            'Controller.Like.afterLike' => 'afterLike',
            'Controller.Activity.afterComment' => 'afterActivityComment',
            'Controller.Comment.afterComment' => 'afterComment',
            'Controller.Share.afterDoShare' => 'afterDoShare',
            'ActivitesController.afterShare' => 'afterPostActivity',
            'Plugin.Controller.Album.afterSaveAlbum' => 'afterSaveAlbum',
            'Plugin.Controller.Photo.afterSavePhoto' => 'afterSavePhoto',
            'Plugin.Controller.Photo.afterSaveItemPhoto' => 'afterSaveItemPhoto',
            'Plugin.Controller.Video.afterSave' => 'afterAddVideo',
            'Plugin.Controller.Blog.afterSaveBlog' => 'afterSaveItem',
            'Plugin.Controller.Event.afterSaveEvent' => 'afterSaveItem',
            'Plugin.Controller.Group.afterSaveGroup' => 'afterSaveItem',
            'Plugin.Controller.Topic.afterSaveTopic' => 'afterSaveItem',
            'Controller.Activity.afterDeleteComment' => 'afterDeleteActivityComment',
            'Controller.Comment.afterDelete' => 'afterDeleteComment',
            'Controller.Activity.afterDeleteActivity' => 'afterDeleteActivity',
            'Plugin.Controller.Activitylog.deleteActivitylog' => 'deleteActivitylog',
            'UserController.deleteUserContent' => 'deleteUserContent',
            'element.reaction.render' => 'reactionRender',
            'Controller.Follow.afterFollow' => 'afterFollow',
            'Controller.Follow.afterUnfollow' => 'afterUnfollow',
            'Controller.Friend.afterAddFriend' => 'afterAddFriend',
            'Controller.Friend.afterRemoveFriend' => 'afterRemoveFriend',
            'Controller.Upload.afterUploadAvatar' => 'afterUploadAvatar',
            'Controller.Photo.afterTagPhoto' => 'afterTagPhoto',
            'Controller.Photo.afterRemoveTagPhoto' => 'afterRemoveTagPhoto',
            'Controller.Group.afterJoinGroup' => 'afterJoinGroup',
            'Controller.Group.afterLeaveGroup' => 'afterLeaveGroup',
            'Controller.Plugin.afterUninstall' => 'afterUninstallPlugin',

            'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu'
        );
    }

    public function apiAfterRenderMenu($e)
    {
        $subject = $e->subject();
        $cuser = MooCore::getInstance()->getViewer();

        if($subject->viewVars['user']['User']['id'] == $cuser['User']['id']) {
            $e->data['result']['activitylog'] = array(
                'text' => __d('activitylog', 'Activity log'),
                'url' => FULL_BASE_URL . $e->subject()->request->base . '/activity_log',
                'cnt' => 0
            );
        }
    }

    public function beforeRender($event)
    {
        if(Configure::read('Activitylog.activitylog_enabled')){
            $e = $event->subject();
            if(!empty($e->viewVars['site_rtl'])){
                $web_css = 'Activitylog.main-rtl';
                $app_css = 'Activitylog.main-app-rtl';
            }else{
                $web_css = 'Activitylog.main';
                $app_css = 'Activitylog.main-app';
            }

            if( $e->theme == 'mooApp'){
                $main_css = $app_css;
            }else{
                $main_css = $web_css;
            }

            $css = array(
                $main_css,
            );

            if(Configure::read('Business.business_enabled'))
            {
                $css[] = 'Business.star-rating';
                $css[] = 'Business.business-widget';
            }

            $e->Helpers->Html->css( $css,
                array('block' => 'css')
            );

            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }
            $e->Helpers->MooRequirejs->addPath(array(
                "mooActivitylog"=>$e->Helpers->MooRequirejs->assetUrlJS("Activitylog.js/main.{$min}js"),
            ));
        }
    }

    public function beforeRenderSectionMenu($event){
        $e = $event->subject();
        if($e->theme != 'mooApp') {
            $cuser = MooCore::getInstance()->getViewer();
            if($e->viewVars['user']['User']['id'] == $cuser['User']['id']) {
                $request = Router::getRequest();
                echo '<a href="' . $request->base . '/activity_log" class="button button-action" ><i class="visible-xs visible-sm material-icons">history</i><i class="hidden-xs hidden-sm">' . __d('activitylog', 'Activity log') . '</i></a>';
            }
        }
    }

    public function afterRenderMenu($event){
        $e = $event->subject();
        if($e->theme == 'mooApp') {
            $cuser = MooCore::getInstance()->getViewer();
            if($e->viewVars['user']['User']['id'] == $cuser['User']['id']) {
                $request = Router::getRequest();
                echo '<li class="mdl-menu__item"><a class="no-ajax" href="' . $request->base . '/activity_log">' . __d('activitylog', 'Activity log') . '</a></li>';
            }
        }
    }

    public function profilenav($event){
        $cmenu = $event->data['cmenu'];
        $request = Router::getRequest();
        echo '<li class="'.($cmenu == 'activitylog' ? 'current' : '').'">';
        echo '<a href="'.$request->base.'/activity_log"><i class="material-icons">history</i>'. __d('activitylog','Activity log').'</a>';
        echo '</li>';
    }

    public function afterLike($event)
    {
        $aLike = $event->data['aLike'];
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        if (!empty($aLike)) {
            $item = MooCore::getInstance()->getItemByType($aLike['Like']['type'],$aLike['Like']['target_id']);
            if(!empty($item)) {
                $item_type = '';
                $item_id = 0;
                $is_reaction = !empty($aLike['Like']['reaction']) ? true : false;

                if($aLike['Like']['thumb_up'] == '0'){
                    $type = 'dislike';
                    $activityLogModel->deleteAll(array('Activitylog.action LIKE' => 'like_%', 'Activitylog.type' => $aLike['Like']['type'], 'Activitylog.target_id' => $aLike['Like']['target_id'], 'Activitylog.user_id' => $aLike['Like']['user_id']));
                }else{
                    $type = 'like';
                    $activityLogModel->deleteAll(array('Activitylog.action LIKE' => 'dislike_%', 'Activitylog.type' => $aLike['Like']['type'], 'Activitylog.target_id' => $aLike['Like']['target_id'], 'Activitylog.user_id' => $aLike['Like']['user_id']));
                }


                switch ($aLike['Like']['type']) {
                    case 'activity':
                        $action = $type . '_post';
                        break;
                    case 'comment':
                        $action = $type . '_comment';
                        $item_id = $item[key($item)]['target_id'];
                        $item_type = $item[key($item)]['type'];
                        break;
                    case 'core_activity_comment':
                        $action = $type . '_activity_comment';
                        $item_id = $item[key($item)]['activity_id'];
                        $item_type = 'activity';
                        break;
                    case 'Photo_Photo':
                        $action = $type . '_photo';
                        break;
                    case 'Photo_Album':
                        $action = $type . '_album';
                        break;
                    default:
                        $action = $type . '_item';
                        break;
                }

                $data = array(
                    'user_id' => $aLike['Like']['user_id'],
                    'user_target_id' => !empty($item['User']['id']) ? $item['User']['id'] : $item[key($item)]['user_id'],
                    'target_id' => $aLike['Like']['target_id'],
                    'action' => $action,
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'type' => $aLike['Like']['type'],
                    'params' => $is_reaction ? $aLike['Like']['reaction'] : '',
                );

                if($is_reaction){
                    $check_exist = $activityLogModel->find('first', array(
                        'conditions' => array(
                            'Activitylog.action LIKE' => 'like_%',
                            'Activitylog.type' => $aLike['Like']['type'],
                            'Activitylog.target_id' => $aLike['Like']['target_id'],
                            'Activitylog.user_id' => $aLike['Like']['user_id'],
                        ),
                    ));
                    if(!empty($check_exist)){
                        $data['id'] = $check_exist['Activitylog']['id'];
                    }
                }
                $activityLogModel->set($data);
                $activityLogModel->save();
            }
        }else{//unlike
            $e = $event->subject();
            $params = $e->params['pass'];
            $cuser = MooCore::getInstance()->getViewer();
            if($params[2] == '0') { //thumb_down
                $activityLogModel->deleteAll(array('Activitylog.action LIKE' => 'dislike_%', 'Activitylog.type' => $params[0], 'Activitylog.target_id' => $params[1], 'Activitylog.user_id' => $cuser['User']['id']));
            }else{
                $activityLogModel->deleteAll(array('Activitylog.action LIKE' => 'like_%', 'Activitylog.type' => $params[0], 'Activitylog.target_id' => $params[1], 'Activitylog.user_id' => $cuser['User']['id']));
            }
        }
    }

    public function afterActivityComment($event)
    {
        $aComment = $event->data['item'];
        if (!empty($aComment)) {
            $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
            $key = key($aComment);
            if($key == 'ActivityComment') {
                $data = array(
                    'user_id' => $aComment['ActivityComment']['user_id'],
                    'user_target_id' => $aComment['Activity']['user_id'],
                    'target_id' => $aComment['ActivityComment']['id'],
                    'action' => 'comment_activity',
                    'item_id' => $aComment['Activity']['id'],
                    'item_type' => 'activity',
                    'type' => 'core_activity_comment',
                );
                $activityLogModel->set($data);
                $activityLogModel->save();
            }
        }
    }

    public function afterComment($event)
    {
        $aComment = $event->data['data'];
        if (!empty($aComment)) {
            $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
            $CommentModel = MooCore::getInstance()->getModel('Comment');
            $item = MooCore::getInstance()->getItemByType($aComment['type'],$aComment['target_id']);
            if(!empty($item)) {
                $comment = $CommentModel->find('first', array(
                    'conditions' => array(
                        'type' => $aComment['type'],
                        'target_id' => $aComment['target_id'],
                        'user_id' => $aComment['user_id'],
                    ),
                    'order' => 'Comment.created DESC'
                ));
                $key = key($item);
                switch ($key) {
                    case 'Photo':
                        $action = 'comment_photo';
                        break;
                    case 'Album':
                        $action = 'comment_album';
                        break;
                    default:
                        $action = 'comment_item';
                        break;
                }
                $data = array(
                    'user_id' => $aComment['user_id'],
                    'user_target_id' =>  !empty($item[$key]['user_id']) ? $item[$key]['user_id'] : 0 ,
                    'target_id' => !empty($comment) ? $comment['Comment']['id'] : 0,
                    'action' => $action,
                    'item_id' => $aComment['target_id'],
                    'item_type' => $aComment['type'],
                    'type' => 'comment',
                );
                $activityLogModel->set($data);
                $activityLogModel->save();
            }
        }
    }

    public function afterDoShare($event)
    {
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');

        $aShare = $event->data['data'];
        $activity_id = $event->data['activity_id'];
        $cuser = MooCore::getInstance()->getViewer();
        $e = $event->subject();
        $data_post = $e->data;
        if($activity_id){
            $type = isset($aShare['params']) ? 'activity' : $aShare['item_type'];
            $item = MooCore::getInstance()->getItemByType($type, $aShare['parent_id']);

            if (!empty($item)) {
                switch ($type) {
                    case 'activity':
                        $action = 'share_post';
                        break;
                    case 'Photo_Album':
                        $action = 'share_album';
                        break;
                    case 'Photo_Photo':
                        $action = 'share_photo';
                        break;
                    default:
                        $action = 'share_item';
                        break;
                }

                $data = array(
                    'user_id' => $aShare['user_id'],
                    'user_target_id' => !empty($item['User']['id']) ? $item['User']['id'] : $item[key($item)]['user_id'],
                    'target_id' => $activity_id,
                    'action' => $action,
                    'item_id' => $aShare['parent_id'],
                    'item_type' => $type,
                    'type' => 'activity',
                );
                $activityLogModel->set($data);
                $activityLogModel->save();

//                if(!empty($data_post['userTagging'])){ //for user tagging
//                    $users = explode(',',$data_post['userTagging']);
//                    foreach ($users as $id) {
//                        $data = array(
//                            'user_id' => $id,
//                            'user_target_id' => $cuser['User']['id'],
//                            'target_id' => $activity_id,
//                            'action' => 'tagged',
//                            'item_id' => 0,
//                            'item_type' => '',
//                            'type' => 'activity',
//                        );
//                        $activityLogModel->clear();
//                        $activityLogModel->set($data);
//                        $activityLogModel->save();
//                    }
//                }
            }
        }
    }

    public function afterPostActivity($event)
    {
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $aActivity = $event->data['activity'];
        $e = $event->subject();
        $data_post = $e->data;
        $action = '';
        if(key($aActivity) == 'Activity') {
            if(strtolower($aActivity['Activity']['type']) == 'user'){
                if($aActivity['Activity']['item_type'] == 'Photo_Album' && $aActivity['Activity']['content'] == ''){
                    if(count(explode(',',$aActivity['Activity']['items'])) > 1){
                        $action = 'post_photos';
                    }else{
                        $action = 'post_photo';
                    }
                }else {
                    $action = 'post_status';
                }
                $user_target_id = $aActivity['Activity']['target_id'] ? $aActivity['Activity']['target_id'] : $aActivity['Activity']['user_id'];
            }else if($aActivity['Activity']['type'] == 'Group_Group'){
                if($aActivity['Activity']['item_type'] == 'Photo_Album' && $aActivity['Activity']['content'] == ''){
                    if(count(explode(',',$aActivity['Activity']['items'])) > 1){
                        $action = 'post_group_photos';
                    }else{
                        $action = 'post_group_photo';
                    }
                }else {
                    $action = 'post_group';
                }
                $user_target_id = $aActivity['Activity']['user_id'];
            }else if($aActivity['Activity']['type'] == 'Event_Event'){
                if($aActivity['Activity']['item_type'] == 'Photo_Album' && $aActivity['Activity']['content'] == ''){
                    if(count(explode(',',$aActivity['Activity']['items'])) > 1){
                        $action = 'post_event_photos';
                    }else{
                        $action = 'post_event_photo';
                    }
                }else {
                    $action = 'post_event';
                }
                $user_target_id = $aActivity['Activity']['user_id'];
            }else if($aActivity['Activity']['action'] == 'business_checkin'){
                $action = 'post_status';
                $user_target_id = $aActivity['Activity']['user_id'];
            }

            if($action) {
                $data = array(
                    'user_id' => $aActivity['Activity']['user_id'],
                    'user_target_id' => $user_target_id,
                    'target_id' => $aActivity['Activity']['id'],
                    'action' => $action,
                    'item_id' => 0,
                    'item_type' => '',
                    'type' => 'activity',
                );
                $activityLogModel->set($data);
                $activityLogModel->save();

                if(!empty($data_post['userTagging'])){ //for user tagging
                    $users = explode(',',$data_post['userTagging']);
                    foreach ($users as $id) {
                        $data = array(
                            'user_id' => $id,
                            'user_target_id' => $aActivity['Activity']['user_id'],
                            'target_id' => $aActivity['Activity']['id'],
                            'action' => 'tagged',
                            'item_id' => 0,
                            'item_type' => '',
                            'type' => 'activity',
                        );
                        $activityLogModel->clear();
                        $activityLogModel->set($data);
                        $activityLogModel->save();
                    }
                }
            }
        }
    }

    public function afterSaveAlbum($event)
    {
        $e = $event->subject();
        $data_post = $e->data;
        if($e->name == 'Albums' && empty($data_post['id'])) {
            $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
            $data = array(
                'user_id' => $event->data['uid'],
                'user_target_id' => 0,
                'target_id' => $event->data['id'],
                'action' => 'create_album',
                'item_id' => 0,
                'item_type' => '',
                'type' => 'Photo_Album',
            );
            $activityLogModel->set($data);
            $activityLogModel->save();
        }
    }

    public function afterSavePhoto($event)
    {
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');

        $data = array(
            'user_id' => $event->data['uid'],
            'user_target_id' => $event->data['uid'],
            'target_id' => $event->data['activity_id'],
            'action' => 'add_photo',
            'item_id' => 0,
            'item_type' => '',
            'type' => 'activity',
        );
        $activityLogModel->set($data);
        $activityLogModel->save();

    }

    public function afterSaveItemPhoto($event)
    {
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $activity = $event->data['activity'];
        if(!empty($activity) && $activity['Activity']['type'] == 'Group_Group') {
            if(count(explode(',',$activity['Activity']['items'])) > 1){
                $action = 'post_group_photos';
            }else{
                $action = 'post_group_photo';
            }

            $data = array(
                'user_id' => $event->data['uid'],
                'user_target_id' => $event->data['uid'],
                'target_id' => $activity['Activity']['id'],
                'action' => $action,
                'item_id' => 0,
                'item_type' => '',
                'type' => 'activity',
            );
            $activityLogModel->set($data);
            $activityLogModel->save();
        }
    }

    public function afterAddVideo($event)
    {
        $e = $event->subject();
        $data_post = $e->data;
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        if(!empty($data_post['action']) && $data_post['action'] == 'wall_post_video'){
            $skip = 1;
        }else{
            $skip = 0;
        }
        if(empty($data_post['id']) && !$skip) {
            $data = array(
                'user_id' => $event->data['uid'],
                'user_target_id' => 0,
                'target_id' => $event->data['id'],
                'action' => 'add_video',
                'item_id' => 0,
                'item_type' => '',
                'type' => 'Video_Video',
            );
            $activityLogModel->set($data);
            $activityLogModel->save();
        }

    }

    public function afterSaveItem($event)
    {
        $e = $event->subject();
        $data_post = $e->data;
        $sType = ($e->plugin ? $e->plugin . '_' : '') . $e->modelClass;
        $cuser = MooCore::getInstance()->getViewer();

        if($sType && isset($event->data['id']) && empty($data_post['id'])) {
            $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
            $data = array(
                'user_id' => $cuser['User']['id'],
                'user_target_id' => 0,
                'target_id' => $event->data['id'],
                'action' => 'create_item',
                'item_id' => 0,
                'item_type' => '',
                'type' => $sType,
            );
            $activityLogModel->set($data);
            $activityLogModel->save();
        }
    }

    public function afterDeleteActivityComment($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $aComment = $event->data['comment'];
        $activityLogModel->deleteAll(array('Activitylog.type' => 'core_activity_comment', 'Activitylog.target_id' => $aComment['ActivityComment']['id']));
    }

    public function afterDeleteComment($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $e = $event->subject();
        $data = $e->data;
        if(!empty($data['id'])) {
            $activityLogModel->deleteAll(array('Activitylog.type' => 'comment', 'Activitylog.target_id' => $data['id']));
        }
    }

    public function afterDeleteActivity($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $aActivity = $event->data['activity'];
        $activityLogModel->deleteAll(array('Activitylog.type' => 'activity', 'Activitylog.target_id' => $aActivity['Activity']['id']));
        $activityLogModel->deleteAll(array('Activitylog.item_type' => 'activity', 'Activitylog.item_id' => $aActivity['Activity']['id']));
    }

    public function deleteUserContent($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $aUser = $event->data['aUser'];
        $activityLogModel->deleteAll(array('Activitylog.user_id' => $aUser['User']['id']));
        $activityLogModel->deleteAll(array('Activitylog.user_target_id' => $aUser['User']['id']));
    }

    public function deleteActivitylog($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $aItem = $event->data['item'];
        $activityLogModel->delete($aItem['Activitylog']['id']);
    }

    public function reactionRender($event){
        $reaction = $event->data['reaction'];
        switch ($reaction){
            case REACTION_LIKE:
                $reaction_notification_class = 'notification_reaction_like';
                break;
            case REACTION_LOVE:
                $reaction_notification_class = 'notification_reaction_love';
                break;
            case REACTION_HAHA:
                $reaction_notification_class = 'notification_reaction_haha';
                break;
            case REACTION_WOW:
                $reaction_notification_class = 'notification_reaction_wow';
                break;
            case REACTION_SAD:
                $reaction_notification_class = 'notification_reaction_sad';
                break;
            case REACTION_ANGRY:
                $reaction_notification_class = 'notification_reaction_angry';
                break;
            case REACTION_COOL:
                $reaction_notification_class = 'notification_reaction_cool';
                break;
            case REACTION_CONFUSED:
                $reaction_notification_class = 'notification_reaction_confused';
                break;
            default:
                $reaction_notification_class = 'notification_reaction_none';
                break;
        }
        echo '<span class="'.$reaction_notification_class.'"></span>&nbsp;';
    }

    public function afterFollow($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $follow = $event->data['follow'];
        $data = array(
            'user_id' => $follow['UserFollow']['user_id'],
            'user_target_id' => $follow['UserFollow']['user_follow_id'],
            'target_id' => 0,
            'action' => 'follow',
            'item_id' => 0,
            'item_type' => '',
            'type' => '',
        );
        $activityLogModel->set($data);
        $activityLogModel->save();
    }

    public function afterUnfollow($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $follow = $event->data['follow'];
        $activityLogModel->deleteAll(array('Activitylog.user_id' => $follow['UserFollow']['user_id'], 'Activitylog.action' => 'follow', 'Activitylog.user_target_id' => $follow['UserFollow']['user_follow_id']));
    }

    public function afterAddFriend($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $cuser = MooCore::getInstance()->getViewer();
        $data = array(
            'user_id' => $cuser['User']['id'],
            'user_target_id' => $event->data['sender_id'],
            'target_id' => 0,
            'action' => 'add_friend',
            'item_id' => 0,
            'item_type' => '',
            'type' => '',
        );
        $activityLogModel->set($data);
        $activityLogModel->save();
        //add log for sender
        $data = array(
            'user_id' => $event->data['sender_id'],
            'user_target_id' => $cuser['User']['id'],
            'target_id' => 0,
            'action' => 'add_friend',
            'item_id' => 0,
            'item_type' => '',
            'type' => '',
        );
        $activityLogModel->clear();
        $activityLogModel->set($data);
        $activityLogModel->save();
    }

    public function afterRemoveFriend($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $activityLogModel->deleteAll(array('Activitylog.user_id' => $event->data['friend_id'], 'Activitylog.action' => 'add_friend', 'Activitylog.user_target_id' => $event->data['uid']));
        $activityLogModel->deleteAll(array('Activitylog.user_id' => $event->data['uid'], 'Activitylog.action' => 'add_friend', 'Activitylog.user_target_id' => $event->data['friend_id']));
    }

    public function afterUploadAvatar($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
             $data = array(
            'user_id' => $event->data['uid'],
            'user_target_id' => $event->data['uid'],
            'target_id' => $event->data['activity_id'],
            'action' => 'user_avatar',
            'item_id' => 0,
            'item_type' => '',
            'type' => 'activity',
        );
        $activityLogModel->set($data);
        $activityLogModel->save();
    }

    public function afterTagPhoto($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $data = array(
            'user_id' => $event->data['user_id'],
            'user_target_id' => $event->data['uid'],
            'target_id' => $event->data['photo_id'],
            'action' => 'tag_photo',
            'item_id' => 0,
            'item_type' => '',
            'type' => 'Photo_Photo',
        );
        $activityLogModel->set($data);
        $activityLogModel->save();
    }

    public function afterRemoveTagPhoto($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $activityLogModel->deleteAll(array('Activitylog.user_id' => $event->data['user_id'], 'Activitylog.action' => 'tag_photo', 'Activitylog.target_id' => $event->data['photo_id']));
    }

    public function afterJoinGroup($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $data = array(
            'user_id' => $event->data['uid'],
            'user_target_id' => $event->data['uid'],
            'target_id' => $event->data['group_id'],
            'action' => 'join_group',
            'item_id' => 0,
            'item_type' => '',
            'type' => 'Group_Group',
        );
        $activityLogModel->set($data);
        $activityLogModel->save();
    }

    public function afterLeaveGroup($event){
        $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
        $activityLogModel->deleteAll(array('Activitylog.user_id' => $event->data['uid'], 'Activitylog.action' => 'join_group', 'Activitylog.target_id' => $event->data['group_id']));
    }

    public function afterUninstallPlugin($event){
        $plugin = $event->data['plugin'];
        if($plugin['Plugin']['key'] == 'Reaction'){
            $activityLogModel = MooCore::getInstance()->getModel('Activitylog.Activitylog');
            $activityLogModel->updateAll(
                array('Activitylog.params' => '1'),
                array('Activitylog.action LIKE' => 'like_%')
            );
        }
    }
}