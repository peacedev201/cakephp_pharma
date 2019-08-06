<?php if ($this->request->is('ajax')): ?>
	<script type="text/javascript">
		require(["jquery", "mooBehavior", "store_store", "mooTooltip"], function($, mooBehavior, store_store, mooTooltip) {
			//mooBehavior.initMoreResults();
			store_store.initReviewStar();
			store_store.initReviewPhotoPopup();
			mooTooltip.init();
		});
	</script>
<?php else: ?>
	<?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires'=>array("jquery", "mooBehavior", "store_store", "mooTooltip"), 
        'object' => array("$", "mooBehavior", "store_store", "mooTooltip"))); ?>
			mooBehavior.initMoreResults();
            store_store.initReviewStar();
			store_store.initReviewPhotoPopup();
			mooTooltip.init();
    <?php $this->Html->scriptEnd(); ?>
<?php endif?>
<?php if($reviews != null):?>
	<?php foreach ($reviews as $index => $review):?>
        <?php echo $this->Element('Store.misc/review_item', array(
            'review' => $review,
            'index' => $index,
            'page' => !empty($page) ? $page : 1,
            'product' => !empty($product) ? $product : null,
        ));?>
    <?php endforeach; ?>
    <?php if(!empty($more_review_url) && count($reviews) == Configure::read('Store.product_review_per_page')):?>
        <?php $this->Html->viewMore($more_review_url, 'review_content') ?>
    <?php endif;?>
<?php else:?>
    <li class="clear text-center no_results">
        <?php echo __d('store', 'No more results found');?> 
    </li>
<?php endif;?>