<?php
$item = $object;
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>
<div class="activity_item">
    <div class="activity_left">
         <a href="<?php echo  $item[key($item)]['moo_href'] ?>">
             <img width="150" class="thum_activity" src="<?php echo  $helper->getImage($item, array('prefix' => '150_square')) ?>" />
         </a>
    </div>
    <div class="activity_right ">
        <div class="activity_header">
            <a href="<?php echo  $item[key($item)]['moo_href'] ?>"><?php echo  htmlspecialchars($item[key($item)]['moo_title']) ?></a>
        </div>
        <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['description'])), 200, array('exact' => false)), Configure::read('Contest.contest_hashtag_enabled')) ?>
      </div>
    <div class="clear"></div>
</div>