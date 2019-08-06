<?php if (count($documents)):?>
	<div class="box2"> 
		<?php if (isset($title_enable) && $title_enable):?>       
        	<h3><?php echo $title ?></h3>
        <?php endif;?>
        <div class="box_content">
        	<?php echo $this->element( 'lists/block_documents', array( 'documents' => $documents ),array('plugin'=>'Document') ); ?>
        </div>
    </div>
<?php endif;?>