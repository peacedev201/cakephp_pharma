<?php
$faq = $object;
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
?>
    <div class="activity_right ">
        <a target="_blank" class="feed_title" href="<?php echo $faq['Faq']['moo_href'] ?>"><?php echo h($faq['Faq']['moo_title']) ?></a>
        <div class="comment_message feed_detail_text">
            <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $faq['Faq']['body'])), 200, array('exact' => false)), 1) ?>

        </div>
    </div>

