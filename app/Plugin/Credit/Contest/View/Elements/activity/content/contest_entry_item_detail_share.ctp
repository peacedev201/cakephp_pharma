<?php 
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$mModel = MooCore::getInstance()->getModel('Contest.ContestEntry');
$item = $mModel->findById($activity['Activity']['parent_id']);
?>


<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']),null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
</div>

<div class="share-content">
    <p class="entry_caption"><?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['caption'])), 200, array('exact' => false)), Configure::read('Contest.contest_hashtag_enabled')) ?></p>
    <a class="entry_image" href="<?php echo $item[key($item)]['moo_href']; ?>" >
        <img width="300" class="thum_activity" src="<?php echo  $helper->getEntryImage($item, array('prefix' => '300_square')) ?>" />
    </a>
    <div class="clear"></div>
</div>