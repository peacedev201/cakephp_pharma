<?php

$this->MooApp->loading();

$this->Html->css(array('MooApp.feed/activites','MooApp.main','MooApp.feed/emoji','MooApp.feed/plugin'), array('block' => 'mooAppOptimizedCss','minify'=>false));


$this->addPhraseJs(array(
        'Like' => __("Like"),
        'like' => __("%s Like"),
        'likes' => __("%s Likes"),
        'dislike' => __("%s Dislike"),
        'dislikes' => __("%s Dislikes"),
        'comment' => __("%s Comment"),
        'comments' => __("%s Comments"),
        'Dislike' => __("Dislike"),
        'time' => __("Time"),
        'location' => __("Location"),
        'address' => __("Address"),
        'noComment' => __("Be the first person comment on this"),
        'report' => __("Report Activity"),
        'notFoundActivity' => __('This activity feed not exist.'),
        'feedDeleted' => __('This activity has been deleted.'),
        'deleteFeed' => __("Delete Activity"),
        'deleteComment' => __("Delete Comment"),
        )
);

?>
 
<?php $this->start('mooAppOptimizedContent'); ?>
 <script type="text/javascript">
     window.activityId = <?php echo $activityId; ?> ;
    <?php if(isset($targetPhotoId)) : ?>
        window.targetPhotoId = <?php echo $targetPhotoId; ?> ;
    <?php endif; ?>
    <?php if(isset($youtubeId)) : ?>
        window.youtubeId = "<?php echo $youtubeId; ?>" ;
    <?php endif; ?>
 </script>
<?php $this->end(); ?>

<?php
$this->MooGzip->script(array('zip'=>'activities.view.bundle.js.gz','unzip'=>'MooApp.activities.view.bundle'));
?>

