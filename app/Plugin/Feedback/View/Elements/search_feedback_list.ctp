<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooBehavior"], function($, mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooBehavior'), 'object' => array('$', 'mooBehavior'))); ?>
mooBehavior.initMoreResults();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php if ($feedbacks != null): 
    echo $this->Html->css(array('Feedback.feedback'));
?>
<ul class="feedback-content-list">
        <?php
            $i = 1;
            foreach ($feedbacks as $feedback):
                $user = $feedback['User'];
                $aFeedbackCategory = $feedback['FeedbackCategory'];
                $aFeedbackStatus = $feedback['FeedbackStatus'];
                $feedback = $feedback['Feedback'];
        ?> 
            <li class="full_content p_m_10" <?php if ($i == count($feedbacks)) echo 'style="border-bottom:0"'; ?>>
                <div class="feedbacks_list_vote_button">
                    <div class="feedback_votes_counts">
                        <p id="feedback_voting_2" class="feedback_<?php echo $feedback['id'] ?>"><?php echo $feedback['total_votes'] ?></p>
                        <span><?php echo $feedback['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
                        <?php if ($feedback['featured']): ?>
                            <i class="feedback_featured"><?php echo __d('feedback','Featured') ?></i>
                        <?php endif ?>
                    </div>
                </div>
                <div class="comment feedback-comment">
                    <a href="<?php echo $this->request->base?>/feedback/feedbacks/view/<?php echo $feedback['id'] ?>/<?php echo seoUrl($feedback['title']) ?>">
                        <b><?php echo h($feedback['title']) ?></b>
                    </a>

                    <div class="date date-small">
                        <?php echo $feedback['views'] . __d('feedback', ' views') ?>, 
                        <?php echo $feedback['comment_count'] . __d('feedback', ' comments') ?>,
                        <?php echo $feedback['total_images'] . __d('feedback', ' pictures') ?>,
                        <?php echo __d('feedback', 'Posted by ') . (!empty($user['name']) ? $this->Moo->getName($user) : '<b>' . $feedback['fullname'] . '</b>') ?>
                        <?php echo __d('feedback', 'about ') . $this->Moo->getTime($feedback['created'], Configure::read('core.date_format'), $utz) ?>
                    </div>
                    <div class="feedback_category_box">
                        <?php if ($aFeedbackCategory['id']): ?> 
                            <?php echo __d('feedback', 'Category: ') ?>
                            <a href="<?php echo $this->request->base; ?>/feedbacks/index/cat/<?php echo $aFeedbackCategory['id'] ?>"><?php echo $aFeedbackCategory['name'] ?></a>
                        <?php endif ?>
                    </div>
                    <div class="comment_message feedback_comment_box">
                        <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $feedback['body'])), 200, array('eclipse' => '')), Configure::read('Feedback.feedback_hashtag_enabled'));?>				
                        <?php if ($aFeedbackStatus['id']): ?>
                            <div class="feedback_status_box">
                                <b><?php echo __d('feedback', 'Status: ') ?></b>
                                <a style='color:<?php echo  $aFeedbackStatus['color']?>' class="feedback_status" href="<?php echo $this->request->base;?>/feedbacks/index/sta/<?php echo $aFeedbackStatus['id'] ?>">
                                    <?php echo $aFeedbackStatus['name'] ?>
                                </a>
                                <div>
                                    <?php if (!$feedback['status_body']): ?>
                                        <?php echo $aFeedbackStatus['default_comment'] ?>
                                    <?php else: ?>
                                        <?php echo $feedback['status_body'] ?>						
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="clear"></div>
            </li>
        <?php 
            $i++;
            endforeach; 
        ?>
        <?php if (count($feedbacks) > 0 && !empty($more_url)): ?>
            <div class="view-more">
                <a href="javascript:void(0)" data-url="<?php echo $more_url?>" class="viewMoreBtn" data-div="list-content"><?php echo __d('feedback', 'Load More')?></a>
            </div>
    <?php endif; ?>
<?php else: ?> 
    <?php echo '<div class="clear" align="center">' . __d('feedback', 'No more results found') . '</div>'; ?>
<?php endif; ?>
</ul>
