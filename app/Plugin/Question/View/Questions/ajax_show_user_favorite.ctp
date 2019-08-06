
<div class="title-modal">
   	<?php echo __d('question','People who favorited this question');?>
   	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
	<ul class="question_user_favorite" >
		<?php foreach ($favorites as $favorite):?>
			<li>
				<?php echo $this->Moo->getItemPhoto(array('User' => $favorite['User']),array( 'prefix' => '50_square'), array('class' => 'img_wrapper'))?>
			</li>
		<?php endforeach;?>
	</ul>	
	<div class="clear"></div>
</div>