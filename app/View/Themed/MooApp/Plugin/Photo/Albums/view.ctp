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
        'dislikes' => __("%s Dislikes"),
        'comment' => __("%s Comment"),
        'comments' => __("%s Comments"),
        'Dislike' => __("Dislike"),
        'noComment' => __("Be the first person comment on this"),
        'uploadPhoto' => __("Upload Photos"),
        'loadMore' => __("View More"),
        'edit' => __("Edit Album"),
        'delete' => __("Delete Album"),
        'report' => __("Report Album"),
        'deleteComment' => __("Delete Comment"),
        )
);

//CUSTOM FOR BOOKMARK PLUGIN
if (Configure::read('Bookmark.bookmark_enabled')) :
    $renderBookmarkItem = new CakeEvent("View.Mooapp.ViewerBookmarkItem", $this, array('object_type' => 'Photo_Album', 'object_id' => $album['Album']['id']));
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
    window.albumId = <?php echo $albumId; ?> ;
    <?php if(isset($album['Category']['name'])) : ?>
        window.albumCategory = "<?php echo $album['Category']['name']; ?>" ;
    <?php endif; ?>
    <?php if ( $uid == $album['User']['id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
        window.canEdit  = true ;
        window.canDelete  = true ;
    <?php endif; ?>
 </script>
<?php $this->end(); ?>

<?php
$this->MooGzip->script(array('zip'=>'albums.view.bundle.js.gz','unzip'=>'MooApp.albums.view.bundle'));
?>