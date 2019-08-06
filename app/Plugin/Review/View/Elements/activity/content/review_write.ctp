<?php
$aReviewUser = $object;
?>

<div class="comment_message">
    <div class="review-star-rating">
        <input readonly value="<?php echo $aReviewUser['ReviewUser']['rating']; ?>" type="number" class="rating form-control hide" data-stars="5" data-size="xs">
    </div>
</div>

<div class="activity_item" style="padding: 10px">
    <?php echo $this->viewMore(h($aReviewUser['ReviewUser']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <div class="clear"></div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooReview'), 'object' => array('mooReview')));?>
mooReview.initReviewStar(true);<?php $this->Html->scriptEnd(); ?>