<?php $video = $video['Video'];?>
<div class="title-modal product_video_detail">
    <?php echo $video['title']?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="video-detail">
        <?php echo $this->Element('Store.video_snippet', array('video' => $video)); ?>
    </div>
    <div style="margin-top: 7px;">	
        <h1 class="video-detail-title"><?php echo h($video['title'])?></h1>
        <div class="video-description truncate" data-more-text="<?php echo __d('store', 'Show More')?>" data-less-text="<?php echo __d('store', 'Show Less')?>">
            <?php echo $this->Moo->formatText( $video['description'], false, true, array('no_replace_ssl' => 1) )?>
        </div>
    </div>
</div>

<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "store_store"], function($, store_store) {
            store_store.initShowMoreContent();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'ServerJS'), 
        'object' => array('$', 'ServerJS')
    ));?>
        ServerJS.init();
    <?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>
