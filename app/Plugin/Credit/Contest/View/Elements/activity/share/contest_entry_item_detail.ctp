<?php
$item = $object;
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>

<p class="entry_caption"><?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['caption'])), 200, array('exact' => false)), Configure::read('Contest.contest_hashtag_enabled')) ?></p>
<a class="entry_image" href="<?php echo $item[key($item)]['moo_href']; ?>" >
    <img width="300" class="thum_activity" src="<?php echo  $helper->getEntryImage($item, array('prefix' => '300_square')) ?>" />
</a>
<div class="clear"></div>

