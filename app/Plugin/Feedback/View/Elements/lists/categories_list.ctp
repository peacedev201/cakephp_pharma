<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if ( !empty( $user_feedback ) ): ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
jQuery(document).ready(function(){
	registerImageOverlay();
});
<?php $this->Html->scriptEnd(); ?>

<?php endif; ?>

<?php if (count($aFeedbacks) > 0): ?>
	<?php foreach ($aFeedbacks as $key => $Feedback): ?>
		<li>
			<div class="feedbacks_list_vote_button">
	    		<div class="feedback_votes_counts">
	    			<p id="feedback_voting_2" class="feedback_<?php echo  $Feedback['Feedback']['id']?>"><?php echo  $Feedback['Feedback']['total_votes']?></p>
                    <span><?php echo $Feedback['Feedback']['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
                                <?php if($aFeedback['Feedback']['featured']): ?>
                                    <i class="feedback_featured"><?php echo __d('feedback','Featured') ?></i>
                                <?php endif ?>
	    		</div>
	    		<div id="feedback_vote_2" class="feedback_vote_button">
	    		<?php $bRemoveVote = false ?>
	    		<?php foreach($Feedback['FeedbackVote'] as $aFeedbackVote): ?>
		    		<?php if($aFeedbackVote['user_id'] == $uid): ?>
						<?php $bRemoveVote = true;break; ?>
		    		<?php endif ?>
		    	<?php endforeach ?>
		    	<?php if($bRemoveVote): ?>
		        	<a class="a_feedback_<?php echo  $Feedback['Feedback']['id']?>" href="javascript:void(0);" onclick="vote(<?php echo  $Feedback['Feedback']['id'] ?>, <?php echo  $uid ?>);"><?php echo __d('feedback', 'Unvote')?></a>
		        <?php else: ?>
		        	<a class="a_feedback_<?php echo  $Feedback['Feedback']['id']?>" href="javascript:void(0);" onclick="vote(<?php echo  $Feedback['Feedback']['id'] ?>, <?php echo  $uid ?>);"><?php echo __d('feedback', 'Vote')?></a>
		        <?php endif ?>
		      	</div>
	    	</div>
			<div class="comment">
				<a href="<?php echo $this->request->base.$url_feedback?>/view/<?php echo $Feedback['Feedback']['id']?>/<?php echo seoUrl($Feedback['Feedback']['title'])?>">
					<b><?php echo h($Feedback['Feedback']['title'])?></b>
				</a>
				<div class="date">
					<?php echo  $Feedback['Feedback']['views'].__d('feedback', ' views') ?>, 
					<?php echo  $Feedback['Feedback']['comment_count'].__d('feedback', ' comments') ?>,
					<?php echo  $Feedback['Feedback']['total_images'].__d('feedback', ' pictures') ?>,
					<?php echo  __d('feedback', 'Posted by ').$this->Moo->getName($Feedback['User']) ?>
					<?php echo  __d('feedback', 'about ').$this->Moo->getTime( $Feedback['Feedback']['created'], Configure::read('core.date_format'), $utz )?>
				</div>
				<div>
				<?php if($Feedback['FeedbackCategory']['id']): ?> 
					<?php echo __d('feedback', 'Category: ')?>
					<a><?php echo  $Feedback['FeedbackCategory']['name']?></a>
				<?php endif ?>
				</div>
				<div class="comment_message">
					<?php echo h($Feedback['Feedback']['body'])?>
					
					<?php if($Feedback['FeedbackStatus']['id']): ?>
					<div class="status comment_message">
						<b><?php echo __d('feedback', 'Status: ')?></b>
						<a style='background-color:<?php echo  $Feedback['FeedbackStatus']['color']?>' class="feedback_status">
							<?php echo  $Feedback['FeedbackStatus']['name']?>
						</a>
						<div>
							<?php if(!$Feedback['Feedback']['status_body']): ?>
								<?php echo  $Feedback['FeedbackStatus']['default_comment']?>
							<?php else: ?>
								<?php echo  $Feedback['Feedback']['status_body']?>
							<?php endif ?>
						</div>
					</div>
					<?php endif ?>
				</div>
			</div>
		</li>
	<?php endforeach ?>
<?php else: ?>
	<?php echo  '<div align="center">' . __d('feedback', 'No more results found') . '</div>' ?>
<?php endif ?>

<?php if (count($aFeedbacks) >= RESULTS_LIMIT): ?>
    <div class="view-more">
        <a href="javascript:void(0)" data-url="<?php echo $more_url?>" class="viewMoreBtn" data-div="list-content"><?php echo __d('feedback', 'Load More')?></a>
    </div>
<?php endif; ?>