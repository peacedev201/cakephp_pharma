<?php
App::uses('CakeEventListener', 'Event');

class StickerListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(        	
            'MooView.beforeRender' => 'beforeRender',
            'View.Elements.activityForm.afterRenderItems' => 'renderSharingForm',
            'ActivitesController.afterShare' => 'afterShare',
            'Element.activities.afterShowFeedContent' => 'afterShowFeedContent',
            'Element.activities.afterRenderCommentForm' => 'afterRenderCommentForm',
            'Controller.Activity.afterComment' => 'afterActivityComment',
            'element.comments.afterShowCommentMessage' => 'afterShowCommentMessage',
            'Controller.Comment.afterEditComment' => 'afterEditComment',
            'Controller.Activity.afterEditComment' => 'afterEditActivityComment',
            'Controller.Activity.afterEditActivity' => 'afterEditActivity',
            'View.Histories.afterShowCommentHistory' => 'afterShowCommentHistory',
            'View.Activities.afterRenderEditActivityForm' => 'afterRenderEditActivityForm',
            'Controller.Comment.afterComment' => 'afterComment'
        );
    }
    
    public function beforeRender($event)
    {
        $e = $event->subject();
        if (!empty($e->request->params['admin']))
        {
            $e->addPhraseJs(array(
                "sticker_confirm_before_leave" => __d('sticker', 'This page is asking you to comfirm you want to leave - please make sure you saved all data.'),
                "sticker_click_here_to_upload" => __d('sticker', 'Click here to upload'),
                "sticker_are_you_sure_you_want_to_delete" => __d('sticker', 'Are you sure you want to delete?'), 
                "sticker_you_must_select_at_least_an_item" => __d('sticker', 'You must select at least an item'), 
            ));
        }
        if(!Configure::read('core.site_offline'))
        {
            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }
            
            $e->addPhraseJs(array(
                "STICKER_IMAGE_EXTENSION" => STICKER_IMAGE_EXTENSION,
                "sticker_animation_interval" => Configure::read('Sticker.sticker_animation_interval'),
                "sticker_is_android" => $e->request->is('androidApp'),
                "sticker_is_mobile" => MooCore::getInstance()->isMobile(null)
            ));
            
            $e->MooPopup->register('stickerModal');
           
            $e->Helpers->MooRequirejs->addPath(array(
                "mooSticker" => $e->Helpers->MooRequirejs->assetUrlJS("Sticker.js/sticker.{$min}js"),
                "mooStickerSlick" => $e->Helpers->MooRequirejs->assetUrlJS("Sticker.js/slick.{$min}js"),
                "mooStickerScrollbar" => $e->Helpers->MooRequirejs->assetUrlJS("Sticker.js/scrollbar.{$min}js"),
            ));

            if (empty($e->request->params['admin'])/* && !MooCore::getInstance()->isMobile(null)*/)
            {
                $e->Helpers->Html->scriptBlock(
                    "require(['jquery','mooSticker', 'mooStickerSlick', 'mooStickerScrollbar'], function($,mooSticker) {mooSticker.initSticker();});", array(
                        'inline' => false,
                    )
                );
                $e->Helpers->Html->css(array(
                    'Sticker.sticker',
                    'Sticker.slick',
                    'Sticker.scrollbar'
                ), array('block' => 'css', 'minify' => false));
                
                if (Configure::read('GifComment.gif_comment_enabled'))
                {
                    $e->Helpers->Html->css(array(
                        'Sticker.sticker_gif'
                    ), array('block' => 'css', 'minify' => false));
                }
            }
            if($e->request->is('androidApp') || $e->request->is('iosApp'))
            {
                $e->Helpers->Html->css(array(
                    'Sticker.sticker_app'
                ), array('block' => 'css', 'minify' => false));
            }
        }
    }
    
    public function renderSharingForm(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline'))
        {
            echo $view->element('Sticker.sharing');
        }
    }
    
    public function afterRenderCommentForm(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline'))
        {
            //load data for edit
            $sticker_image = array();
            if(!empty($event->data['type']) && ($event->data['type'] == 'activity_comment_edit' || $event->data['type'] == 'item_comment_edit'))
            {
                $mComment = MooCore::getInstance()->getModel('Comment');
                $mActivityComment = MooCore::getInstance()->getModel('ActivityComment');
                $mStickerImage = MooCore::getInstance()->getModel('Sticker.StickerImage');
                $sticker_image_id = "";
                if($event->data['type'] == "activity_comment_edit")
                {
                    $activity_comment = $mActivityComment->findById($event->data['id']);
                    $sticker_image_id = $activity_comment['ActivityComment']['sticker_image_id'];
                }
                else
                {
                    $comment = $mComment->findById($event->data['id']);
                    $sticker_image_id = $comment['Comment']['sticker_image_id'];
                }
                if($sticker_image_id != "")
                {
                    $sticker_image = $mStickerImage->getDetail($sticker_image_id);
                }
                $view->set('sticker_image_id', $sticker_image_id);
            }
            
            $photo_theater = false;
            if($view->request->controller == "photos" && ($view->request->action == "view" || $view->request->action == "ajax_view" || $view->request->action == "ajax_view_theater") && $view->request->is('ajax'))
            {
                $photo_theater = true;
            }
            $view->set(array(
                'item_id' => $event->data['id'],
                'item_type' => $event->data['type'],
                'photo_theater' => $photo_theater,
                'sticker_image' => $sticker_image
            ));
            echo $view->element('Sticker.sharing');
        }
    }
    
    public function afterRenderEditActivityForm(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline'))
        {
            $activity = $event->data['activity']['Activity'];
            $mStickerImage = MooCore::getInstance()->getModel('Sticker.StickerImage');
            $sticker_image_id = $activity['sticker_image_id'];
            $sticker_image = array();
            if($sticker_image_id > 0)
            {
                $sticker_image = $mStickerImage->getDetail($sticker_image_id);
            }
            
            $view->set(array(
                'sticker_image_id' => $sticker_image_id,
                'item_id' => $activity['id'],
                'item_type' => 'activity_edit',
                'photo_theater' => false,
                'sticker_image' => $sticker_image
            ));
            echo $view->element('Sticker.sharing');
        }
    }
    
    public function afterShare(CakeEvent $event)
    {
        $view = $event->subject();
        $activity = $event->data['activity']['Activity'];
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline'))
        {
            if(!empty($activity['sticker_image_id']))
            {
                $mSticker = MooCore::getInstance()->getModel('Sticker.Sticker');
            
                $mSticker->saveLog(MooCore::getInstance()->getViewer(true), $view->data['sticker_image_id']);
            }
        }
    }
    
    public function afterShowFeedContent(CakeEvent $event)
    {
        $view = $event->subject();
        if(!Configure::read('core.site_offline') && 
           (!empty($event->data['activity']['Activity']['sticker_image_id']) || $event->data['activity']['Activity']['action'] == 'comment_add_photo'))
        {
            $activity = $event->data['activity']['Activity'];
            $allow_render = true;
            if($event->data['activity']['Activity']['action'] == 'comment_add_photo')
            {
                $mComment = MooCore::getInstance()->getModel('Comment');
                $comment = $mComment->find('first', array(
                    'conditions' => array(
                        'Comment.type' => 'Photo_Photo', 
                        'Comment.target_id' => $activity['item_id']
                    ),
                    'order' => array('Comment.id' => 'DESC')
                ));
                if(!empty($comment['Comment']['sticker_image_id']))
                {
                    $activity['sticker_image_id'] = $comment['Comment']['sticker_image_id'];
                }
                else
                {
                    $allow_render = false;
                }
            }
            if($allow_render)
            {
                $mStickerImage = MooCore::getInstance()->getModel('Sticker.StickerImage');
                $sticker_image = $mStickerImage->getDetail($activity['sticker_image_id']);
                $view->set('sticker_image', $sticker_image);
                echo $view->element('Sticker.sticker_feed_content');
            }
        }
    }

    public function afterActivityComment(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline') && !empty($event->data['item']['ActivityComment']['sticker_image_id']))
        {
            $mSticker = MooCore::getInstance()->getModel('Sticker.Sticker');
                
            $activity_comment = $event->data['item']['ActivityComment'];
            $mSticker->saveLog(MooCore::getInstance()->getViewer(true), $event->data['item']['ActivityComment']['sticker_image_id']);
        }
    }
    
    public function afterEditComment(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline'))
        {
            $mSticker = MooCore::getInstance()->getModel('Sticker.Sticker');
            $mComment = MooCore::getInstance()->getModel('Comment');
            
            $data = $event->data['data'];
            $comment = $event->data['comment'];
            $sticker_image_id = !empty($data['sticker_image_id']) ? $data['sticker_image_id'] : "";
            
            //update comment
            $params = array(
                'sticker_image_id' => "'".$sticker_image_id."'",
            );
            if($sticker_image_id > 0)
            {
                $params['thumbnail'] = "''";
                $comment['Comment']['thumbnail'] = '';
            }
            $mComment->updateAll($params, array(
                'Comment.id' => $comment['Comment']['id']
            ));
            $comment['Comment']['sticker_image_id'] = $sticker_image_id;
            
            //save log
            $this->saveCommentHistory($sticker_image_id, "Comment", $comment['Comment']['id']);
            
            $view->set('comment', $comment);
        }
    }
    
    public function afterEditActivityComment(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline'))
        {
            $mSticker = MooCore::getInstance()->getModel('Sticker.Sticker');
            $mActivityComment = MooCore::getInstance()->getModel('ActivityComment');
            
            $data = $event->data['data'];
            $activity_comment = $event->data['item'];
            $sticker_image_id = !empty($data['sticker_image_id']) ? $data['sticker_image_id'] : "";
            
            //update comment
            $params = array(
                'sticker_image_id' => "'".$sticker_image_id."'",
            );
            if($sticker_image_id > 0)
            {
                $params['thumbnail'] = "''";
                $activity_comment['ActivityComment']['thumbnail'] = '';
            }
            $mActivityComment->updateAll($params, array(
                'ActivityComment.id' => $activity_comment['ActivityComment']['id']
            ));
            $activity_comment['ActivityComment']['sticker_image_id'] = $sticker_image_id;
            
            //save log
            $this->saveCommentHistory($sticker_image_id, "Core_Activity_Comment", $activity_comment['ActivityComment']['id']);
            
            $view->set('activity_comment', $activity_comment);
        }
    }
    
    public function afterEditActivity(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline'))
        {
            $mSticker = MooCore::getInstance()->getModel('Sticker.Sticker');
            $mActivity = MooCore::getInstance()->getModel('Activity');
            
            $data = $event->data['data'];
            $activity = $event->data['item'];
            $sticker_image_id = !empty($data['sticker_image_id']) ? $data['sticker_image_id'] : "";
            
            //update activity
            $params = array(
                'sticker_image_id' => "'".$sticker_image_id."'",
            );
            if($sticker_image_id > 0)
            {
                $params['item_type'] = "''";
                $params['items'] = "''";
                $params['item_id'] = "''";
                $activity['Activity']['item_type'] = '';
                $activity['Activity']['items'] = '';
                $activity['Activity']['item_id'] = '';
            }
            $mActivity->updateAll($params, array(
                'Activity.id' => $activity['Activity']['id']
            ));
            $activity['Activity']['sticker_image_id'] = $sticker_image_id;

            //save log
            $this->saveCommentHistory($sticker_image_id, "Activity", $activity['Activity']['id']);
            
            $view->set('activity', $activity);
        }
    }
    
    private function saveCommentHistory($sticker_image_id, $type, $target_id)
    {
        if(!empty($sticker_image_id))
        {
            $mSticker = MooCore::getInstance()->getModel('Sticker.Sticker');
            $mCommentHistory = MooCore::getInstance()->getModel('CommentHistory');
            
            //update comment history
            $last_history = $mCommentHistory->getLastHistory($type, $target_id);
            if($last_history != null)
            {
                $mCommentHistory->updateAll(array(
                    'sticker_image_id' => "'".$sticker_image_id."'",
                ), array(
                    'CommentHistory.id' => $last_history['CommentHistory']['id']
                ));
            }

            //save log
            $mSticker->saveLog(MooCore::getInstance()->getViewer(true), $sticker_image_id);
        }
    }
    
    public function afterShowCommentMessage(CakeEvent $event)
    {
        $view = $event->subject();
        if(!Configure::read('core.site_offline') && (!empty($event->data['comment'] || !empty($event->data['comment']['ActivityComment']))))
        {
            $mStickerImage = MooCore::getInstance()->getModel('Sticker.StickerImage');
            if(!empty($event->data['comment']['ActivityComment']))
            {
                $comment = $event->data['comment']['ActivityComment'];
            }
            else if(!empty($event->data['comment']['Comment']))
            {
                $comment = $event->data['comment']['Comment'];
            }
            else
            {
                $comment = $event->data['comment'];
            }
            $sticker_image = $mStickerImage->getDetail($comment['sticker_image_id']);
            $view->set('sticker_image', $sticker_image);
            echo $view->element('Sticker.misc/sticker_animation');
        }
    }
    
    public function afterShowCommentHistory(CakeEvent $event)
    {
        $view = $event->subject();
        if(!Configure::read('core.site_offline') && !empty($event->data['history']['CommentHistory']['sticker_image_id']))
        {
            $mStickerImage = MooCore::getInstance()->getModel('Sticker.StickerImage');
            $sticker_image = $mStickerImage->getDetail($event->data['history']['CommentHistory']['sticker_image_id']);
            $view->set('sticker_image', $sticker_image);
            echo $view->element('Sticker.misc/sticker_animation');
        }
    }
    
    public function afterComment(CakeEvent $event)
    {
        $view = $event->subject();
        if(Configure::read('Sticker.sticker_enabled') && !Configure::read('core.site_offline') && !empty($event->data['data']['sticker_image_id']))
        {
            $data = $event->data['data'];
            $mActivity = MooCore::getInstance()->getModel('Activity');
            $activity = $mActivity->find('first', array('conditions' => array(
                'Activity.item_type' => $data['type'],
                'Activity.item_id' => $data['target_id']
            )));
            if ($activity != null)
            {
                $mActivity->updateAll(array(
                    'sticker_image_id' => "'".$data['sticker_image_id']."'",
                ), array(
                    'Activity.id' => $activity['Activity']['id']
                ));
            }
        }
    }
}