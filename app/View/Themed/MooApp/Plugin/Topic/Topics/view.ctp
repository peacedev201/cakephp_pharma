<?php
$this->MooApp->loading();
$this->Html->css(array('MooApp.main','MooApp.feed/emoji'), array('block' => 'mooAppOptimizedCss','minify'=>false));
$this->addPhraseJs(array(
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
        'noComment' => __("Be the first person comment on this"),
        'edit' => __("Edit Topic"),
        'delete' => __("Delete Topic"),
        'report' => __("Report Topic"),
        'pin' => __( 'Pin Topic'),
        'unPin' => __( 'Unpin Topic'),
        'lock' => __( 'Lock Topic'),
        'unLock' => __( 'Unlock Topic'),
        'deleteComment' => __("Delete Comment"),
        )
);

//CUSTOM FOR BOOKMARK PLUGIN
if (Configure::read('Bookmark.bookmark_enabled')) :
    $renderBookmarkItem = new CakeEvent("View.Mooapp.ViewerBookmarkItem", $this, array('object_type' => 'Topic_Topic', 'object_id' => $topic['Topic']['id']));
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
    window.topicId = <?php echo $topicId; ?> ;
    window.topicCategory = "<?php echo $topic['Category']['name']; ?>" ;
    <?php if ( ($topic['Topic']['user_id'] == $uid ) || ( !empty($cuser['Role']['is_admin']) ) ): ?>
    window.canEdit  = true ;
    window.canDelete  = true ;
    <?php endif; ?>
    <?php if ( ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
        <?php if ( !$topic['Topic']['pinned'] ): ?>
            window.pin  = "#pin"; 
        <?php else: ?>
            window.pin = "#unpin"; 
        <?php endif; ?>
        <?php if ( !$topic['Topic']['locked'] ): ?>
            window.canLock  = true; 
        <?php else: ?>
            window.canUnlock = true; 
        <?php endif; ?>
    <?php endif; ?>
 </script>
<?php $this->end(); ?>

<?php
$this->MooGzip->script(array('zip'=>'topics.view.bundle.js.gz','unzip'=>'MooApp.topics.view.bundle'));
?>