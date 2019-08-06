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
        'edit' => __("Edit Video"),
        'delete' => __("Delete Video"),
        'report' => __("Report Video"),
        'deleteComment' => __("Delete Comment"),
        )
);

//CUSTOM FOR BOOKMARK PLUGIN
if (Configure::read('Bookmark.bookmark_enabled')) :
    $renderBookmarkItem = new CakeEvent("View.Mooapp.ViewerBookmarkItem", $this, array('object_type' => 'Video_Video', 'object_id' => $video['Video']['id']));
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
    window.videoId = <?php echo $videoId; ?> ;
    <?php if($video['Category']['name']) : ?>
    window.videoCategory = "<?php echo $video['Category']['name']; ?>" ;
    <?php endif; ?>
    <?php if ($video['User']['id'] == $uid || ( !empty($cuser) && $cuser['Role']['is_admin'] ) || ( !empty($admins) && in_array($uid, $admins) )): ?>
    window.canEdit = true ;
    window.canDelete = true ;
    <?php endif; ?>
 </script>
<?php $this->end(); ?>

<?php
$this->MooGzip->script(array('zip'=>'videos.view.bundle.js.gz','unzip'=>'MooApp.videos.view.bundle'));
?>