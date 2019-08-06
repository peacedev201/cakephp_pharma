<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$mModel = MooCore::getInstance()->getModel('Contest.Contest');
$item = $mModel->findById($activity['Activity']['parent_id']);
?>


<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if (!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
</div>


<div class="share-content">
    <div class="activity_left">
        <a href="<?php echo $item[key($item)]['moo_href'] ?>">
            <img src="<?php echo $helper->getImage($item, array('prefix' => '150_square')) ?>" class="img_wrapper2" />
        </a>
    </div>
    <div class="activity_right ">
        <a class="feed_title" href="<?php echo $item[key($item)]['moo_href'] ?>"><?php echo htmlspecialchars($item[key($item)]['moo_title']) ?></a>
        <div class="comment_message feed_detail_text">
            <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['description'])), 200, array('exact' => false)), Configure::read('Contest.contest_hashtag_enabled')) ?>
        </div>
    </div>
</div>