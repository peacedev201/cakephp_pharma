<?php if (count($tags)):?>
	<div class="box2">        
        <?php if (isset($title_enable) && $title_enable):?>       
        	<h3><?php echo $title ?></h3>
        <?php endif;?>
        <div class="box_content">
        	 <?php echo $this->element( 'blocks/tags_item_block',array("tags"=>$tags) ); ?>
        </div>
    </div>
<?php endif;?>