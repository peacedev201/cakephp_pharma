<?php
$item = $object;
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>
<div class="blog_feed">
    <div class="activity_left">
        <a target="_blank" href="<?php echo $item[key($item)]['moo_href'] ?>">
            <img class="thum_activity" src="<?php echo $this->request->webroot ?>theme/<?php echo $this->theme ?>/img/s.png" style="background-image:url(<?php echo $helper->getEntryImage($item, array()) ?>)"  />
        </a>
    </div>
    <div class="activity_right ">
        <p class="entry_caption"><?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['caption'])), 200, array('exact' => false)); ?></p>
    </div>
    <div class="clear"></div>
</div>

