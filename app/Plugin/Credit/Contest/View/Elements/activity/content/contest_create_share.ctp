<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>


<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if (!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
</div>


<div class="share-content" >
    <?php
    $activityModel = MooCore::getInstance()->getModel('Activity');
    $parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
    $item = MooCore::getInstance()->getItemByType($parentFeed['Activity']['item_type'], $parentFeed['Activity']['item_id']);
    ?>
    <div class="activity_feed_image">
        <?php echo $this->Moo->getItemPhoto(array('User' => $parentFeed['User']), array('prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_large')) ?>
    </div>
    <div class="activity_feed_content">
        <div class="comment">
            <div class="activity_text">
                <?php echo $this->Moo->getName($parentFeed['User']) ?>
                <?php echo __d('contest', 'posted a new contest'); ?>
            </div>

            <div class="parent_feed_time">
                <span class="date"><?php echo $this->Moo->getTime($parentFeed['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="activity_feed_content_text">
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
    <div class="clear"></div>
</div>
