<?php
$this->MooApp->loading();
$this->Html->css(array('MooApp.main','MooApp.feed/emoji'), array('block' => 'mooAppOptimizedCss','minify'=>false));
$this->addPhraseJs(array(
        'in' => __("posted in"),
        'by' => __("by"),
        'Like' => __("Like"),
        'like' => __("%s Like"),
        'likes' => __("%s Likes"),
        'dislike' => __("%s Dislike"),
        'Dislike' => __("Dislike"),
        'dislikes' => __("%s Dislikes"),
        'comment' => __("%s Comment"),
        'comments' => __("%s Comments"),
        'noComment' => __("Be the first person comment on this"),
        'edit' => __("Edit Entry"),
        'delete' => __("Delete Entry"),
        'report' => __("Report Entry"),
        'deleteComment' => __("Delete Comment"),
        )
);

//CUSTOM FOR BOOKMARK PLUGIN
if (Configure::read('Bookmark.bookmark_enabled')) :
    $renderBookmarkItem = new CakeEvent("View.Mooapp.ViewerBookmarkItem", $this, array('object_type' => 'Blog_Blog', 'object_id' => $blog['Blog']['id']));
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
    window.blogId = <?php echo $blogId; ?> ;
    window.blogCategory = "<?php echo $blog['Category']['name']; ?>" ;
    <?php if ($blog['User']['id'] == $uid || ( !empty($cuser) && $cuser['Role']['is_admin'] )): ?>
    window.canEdit  = true ;
    <?php endif; ?>
    <?php if ( ($blog['Blog']['user_id'] == $uid ) || ( !empty( $blog['Blog']['id'] ) && !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
    window.canDelete  = true ;
    <?php endif; ?>
 </script>
<?php $this->end(); ?>

<?php
$this->MooGzip->script(array('zip'=>'blogs.view.bundle.js.gz','unzip'=>'MooApp.blogs.view.bundle'));
?>