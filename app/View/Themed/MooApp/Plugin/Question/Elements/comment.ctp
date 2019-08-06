<?php 
	$helper = MooCore::getInstance()->getHelper('Question_Question');
	$historyModel = MooCore::getInstance()->getModel("Question.QuestionContentHistory");
?>
<div class="row comment-item" id="comment_<?php echo $comment['QuestionComment']['id']?>">
    <div class="col-md-2 comment-avatar">
        <img src="<?php echo $this->Moo->getItemPhoto(array('User' => $comment['User']),array( 'prefix' => '50_square'), array(), true);?>" class="avatar" alt="">
		<p class="cmt-author">
            <a href="<?php echo $comment['User']['moo_href']?>" title="<?php echo $comment['User']['moo_title']?>">
                <?php echo $comment['User']['moo_title']?>                
            </a>
            <?php echo $helper->getHtmlBadge($comment['User']['id']); ?>
        </p>
    </div>
    <div class="col-md-10 comment-content">
        <div class="cm-content-wrap">
            <div class="cm-wrap">   
            	<?php echo $this->viewMore(h($comment['QuestionComment']['content']),null, null, null, true, array('no_replace_ssl' => 1));?>         	
			</div>
			<div id="comment_edit_<?php echo $comment['QuestionComment']['id']?>" class="comments-form_content" style="display:none;">
				<textarea></textarea>
				<div class="clear"></div>
				<div class="commentButtonApp">
					<a href="javascript:void(0)" data-id="<?php echo $comment['QuestionComment']['id']?>" class="question_cancel_edit_comment mdl-button--raised mdl-button--colored1"><?php echo __('Cancel');?></a>															
					<a href="javascript:void(0)" data-id="<?php echo $comment['QuestionComment']['id']?>" class="question_save_edit_comment mdl-button--raised mdl-button--colored1"><?php echo __('Edit');?></a>
				</div>
			</div>
            <span class="comment-time">
            	<?php echo $this->Moo->getTime( $comment['QuestionComment']['created'], Configure::read('core.date_format'), $utz )?>
            	<a id="comment_history_<?php echo $comment['QuestionComment']['id']?>" href="<?php echo $this->base?>/questions/content_history/Comment/<?php echo $comment['QuestionComment']['id']?>" data-target="#questionModal" data-toggle="modal" class="edit-btn" title="<?php echo __d('question','Show edit history');?>" data-dismiss="modal" data-backdrop="true" style="<?php if (!$comment['QuestionComment']['edited']) echo 'display:none;'?>"><?php echo $historyModel->getText('Comment',$comment['QuestionComment']['id']);?></a>
            </span>
            <?php if ($helper->canEditComment($comment,MooCore::getInstance()->getViewer())):?>
				<span class="comment-edit">
	                <a data-id="<?php echo $comment['QuestionComment']['id']?>" class="question_edit_comment edit-comment" href="javascript:void(0)">
	                   <i class="material-icons">mode_edit</i>
	                </a>
	                <a data-id="<?php echo $comment['QuestionComment']['id']?>" class="question_delete_comment delete-comment" href="javascript:void(0)">
						<i class="material-icons">delete</i>
	                </a>
	            </span>                            
            <?php endif;?>
        </div><!-- END COMMENT CONTENT -->                 
    </div>
</div>