<?php
if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if ( !empty( $user_feedback ) ): ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
jQuery(document).ready(function(){
registerImageOverlay();
});
<?php $this->Html->scriptEnd(); ?>

<?php endif; ?>

<?php if (count($aFeedbacks) > 0): ?>
	<?php foreach ($aFeedbacks as $key => $aFeedback): 
            $aFeedback['Feedback']['title'] = htmlspecialchars($aFeedback['Feedback']['title']);
            ?>
<li>
    <div class="feedbacks_list_vote_button">
        <div class="feedback_votes_counts">
            <?php if($aFeedback['Feedback']['approved']):?>
                <p id="feedback_voting_2" class="feedback_<?php echo  $aFeedback['Feedback']['id']?>"><?php echo  $aFeedback['Feedback']['total_votes']?></p>
                <span><?php echo $aFeedback['Feedback']['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
                    <?php if($aFeedback['Feedback']['featured']): ?>
                        <i class="feedback_featured"><?php echo __d('feedback','Featured') ?></i>
                    <?php endif ?>
            <?php else:?>
                <p id="feedback_voting_2" class="feedback_<?php echo  $aFeedback['Feedback']['id']?> feedback_pending">
                    <?php echo __d('feedback', 'Pending publication');?>
                </p>
            <?php endif;?>
        </div>
        <?php if($aFeedback['Feedback']['approved']):?>
        <div id="feedback_vote_2" class="feedback_vote_button">
	    		<?php $bRemoveVote = false ?>
                <?php if(!empty($aFeedback['FeedbackVote'])):?>
                    <?php foreach($aFeedback['FeedbackVote'] as $aFeedbackVote): ?>
                        <?php if($aFeedbackVote['user_id'] == $uid): ?>
                            <?php $bRemoveVote = true;break; ?>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif;?>
		    	<?php if($bRemoveVote): ?>
            <a class="a_feedback_<?php echo  $aFeedback['Feedback']['id']?> fb_vote" ref="<?php echo  $aFeedback['Feedback']['id'] ?>" href="javascript:void(0);"><?php echo __d('feedback', 'Unvote')?></a>
		        <?php else: ?>
            <a class="a_feedback_<?php echo  $aFeedback['Feedback']['id']?> fb_vote" ref="<?php echo  $aFeedback['Feedback']['id'] ?>" href="javascript:void(0);"><?php echo __d('feedback', 'Vote')?></a>
		        <?php endif ?>
        </div>
        <?php endif ?>
    </div>
    <div class="comment feedback-comment">
        <a href="<?php echo $this->request->base?>/feedbacks/view/<?php echo $aFeedback['Feedback']['id']?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>">
            <b><?php echo $aFeedback['Feedback']['title']?></b>
        </a>

        <div class="extra_info">
                    <?php echo  __d('feedback', 'Posted by ').(!empty($aFeedback['User']['name']) ? $this->Moo->getName($aFeedback['User']) : '<b>'.$aFeedback['Feedback']['fullname'].'</b>') ?>
                    <?php echo  __d('feedback', 'about ').$this->Moo->getTime( $aFeedback['Feedback']['created'], Configure::read('core.date_format'), $utz )?>
                    <?php
                        switch($aFeedback['Feedback']['privacy']){
                            case 1:
                                $icon_class = 'fa fa-globe';
                                $tooltip = 'Shared with: Everyone';
                                break;
                            case 2:
                                $icon_class = 'fa fa-group';
                                $tooltip = 'Shared with: Friends Only';
                                break;
                            case 3:
                                $icon_class = 'fa fa-user';
                                $tooltip = 'Shared with: Only Me';
                                break;
                        }
                    ?>
                    <a style="color:#888;" class="tip" href="javascript:void(0);" original-title="<?php echo  __d('feedback', $tooltip) ?>"> 
                        <i class="<?php echo  $icon_class ?>"></i>
                    </a>
        </div>
        <div class="">
				<?php if($aFeedback['FeedbackCategory']['id']): ?> 
					<?php echo __d('feedback', 'Category: ')?>
            <a href="<?php echo $this->request->base;?>/feedbacks/index/cat/<?php echo  $aFeedback['FeedbackCategory']['id']?>"><?php echo  $aFeedback['FeedbackCategory']['name']?></a>
				<?php endif ?>
        </div>
        <div class="">
					<?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $aFeedback['Feedback']['body'])), 200, array('eclipse' => '')), Configure::read('Feedback.feedback_hashtag_enabled'));?>				
					<?php if($aFeedback['FeedbackStatus']['id']): ?>
            <div class="">
                <b><?php echo __d('feedback', 'Status: ')?></b>
                <a style='color:<?php echo  $aFeedback['FeedbackStatus']['color']?>' class="feedback_status" href="<?php echo $this->request->base;?>/feedbacks/index/sta/<?php echo  $aFeedback['FeedbackStatus']['id']?>">
							<?php echo  $aFeedback['FeedbackStatus']['name']?>
                </a>
                <div>
							<?php if(!$aFeedback['Feedback']['status_body']): ?>
                                <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), htmlspecialchars($aFeedback['FeedbackStatus']['default_comment']))), 200, array('eclipse' => '')), Configure::read('Feedback.feedback_hashtag_enabled'));?>	
							<?php else: ?>
                                <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), htmlspecialchars($aFeedback['Feedback']['status_body']))), 200, array('eclipse' => '')), Configure::read('Feedback.feedback_hashtag_enabled'));?>	
							<?php endif ?>
                </div>
            </div>
					<?php endif ?>
        </div>    
        <div class="like-section" style="margin-top: 5px">
            <div class="like-action">
                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $aFeedback['Feedback']['id'] ?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>">
                    <i class="material-icons">comment</i>
                </a>
                
                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $aFeedback['Feedback']['id'] ?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>">
                    <span id="comment_count"><?php echo $aFeedback['Feedback']['comment_count']?></span>
                </a>
                
                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $aFeedback['Feedback']['id'] ?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>" class="<?php if (!empty($uid) && !empty($like['Like']['thumb_up'])): ?>active<?php endif; ?>">
                    <i class="material-icons">thumb_up</i>
                </a>
                <?php
                $this->MooPopup->tag(array(
                    'href'=>$this->Html->url(array(
                        "controller" => "likes",
                        "action" => "ajax_show",
                        "plugin" => false,
                        'Feedback_Feedback',
                        $aFeedback['Feedback']['id'],
                    )),
                    'title' => __('People Who Like This'),
                    'innerHtml'=>'<span id="like_count">'.$aFeedback['Feedback']['like_count'].'</span>',
                ));
                ?>
                <?php if(empty($hide_dislike)): ?>
                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $aFeedback['Feedback']['id'] ?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>" class="<?php if (!empty($uid) && isset($like['Like']['thumb_up']) && $like['Like']['thumb_up'] == 0): ?>active<?php endif; ?>">
                    <i class="material-icons">thumb_down</i>
                </a>
                <?php
                $this->MooPopup->tag(array(
                    'href'=>$this->Html->url(array(
                        "controller" => "likes",
                        "action" => "ajax_show",
                        "plugin" => false,
                        'Feedback_Feedback',
                        $aFeedback['Feedback']['id'],
                        '1',
                    )),
                    'title' => __('People Who DisLike This'),
                    'innerHtml'=>'<span id="dislike_count">'.$aFeedback['Feedback']['dislike_count'].'</span>',
                ));
                ?>
                <?php endif; ?>
                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $aFeedback['Feedback']['id'] ?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>">
                    <i class="material-icons">visibility</i><?php echo $aFeedback['Feedback']['views']?></span>
                </a>
				<a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $aFeedback['Feedback']['id'] ?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>">
                    <i class="material-icons">share</i> <span><?php echo  $aFeedback['Feedback']['share_count'] ?></span>
                </a>
            </div>
        </div>
    </div>    
</li>
	<?php endforeach ?>
<?php else: ?>
	<?php echo  '<div align="center" style="clear: both;margin-top: 30px;">' . __d('feedback', 'No more results found') . '</div>' ?>
<?php endif ?>

<?php if (count($aFeedbacks) >= Configure::read('Feedback.feedback_item_per_pages')): ?>
<div class="view-more">
    <a href="javascript:void(0)" data-url="<?php echo $more_url?>" class="viewMoreBtn" data-div="list-content"><?php echo __d('feedback', 'Load More')?></a>
</div>
<?php endif; ?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooFeedback"], function($,mooFeedback) {
        mooFeedback.initOnLoadMore();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooFeedback'), 'object' => array('$', 'mooFeedback'))); ?>
mooFeedback.initOnLoadMore();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>