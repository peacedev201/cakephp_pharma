<?php if ($page == 1):?>
<div class="title-modal">
    <?php echo __d('question','Edit History') ?>
    <button type="button" class="hide_popup" data-dismiss="modal"><span aria-hidden="true"><i class="material-icons md-24">close</i></span></button>
</div>
<div class="modal-body">
<ul id="list-content" class="edit-history question_des">
<?php endif;?>
<?php
	foreach ($histories as $history){
		?>
		<li>
			<?php echo $this->Moo->getItemPhoto(array('User' => $history['User']), array( 'prefix' => '50_square'))?>
			<div>
				<div><?php echo $this->Moo->getName($history['User'])?></div>
				<?php echo $this->Moo->getTime( $history['QuestionContentHistory']['created'], Configure::read('core.date_format'), $utz )?>
				<p><?php echo $history['QuestionContentHistory']['content'];?></p>
			</div>
		</li>
		<?php 	
	} 	
?>
<?php if ($page == 1):?>	
</ul>
<?php if ($historiesCount > $page * Configure::read("Question.question_item_per_pages"))
	{
		?>
		<div>
			<?php $this->Html->viewMore($more_url); ?>
		</div>
		<?php 
	}
	?>
</div>
<?php endif;?>