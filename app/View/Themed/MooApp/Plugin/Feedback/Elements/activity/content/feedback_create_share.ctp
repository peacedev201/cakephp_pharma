<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
    <div class="share-content">
    <?php
        $activityModel = MooCore::getInstance()->getModel('Activity');
        $parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
        $aFeedback = MooCore::getInstance()->getItemByType($parentFeed['Activity']['item_type'], $parentFeed['Activity']['item_id']);
    ?>
    <div class="activity_feed_content">
       
        <div class="activity_text">
            <?php echo $this->Moo->getName($parentFeed['User']) ?>
            <?php echo __d('feedback', 'created a new feedback'); ?>
        </div>
        
        <div class="parent_feed_time">
            <span class="date"><?php echo $this->Moo->getTime($aFeedback['Feedback']['created'], Configure::read('core.date_format'), $utz) ?></span>
        </div>
        
    </div>
    <div class="clear"></div>
    <div class="activity_feed_content_text">
     <div class="activity_left">
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
    </div>

</div>

