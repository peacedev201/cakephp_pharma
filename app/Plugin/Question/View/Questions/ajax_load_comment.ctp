<div class="comment_list">
	<?php foreach ($comments as $comment):?>
		<?php echo $this->element('comment',array('comment'=>$comment),array('plugin'=>'Question'));?>
	<?php endforeach;?>
</div>