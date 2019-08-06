<?php if (count($polls)):?>
	<div class="box2">        
        <?php if (isset($title_enable) && $title_enable):?>       
        	<h3><?php echo $title ?></h3>
        <?php endif;?>
        <div class="box_content">
        	<?php echo $this->element( 'lists/block_polls', array( 'polls' => $polls ) ,array('plugin'=>'Poll')); ?>
        </div>
    </div>
<?php endif;?>