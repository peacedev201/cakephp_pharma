<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<?php $this->end(); ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<?php $this->end(); ?>
<div class="bar-content">
    <div class="content_center">
    	<?php echo $this->Element('mobile_dashboard_menu');?>
        <?php echo $this->Element($element);?>
    </div>
</div>