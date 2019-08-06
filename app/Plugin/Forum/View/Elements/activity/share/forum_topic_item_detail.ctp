<?php
$topic = $object;
$topicModel = $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
?>

    <div class="activity_left">
        <a target="_blank" href="<?php echo $topic['ForumTopic']['moo_href'] ?>">
            <img width="150" class="thum_activity" src="<?php echo $forumHelper->getTopicImage($topic, array('prefix' => '150')) ?>" />
        </a>
    </div>
    <div class="activity_right ">
        <div class="activity_header">
            <a target="_blank" href="<?php echo $topic['ForumTopic']['moo_href'] ?>"><?php echo h($topic['ForumTopic']['moo_title']) ?></a>
        </div>
        <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $forumHelper->bbcodetohtml($topic['ForumTopic']['description'],true)), 200, array('exact' => false)), Configure::read('Forum.forum_enable_hashtag'))) ?>
    </div>
    <div class="clear"></div>
