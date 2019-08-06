<?php
$forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
?>
<div class="comment_message">
	<?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>

    <?php
    $activityModel = MooCore::getInstance()->getModel('Activity');
    $parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
    if(!empty($parentFeed)) {
        $topic = MooCore::getInstance()->getItemByType($parentFeed['Activity']['item_type'], $parentFeed['Activity']['item_id']);
    }
    ?>
    <?php if(isset($topic)):?>
    <div class="share-content">
    	<div class="activity_feed_content">
            
            <div class="activity_text">
                <?php echo $this->Moo->getName($parentFeed['User']) ?>
                <?php echo __d('forum','created a new topic'); ?>
            </div>
            
            <div class="parent_feed_time">
                <span class="date"><?php echo $this->Moo->getTime($parentFeed['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
            
        </div>
		<div class="activity_item">
            <div class="activity_left">
		     <div class="document_img">
				<a href="<?php echo $topic['ForumTopic']['moo_href']?>">
					<img alt="" src="<?php echo $forumHelper->getTopicImage($topic, array('prefix' => '150_square'));?>">
				</a>
			</div>
            </div>
		    <div class="activity_right ">
		        <div class="activity_header">
		            <a href="<?php echo  $topic['ForumTopic']['moo_href'] ?>"><?php echo  h($topic['ForumTopic']['moo_title']) ?></a>
		        </div>
		        <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $forumHelper->bbcodetohtml($topic['ForumTopic']['description'],true)),'<a><b>'), 200, array('exact' => false, 'html' => true)),Configure::read('Forum.forum_enable_hashtag') ))?>
		      </div>
		    <div class="clear"></div>
		</div>

	</div>
    <?php endif;?>
</div>