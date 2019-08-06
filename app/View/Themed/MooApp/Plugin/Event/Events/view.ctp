<?php
$this->MooApp->loading();
$this->Html->css(array('MooApp.feed/activites','MooApp.main','MooApp.feed/emoji','MooApp.feed/plugin'), array('block' => 'mooAppOptimizedCss','minify'=>false));
$this->addPhraseJs(array(
        'privacy' => __('Privacy'),
        'public' => __('Public'),
        'private' => __('Private'),
        'category' => __('Category'),
        'time' => __('Time'),
        'viewMap' => __('View Map'),
        'location' => __('Location'),
        'address' => __('Address'),
        'userName' => __('Created by'),
        'des' => __('Description'),
        'attend' => __('Attend event'),
        'attendTitle' => __('Attending'),
        'maybeTitle' => __('Maybe Attending'),
        'notAttendTitle' => __('Not Attending'),
        'awaitingTitle' => __('Awaiting Response'),
        'yourRSVP' => __('Your RSVP'),
        'yes' => __('Yes'),
        'no' => __('No'),
        'maybe' => __('Maybe'),
        'showMore' => __('Show More'),
        'showLess' => __('Show Less'),
        'reportEvent' => __('Report Event'),
        'shareEvent' => __('Share Event'),
        'inviteFriend' => __('Invite Friends'),
        'edit' => __('Edit Event'),
        'delete' => __('Delete Event'),
    
        'like' => __("%s like"),
        'likes' => __("%s likes"),
        'dislike' => __("%s dislike"),
        'dislikes' => __("%s dislikes"),
        'comment' => __("%s comment"),
        'comments' => __("%s comments"),
        'time' => __("Time"),
        'location' => __("Location"),
        'address' => __("Address"),
        'newFeedPlaceholder' => __("What's on your mind?"),
        'report' => __("Report Activity"),
        'deleteFeed' => __("Delete Activity"),
        'deleteComment' => __("Delete Comment"),
        )
);
$awaitingUser = $attendingUser = $notAttendingUser = $maybeUser = array();
if (!empty($maybe)): 
    foreach ($maybe as $user) :
        $maybeUser[] = array (
            'name' => $user['User']['name'],
            'url' => FULL_BASE_URL . $user['User']['moo_href'],
            'avatar' => $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '50_square'), array(), true),
        ); 
    endforeach;
endif;
if (!empty($attending)): 
    foreach ($attending as $user) :
        $attendingUser[] = array (
            'name' => $user['User']['name'],
            'url' => FULL_BASE_URL . $user['User']['moo_href'],
            'avatar' => $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '50_square'), array(), true),
        ); 
    endforeach;
endif;
if (!empty($not_attending)): 
    foreach ($not_attending as $user) :
        $notAttendingUser[] = array (
            'name' => $user['User']['name'],
            'url' => FULL_BASE_URL . $user['User']['moo_href'],
            'avatar' => $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '50_square'), array(), true),
        ); 
    endforeach;
endif;
if (!empty($awaiting)): 
    foreach ($awaiting as $user) :
        $awaitingUser[] = array (
            'name' => $user['User']['name'],
            'url' => FULL_BASE_URL . $user['User']['moo_href'],
            'avatar' => $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '50_square'), array(), true),
        ); 
    endforeach;
endif;

if(!empty($my_rsvp)) :
    $myRSVP = $my_rsvp['EventRsvp']['rsvp'];
else :
    $myRSVP = 0;
endif;

//CUSTOM FOR BOOKMARK PLUGIN
if (Configure::read('Bookmark.bookmark_enabled')) :
    $renderBookmarkItem = new CakeEvent("View.Mooapp.ViewerBookmarkItem", $this, array('object_type' => 'Event_Event', 'object_id' => $event['Event']['id']));
    $this->getEventManager()->dispatch($renderBookmarkItem);
    $result = $renderBookmarkItem->result['result'];
    $isViewerBookmark = 0;

    if(isset($result['isViewerBookmark'])) {
        $isViewerBookmark = $result['isViewerBookmark'];
    }
endif;
//END CUSTOM FOR BOOKMARK PLUGIN
?>
 
<?php $this->start('mooAppOptimizedContent'); ?>
<script type="text/javascript">
    <?php if (isset($isViewerBookmark) && Configure::read('Bookmark.bookmark_enabled')): ?>
    window.isViewerBookmark = <?php echo $isViewerBookmark; ?>;
    <?php endif; ?>
     window.eventId = <?php echo $event['Event']['id']; ?> ;
     window.myRSVP =  <?php echo $myRSVP ; ?> ;
     <?php if ( $event['Event']['user_id'] == $uid || ( $uid && !empty($cuser) && $cuser['Role']['is_admin'] ) ):   ?>
     window.canEdit = true ;
     window.canDelete = true ;
     <?php endif ?>
     <?php if ( ( !empty($uid) && $event['Event']['type'] == PRIVACY_PUBLIC ) || ( $uid == $event['User']['id'] ) ):   ?>
     window.canInvite = true ;
     <?php endif ?>
     <?php if ($event['Event']['type'] != PRIVACY_PRIVATE):   ?>
     window.canShare = true ;
     <?php endif ?>
     <?php if (!empty($attendingUser)):  ?>
     window.attendUser = <?php echo json_encode($attendingUser); ?> ;
     <?php endif ?>
     <?php if (!empty($notAttendingUser)):  ?>
     window.notAttendUser = <?php echo json_encode($notAttendingUser); ?> ;
     <?php endif ?>
     <?php if (!empty($maybeUser)):  ?>
     window.maybeUser = <?php echo json_encode($maybeUser); ?> ;
     <?php endif ?>
     <?php if (!empty($awaitingUser)):  ?>
     window.awaitingUser = <?php echo json_encode($awaitingUser); ?> ;
     <?php endif ?>
     <?php if(Configure::read('MooApp.show_more_button_what_new_box')==1): ?>
     window.show_more_button = true;
     <?php else: ?>
         window.show_more_button = false;
     <?php endif; ?>
</script>
<?php $this->end(); ?>

 <?php
$this->MooGzip->script(array('zip'=>'events.view.bundle.js.gz','unzip'=>'MooApp.events.view.bundle'));
?>
