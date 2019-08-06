<?php 
    $is_app = $this->request->is('androidApp') || $this->request->is('iosApp');
?>
<?php if(!$is_app):?>
<div class="title-modal sticker_title_modal">
    <?php echo __d('sticker', 'Sticker');?>    
    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
</div>
<?php endif;?>
<div class="modal-body">
    <?php echo $this->Form->hidden('sticker_item_type', array(
        'id' => 'sticker_item_type',
        'value' => $item_type
    ));?>
    <?php echo $this->Form->hidden('sticker_item_id', array(
        'id' => 'sticker_item_id',
        'value' => $item_id
    ));?>
    <?php echo $this->Form->hidden('sticker_photo_theater', array(
        'id' => 'sticker_photo_theater',
        'value' => $photo_theater
    ));?>
    <section class="sticker_list slider">
        <div class="sticker_item sticker_item_search current" title="<?php echo __d('sticker', 'Search');?>">
            <i class="material-icons">search</i>
        </div>
        <div class="sticker_item sticker_item_recent" title="<?php echo __d('sticker', 'Recent');?>">
            <i class="material-icons">access_time</i>
        </div>
        <?php if($stickers != null):?>
            <?php foreach($stickers as $k => $sticker):
                $sticker = $sticker['Sticker'];
            ?>
            <div class="sticker_item sticker_item_sticker" data-id="<?php echo $sticker['id'];?>" title="<?php echo $sticker['name'];?>">
                <img src="<?php echo $this->Sticker->getStickerIcon($sticker);?>" />
            </div>
            <?php endforeach;?>
        <?php endif;?>
    </section >
    <div id="sticker_images" class="scrollbar-inner">
        <?php echo $this->render('Sticker.Elements/sticker_modal_search');;?>
    </div>
    <?php if($is_app):?>
        <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" data-dismiss="modal">
            <?php echo __d('sticker', 'Close');?>
        </button>
    <?php endif;?>
</div>