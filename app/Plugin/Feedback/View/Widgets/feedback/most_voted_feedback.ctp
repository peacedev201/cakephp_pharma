<?php if (Configure::read('Feedback.feedback_enabled')): ?>
    <?php if ($mostVotedFeedbacks != null): ?> 
        <div class="box2 search-friend box2_feedback">
            <h3><?php echo __d('feedback', 'Most Voted Feedback'); ?></h3>
            <div class="box_content">
                <ul class="feedback_list_widget">
                    <?php
                        foreach ($mostVotedFeedbacks as $feedback):
                            $feedback_category = $feedback['FeedbackCategory'];
                            $feedback = $feedback['Feedback'];
                    ?>
                        <li>
                            <div class="feedbacks_list_vote_button">
                                <div class="feedback_votes_counts">
                                    <p class="feedback_<?php echo $feedback['id'] ?>" id="feedback_voting_2"><?php echo $feedback['total_votes'];?></p>
                                    <span><?php echo $feedback['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
                                    <?php if($feedback['featured']): ?>
                                        <i class="feedback_featured"><?php echo __d('feedback','Featured') ?></i>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="comment feedback-comment">
                                <a href="<?php echo $feedback['moo_href']; ?>">
                                    <b><?php echo h($feedback['title']) ?></b>
                                </a>

                                <div class="date date-small">
                                   <?php $feedback['views']>1?printf( __d('feedback', '%s views'), $feedback['views']):printf( __d('feedback', '%s view'), $feedback['views'])  ?>,    
                                   <?php echo __n('%s comment', '%s comments', $feedback['comment_count'], $feedback['comment_count'] )?>
                                </div>
                                <div class="feedback_category_box">
                                    <?php if ($feedback_category['id']): ?> 
                                        <?php echo __d('feedback', 'Category: ') ?>
                                        <a href="<?php echo $this->request->base;?>/feedbacks/index/cat/<?php echo $feedback_category['id']?>"><?php echo $feedback_category['name'] ?></a>
                                    <?php endif ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?> 
                </ul>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>