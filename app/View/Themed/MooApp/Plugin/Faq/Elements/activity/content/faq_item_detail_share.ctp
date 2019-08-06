<?php 
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
$faqModel = MooCore::getInstance()->getModel('Faq_Faq');
$faq = $faqModel->findById($activity['Activity']['parent_id']);
?>


<div class="comment_message">
<?php echo $this->viewMore(h($activity['Activity']['content']),null, null, null, true, array('no_replace_ssl' => 1)); ?>
<?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
</div>


<div class="share-content">
    <div class="activity_right ">
        <a class="feed_title" href="<?php echo $faq['Faq']['moo_href']?>"><?php echo h($faq['Faq']['moo_title'])?></a>
        <div class="comment_message feed_detail_text">
            <?php echo  $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $faq['Faq']['body'])), 200, array('exact' => false)),1) ?>
            
        </div>
    </div>
</div>