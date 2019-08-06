<?php
$this->MooApp->loading();

$this->addPhraseJs(array(

        'category' => __('Category'),
        'des' => __('Description'),
        'type' => __('Type'),

        'showMore' => __('Show More'),
        'showLess' => __('Show Less'),
        'edit' => __('Edit Group'),
        'delete' => __('Delete Group'),
        'leave' => __('Leave Group'),
        'reportGroup' => __('Report Group'),
        'shareGroup' => __('Share Group'),
        'inviteFriend' => __('Invite Friends'),
        'requests' => __('%s Join Requests'),
        'request' => __('%s Join Request'),
        'public' => __('Public (anyone can view and join)'),
        'private' => __('Private (only group members can view details)'),
        'restricted' => __('Restricted (anyone can join upon approval)'),
        'turnOff' => __( 'Turn Off Notification'),
        'turnOn' => __( 'Turn On Notification'),
        'feature' => __( 'Feature Group'),
        'unFeature' => __( 'Unfeature Group'),
        'groupAdmin' => __( 'Group Admin'),
        'memberList' => __( 'Group Members'),
        'join' => __( 'Join Group'),
        'Mdetail' => __( 'Details'),
        'Mmember' => __( 'Members'),
        'Mphoto' => __( 'Photos'),
        'Mvideo' => __( 'Videos'),
        'Mtopic' => __( 'Topics'),
    
        'like' => __("%s like"),
        'likes' => __("%s likes"),
        'dislike' => __("%s dislike"),
        'dislikes' => __("%s dislikes"),
        'comment' => __("%s comment"),
        'comments' => __("%s comments"),
        'members' => __("members"),
        'newFeedPlaceholder' => __("What's on your mind?"),
        'report' => __("Report Activity"),
        'deleteFeed' => __("Delete Activity"),
        'deleteComment' => __("Delete Comment"),
    
        'inGroup' => __('In group'),
        'in' => __("posted in"),
        'by' => __("by"),
        'Like' => __("Like"),
        'like' => __("%s Like"),
        'likes' => __("%s Likes"),
        'dislike' => __("%s Dislike"),
        'dislikes' => __("%s Dislikes"),
        'comment' => __("%s Comment"),
        'comments' => __("%s Comments"),
        'Dislike' => __("Dislike"),
        'noComment' => __("Be the first person comment on this")
    
        )
);
$topic_id = !empty( $this->request->named['topic_id'] ) ? $this->request->named['topic_id'] : 0;
$video_id = !empty( $this->request->named['video_id'] ) ? $this->request->named['video_id'] : 0;

if($topic_id == 0 && $video_id == 0):
    $this->Html->css(array('MooApp.feed/activites','MooApp.main','MooApp.feed/emoji','MooApp.feed/plugin'), array('block' => 'mooAppOptimizedCss','minify'=>false));
else:
    $this->Html->css(array('MooApp.main','MooApp.feed/emoji'), array('block' => 'mooAppOptimizedCss','minify'=>false));
endif;



$adminList = $memberList  = array();
if(!empty($group_admins) && count($group_admins)): 
    if(!(empty($is_member) && !empty($group) && $group['Group']['type'] == PRIVACY_PRIVATE)):
        foreach ($group_admins as $user) : 
            if($user) :
                $adminList[] = array (
                    'name' => $user['User']['name'],
                    'url' => FULL_BASE_URL . $user['User']['moo_href'],
                    'avatar' => $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '50_square'), array(), true),
                ); 
            endif;
        endforeach;
    endif;
endif;
if(!empty($members) && count($members)):
    if(!(empty($is_member) && !empty($group) && $group['Group']['type'] == PRIVACY_PRIVATE)):
        foreach ($members as $user) :
            if(isset($user['User'])) :
                $memberList[] = array (
                    'name' => $user['User']['name'],
                    'url' => FULL_BASE_URL . $user['User']['moo_href'],
                    'avatar' => $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '50_square'), array(), true),
                ); 
            endif;
        endforeach;
    endif;
endif;

$display = true;
        if ($group['Group']['type'] == PRIVACY_PRIVATE) {
            if (empty($is_member)) {
                $display = false;
                if(!empty($cuser) && $cuser['Role']['is_admin'])
                    $display = true;
            }
        }


//CUSTOM FOR BOOKMARK PLUGIN
if (Configure::read('Bookmark.bookmark_enabled')) :
    $renderBookmarkItem = new CakeEvent("View.Mooapp.ViewerBookmarkItem", $this, array('object_type' => 'Group_Group', 'object_id' => $group['Group']['id']));
    $this->getEventManager()->dispatch($renderBookmarkItem);
    $result = $renderBookmarkItem->result['result'];
    $isViewerBookmark = 0;

    if(isset($result['isViewerBookmark'])) {
        $isViewerBookmark = $result['isViewerBookmark'];
    }
endif;
//END CUSTOM FOR BOOKMARK PLUGIN

//echo '<pre>';print_r(count($members));die;
?>
 
<?php $this->start('mooAppOptimizedContent'); ?>
<script type="text/javascript">
    <?php if (isset($isViewerBookmark) && Configure::read('Bookmark.bookmark_enabled')): ?>
    window.isViewerBookmark = <?php echo $isViewerBookmark; ?>;
    <?php endif; ?>
     window.groupId = <?php echo $group['Group']['id']; ?> ;
     window.videoId = <?php echo $video_id ?> ;
     window.topicId = <?php echo $topic_id ?> ;
           <?php if(Configure::read('MooApp.show_more_button_what_new_box')==1): ?>
     window.show_more_button = true;
     <?php else: ?>
         window.show_more_button = false;
     <?php endif; ?>
     <?php if ((empty($uid) && !empty($invited_user)) ||
(!empty($uid) && (($group['Group']['type'] != PRIVACY_PRIVATE && empty($my_status['GroupUser']['status'])) || ($group['Group']['type'] == PRIVACY_PRIVATE && !empty($my_status) && $my_status['GroupUser']['status'] == 0 ) ) ) ):   ?>
     window.visibleJoin = true ;
     <?php endif ?>
         
     <?php if (!empty($request_count)): ?>
         window.visibleRequest = true ;
     <?php endif ?>
         
     <?php if($groupActivities['check_post_status']):?>
         window.canPostFeed = true ;
     <?php endif ?>
     <?php if (!empty($adminList)):  ?>
     window.adminList = <?php echo json_encode($adminList); ?> ;
     <?php endif ?>
     <?php if (!empty($memberList)):  ?>
     window.memberList = <?php echo json_encode($memberList); ?> ;
     <?php endif ?>
         
     <?php if($display): ?>   
         window.canReport = true ;
         window.canView = true ;
        <?php if ( ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_MEMBER  && $group['Group']['type'] != PRIVACY_PRIVATE) ||
                                   !empty($cuser['Role']['is_admin'] ) ||
                                   ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN)
                                   ):  ?>
        window.canInvite = true ;
        <?php endif ?>

        <?php if ( ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN &&  $group['Group']['user_id'] == $uid) || !empty($cuser['Role']['is_admin'] ) ):  ?>
        window.canEdit = true ;
        window.canDelete = true ;
        <?php endif ?>
            
        <?php if ( !empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) && ( $uid != $group['Group']['user_id'] ) ): ?>
        window.canLeave = true ;
        <?php endif ?>
            
        <?php if ($group['Group']['type'] != PRIVACY_PRIVATE && $group['Group']['type'] != PRIVACY_RESTRICTED): ?>
        window.canShare = true ;
        <?php endif ?>
            
        <?php if (isset($my_status['GroupUser']['status'])): ?>
            <?php
            $settingModel = MooCore::getInstance()->getModel("Group.GroupNotificationSetting");
            $checkStatus = $settingModel->getStatus($group['Group']['id'],$uid);
            ?>
            <?php if ($checkStatus) :?>
                window.notify = '#off' ;
            <?php else : ?>
                window.notify = '#on' ;
            <?php endif ?>
        <?php endif ?>
            
            
        <?php if ( ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
            <?php if ( !$group['Group']['featured'] ):?>
                window.feature = '#feature' ;
            <?php else : ?>
                window.feature = '#unfeature'  ;
            <?php endif ?>
        <?php endif ?>
            
            
    <?php endif ?>
         
</script>
<?php $this->end(); ?>

 <?php
$this->MooGzip->script(array('zip'=>'groups.view.bundle.js.gz','unzip'=>'MooApp.groups.view.bundle'));
?>
