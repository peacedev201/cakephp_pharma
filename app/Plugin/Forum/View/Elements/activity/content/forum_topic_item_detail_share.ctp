<?php
$forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
$topic = MooCore::getInstance()->getItemByType('Forum_Forum_Topic',$activity['Activity']['parent_id']);
?>


<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
</div>


<div class="share-content activity_item">
    <div class="activity_left">
     <div class="forum_img">
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