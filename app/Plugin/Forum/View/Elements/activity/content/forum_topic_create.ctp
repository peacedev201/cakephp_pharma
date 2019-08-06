<?php
$topic = $object;
$topicModel = $topicHelper = MooCore::getInstance()->getHelper('Forum_Forum');
$forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
?>
<div class="activity_item">
    <div class="activity_left">
     <div class="forum_topic_img">
		<a href="<?php echo $topic['ForumTopic']['moo_href']?>">
			<img alt="" src="<?php echo $topicHelper->getTopicImage($topic, array('prefix' => '150_square'));?>">
		</a>
	</div>
    </div>
    <div class="activity_right">
        <div class="activity_header">
            <a href="<?php echo $topic['ForumTopic']['moo_href'] ?>"><?php echo h($topic['ForumTopic']['moo_title']) ?></a>
        </div>
        <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $forumHelper->bbcodetohtml($topic['ForumTopic']['description'], true)),'<a><b>'), 200, array('exact' => false, 'html' => true)),Configure::read('Forum.forum_enable_hashtag') ))?>
      </div>
    <div class="clear"></div>
</div>