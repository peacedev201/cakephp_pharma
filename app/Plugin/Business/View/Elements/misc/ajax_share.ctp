<?php
   if (!empty($activity)): 
       $historyModel = MooCore::getInstance()->getModel('CommentHistory');
       $admins_current = (isset($admins) ? array_merge($admins,array($activity['Activity']['user_id'])) : array($activity['Activity']['user_id']));
   	$item_type = $activity['Activity']['item_type'];
       if($item_type == 'Business_Business_Branch')
       {
           $item_type == 'Business_Business';
       }
   	if ($activity['Activity']['plugin'])
   	{
   		$options = array('plugin'=>$activity['Activity']['plugin']);
   	}
   	else
   	{
   		$options = array();
   	}
   	
   	if ($item_type)
   	{
   		list($plugin, $name) = mooPluginSplit($item_type);
   		$object = MooCore::getInstance()->getItemByType($item_type,$activity['Activity']['item_id']);
   		
   	}
   	else
   	{
   		$plugin = '';
   		$name ='';
   		$object = null;
   	}
   ?>
<li id="activity_<?php echo $activity['Activity']['id']?>">
    <div class="feed_main_info">
      <?php
         // delete link available for activity poster, site admin and item admins
         if ( $activity['Activity']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins_current ) && in_array( $uid, $admins_current ) || (!empty($activity['UserTagging']) && in_array($uid, explode(',', $activity['UserTagging']['users_taggings']) ) ) || $this->MooPeople->isMentioned($uid, $activity['Activity']['id'])  ) ):
         ?>
        <div class="dropdown edit-post-icon">
         <?php if (!empty($uid)): ?>
            <a href="javascript:void(0)" data-toggle="dropdown" class="cross-icon">
                <i class="material-icons">more_vert</i>
            </a>
            <ul class="dropdown-menu">
            <?php 
               $item_type =  empty($activity['Activity']['item_type']) ? 'activity' : $activity['Activity']['item_type'];
               ?>
            <?php if (($uid == $activity['Activity']['user_id']) || $this->MooPeople->isTagged($uid, $activity['Activity']['id'], 'activity'/*$item_type*/) || $this->MooPeople->isMentioned($uid, $activity['Activity']['id'])): ?>
                <li>
               <?php
                  $item_id = !empty($activity['Activity']['item_id']) ? $activity['Activity']['item_id'] : $activity['Activity']['id'];
                  ?>
               <?php if ( $activity['Activity']['params'] == 'item' && (isset($object[$name]['like_count']))): ?>
               <?php
                  $title = $this->Moo->isNotificationStop($item_id, $item_type) ? __d('business', 'Turn on notifications') : __d('business', 'Stop Notifications');
                  
                      $this->MooPopup->tag(array(
                             'href'=>$this->Html->url(array("controller" => "notifications",
                                                            "action" => "stop",
                                                            "plugin" => false,
                                                          $item_type,
                                                            $item_id
                                                        )),
                             'title' => $title,
                             'innerHtml'=> $title,
                              'id' => 'stop_notification_' . $item_type. $item_id
                     ));
                  ?>
               <?php else: ?>
               <?php
                  $title = $this->Moo->isNotificationStop($activity['Activity']['id'], 'activity') ? __d('business', 'Turn on notifications') : __d('business', 'Stop Notifications');
                  
                      $this->MooPopup->tag(array(
                             'href'=>$this->Html->url(array("controller" => "notifications",
                                                            "action" => "stop",
                                                            "plugin" => false,
                                                          'activity',
                                                            $activity['Activity']['id']
                                                        )),
                             'title' => $title,
                             'innerHtml'=> $title,
                              'id' => 'stop_notification_' . 'activity'. $activity['Activity']['id']
                     ));
                  ?>
               <?php endif; ?>
                </li>
            <?php endif; ?>
            <?php if(!empty($activity['UserTagging']['users_taggings']) && $activity['Activity']['user_id'] == $uid ): ?>
                <li>
               <?php
                  $this->MooPopup->tag(array(
                         'href'=>$this->Html->url(array("controller" => "friends",
                                                        "action" => "tagged",
                                                        "plugin" => false,
                                                        $activity['Activity']['id']
                                                    )),
                         'title' => __d('business', 'Tag Friends'),
                         'innerHtml'=> __d('business', 'Tag Friends'),
                  ));
                  ?>
                </li>
            <?php endif; ?>
            <?php if (isset($activity['UserTagging']['users_taggings']) && in_array($uid, explode(',', $activity['UserTagging']['users_taggings']) ) || $this->MooPeople->isMentioned($uid, $activity['Activity']['id']) ): ?>
                <li>
                    <a class="owner-remove-tags" data-activity-id="<?php echo $activity['Activity']['id']; ?>" data-activity-item-type="activity<?php //echo empty($activity['Activity']['item_type']) ? 'activity' : $activity['Activity']['item_type']; ?>" href="javascript:void(0)">
               <?php echo __d('business', 'Remove Tags'); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (($activity['Activity']['user_id'] == $uid || $cuser['Role']['is_admin']) &&  $activity['Activity']['action'] == 'wall_post'):?>
                <li>
                    <a class="admin-edit-activity editActivity" data-activity-id="<?php echo $activity['Activity']['id']?>" href="javascript:void(0)">
               <?php echo __d('business', 'Edit Post'); ?>
                    </a>
                </li>
            <?php endif;?>
            <?php if (( (!empty($admins_current) && in_array($uid, $admins_current)) || $activity['Activity']['user_id'] == $uid || $cuser['Role']['is_admin'])): ?>
                <li>
                    <a class="admin-or-owner-remove-activity removeActivity" data-activity-id="<?php echo $activity['Activity']['id']?>" href="javascript:void(0)">
               <?php echo __d('business', 'Delete Post'); ?>
                    </a>
                </li>
            <?php endif; ?>
            </ul>
         <?php endif; ?>
        </div>
      <?php endif; ?>
        <div class="activity_feed_image">
         <?php echo $this->Moo->getItemPhoto(array('User' => $activity['User']),array( 'prefix' => '100_square','tooltip' => true), array('class' => 'img_wrapper2 user_avatar_96'))?>
        </div>
        <div class="activity_feed_content">
            <div class="comment hasDelLink">
                <div class="activity_text">
               <?php echo $this->Moo->getName($activity['User'])?>
               <?php
                  echo $this->element('activity/text/' . $activity['Activity']['action'], array('activity' => $activity,'object'=>$object),$options);
                  ?>
                </div>
                <div class="feed_time">
               <?php if ( $activity['Activity']['params'] != 'no-comments' ): ?>
                    <a href="<?php echo $this->request->base?>/users/view/<?php echo $activity['Activity']['user_id']?>/activity_id:<?php echo $activity['Activity']['id']?>" class="date"><?php echo $this->Moo->getTime( $activity['Activity']['created'], Configure::read('core.date_format'), $utz )?></a>
               <?php else: ?>
                    <span class="date"><?php echo $this->Moo->getTime( $activity['Activity']['created'], Configure::read('core.date_format'), $utz )?></span>
               <?php endif; ?>
               <?php
                  $this->MooPopup->tag(array(
                         'href'=>$this->Html->url(array("controller" => "histories",
                                                        "action" => "ajax_show",
                                                        "plugin" => false,
                                                        'activity',
                                                        $activity['Activity']['id']
                                                    )),
                         'title' => __d('business', 'Show edit history'),
                         'innerHtml'=> $historyModel->getText('activity',$activity['Activity']['id']),
                      'style' => empty($activity['Activity']['edited']) ? 'display:none' : '',
                      'id' => 'history_activity_'. $activity['Activity']['id'],
                      'class' => 'edit-btn',
                  'data-dismiss'=>'modal'
                  ));
                  ?>
                </div>
            </div>
            <div class="activity_feed_content_text" id="activity_feed_content_text_<?php echo $activity['Activity']['id'];?>">
            <?php
               //$activity['Activity']['content'] = $this->renderMention($activity['Activity']['content']);
               echo $this->element('activity/content/' . $activity['Activity']['action'], array('activity' => $activity,'object'=>$object),$options);
               ?>
            </div>
         <?php if($activity['Activity']['params'] != 'no-comments'): ?>
            
         <?php endif; ?>
                </div>
            <div class="clear"></div>
        </div>
        <div class="feed_comment_info">
            <?php if ( (!($activity['Activity']['item_type'] == 'Topic_Topic' && isset($object['Topic']) && $object['Topic']['locked']) ) || (!empty($cuser) && $cuser['Role']['is_admin']) ): ?>
                <div class="date">
               <?php if ( $activity['Activity']['params'] == 'mobile' ) echo __d('business', 'via mobile'); ?>
               <?php if ( !isset($is_member) || $is_member || $cuser['Role']['is_admin'] ): ?>
               <?php if( (isset($groupTypeItem) && $groupTypeItem['is_member']) || (!isset($groupTypeItem)) ) : ?>
                    <!-- comment -->
               <?php if ( $activity['Activity']['params'] == 'item' && (isset($object[$name]['like_count']))): ?>
                    <div class="progress_like_dislike">
                        <span class="btn-feed">
                            <a href="javascript:void(0)" data-id="<?php echo $activity['Activity']['id']?>" data-type="activity" data-status="1" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && !empty( $activity['Likes'][$uid] ) ): ?>active<?php endif; ?>"><i class="material-icons dp-18">thumb_up</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Like') ?></span></a>
                  <?php
                     $this->MooPopup->tag(array(
                            'href'=>$this->Html->url(array("controller" => "likes",
                                                           "action" => "ajax_show",
                                                           "plugin" => false,
                                                           $item_type,
                                                           $activity['Activity']['item_id'],
                                                       )),
                            'title' => __d('business', 'People Who Like This'),
                            'innerHtml'=> '<span id="'. $item_type . '_like_' . $activity['Activity']['item_id'] . '">' . $object[$name]['like_count'] . '</span>',
                         'data-dismiss' => 'modal'
                     ));
                     ?>
                        </span>
                  <?php if(empty($hide_dislike)): ?>
                        <span class="btn-feed">
                            <a href="javascript:void(0)" data-id="<?php echo $activity['Activity']['id']?>" data-type="activity" data-status="0" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && isset( $activity['Likes'][$uid] ) && $activity['Likes'][$uid] == 0 ): ?>active<?php endif; ?>"><i class="material-icons dp-18">thumb_down</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Dislike') ?></span></a>
                  <?php
                     $this->MooPopup->tag(array(
                              'href'=>$this->Html->url(array("controller" => "likes",
                                                             "action" => "ajax_show",
                                                             "plugin" => false,
                                                             $item_type,
                                                             $activity['Activity']['item_id'],1
                                                         )),
                              'title' => __d('business', 'People Who Dislike This'),
                              'innerHtml'=> '<span id="'.  $item_type . '_dislike_' . $activity['Activity']['item_id'] . '">' . $object[$name]['dislike_count'] . '</span>',
                     ));
                     ?>
                        </span>
                  <?php endif; ?>
                  <?php
                     $totalAction = $object[$name]['like_count'] + $object[$name]['dislike_count'];
                     if($totalAction > 0){
                       $like_percent = ($object[$name]['like_count']/$totalAction)*100;
                       $dislike_percent = ($object[$name]['dislike_count']/$totalAction)*100;
                     }
                     
                     ?>
                        <span id="like_percent_<?php echo $activity['Activity']['id']?>" class="like_percent" style="width:<?php if(isset($like_percent)) echo $like_percent; ?>%"></span>
                        <span id="dislike_percent_<?php echo $activity['Activity']['id']?>" class="dislike_percent" style="width:<?php if(isset($dislike_percent)) echo $dislike_percent; ?>%"></span>
                    </div>
                    <span class="btn-feed p_inside">
                        <a href="javascript:void(0)" class="showCommentForm" data-id="<?php echo $activity['Activity']['id']?>"><i class="material-icons dp-18">forum</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Comment') ?><span></a> 
                                </span>
                                <!-- Share activity -->
               <?php echo $this->element('share', array('activity' => $activity)); ?>
                                <!-- End Share activity -->
               <?php else: ?>
                                <div class="progress_like_dislike">
                                    <span class="btn-feed">
                                        <a href="javascript:void(0)" data-id="<?php echo $activity['Activity']['id']?>" data-type="activity" data-status="1" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && !empty( $activity_likes['activity_likes'][$activity['Activity']['id']] ) ): ?>active<?php endif; ?>"><i class="material-icons dp-18">thumb_up</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Like') ?></span></a>
                  <?php
                     $this->MooPopup->tag(array(
                            'href'=>$this->Html->url(array("controller" => "likes",
                                                           "action" => "ajax_show",
                                                           "plugin" => false,
                                                           'activity',
                                                           $activity['Activity']['id'],
                                                       )),
                            'title' => __d('business', 'People Who Like This'),
                            'innerHtml'=> '<span id="activity_like_'. $activity['Activity']['id']. '">' . $activity['Activity']['like_count'] . '</span>',
                         'data-dismiss' => 'modal'
                     ));
                     ?>
                                    </span>
                  <?php if(empty($hide_dislike)): ?>
                                    <span class="btn-feed">
                                        <a href="javascript:void(0)" data-id="<?php echo $activity['Activity']['id']?>" data-type="activity" data-status="0" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && isset( $activity_likes['activity_likes'][$activity['Activity']['id']] ) && $activity_likes['activity_likes'][$activity['Activity']['id']] == 0 ): ?>active<?php endif; ?>"><i class="material-icons dp-18">thumb_down</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Dislike') ?></span></a>
                  <?php
                     $this->MooPopup->tag(array(
                            'href'=>$this->Html->url(array("controller" => "likes",
                                                           "action" => "ajax_show",
                                                           "plugin" => false,
                                                           'activity',
                                                           $activity['Activity']['id'],1
                                                       )),
                            'title' => __d('business', 'People Who Dislike This'),
                            'innerHtml'=> '<span id="activity_dislike_' . $activity['Activity']['id'] . '">' .  $activity['Activity']['dislike_count'] . '</span>',
                     ));
                     ?>
                                    </span>
                  <?php endif; ?>
                  <?php
                     $totalAction = $activity['Activity']['like_count'] + $activity['Activity']['dislike_count'];
                     if($totalAction > 0){
                       $like_percent = ($activity['Activity']['like_count']/$totalAction)*100;
                       $dislike_percent = ($activity['Activity']['dislike_count']/$totalAction)*100;
                     }
                     ?>
                                    <span id="like_percent_<?php echo $activity['Activity']['id']?>" class="like_percent" style="width:<?php if(isset($like_percent)) echo $like_percent; ?>%"></span>
                                    <span id="dislike_percent_<?php echo $activity['Activity']['id']?>" class="dislike_percent" style="width:<?php if(isset($dislike_percent)) echo $dislike_percent; ?>%"></span>
                                </div>
                                <span class="btn-feed p_inside">
                                    <a href="javascript:void(0)" class="showCommentForm" data-id="<?php echo $activity['Activity']['id']?>"><i class="material-icons dp-18">forum</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Comment') ?></span></a>
                                </span>
                                <!-- Share activity -->
               <?php echo $this->element('share', array('activity' => $activity)); ?>
                                <!-- End Share activity -->
               <?php endif; ?>
               <?php endif; ?>
               <?php endif; ?>
                                </div>
            <?php endif; ?>
            <?php if( (isset($groupTypeItem) && $groupTypeItem['is_member']) || (!isset($groupTypeItem)) ) : ?>
                                <ul class="activity_comments comment_list" id="comments_<?php echo $activity['Activity']['id']?>" <?php if (empty($activity[ 'ActivityComment']) && empty($activity[ 'PhotoComment']) && empty($activity[ 'Activity'][ 'like_count']) && empty($activity[ 'ItemComment']) && ( $activity[ 'Activity'][ 'params'] !='item' || empty($object[$name][ 'like_count']) ) ) echo 'style="display:none"'; ?>>
               <?php
                  // comment form
                  if ($activity['Activity']['params'] != 'no-comments' && ( (isset($is_member) && $is_member) || (!empty($cuser) && $cuser['Role']['is_admin']) || !($activity['Activity']['item_type'] == 'Topic_Topic' && isset($object['Topic']) && $object['Topic']['locked']))):
                  ?>
                                    <li id="newComment_<?php echo $activity['Activity']['id']?>">
                  <?php echo $this->Moo->getItemPhoto(array('User' => $cuser), array( 'prefix' => '50_square','tooltip' => true), array('class' => 'user_avatar_small img_wrapper2'))?>
                                        <div class="comment">
                                            <textarea class="commentBox showCommentBtn" data-id="<?php echo $activity['Activity']['id']?>" placeholder="<?php echo __d('business', 'Write a comment...')?>" id="commentForm_<?php echo $activity['Activity']['id']?>"></textarea>
                                            <div class="clear"></div>
                                            <div style="display:block;" class="commentButton" id="commentButton_<?php echo $activity['Activity']['id']?>">
                        <?php if ( !empty( $uid ) ): ?>
                                                <input type="hidden" id="comment_image_<?php echo $activity['Activity']['id'];?>" />
                                                <div id="comment_button_attach_<?php echo $activity['Activity']['id'];?>"></div>
                                                <a href="javascript:void(0)" <?php if ( $activity[ 'Activity'][ 'params']=='item' && isset($object[$name][ 'comment_count'])): ?> class="btn btn-action  viewer-submit-item-comment" data-item-type="<?php echo $item_type?>" data-activity-item-id="<?php echo $activity['Activity']['item_id']?>" data-activity-id="<?php echo $activity['Activity']['id']?>" <?php else: ?> class="btn btn-action  viewer-submit-comment" data-activity-id="<?php echo $activity['Activity']['id']?>" <?php endif; ?>><?php echo __d('business', 'Comment')?></a>
                        <?php if($this->request->is('ajax')): ?>
                                                <script type="text/javascript">
                                                    require(["jquery", "mooAttach"], function ($, mooAttach) {
                                                        mooAttach.registerAttachComment(<?php echo $activity['Activity']['id'];?>);
                                                    });
                                                </script>
                        <?php else: ?>
                        <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,'requires'=>array('jquery','mooAttach'), 'object' => array('$', 'mooAttach'))); ?> mooAttach.registerAttachComment(
                        <?php echo $activity['Activity']['id'];?>);
                        <?php $this->Html->scriptEnd(); ?>
                        <?php endif;?>
                        <?php else: ?>
                        <?php echo __d('business', 'Please login or register')?>
                        <?php endif; ?>
                                            </div>
                                            <div id="comment_preview_image_<?php echo $activity['Activity']['id'];?>"></div>
                                        </div>
                                    </li>
               <?php
                  endif;
                                    // end comment form
                  ?>
               <?php
                  // item comments
                  if ( !empty($activity['ItemComment']) ):
                        
                    ?>
               <?php
                  foreach ($activity['ItemComment'] as $comment):
                  ?>
                                    <li id="itemcomment_<?php echo $comment['Comment']['id']?>">
                  <?php echo $this->Moo->getItemPhoto(array('User' => $comment['User']), array( 'prefix' => '50_square','tooltip' => true), array('class' => 'user_avatar_small img_wrapper2'))?>
                  <?php
                     // delete link available for activity poster, site admin and admins array
                     if ( $comment['Comment']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins_current ) && in_array( $uid, $admins_current ) ) ):
                     ?>
                                        <div class="dropdown edit-post-icon comment-option">
                                            <a href="javascript:void(0)" data-toggle="dropdown" class="cross-icon">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <ul class="dropdown-menu">
                        <?php if ($comment['Comment']['user_id'] == $uid || $cuser['Role']['is_admin']):?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="return editItemComment(<?php echo $comment['Comment']['id']?>)">
                           <?php echo __d('business', 'Edit Comment'); ?>
                                                    </a>
                                                </li>
                        <?php endif; ?>
                                                <li>
                                                    <a class="admin-or-owner-confirm-delete-item-comment" href="javascript:void(0)" data-comment-id="<?php echo $comment['Comment']['id']?>">
                           <?php echo __d('business', 'Delete Comment'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                  <?php endif; ?>
                                        <div class="comment hasDelLink">
                     <?php echo $this->Moo->getName($comment['User'])?>
                                            <span id="item_feed_comment_text_<?php echo $comment['Comment']['id']?>">
                        <?php
                           echo $this->Business->viewMore(h($comment['Comment']['message'])); 
                           ?>
                        <?php if ($comment['Comment']['thumbnail']):?>
                                                <div class="comment_thumb">
                                                    <a href="<?php echo $this->Moo->getImageUrl($comment,array());?>">
                           <?php echo $this->Moo->getImage($comment,array('prefix'=>'200','tooltip' => true));?>
                                                    </a>
                                                </div>
                        <?php endif;?>
                                            </span>
                                            <div class="feed-time date">
                        <?php echo $this->Moo->getTime( $comment['Comment']['created'], Configure::read('core.date_format'), $utz )?>
                        <?php
                           $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "histories",
                                                                   "action" => "ajax_show",
                                                                   "plugin" => false,
                                                                   'comment',
                                                                   $comment['Comment']['id']
                                                               )),
                                    'title' => __d('business', 'Show edit history'),
                                    'innerHtml'=> $historyModel->getText('comment',$comment['Comment']['id']),
                                 'style' => empty($comment['Comment']['edited']) ? 'display:none;' : '',
                                 'id' => 'history_item_comment_'. $comment['Comment']['id'],
                                 'class' => 'edit-btn',
                                 'data-dismiss'=>'modal'
                           ));
                           ?>
                                                <span class="btn-feed">                                
                                                    <a href="javascript:void(0)" data-id="<?php echo $comment['Comment']['id']?>" data-type="comment" data-status="1" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && !empty( $activity_likes['item_comment_likes'][$comment['Comment']['id']] ) ): ?>active<?php endif; ?>">
                                                        <i class="material-icons dp-18">thumb_up</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Like') ?></span></a>
                        <?php
                           $this->MooPopup->tag(array(
                                  'href'=>$this->Html->url(array("controller" => "likes",
                                                                 "action" => "ajax_show",
                                                                 "plugin" => false,
                                                                 'comment',
                                                                 $comment['Comment']['id'],
                                                             )),
                                  'title' => __d('business', 'People Who Like This'),
                                  'innerHtml'=> '<span id="comment_like_'.  $comment['Comment']['id'] . '">' . $comment['Comment']['like_count'] . '</span>',
                               'data-dismiss' => 'modal'
                           ));
                           ?>
                                                </span>
                        <?php if(empty($hide_dislike)): ?>
                                                <span class="btn-feed">
                                                    <a href="javascript:void(0)" data-id="<?php echo $comment['Comment']['id']?>" data-type="comment" data-status="0" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && isset( $activity_likes['item_comment_likes'][$comment['Comment']['id']] ) && $activity_likes['item_comment_likes'][$comment['Comment']['id']] == 0 ): ?>active<?php endif; ?>"><i class="material-icons dp-18">thumb_down</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Dislike') ?></span></a>
                        <?php
                           $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "likes",
                                                                   "action" => "ajax_show",
                                                                   "plugin" => false,
                                                                   'comment',
                                                                   $comment['Comment']['id'],1
                                                               )),
                                    'title' => __d('business', 'People Who Dislike This'),
                                    'innerHtml'=> '<span id="comment_dislike_' .  $comment['Comment']['id'] . '">' . $comment['Comment']['dislike_count'] . '</span>',
                           ));
                           ?>
                                                </span>
                        <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
               <?php endforeach; ?>
               <?php if ( count( $activity['ItemComment'] ) >= 2 ): ?>
                                    <li><i class="material-icons dp-18">forum</i>
                                        <a href="<?php echo $object[$name]['moo_href'];?>">
                  <?php echo __d('business', 'View all comments')?>
                                        </a>
                                    </li>
               <?php endif; ?>
               <?php endif; ?>
               <?php
                  // photo comments
                        if(!empty($activity['PhotoComment'])):?>
               <?php
                  foreach ($activity['PhotoComment'] as $key => $comment):
                      $class = '';
                      if ( count($activity['PhotoComment']) > 2 && $key > 1 )
                          $class = 'hidden';
                  
                      ?>
                                    <li id="photo_comment_<?php echo $comment['Comment']['id']?>" class="<?php echo $class?>">
                  <?php echo $this->Moo->getItemPhoto(array('User' => $comment['User']),array('class' => 'user_avatar_small', 'prefix' => '50_square','tooltip' => true), array('class' => 'user_avatar_small img_wrapper2'))?>
                  <?php
                     // delete link available for activity poster, site admin and admins array           
                     if ( $comment['Comment']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins_current ) && in_array( $uid, $admins_current ) ) ):
                         ?>
                                        <div class="dropdown edit-post-icon comment-option">
                                            <a href="javascript:void(0)" data-toggle="dropdown" class="cross-icon">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <ul class="dropdown-menu">
                        <?php if ($comment['Comment']['user_id'] == $uid || $cuser['Role']['is_admin']):?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="return editItemComment(<?php echo $comment['Comment']['id']?>, true)">
                           <?php echo __d('business', 'Edit Comment'); ?>
                                                    </a>
                                                </li>
                        <?php endif; ?>
                                                <li>
                                                    <a class="admin-or-owner-confirm-delete-photo-comment" href="javascript:void(0)" data-comment-id="<?php echo $comment['Comment']['id']?>">
                           <?php echo __d('business', 'Delete Comment'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                  <?php endif; ?>
                                        <div class="comment hasDelLink">
                     <?php echo $this->Moo->getName($comment['User'])?>
                                            <div class="feed-time date">
                        <?php echo $this->Moo->getTime( $comment['Comment']['created'], Configure::read('core.date_format'), $utz )?>
                        <?php
                           $this->MooPopup->tag(array(
                                   'href'=>$this->Html->url(array("controller" => "histories",
                                               "action" => "ajax_show",
                                               "plugin" => false,
                                               'comment',
                                               $comment['Comment']['id']
                                           )),
                                   'title' => __d('business', 'Show edit history'),
                                   'innerHtml'=> $historyModel->getText('comment',$comment['Comment']['id']),
                                   'style' => empty($comment['Comment']['edited']) ? 'display:none;' : '',
                                   'id' => 'history_item_comment_'. $comment['Comment']['id'],
                                   'class' => 'edit-btn',
                                   'data-dismiss'=>'modal'
                               ));
                           ?>
                                            </div>
                                            <span id="photo_feed_comment_text_<?php echo $comment['Comment']['id']?>">
                        <?php
                           echo $this->Business->viewMore(h($comment['Comment']['message']));
                           ?>
                        <?php if ($comment['Comment']['thumbnail']):?>
                                                <div class="comment_thumb">
                                                    <a href="<?php echo $this->Moo->getImageUrl($comment,array());?>">
                           <?php echo $this->Moo->getImage($comment,array('prefix'=>'200'));?>
                                                    </a>
                                                </div>
                        <?php endif;?>
                                            </span>
                                            <div class="cmt_action">
                                                <span class="btn-feed">
                                                    <a href="javascript:void(0)" data-id="<?php echo $comment['Comment']['id']?>" data-type="photo_comment" data-status="1" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && !empty( $comment['Comment']['like_count'] ) ): ?>active<?php endif; ?>"><i class="material-icons dp-18">thumb_up</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Like') ?></span></a>
                        <?php
                           $this->MooPopup->tag(array(
                                   'href'=>$this->Html->url(array("controller" => "likes",
                                               "action" => "ajax_show",
                                               "plugin" => false,
                                               'comment',
                                               $comment['Comment']['id'],
                                           )),
                                   'title' => __d('business', 'People Who Like This'),
                                   'innerHtml'=> '<span id="photo_comment_like_'.  $comment['Comment']['id'] . '">' . $comment['Comment']['like_count'] . '</span>',
                                   'data-dismiss' => 'modal'
                               ));
                           ?>
                                                </span>
                        <?php if(empty($hide_dislike)): ?>
                                                <span class="btn-feed">
                                                    <a href="javascript:void(0)" data-id="<?php echo $comment['Comment']['id']?>" data-type="photo_comment" data-status="0" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && !empty( $comment['Comment']['dislike_count'] ) ): ?>active<?php endif; ?>"><i class="material-icons dp-18">thumb_down</i> <span class="hidden-xs hidden-sm"><?php echo __d('business','Dislike') ?></span></a>
                        <?php
                           $this->MooPopup->tag(array(
                                   'href'=>$this->Html->url(array("controller" => "likes",
                                               "action" => "ajax_show",
                                               "plugin" => false,
                                               'comment',
                                               $comment['Comment']['id'],1
                                           )),
                                   'title' => __d('business', 'People Who Dislike This'),
                                   'innerHtml'=> '<span id="photo_comment_dislike_' .  $comment['Comment']['id'] . '">' . $comment['Comment']['dislike_count'] . '</span>',
                               ));
                           ?>
                                                </span>
                        <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
               <?php endforeach; ?>
               <?php if ( count( $activity['PhotoComment'] ) > 2 ): ?>
                                    <li id="all_comments_<?php echo $activity['Activity']['id']?>"><i class="material-icons">forum</i>
                                        <a href="javascript:void(0)" class="showAllComments" data-id="<?php echo $activity['Activity']['id']?>">
                  <?php echo __d('business', 'View all %s comments', count($activity['PhotoComment']))?>
                                        </a>
                                    </li>
               <?php endif; ?>
               <?php
                  elseif (!empty($activity['ActivityComment'])):
                        
                    ?>
               <?php
                  foreach ($activity['ActivityComment'] as $key => $comment):
                    $class = '';
                    if ( count($activity['ActivityComment']) > 2 && $key > 1 )
                      $class = 'hidden';
                  ?>
                                    <li id="comment_<?php echo $comment['id']?>" class="<?php echo $class?>">
                  <?php echo $this->Moo->getItemPhoto(array('User' => $comment['User']),array('class' => 'user_avatar_small', 'prefix' => '50_square'), array('class' => 'user_avatar_small img_wrapper2'))?>
                  <?php
                     // delete link available for activity poster, site admin and admins array
                     if ( ($comment['user_id'] == $uid) || ($activity['Activity']['user_id'] == $uid) || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins_current ) && in_array( $uid, $admins_current ) ) ):
                     ?>
                                        <div class="dropdown edit-post-icon comment-option">
                                            <a href="javascript:void(0)" data-toggle="dropdown" class="cross-icon">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <ul class="dropdown-menu">
                        <?php if ($comment['user_id'] == $uid || $cuser['Role']['is_admin']):?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="return editActivityComment(<?php echo $comment['id']?>)">
                           <?php echo __d('business', 'Edit Comment'); ?>
                                                    </a>
                                                </li>
                        <?php endif; ?>
                                                <li>
                                                    <a class="admin-or-owner-confirm-delete-activity-comment" data-activity-comment-id="<?php echo $comment['id']?>" href="javascript:void(0)">
                           <?php echo __d('business', 'Delete Comment'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                  <?php endif; ?>
                                        <div class="comment hasDelLink">
                     <?php echo $this->Moo->getName($comment['User'])?>
                                            <div class="feed-time date">
                        <?php echo $this->Moo->getTime( $comment['created'], Configure::read('core.date_format'), $utz )?>
                        <?php
                           $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "histories",
                                                                   "action" => "ajax_show",
                                                                   "plugin" => false,
                                                                   'core_activity_comment',
                                                                   $comment['id']
                                                               )),
                                    'title' => __d('business', 'Show edit history'),
                                    'innerHtml'=> $historyModel->getText('core_activity_comment',$comment['id']),
                                 'style' => empty($comment['edited']) ? 'display:none;' : '',
                                 'id' => 'history_activity_comment_'. $comment['id'],
                                 'class' => 'edit-btn',
                                 'data-dismiss'=>'modal'
                           ));
                           ?>
                                            </div>
                                            <span id="activity_feed_comment_text_<?php echo $comment['id']?>">
                        <?php
                           echo $this->Business->viewMore(h($comment['comment']),null,null,null,true,array('no_replace_ssl'=>1));
                           ?>            
                        <?php if ($comment['thumbnail']):?>
                                                <div class="comment_thumb">
                                                    <a href="<?php echo $this->Moo->getImageUrl(array('ActivityComment'=>$comment),array());?>">
                           <?php echo $this->Moo->getImage(array('ActivityComment'=>$comment),array('prefix'=>'200'));?>
                                                    </a>
                                                </div>
                        <?php endif;?>
                                            </span>
                                            <div class="cmt_action">
                                                <span class="btn-feed">                                      
                                                    <a href="javascript:void(0)" data-id="<?php echo $comment['id']?>" data-type="core_activity_comment" data-status="1" class="comment-thumb likeActivity <?php if ( !empty( $uid ) && !empty( $activity_likes['comment_likes'][$comment['id']] ) ): ?>active<?php endif; ?>"> <i class="material-icons dp-18">thumb_up</i> </a>
                        <?php
                           $this->MooPopup->tag(array(
                                  'href'=>$this->Html->url(array("controller" => "likes",
                                                                 "action" => "ajax_show",
                                                                 "plugin" => false,
                                                                 'core_activity_comment',
                                                                 $comment['id'],
                                                             )),
                                  'title' => __d('business', 'People Who Like This'),
                                  'innerHtml'=> '<span id="core_activity_comment_like_'. $comment['id'] . '">' . $comment['like_count'] . '</span>', )); ?>
                                                </span>
                        <?php if(empty($hide_dislike)): ?>
                                                <span class="btn-feed">
                                                    <a href="javascript:void(0)" data-id="<?php echo $comment['id']?>" data-type="core_activity_comment" data-status="0"  class="comment-thumb likeActivity <?php if ( !empty( $uid ) && isset( $activity_likes['comment_likes'][$comment['id']] ) && $activity_likes['comment_likes'][$comment['id']] == 0 ): ?>active<?php endif; ?>"> <i class="material-icons dp-18">thumb_down</i> </a>
                        <?php
                           $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "likes",
                                                                   "action" => "ajax_show",
                                                                   "plugin" => false,
                                                                   'core_activity_comment',
                                                                   $comment['id'],1
                                                               )),
                                    'title' => __d('business', 'People Who Dislike This'),
                                    'innerHtml'=> '<span id="core_activity_comment_dislike_'. $comment['id'] . '">' .  $comment['dislike_count'] . '</span>', )); ?>
                                                </span>
                        <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
               <?php
                  endforeach;
                                          ?>
               <?php if ( count( $activity['ActivityComment'] ) > 2 ): ?>
                                    <li id="all_comments_<?php echo $activity['Activity']['id']?>"><i class="material-icons">forum</i>
                                        <a href="javascript:void(0)" class="showAllComments" data-id="<?php echo $activity['Activity']['id']?>">
                  <?php echo __d('business', 'View all %s comments', count($activity['ActivityComment']))?>
                                        </a>
                                    </li>
               <?php
                  endif; ?>
               <?php endif;
                  ?>
                                </ul>
            <?php endif; ?>
                                </div>
    </li>
<?php endif;?>

