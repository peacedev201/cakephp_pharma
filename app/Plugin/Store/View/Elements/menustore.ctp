
<?php

if(defined('STORE_URL')):?>
<div class="menustore-wrapper">
    <div class="menu-store-contain">
        <?php if($this->Store->allowBuyProduct()):?>
        <a title="<?php echo __d('store', 'View your Cart');?>" href="<?php echo STORE_URL;?>carts">
            <i class="material-icons">shopping_cart</i>
            <div id="cart_balloon"><?php echo $total_quantity;?></div>
        </a>
        <?php endif;?>
        <?php if(Configure::read('store.uid') > 0):?>
            <a title="<?php echo __d('store', 'See your Wishlist');?>" href="<?php echo STORE_URL.'?type=my_wishlist';?>">
                <i class="material-icons">favorite</i>
            </a>
            <?php if($this->Store->allowBuyProduct()):?>
            <a title="<?php echo __d('store', 'Checkout your order');?>" href="<?php echo STORE_URL.'?type=my_orders';?>">
                <i class="material-icons">local_shipping</i>
            </a>
            <?php endif;?>
        <?php endif;?>
        <?php if($hasStore):?>
        <a title="<?php echo __d('store', 'Seller Manager');?>" href="<?php echo STORE_URL;?>manager">
            <i class="material-icons">settings</i>
        </a>
        <?php endif;?>
    </div>
</div>
<?php endif;?>