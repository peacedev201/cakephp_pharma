<?php if ($page == 1):?>
<div class="title-modal">
   	<?php echo __d('poll','People who voted for this option');?>
   	<button type="button" class="hide_popup"  data-dismiss="modal"><span aria-hidden="true"><i class="material-icons md-24">close</i></span></button>
</div>
<div class="modal-body">
	<ul class="poll_user_answer"id="poll_list_user_answer">
		<?php foreach ($answers as $answer):?>
			<li>
				<?php echo $this->Moo->getItemPhoto(array('User' => $answer['User']),array( 'prefix' => '50_square'), array('class' => 'img_wrapper'))?>
			</li>
		<?php endforeach;?>
	</ul>
	<div class="clear"></div>
	<?php if ($item['PollItem']['total_user'] > $page*$limit):?>
		<div class="poll_user_more"><a id="poll_user_more" data-id="<?php echo $item['PollItem']['id'] ?>" href="javascript:void(0);"><?php echo __d('poll','View more');?></a></div>
		<script type="text/javascript">
		    require(["jquery","mooPoll"], function($,mooPoll) {
		    	mooPoll.initOnListingUserAnswer();
		    });
		</script>
	<?php endif;?>
</div>
<?php else:?>
	<?php foreach ($answers as $answer):?>
		<li>
			<?php echo $this->Moo->getItemPhoto(array('User' => $answer['User']),array( 'prefix' => '50_square'), array('class' => 'img_wrapper'))?>
		</li>
	<?php endforeach;?>
	<?php if ($item['PollItem']['total_user'] > $page*$limit):?>
		<script>
			$('.poll_user_more').show();
		</script>
	<?php endif;?>
<?php endif;?>