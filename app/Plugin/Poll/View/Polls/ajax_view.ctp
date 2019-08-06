<div class="title-modal" style="padding-right: 40px;">
   	<?php echo $poll['Poll']['moo_title'];?>
   	<button style="display: block;" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
	<div class="poll_content poll_<?php echo $poll['Poll']['id']?>">
		<?php echo $this->element('Poll.poll_detail',array('poll'=>$poll,'items'=>$items, 'max_answer'=>$max_answer));?>
	</div>
</div>