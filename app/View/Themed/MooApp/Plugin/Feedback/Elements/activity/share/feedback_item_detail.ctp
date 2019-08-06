<?php
$aFeedback = $object;
?>
<div class="activity_feed_content_text">
    <div class="activity_left feedback_left">
        <div class="feedback_votes_counts">
            <p id="feedback_voting_2" class="feedback_<?php echo  $aFeedback['Feedback']['id']?>"><?php echo  $aFeedback['Feedback']['total_votes']?></p>
            <span><?php echo $aFeedback['Feedback']['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
        </div>
    </div>
    <div class="activity_right ">
        <div class="activity_header">
            <a target="_blank" href="<?php echo $aFeedback['Feedback']['moo_href'] ?>"><?php echo h($aFeedback['Feedback']['moo_title']) ?></a>
        </div>
        <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $aFeedback['Feedback']['body'])), 200, array('exact' => false)), Configure::read('Feedback.feedback_hashtag_enabled')) ?>
    </div>
</div>
<div class="clear"></div>
