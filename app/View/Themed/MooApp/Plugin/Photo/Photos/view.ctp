<?php
$this->MooApp->loading();
$this->Html->css(array('MooApp.main','MooApp.feed/emoji'), array('block' => 'mooAppOptimizedCss','minify'=>false));
$this->addPhraseJs(array(
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
        'tagLabel' => __("In this photo :"),
        'canNotView' => __("You can't view or interact with this image because of view privacy."),
        'deleteComment' => __("Delete Comment"),
        )
);
$canView = 1;
if(!$is_show_full_photo) $canView = 0;

//$canShare = false;
//if (!empty($photo['Album']['moo_privacy']) && $photo['Album']['moo_privacy'] != PRIVACY_ME):
//    $canShare = true;
//elseif (!empty($photo['Group']['moo_privacy']) && $photo['Group']['moo_privacy'] != PRIVACY_RESTRICTED && $photo['Group']['moo_privacy'] != PRIVACY_PRIVATE):
//    $canShare = true;
//endif;
//CUSTOM FOR BOOKMARK PLUGIN
if (Configure::read('Bookmark.bookmark_enabled')) :
    $renderBookmarkItem = new CakeEvent("View.Mooapp.ViewerBookmarkItem", $this, array('object_type' => 'Photo_Photo', 'object_id' => $photoId));
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
    window.photoId = <?php echo $photoId; ?> ;
    window.canView = <?php echo $canView; ?> ;
 </script>
<?php $this->end(); ?>

<?php
$this->MooGzip->script(array('zip'=>'photos.view.bundle.js.gz','unzip'=>'MooApp.photos.view.bundle'));
?>