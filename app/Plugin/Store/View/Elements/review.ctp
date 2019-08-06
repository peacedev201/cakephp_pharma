<?php if($product['StoreProduct']['allow_review']):?>
    <?php if($user == null):?>
        <div class="login_mess">
        <?php echo __d('store', 'Login or register to post your review');?>
        </div>
    <?php elseif($user['id'] != $product['StoreProduct']['user_id'] && 
            !$this->Store->isBoughtProduct($product['StoreProduct']['id']) && 
            Configure::read('Store.store_only_buyer_can_review') == 1 &&
            $product['StoreProduct']['new_price'] > 0):?>
        <div class="login_mess">
        <?php echo __d('store', 'Please buy product to post your review');?>
        </div>
    <?php elseif($product['StoreProduct']['user_id'] != $user['id']):?>
        <a class="btn btn-action btnReview btn_write_review pull-right" href="javascript:void(0)" data-product_id="<?php echo $product_id;?>" data-view_detail="1" <?php if($is_reviewed):?>style="display: none"<?php endif;?>>
            <?php echo __d('store', 'Write review');?>
        </a>
    <?php endif;?>
<?php endif;?>
<div class="clear"></div>
<ul id="review_content" class="list6"></ul>