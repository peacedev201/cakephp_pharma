<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>


    <div class="comment_message">
        <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
        <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
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
                <?php echo __d('contest', 'submitted a entry to'); ?> <a href="<?php echo $item['Contest']['moo_href']; ?>"><?php echo $item['Contest']['moo_title']; ?></a>
        </div>

        <div class="parent_feed_time">
            <span class="date"><?php echo $this->Moo->getTime($parentFeed['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
        </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="activity_feed_content_text blog_feed">
        <div class="activity_left">
            <a href="<?php echo $item[key($item)]['moo_href'] ?>">
            <img class="thum_activity" src="<?php echo $this->request->webroot ?>theme/<?php echo $this->theme ?>/img/s.png" style="background-image:url(<?php echo $helper->getEntryImage($item, array()) ?>)"  />
        </a>
        </div>
        <div class="activity_right ">
            <p class="entry_caption"><?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['caption'])), 200, array('exact' => false)) ; ?></p>
        </div>
    </div>
    <div class="clear"></div>
</div>
