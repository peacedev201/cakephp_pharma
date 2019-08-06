<?php
$item = $object;
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>

<div class="activity_left">
    <a target="_blank" href="<?php echo $item[key($item)]['moo_href'] ?>">
        <img src="<?php echo $helper->getImage($item, array('prefix' => '150_square')) ?>" class="img_wrapper2" />
    </a>
</div>
<div class="activity_right ">
    <a target="_blank" class="feed_title" href="<?php echo $item[key($item)]['moo_href'] ?>"><?php echo htmlspecialchars($item[key($item)]['moo_title']) ?></a>
    <div class="comment_message feed_detail_text">
        <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['description'])), 200, array('exact' => false)), Configure::read('Contest.contest_hashtag_enabled')) ?>
    </div>
</div>

