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
    <div class="blog_feed">
        <div class="activity_left">
            <a href="<?php echo $item[key($item)]['moo_href'] ?>">
                <img class="thum_activity" src="<?php echo $this->request->webroot ?>theme/<?php echo $this->theme ?>/img/s.png" style="background-image:url(<?php echo $helper->getEntryImage($item, array()) ?>)"  />
            </a>
        </div>
        <div class="activity_right ">
               <p class="entry_caption"><?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $item[key($item)]['caption'])), 200, array('exact' => false)); ?></p>
        </div>
        <div class="clear"></div>
    </div>
</div>