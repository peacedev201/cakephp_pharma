<?php if ($page == 1):?>
<div class="title-modal">
    <?php echo __d('question','Edit History') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
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
		<div id="question_content_history">
			<?php $this->Html->viewMore($more_url); ?>
		</div>
		<script>
		require(["jquery"], function($) {
			$('#question_content_history .viewMoreBtn').click(function(){
				$(this).spin('small');
				$.post(mooConfig.url.base + $(this).data('url'),function(data){
					$('#list-content.edit-history').append(data);
					$('#question_content_history').hide();
				});
			});
		});
		</script>
		<?php 
	}
	?>
</div>
<?php endif;?>