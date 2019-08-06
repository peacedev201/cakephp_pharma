<?php
$feedbackModel = MooCore::getInstance()->getModel('Feedback_Feedback');
$aFeedback = MooCore::getInstance()->getItemByType('Feedback_Feedback',$activity['Activity']['parent_id']);

?>


<?php if (!empty($activity['Activity']['content'])): ?>
<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
</div>
<?php endif; ?>

<div class="activity_item">
    <div class="activity_left">
       <div class="feedback_votes_counts">
            <p id="feedback_voting_2" class="feedback_<?php echo  $aFeedback['Feedback']['id']?>"><?php echo  $aFeedback['Feedback']['total_votes']?></p>
            <span><?php echo $aFeedback['Feedback']['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
        </div>
    </div>

    <div class="activity_right ">
        <div class="activity_header">
            <a href="<?php echo  $aFeedback['Feedback']['moo_href'] ?>"><?php echo  h($aFeedback['Feedback']['moo_title']) ?></a>
        </div>
        <?php echo  $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $aFeedback['Feedback']['body'])), 200, array('exact' => false)), Configure::read('Feedback.feedback_hashtag_enabled')) ?>
      </div>
    <div class="clear"></div>
</div>