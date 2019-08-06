<?php
$topic = $object;
$topicModel = $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
?>
<div class="activity_feed_content">
	<div class="activity_text">
		<?php echo $this->Moo->getName($topic['User'], true, true) ?>
		<?php echo __d('forum','created a new topic'); ?>
	</div>
	
	<div class="parent_feed_time">
		<span class="date"><?php echo $this->Moo->getTime($topic['ForumTopic']['created'], Configure::read('core.date_format'), $utz) ?></span>
	</div>
</div>
<div class="clear"></div>
<div class="activity_item">
    <div class="activity_left">
     <div class="forum_topic_img">
		<a href="<?php echo $topic['ForumTopic']['moo_href']?>">
			<img alt="" src="<?php echo $forumHelper->getTopicImage($topic, array('prefix' => '150_square'));?>">
		</a>
	</div>
    </div>
    <div class="activity_right">
        <div class="activity_header">
            <a href="<?php echo  $topic['ForumTopic']['moo_href'] ?>"><?php echo  h($topic['ForumTopic']['moo_title']) ?></a>
        </div>
        <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $forumHelper->bbcodetohtml($topic['ForumTopic']['description'],true)), 200, array('exact' => false)),Configure::read('Forum.forum_enable_hashtag') ))?>
      </div>
    <div class="clear"></div>
</div>