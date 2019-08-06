<?php $this->setCurrentStyle(1); ?>
<?php if(MooCore::getInstance()->getViewer(true) == null):?>
<style>
    .shareFeedBtn{
        display: none;
    }
</style>
<?php endif;?>
<?php if($is_app):?>
    <?php echo $this->Element('widgets/detail_info');?>
    <?php echo $this->Element('widgets/detail_section');?>
<?php endif;?>