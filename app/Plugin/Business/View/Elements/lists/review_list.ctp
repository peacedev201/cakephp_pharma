<?php
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
?>
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior", "mooBusiness"], function($, mooBehavior, mooBusiness) {
        mooBehavior.initMoreResults();
        mooBusiness.initBusinessReviewData();
    });
</script>
<?php endif?>
<?php if($reviews != null):?>
	<?php foreach ($reviews as $index => $review):?>
        <?php echo $this->Element('Business.misc/review_item', array(
            'review' => $review,
            'index' => $index,
            'page' => !empty($page) ? $page : 1,
            'business' => !empty($business) ? $business : null,
            'can_response_review' => $can_response_review
        ));?>
    <?php endforeach; ?>
    <?php if(!empty($more_review_url) && count($reviews) >= Configure::read('Business.business_review_per_page')):?>
        <?php $this->Html->viewMore($more_review_url, 'review_content') ?>
    <?php endif;?>
<?php else:?>
	<?php echo '<li class="clear text-center">' . __d('business', 'No more results found') . '</li>';?>
<?php endif;?>