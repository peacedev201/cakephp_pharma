<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","store_manager"], function($,store_manager) {
            store_manager.initAfterFetchVideo();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'store_manager'), 
        'object' => array('$', 'store_manager')
    ));?>
        store_manager.initAfterFetchVideo();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php
$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>


<ul class="list6 list6sm2">
    <?php echo $this->Form->hidden('id', array('value' => $video['Video']['id'])); ?>
    <?php echo $this->Form->hidden('source_id', array('value' => $video['Video']['source_id'])); ?>
    <?php echo $this->Form->hidden('thumb', array('value' => $video['Video']['thumb'])); ?>
    <li>
        <div class="col-md-2">
            <label><?php echo __d('store', 'Video Title')?></label>
        </div>
        <div class="col-md-10">
            <?php echo $this->Form->text('title', array('value' => $video['Video']['title'])); ?>
        </div>
        <div class="clear"></div>
    </li>
    <li>
        <div class="col-md-2">
            <label><?php echo __d('store', 'Category')?></label>
        </div>
        <div class="col-md-10">
            <?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $video['Video']['category_id'] ) ); ?>
        </div>
        <div class="clear"></div>


    </li>
    <li>
        <div class="col-md-2">
            <label><?php echo __d('store', 'Description')?></label>
        </div>
        <div class="col-md-10">
            <?php echo $this->Form->textarea('description', array('value' => $video['Video']['description'])); ?>
        </div>
        <div class="clear"></div>
    </li>
    <li>
        <div class="col-md-2">
            <label><?php echo __d('store', 'Tags')?></label>
        </div>
        <div class="col-md-10">
            <?php echo $this->Form->text('tags', array('value' => $tags_value)); ?> <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'Separated by commas or space')?>">(?)</a>
        </div>
        <div class="clear"></div>
    </li>
    <li>
        <div class="col-md-2">
            <label><?php echo __d('store', 'Privacy')?></label>
        </div>
        <div class="col-md-10">
            <?php
                echo $this->Form->select('privacy',
                    array( 
                        PRIVACY_EVERYONE => __d('store', 'Everyone'),
                        PRIVACY_FRIENDS  => __d('store', 'Friends Only'),
                        PRIVACY_ME 	  => __d('store', 'Only Me')
                    ),
                    array( 
                        'value' => $video['Video']['privacy'],
                        'empty' => false
                    )
                );
            ?>
        </div>
        <div class="clear"></div>
    </li>
    <li>
        <div class="col-md-2">
            <label>&nbsp;</label>
        </div>
        <div class="col-md-10">
            <button type='button' class='btn btn-primary' id="saveBtn"><?php echo __d('store', 'Save Video')?></button>
            <?php if ( !empty( $video['Video']['id'] ) ): ?>
                    <a href="javascript:void(0)" data-id="<?php echo $video['Video']['id'] ?>" class="btn btn-default deleteVideo"><?php echo __d('store', 'Delete Video')?></a>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </li>
</ul>
<div class="error-message" style="display:none;margin-top:10px;"></div>