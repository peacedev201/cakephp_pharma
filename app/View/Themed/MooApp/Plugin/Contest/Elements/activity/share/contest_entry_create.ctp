<?php
$item = $object;
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>

<div class="activity_feed_image">
    <?php echo $this->Moo->getItemPhoto(array('User' => $item['User']), array('prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_large')) ?>
</div>
<div class="activity_feed_content">
    <div class="comment">
        <div class="activity_text">
            <?php echo $this->Moo->getName($item['User']) ?>
            <?php echo __d('contest', 'submitted a entry to'); ?> <a href="<?php echo $item['Contest']['moo_href']; ?>"><?php echo $item['Contest']['moo_title']; ?></a>
        </div>
        <div class="parent_feed_time">
            <span class="date"><?php echo $this->Moo->getTime($item[key($item)]['created'], Configure::read('core.date_format'), $utz) ?></span>
        </div>
    </div>
</div>
<div class="clear"></div>
<div class="activity_feed_content_text blog_feed">
    <div class="activity_left">
        <a target="_blank" href="<?php echo $item[key($item)]['moo_href'] ?>">
            <img class="thum_activity" src="<?php echo $this->request->webroot ?>theme/<?php echo $this->theme ?>/img/s.png" style="background-image:url(<?php echo $helper->getEntryImage($item, array()) ?>)"  />
        </a>
    </div>
    <div class="activity_right ">
        <p class="entry_caption"><?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['caption'])), 200, array('exact' => false)); ?></p>
    </div>
</div>

