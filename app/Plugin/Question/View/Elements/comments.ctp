<?php 
	$helper = MooCore::getInstance()->getHelper('Question_Question');
	$uid = MooCore::getInstance()->getViewer(true);
	$viewer = MooCore::getInstance()->getViewer();
	$canComment = $helper->can('leave_comment',$viewer);
?>
<div class="comments-wrapper">
	<div class="comment_list">
		<?php foreach ($comments as $comment):?>
			<?php echo $this->element('comment',array('comment'=>$comment),array('plugin'=>'Question'));?>
		<?php endforeach;?>
	</div>
	<?php if ($comment_count > count($comments)):?>
		<div class="more_comment" >
			<a class="question_more_comment" data-id="<?php echo $id;?>" data-type="<?php echo $type;?>" href="javascript:void(0);"><?php echo __d('question','show %s more comments','<b>'.($comment_count - count($comments)).'</b>');?></a>
		</div>
	<?php endif;?>
	<div <?php if ($comment_count > count($comments)):?>style="display:none;"<?php endif;?> class="comments-form">
		<a href="javascript:void(0);" <?php if ($canComment == QUESTION_CAN_ERROR_NONE):?>class="question_show_comment"<?php else:?> data-container="body" tabindex="0" data-toggle="popover" role="button" data-trigger="focus" role="button" data-content="<?php if ($canComment == QUESTION_CAN_ERROR_LOGIN) echo __d("question","You must be logged in to perform this action"); else echo __d("question","You do not have enough points to comment");?>" <?php endif;?> >
			<?php echo __d('question','Add comment')?>
			<i class="material-icons">comment</i>
		</a>
		<?php if ($canComment == QUESTION_CAN_ERROR_NONE):?>
			<div class="comments-form_content" style="display:none;">
				<a class="moocore_tooltip_link" data-item_id="<?php echo $viewer['User']['id']?>" data-item_type="user" href="<?php echo $viewer['User']['moo_href']?>">						
					<img class="avatar" src="<?php echo $this->Moo->getItemPhoto(array('User' => $viewer['User']),array( 'prefix' => '50_square'), array(), true);?>">											
				</a>
				<div class="comments-right">
					<textarea></textarea>
					<div class="clear"></div>
					<div class="commentButton">															
						<a href="javascript:void(0)" data-id="<?php echo $id;?>" data-type="<?php echo $type;?>" class="btn btn-action question_comment_action"><?php echo __('Comment');?></a>
					</div>
				</div>
			</div>
		<?php endif;?>
	</div>
</div>