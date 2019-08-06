<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooBusiness"], function($,mooBusiness) {
        <?php if($is_app):?>mooBusiness.initSaveBusinessCover();<?php endif;?>
        mooBusiness.initBusinessCoverUploader();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooBusiness'), 'object' => array('$', 'mooBusiness'))); ?>
    mooBusiness.initBusinessCoverUploader();
    <?php if($is_app):?>mooBusiness.initSaveBusinessCover();<?php endif;?>
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>

<?php $this->setCurrentStyle(4); ?>
<div class="title-modal">
    <?php echo __d('business','Business Cover Picture')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body cus-bus-modal">
    <div id="cover_wrapper">
        <input type="hidden" value="<?php echo $business_id; ?>" id="business_id">
        <input type="hidden" value="<?php echo $business['Business']['cover']; ?>" id="image_name">
        <img src="<?php echo $this->storage->getUrl($business['Business']["id"],'',$business['Business']['cover'],"business_covers"); ?>"  id="cover-img">
    </div>

    <div class="Metronic-alerts alert alert-warning fade in"><?php echo __d('business', "Optimal size 870x312px"); ?></div>

    <div id="select-1" class="ava-upload" style="margin-top:10px;"></div>
</div>
<div class="modal-footer">
    <a id="saveChangeCoverButton" class="button save-cover" href="javascript:void(0)" data-id="<?php echo $business_id; ?>">
        <?php echo __d('business', 'Save Cover Picture');?>
    </a>
    <a id="cancelChangeCoverButton" class="button" href="javascript:void(0)" data-dismiss="modal" <?php if($is_app):?>onclick="window.mobileAction.backOnly();"<?php endif;?>>
        <?php echo __d('business', 'Cancel');?>
    </a>
</div>

<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>
