<div class="review_star">
    <input readonly value="<?php echo $product['rating']; ?>" type="number" class="rating form-control hide">
</div>
<span class="total-rating"><?php echo $product['rating'];?> / <?php echo PRODUCT_MAX_RATING;?></span>
<span class="total-rating">(<?php echo $product['rating_count'];?> <?php echo ($product['rating_count'] > 1) ? __d('store', 'users') : __d('store', 'user');?>)</span>
<div class="clear"></div>