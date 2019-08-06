<?php
//UPDATE `likes` SET `reaction`= 0 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `likes` WHERE `thumb_up` = 0) AS L);
//UPDATE `likes` SET `reaction`= 1 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `likes` WHERE `thumb_up` = 1 AND `reaction` = 0) AS L);
App::uses('CakeEventListener', 'ReactionListener');

class ReactionListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'Controller.beforeRender' => 'controllerBeforeRender',
            'MooView.beforeRender' => 'beforeRender',
            'element.activities.renderLikeButton' => 'renderActivityReactionButton',
            'element.activities.renderLikeReview' => 'renderActivityReactionReview',
            'element.comments.renderLikeButton' => 'renderCommentReactionButton',
            'element.comments.renderLikeReview' => 'renderCommentReactionReview',
            'element.items.renderLikeButton' => 'renderItemReactionButton',
            'element.items.renderLikeReview' => 'renderItemReactionReview',
            'element.photos.renderLikeButton' => 'renderPhotoReactionButton',
            'element.photos.renderLikeReview' => 'renderPhotoReactionReview',
            'element.notification.render' => 'renderNotificationIcon',
            'Notification.Controller.ModelBehavior.NotificationBehavior.BeforeSendToMention' => 'BeforeSendToMention',
            'Notification.Controller.ModelBehavior.NotificationBehavior.BeforeSendToTagged' => 'BeforeSendToTagged',
            'Model.afterSave' => 'doAfterSave',
            'ApiHelper.AfterRenderApiFeed' => 'checkViewerReactionActivity',
            'Api.View.ApiComment.afterRenderApiComment' => 'checkViewerReaction',
            'Api.View.ApiVideo.afterRenderApiVideoDetail' => 'checkViewerReaction',
            'Api.View.ApiPhoto.afterRenderApiPhotoDetail' => 'checkViewerReaction',
            'Api.View.ApiAlbum.afterRenderApiAlbumDetail' => 'checkViewerReaction',
            'Api.View.ApiBlog.afterRenderApiBlogDetail' => 'checkViewerReaction',
            'Api.View.ApiBlog.afterRenderApiBlogList' => 'checkViewerReaction',
            'Api.View.ApiTopic.afterRenderApiTopicDetail' => 'checkViewerReaction',
            'Api.View.ApiTopic.afterRenderApiTopicList' => 'checkViewerReaction'
        );
    }

    public function controllerBeforeRender($oEvent) {
        $oController = $oEvent->subject();

        if(Configure::read('Reaction.reaction_enabled')) {
            $hide_like = 1;
            $hide_dislike = 1;
            $oController->set('hide_dislike', $hide_dislike);
            $oController->set('hide_like', $hide_like);
        }
    }

    private function _fixed_table(){
        if( !Configure::read('Reaction.reaction_enabled') ){
            $db = ConnectionManager::getDataSource("default");
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $table_prefix = $mSetting->tablePrefix;
            $db->query("UPDATE `".$table_prefix."reactions` SET `is_update`= 1");
            $db->query("UPDATE `".$table_prefix."likes` SET `reaction`= 0 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `".$table_prefix."likes` WHERE `thumb_up` = 0) AS L)");
            $db->query("UPDATE `".$table_prefix."likes` SET `reaction`= 1 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `".$table_prefix."likes` WHERE `thumb_up` = 1 AND `reaction` = 0) AS L);");

            Cache::clearGroup('reaction');
        }
    }

    public function doAfterSave($event)
    {
        $model = $event->subject();
        $type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);

        if ($type == 'Setting')
        {
            $this->_fixed_table();
        }
    }

    public function BeforeSendToMention($event){
        if(Configure::read('Reaction.reaction_enabled')){
            if(isset($event->data['like']['reaction'])){
                $action = &$event->data['action'];
                $plugin_name = &$event->data['plugin_name'];
                $notification_params = &$event->data['notification_params'];

                $plugin_name = 'reaction';
                $notification_params = serialize(array(
                    'reaction' => $event->data['like']['reaction'],
                    'core_action' => $action,
                    'text' => ''
                ));
                if($action == 'like_mentioned_post'){
                    $action = 'reaction_mentioned_post';
                }elseif($action == 'like_mentioned_comment'){
                    $action = 'reaction_mentioned_comment';
                }
            }
        }
    }

    public function BeforeSendToTagged($event){
        if(Configure::read('Reaction.reaction_enabled')){
            if(isset($event->data['like']['reaction'])){
                $action = &$event->data['action'];
                $plugin_name = &$event->data['plugin_name'];
                $notification_params = &$event->data['notification_params'];

                $plugin_name = 'reaction';
                $notification_params = serialize(array(
                    'reaction' => $event->data['like']['reaction'],
                    'core_action' => $action,
                    'text' => ''
                ));

                if($action == 'like_tagged_status'){
                    $action = 'reaction_tagged_status';
                }
            }
        }
    }

    public function beforeRender($event)
    {
        $e = $event->subject();

        if(Configure::read('Reaction.reaction_enabled')){
            //var_dump(MooCore::getInstance()->isMobile(null)); die();
            if(!empty($e->viewVars['site_rtl'])){
                $css_direction = 'Reaction.reaction-rtl';
                $css_mobile = 'Reaction.reaction-mobile-rtl';
                $css_web = 'Reaction.reaction-web-rtl';
                $css_app = 'Reaction.reaction-app-rtl';
            }else{
                $css_direction = 'Reaction.reaction-ltr';
                $css_mobile = 'Reaction.reaction-mobile';
                $css_web = 'Reaction.reaction-web';
                $css_app = 'Reaction.reaction-app';
            }

            if( MooCore::getInstance()->isMobile(null)){
                $e->Helpers->Html->css( array(
                    'Reaction.reaction',
                    $css_mobile
                ),
                    array('block' => 'css')
                );
            }else{
                $e->Helpers->Html->css( array(
                    'Reaction.reaction',
                    $css_web
                ),
                    array('block' => 'css')
                );
            }

            $e->Helpers->Html->css( array(
                $css_direction
            ),
                array('block' => 'css')
            );

            if ($e->theme == 'mooApp') {
                $e->Helpers->Html->css( array(
                    $css_app
                ),
                    array('block' => 'css')
                );
            }
            /*if (Configure::read('debug') != 0){
                $e->Helpers->Html->css( array(
                    'About.main'
                ),
                    array('block' => 'css')
                );
            }
            else
            {
                $e->Helpers->Minify->css(array(
                    'About.main'
                ));
            }*/

            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }

            $e->Helpers->MooRequirejs->addPath(array(
                "mooReaction" => $e->Helpers->MooRequirejs->assetUrlJS("reaction/js/reaction.{$min}js")
            ));
            $e->Helpers->MooRequirejs->addShim(array(
                'mooReaction'=>array("deps" =>array('jquery'))
            ));

            $e->Helpers->MooPopup->register('themeModal');
        }
    }

    private function getClassReaction($reactionType = -1){
        $class= '';
        switch ($reactionType){
            case REACTION_DISLIKE:
                $class = '';//'react-active-dislike';
                break;
            case REACTION_LIKE:
                $class = 'react-active-like';
                break;
            case REACTION_LOVE:
                $class = 'react-active-love';
                break;
            case REACTION_HAHA:
                $class = 'react-active-haha';
                break;
            case REACTION_WOW:
                $class = 'react-active-wow';
                break;
            case REACTION_SAD:
                $class = 'react-active-sad';
                break;
            case REACTION_ANGRY:
                $class = 'react-active-angry';
                break;
            case REACTION_COOL:
                $class = 'react-active-cool';
                break;
            case REACTION_CONFUSED:
                $class = 'react-active-confused';
                break;
            default:
                break;
        }
        return $class;
    }

    private function getReactionReviewHTML($data_type, $data_id, $myReaction, $element_id, $element_prefix = '', $class = ''){
        $request_base = Router::getRequest()->base;
        $html = '<div id="'.$element_prefix.'reaction_result_'. $element_id .'" class="reaction-review '.$class.'">';
        if(!empty($myReaction)){
            //if(Configure::read('Reaction.reaction_like_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LIKE . '" id="' . $element_prefix . 'react-result-like-' . $element_id . '" class="react-review react-active-like' . (($myReaction['Reaction']['like_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Like').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['like_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_love_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LOVE . '" id="' . $element_prefix . 'react-result-love-' . $element_id . '" class="react-review react-active-love' . (($myReaction['Reaction']['love_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Love').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['love_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_haha_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_HAHA . '" id="' . $element_prefix . 'react-result-haha-' . $element_id . '" class="react-review react-active-haha' . (($myReaction['Reaction']['haha_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Haha').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['haha_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_wow_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_WOW . '" id="' . $element_prefix . 'react-result-wow-' . $element_id . '" class="react-review react-active-wow' . (($myReaction['Reaction']['wow_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Wow').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['wow_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_cool_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_COOL . '" id="' . $element_prefix . 'react-result-cool-' . $element_id . '" class="react-review react-active-cool' . (($myReaction['Reaction']['cool_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Cool').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['cool_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_confused_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_CONFUSED . '" id="' . $element_prefix . 'react-result-confused-' . $element_id . '" class="react-review react-active-confused' . (($myReaction['Reaction']['confused_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Confused').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['confused_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_sad_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_SAD . '" id="' . $element_prefix . 'react-result-sad-' . $element_id . '" class="react-review react-active-sad' . (($myReaction['Reaction']['sad_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="sad" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['sad_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_angry_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_ANGRY . '" id="' . $element_prefix . 'react-result-angry-' . $element_id . '" class="react-review react-active-angry' . (($myReaction['Reaction']['angry_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="angry" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['angry_count'] . '</span></a>';
            //}
            $html .= '<a href="'.$request_base.'/reactions/ajax_show/'.$data_type.'/'.$data_id.'/'.REACTION_ALL.'" id="'.$element_prefix.'react-result-total-'.$element_id.'" class="react-count-all'. (($myReaction['Reaction']['total_count'] == 0) ? ' react-see-hide': '') .'" data-target="#themeModal" data-toggle="modal" data-title="'. __d('reaction', 'All Likes') .'" data-dismiss="modal" data-backdrop="true">'.$myReaction['Reaction']['total_count'].'</a>';
        }else{
            //if(Configure::read('Reaction.reaction_like_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LIKE . '" id="' . $element_prefix . 'react-result-like-' . $element_id . '" class="react-review react-active-like' . (($myReaction['Reaction']['total_count'] == 0) ? ' react-see-hide' : '') . '" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Like').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">' . $myReaction['Reaction']['total_count'] . '</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_love_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LOVE . '" id="' . $element_prefix . 'react-result-love-' . $element_id . '" class="react-review react-active-love react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Love').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_haha_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_HAHA . '" id="' . $element_prefix . 'react-result-haha-' . $element_id . '" class="react-review react-active-haha react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Haha').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_wow_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_WOW . '" id="' . $element_prefix . 'react-result-wow-' . $element_id . '" class="react-review react-active-wow react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Wow').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_cool_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_COOL . '" id="' . $element_prefix . 'react-result-cool-' . $element_id . '" class="react-review react-active-cool react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Cool').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_confused_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_CONFUSED . '" id="' . $element_prefix . 'react-result-confused-' . $element_id . '" class="react-review react-active-confused react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Confused').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_sad_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_SAD . '" id="' . $element_prefix . 'react-result-sad-' . $element_id . '" class="react-review react-active-sad react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Sad').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>';
            //}
            //if(Configure::read('Reaction.reaction_angry_enabled')) {
            $html .= '<a href="' . $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_ANGRY . '" id="' . $element_prefix . 'react-result-angry-' . $element_id . '" class="react-review react-active-angry react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="'.__d('reaction', 'Angry').'" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>';
            //}
            $html .= '<a href="'.$request_base.'/reactions/ajax_show/'.$data_type.'/'.$data_id.'/'.REACTION_ALL.'" id="'.$element_prefix.'react-result-total-'.$element_id.'" class="react-count-all'. (($myReaction['Reaction']['total_count'] == 0) ? ' react-see-hide': '') .'" data-target="#themeModal" data-toggle="modal" data-title="'. __d('reaction', 'All Likes') .'" data-dismiss="modal" data-backdrop="true">'.$myReaction['Reaction']['total_count'].'</a>';
        }
        $html .= '</div>';

        return $html;
    }

    private function getReactionButtonHTML($data_type, $data_id, $current_reaction_type, $current_reaction_active, $current_reaction_class, $element_id, $element_prefix = '', $class = ''){
        $reactionList = array(
            REACTION_DISLIKE => __d('reaction','Like'),//__d('reaction','Dislike'),
            REACTION_LIKE => __d('reaction','Like'),
            REACTION_LOVE => __d('reaction','Love'),
            REACTION_HAHA => __d('reaction','Haha'),
            REACTION_WOW => __d('reaction','Wow'),
            REACTION_SAD => __d('reaction','Sad'),
            REACTION_ANGRY => __d('reaction','Angry'),
            REACTION_COOL => __d('reaction','Cool'),
            REACTION_CONFUSED => __d('reaction','Confused')
        );

        $html = '<div id="'.$element_prefix.'reaction_'.$element_id.'" class="reaction-options '.$class.'">';
        $html .= '<div class="react-overview"></div>';
        $html .= '<a class="react-btn" href="#" data-id="'.$data_id.'" data-type="'.$data_type.'" data-reaction="'.$current_reaction_type.'" data-label="'.$reactionList[$current_reaction_type].'"><span class="'.$current_reaction_class.'"><i class="material-icons">thumb_up</i>'.$reactionList[$current_reaction_type].'</span></a>';
        $html .= '<div class="reacts">';

        //if(Configure::read('Reaction.reaction_like_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_LIKE)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_LIKE.'" data-label="' . __d('reaction', 'Like') . '">';
            $html .= '<div class="react-icon react-like" data-name="' . __d('reaction', 'Like') . '"></div>';
            $html .= '</div>';
        //}

        if(Configure::read('Reaction.reaction_love_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_LOVE)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_LOVE.'" data-label="' . __d('reaction', 'Love') . '">';
            $html .= '<div class="react-icon react-love" data-name="' . __d('reaction', 'Love') . '"></div>';
            $html .= '</div>';
        }

        if(Configure::read('Reaction.reaction_haha_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_HAHA)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_HAHA.'" data-label="' . __d('reaction', 'Haha') . '">';
            $html .= '<div class="react-icon react-haha" data-name="' . __d('reaction', 'Haha') . '"></div>';
            $html .= '</div>';
        }

        if(Configure::read('Reaction.reaction_wow_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_WOW)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_WOW.'" data-label="' . __d('reaction', 'Wow') . '">';
            $html .= '<div class="react-icon react-wow" data-name="' . __d('reaction', 'Wow') . '"></div>';
            $html .= '</div>';
        }

        if(Configure::read('Reaction.reaction_cool_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_COOL)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_COOL.'" data-label="' . __d('reaction', 'Cool') . '">';
            $html .= '<div class="react-icon react-cool" data-name="' . __d('reaction', 'Cool') . '"></div>';
            $html .= '</div>';
        }

        if(Configure::read('Reaction.reaction_confused_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_CONFUSED)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_CONFUSED.'" data-label="' . __d('reaction', 'Confused') . '">';
            $html .= '<div class="react-icon react-confused" data-name="' . __d('reaction', 'Confused') . '"></div>';
            $html .= '</div>';
        }

        if(Configure::read('Reaction.reaction_sad_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_SAD)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_SAD.'" data-label="' . __d('reaction', 'Sad') . '">';
            $html .= '<div class="react-icon react-sad" data-name="' . __d('reaction', 'Sad') . '"></div>';
            $html .= '</div>';
        }

        if(Configure::read('Reaction.reaction_angry_enabled')) {
            $html .= '<div class="react-circle'. (($current_reaction_active == REACTION_ANGRY)? ' react-active': '') .'" data-id="' . $data_id . '" data-type="' . $data_type . '" data-reaction="'.REACTION_ANGRY.'" data-label="' . __d('reaction', 'Angry') . '">';
            $html .= '<div class="react-icon react-angry" data-name="' . __d('reaction', 'Angry') . '"></div>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function checkReactionCounter($data_id, $data_type){
        $reactionModel = MooCore::getInstance()->getModel('Reaction.Reaction');

        $myReaction = Cache::read('reaction.reaction_item.'.$data_id.$data_type,'reaction');
        if (!$myReaction){
            $myReaction = $reactionModel->find('first', array(
                'conditions' => array('target_id' => $data_id, 'type' => $data_type)
            ));
            if(!empty($myReaction)){
                Cache::write('reaction.reaction_item.'.$data_id.$data_type,$myReaction,'reaction');
            }
        }

        $flagUpdate = false;
        if(!empty($myReaction)){
            if($myReaction['Reaction']['is_update']){
                $flagUpdate = true;
            }
        }else{
            $flagUpdate = true;
        }

        if($flagUpdate){
            list($plugin, $model) = mooPluginSplit($data_type);
            if ($plugin)
                $itemModel = MooCore::getInstance()->getModel( $plugin.'.'.$model );
            else
                $itemModel = MooCore::getInstance()->getModel( $model );

            $itemModel->updateCounter($data_id, 'like_count', array('Like.type' => $data_type, 'Like.target_id' => $data_id, 'Like.thumb_up' => 1), 'Like');
            $itemModel->updateCounter($data_id, 'dislike_count', array('Like.type' => $data_type, 'Like.target_id' => $data_id, 'Like.thumb_up' => 0), 'Like');

            $myReaction = $reactionModel->updateReactionCounter($data_type, $data_id);
        }
        return $myReaction;
    }

    public function renderActivityReactionReview($oEvent) {
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();
            $activity = $oEvent->data['activity'];
            $data_type = $oEvent->data['item_type'];

            $data_id = $activity['id'];

            if ($e->request->is('ajax')) {
                $element_id = 'ajax' . $data_type . $data_id;
            } else {
                $element_id = $data_type . $data_id;
            }

            $myReaction = $this->checkReactionCounter($data_id,$data_type);

            //echo $this->getReactionReviewHTML($data_type, $data_id, $myReaction, $element_id, '', 'feed-reaction-review');
            echo $e->element('Reaction.reaction_review', array(
                'data_type' => $data_type,
                'data_id' => $data_id,
                'myReaction' => $myReaction,
                'element_id' => $element_id,
                'element_prefix' => '',
                'class' => 'feed-reaction-review'
            ));
        }
    }

    public function renderActivityReactionButton($oEvent) {
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();

            if (isset($oEvent->data['activity'])) {
                $activity = $oEvent->data['activity'];
                $uid = $oEvent->data['uid'];
                $data_type = $oEvent->data['item_type'];

                $likeModel = MooCore::getInstance()->getModel('Like');

                $data_id = $activity['id'];

                if ($e->request->is('ajax')) {
                    $element_id = 'ajax' . $data_type . $data_id;
                } else {
                    $element_id = $data_type . $data_id;
                }

                $myLike = $likeModel->find('first', array('conditions' => array(
                    'user_id' => $uid,
                    'type' => $data_type,
                    'target_id' => $data_id
                )));

                if (!empty($myLike)) {
                    if($myLike['Like']['thumb_up'] == 0){
                        $class = $this->getClassReaction(0);
                        $current_reaction_type = 1;
                        $current_reaction_active = null;
                    }else{
                        $class = $this->getClassReaction($myLike['Like']['reaction']);
                        $current_reaction_type = ($myLike['Like']['reaction'] == 0) ? 1 : $myLike['Like']['reaction'];
                        $current_reaction_active = $myLike['Like']['reaction'];
                    }
                } else {
                    $class = $this->getClassReaction();
                    $current_reaction_type = 1;
                    $current_reaction_active = null;
                }

                /*echo $this->getReactionButtonHTML($data_type, $data_id, $current_reaction_type, $current_reaction_active, $class, $element_id, '', 'feed-reaction-options');
                if ($e->request->is('ajax')) {
                    echo '<script type="text/javascript">require(["jquery","mooReaction"], function($, mooReaction) {mooReaction.initActivityReaction("' . $element_id . '");});</script>';
                } else {
                    $e->Helpers->Html->scriptBlock(
                        "require(['jquery','mooReaction'], function($,mooReaction) {\$(document).ready(function(){ mooReaction.initActivityReaction('" . $element_id . "'); });});",
                        array(
                            'inline' => false,
                        )
                    );
                }*/

                echo $e->element('Reaction.reaction_button', array(
                    'data_type' => $data_type,
                    'data_id' => $data_id,
                    'current_reaction_type' => $current_reaction_type,
                    'current_reaction_active' => $current_reaction_active,
                    'current_reaction_class' => $class,
                    'element_id' => $element_id,
                    'element_prefix' => '',
                    'class' => 'feed-reaction-options',
                    'action' => 'Activity'
                ));
            }
        }
    }

    public function renderCommentReactionReview($oEvent){
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();
            $comment = $oEvent->data['comment'];
            $data_type = $oEvent->data['item_type'];

            if($data_type == 'photo_comment'){
                $data_type_tmp = 'comment';
            }else{
                $data_type_tmp = $data_type;
            }

            $data_id = $comment['id'];

            if ($e->request->is('ajax')) {
                $element_id = 'ajax' . $data_type . $data_id;
            } else {
                $element_id = $data_type . $data_id;
            }

            $myReaction = $this->checkReactionCounter($data_id,$data_type_tmp);

            //echo $this->getReactionReviewHTML($data_type, $data_id, $myReaction, $element_id, 'comment-', 'comment-reaction-review');
            echo $e->element('Reaction.reaction_review', array(
                'data_type' => $data_type,
                'data_id' => $data_id,
                'myReaction' => $myReaction,
                'element_id' => $element_id,
                'element_prefix' => 'comment-',
                'class' => 'comment-reaction-review'
            ));
        }
    }

    public function renderCommentReactionButton($oEvent) {
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();

            if (isset($oEvent->data['comment'])) {
                $comment = $oEvent->data['comment'];
                $uid = $oEvent->data['uid'];
                $data_type = $oEvent->data['item_type'];

                if($data_type == 'photo_comment'){
                    $data_type_tmp = 'comment';
                }else{
                    $data_type_tmp = $data_type;
                }

                $likeModel = MooCore::getInstance()->getModel('Like');

                $data_id = $comment['id'];

                if ($e->request->is('ajax')) {
                    $element_id = 'ajax' . $data_type . $data_id;
                } else {
                    $element_id = $data_type . $data_id;
                }

                $myLike = $likeModel->find('first', array('conditions' => array(
                    'user_id' => $uid,
                    'type' => $data_type_tmp,
                    'target_id' => $data_id
                )));

                if (!empty($myLike)) {
                    if($myLike['Like']['thumb_up'] == 0){
                        $class = $this->getClassReaction(0);
                        $current_reaction_type = 1;
                        $current_reaction_active = null;
                    }else{
                        $class = $this->getClassReaction($myLike['Like']['reaction']);
                        $current_reaction_type = ($myLike['Like']['reaction'] == 0) ? 1 : $myLike['Like']['reaction'];
                        $current_reaction_active = $myLike['Like']['reaction'];
                    }
                } else {
                    $class = $this->getClassReaction();
                    $current_reaction_type = 1;
                    $current_reaction_active = null;
                }

                /*echo $this->getReactionButtonHTML($data_type, $data_id, $current_reaction_type, $current_reaction_active, $class, $element_id, 'comment_', 'comment-reaction-options');
                if ($e->request->is('ajax')) {
                    echo '<script type="text/javascript">require(["jquery","mooReaction"], function($, mooReaction) {mooReaction.initCommentReaction("' . $element_id . '");});</script>';
                } else {
                    $e->Helpers->Html->scriptBlock(
                        "require(['jquery','mooReaction'], function($,mooReaction) {\$(document).ready(function(){ mooReaction.initCommentReaction('" . $element_id . "'); });});",
                        array(
                            'inline' => false,
                        )
                    );
                }*/

                echo $e->element('Reaction.reaction_button', array(
                    'data_type' => $data_type,
                    'data_id' => $data_id,
                    'current_reaction_type' => $current_reaction_type,
                    'current_reaction_active' => $current_reaction_active,
                    'current_reaction_class' => $class,
                    'element_id' => $element_id,
                    'element_prefix' => 'comment_',
                    'class' => 'comment-reaction-options',
                    'action' => 'Comment'
                ));
            }
        }
    }

    public function renderItemReactionButton($oEvent){
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();
            $item = $oEvent->data['item'];
            $uid = $oEvent->data['uid'];
            $data_type = $oEvent->data['item_type'];

            $likeModel = MooCore::getInstance()->getModel('Like');

            $data_id = $item['id'];

            if ($e->request->is('ajax')) {
                $element_id = 'ajax' . $data_type . $data_id;
            } else {
                $element_id = $data_type . $data_id;
            }

            $myLike = $likeModel->find('first', array('conditions' => array(
                'user_id' => $uid,
                'type' => $data_type,
                'target_id' => $data_id
            )));

            if (!empty($myLike)) {
                if($myLike['Like']['thumb_up'] == 0){
                    $class = $this->getClassReaction(0);
                    $current_reaction_type = 1;
                    $current_reaction_active = null;
                }else{
                    $class = $this->getClassReaction($myLike['Like']['reaction']);
                    $current_reaction_type = ($myLike['Like']['reaction'] == 0) ? 1 : $myLike['Like']['reaction'];
                    $current_reaction_active = $myLike['Like']['reaction'];
                }
            } else {
                $class = $this->getClassReaction();
                $current_reaction_type = 1;
                $current_reaction_active = null;
            }

            /*echo $this->getReactionButtonHTML($data_type, $data_id, $current_reaction_type, $current_reaction_active, $class, $element_id, 'item_', 'item-reaction-options');
            if ($e->request->is('ajax')) {
                echo '<script type="text/javascript">require(["jquery","mooReaction"], function($, mooReaction) {mooReaction.initItemReaction("' . $element_id . '");});</script>';
            } else {
                $e->Helpers->Html->scriptBlock(
                    "require(['jquery','mooReaction'], function($,mooReaction) {\$(document).ready(function(){ mooReaction.initItemReaction('" . $element_id . "'); });});",
                    array(
                        'inline' => false,
                    )
                );
            }*/

            echo $e->element('Reaction.reaction_button', array(
                'data_type' => $data_type,
                'data_id' => $data_id,
                'current_reaction_type' => $current_reaction_type,
                'current_reaction_active' => $current_reaction_active,
                'current_reaction_class' => $class,
                'element_id' => $element_id,
                'element_prefix' => 'item_',
                'class' => 'item-reaction-options',
                'action' => 'Item'
            ));
        }
    }

    public function renderItemReactionReview($oEvent){
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();
            $item = $oEvent->data['item'];
            $data_type = $oEvent->data['item_type'];

            $data_id = $item['id'];

            if ($e->request->is('ajax')) {
                $element_id = 'ajax' . $data_type . $data_id;
            } else {
                $element_id = $data_type . $data_id;
            }

            $myReaction = $this->checkReactionCounter($data_id,$data_type);

            //echo $this->getReactionReviewHTML($data_type, $data_id, $myReaction, $element_id, 'item-', 'item-reaction-review');
            echo $e->element('Reaction.reaction_review', array(
                'data_type' => $data_type,
                'data_id' => $data_id,
                'myReaction' => $myReaction,
                'element_id' => $element_id,
                'element_prefix' => 'item-',
                'class' => 'item-reaction-review'
            ));
        }
    }

    public function renderPhotoReactionReview($oEvent){
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();
            $photo = $oEvent->data['photo'];
            $data_type = $oEvent->data['item_type'];

            $data_id = $photo['Photo']['id'];

            if ($e->request->is('ajax')) {
                $element_id = 'ajax' . $data_type . $data_id;
            } else {
                $element_id = $data_type . $data_id;
            }

            $myReaction = $this->checkReactionCounter($data_id,$data_type);

            //echo $this->getReactionReviewHTML($data_type, $data_id, $myReaction, $element_id, 'photo-', 'photo-reaction-review');
            echo $e->element('Reaction.reaction_review', array(
                'data_type' => $data_type,
                'data_id' => $data_id,
                'myReaction' => $myReaction,
                'element_id' => $element_id,
                'element_prefix' => 'photo-',
                'class' => 'photo-reaction-review'
            ));
        }
    }

    public function renderPhotoReactionButton($oEvent){
        if(Configure::read('Reaction.reaction_enabled')) {
            $e = $oEvent->subject();

            $photo = $oEvent->data['photo'];
            $uid = $oEvent->data['uid'];
            $data_type = $oEvent->data['item_type'];

            $likeModel = MooCore::getInstance()->getModel('Like');

            $data_id = $photo['Photo']['id'];

            if ($e->request->is('ajax')) {
                $element_id = 'ajax' . $data_type . $data_id;
            } else {
                $element_id = $data_type . $data_id;
            }

            $myLike = $likeModel->find('first', array('conditions' => array(
                'user_id' => $uid,
                'type' => $data_type,
                'target_id' => $data_id
            )));

            if (!empty($myLike)) {
                if($myLike['Like']['thumb_up'] == 0){
                    $class = $this->getClassReaction(0);
                    $current_reaction_type = 1;
                    $current_reaction_active = null;
                }else{
                    $class = $this->getClassReaction($myLike['Like']['reaction']);
                    $current_reaction_type = ($myLike['Like']['reaction'] == 0) ? 1 : $myLike['Like']['reaction'];
                    $current_reaction_active = $myLike['Like']['reaction'];
                }
            } else {
                $class = $this->getClassReaction();
                $current_reaction_type = 1;
                $current_reaction_active = null;
            }

            /*echo $this->getReactionButtonHTML($data_type, $data_id, $current_reaction_type, $current_reaction_active, $class, $element_id, 'photo_', 'photo-reaction-options');
            if ($e->request->is('ajax')) {
                echo '<script type="text/javascript">require(["jquery","mooReaction"], function($, mooReaction) {mooReaction.initPhotoReaction("' . $element_id . '");});</script>';
            } else {
                $e->Helpers->Html->scriptBlock(
                    "require(['jquery','mooReaction'], function($,mooReaction) {\$(document).ready(function(){ mooReaction.initPhotoReaction('" . $element_id . "'); });});",
                    array(
                        'inline' => false,
                    )
                );
            }*/

            echo $e->element('Reaction.reaction_button', array(
                'data_type' => $data_type,
                'data_id' => $data_id,
                'current_reaction_type' => $current_reaction_type,
                'current_reaction_active' => $current_reaction_active,
                'current_reaction_class' => $class,
                'element_id' => $element_id,
                'element_prefix' => 'photo_',
                'class' => 'photo-reaction-options',
                'action' => 'Photo'
            ));
        }
    }

    public function renderNotificationIcon($oEvent){
        $noti = $oEvent->data['noti'];
        $param = @unserialize($noti['Notification']['params']);
        if(is_array($param) && isset($param['reaction'])) {
            $reaction = $param['reaction'];
            switch ($reaction) {
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
            echo '<span class="' . $reaction_notification_class . '"></span>';
        }
    }

    private function getLabelReaction($reactionType = -1){
        $label= '';
        switch ($reactionType){
            case REACTION_DISLIKE:
                $label = REACTION_LABEL_DISLIKE;
                break;
            case REACTION_LIKE:
                $label = REACTION_LABEL_LIKE;
                break;
            case REACTION_LOVE:
                $label = REACTION_LABEL_LOVE;
                break;
            case REACTION_HAHA:
                $label = REACTION_LABEL_HAHA;
                break;
            case REACTION_WOW:
                $label = REACTION_LABEL_WOW;
                break;
            case REACTION_SAD:
                $label = REACTION_LABEL_SAD;
                break;
            case REACTION_ANGRY:
                $label = REACTION_LABEL_ANGRY;
                break;
            case REACTION_COOL:
                $label = REACTION_LABEL_COOL;
                break;
            case REACTION_CONFUSED:
                $label = REACTION_LABEL_CONFUSED;
                break;
            default:
                break;
        }
        return $label;
    }

    public function checkViewerReactionActivity($event){
        //$uid = MooCore::getInstance()->getViewer(true);

        if(Configure::read('Reaction.reaction_enabled')){
            $activity = $event->data['data']['Activity'];
            $object = $event->data['objectPlugin'];
            $object_id = $event->data['data']['Activity']['id'];
            $item_type = $event->data['data']['Activity']['item_type'];

            //$likeModel = MooCore::getInstance()->getModel('Like');

            if(!empty($item_type)){
                list($plugin, $name) = mooPluginSplit($item_type);
            }else{
                $plugin = '';
                $name = '';
            }

            if ( $activity['params'] == 'item' && (isset($object[$name]['like_count']))){
                $object_type = $item_type;
                $object_id = $activity['item_id'];
            }else{
                $object_type = 'activity';
            }

            $result = $this->_checkViewerReactionResult($object_id, $object_type);
            $event->result['result']['reaction'] = $result;
        }else{
            $event->result['result']['reaction'] = array(
                'isPluginActive' => 0
            );
        }
    }

    public function checkViewerReaction($event){

        if(Configure::read('Reaction.reaction_enabled')){
            $data_id = $event->data['item_id'];
            $data_type = $event->data['item_type'];

            $result = $this->_checkViewerReactionResult($data_id, $data_type);
            $event->result['result']['reaction'] = $result;
        }else{
            $event->result['result']['reaction'] = array(
                'isPluginActive' => 0
            );
        }
    }

    private function _checkViewerReactionResult($data_id, $data_type){
        $uid = MooCore::getInstance()->getViewer(true);

        $likeModel = MooCore::getInstance()->getModel('Like');

        $myReaction = $this->checkReactionCounter($data_id,$data_type);

        $myLike = $likeModel->find('first', array('conditions' => array(
            'user_id' => $uid,
            'type' => $data_type,
            'target_id' => $data_id
        )));

        $myReactionTypeActive = -1;
        $isLike = 0;
        if (!empty($myLike)) {//have like or dislike
            if($myLike['Like']['thumb_up'] == 0){//dislike
                $myReactionTypeActive = 1;
            }else{//like
                $myReactionTypeActive = ($myLike['Like']['reaction'] == 0) ? 1 : $myLike['Like']['reaction'];
                $isLike = 1;
            }
        }

        return array(
            'isPluginActive' => 1,
            'isLike' => $isLike,
            'objectType' => $data_type,
            'objectId' => $data_id,
            'currentType' => $myReactionTypeActive,
            'currentTypeLabel' => $this->getLabelReaction($myReactionTypeActive),
            'countAll' => $myReaction['Reaction']['total_count'],
            'typeList' => array(
                'like' => array(
                    'sysActive' => 1,
                    'type' => REACTION_LIKE,
                    'count' => $myReaction['Reaction']['like_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_LIKE) ? 1: 0,
                    'name' => __d('reaction', 'Like')
                ),
                'love' => array(
                    'sysActive' => (Configure::read('Reaction.reaction_love_enabled')) ? 1 : 0 ,
                    'type' => REACTION_LOVE,
                    'count' => $myReaction['Reaction']['love_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_LOVE) ? 1: 0,
                    'name' => __d('reaction', 'Love')
                ),
                'haha' => array(
                    'sysActive' => (Configure::read('Reaction.reaction_haha_enabled')) ? 1 : 0 ,
                    'type' => REACTION_HAHA,
                    'count' => $myReaction['Reaction']['haha_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_HAHA) ? 1: 0,
                    'name' => __d('reaction', 'Haha')
                ),
                'wow' => array(
                    'sysActive' => (Configure::read('Reaction.reaction_wow_enabled')) ? 1 : 0 ,
                    'type' => REACTION_WOW,
                    'count' => $myReaction['Reaction']['wow_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_WOW) ? 1: 0,
                    'name' => __d('reaction', 'Wow')
                ),
                'cool' => array(
                    'sysActive' => (Configure::read('Reaction.reaction_cool_enabled')) ? 1 : 0 ,
                    'type' => REACTION_COOL,
                    'count' => $myReaction['Reaction']['cool_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_COOL) ? 1: 0,
                    'name' => __d('reaction', 'Cool')
                ),
                'confused' => array(
                    'sysActive' => (Configure::read('Reaction.reaction_confused_enabled')) ? 1 : 0 ,
                    'type' => REACTION_CONFUSED,
                    'count' => $myReaction['Reaction']['confused_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_CONFUSED) ? 1: 0,
                    'name' => __d('reaction', 'Confused')
                ),
                'sad' => array(
                    'sysActive' => (Configure::read('Reaction.reaction_sad_enabled')) ? 1 : 0 ,
                    'type' => REACTION_SAD,
                    'count' => $myReaction['Reaction']['sad_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_SAD) ? 1: 0,
                    'name' => __d('reaction', 'Sad')
                ),
                'angry' => array(
                    'sysActive' => (Configure::read('Reaction.reaction_angry_enabled')) ? 1 : 0 ,
                    'type' => REACTION_ANGRY,
                    'count' => $myReaction['Reaction']['angry_count'],
                    'reacted' => ($myReactionTypeActive == REACTION_ANGRY) ? 1: 0,
                    'name' => __d('reaction', 'Angry')
                )
            )
        );
    }
}
