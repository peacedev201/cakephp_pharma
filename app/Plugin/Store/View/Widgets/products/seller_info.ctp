<?php if(Configure::read('Store.store_enabled') && !empty($seller)):?>
    <div class="box2 search-friend">
        <h3><?php echo __d('store', 'Seller Info');?></h3>
        <?php echo $this->Element('Store.widgets/seller_info', array(
            'store' => $seller
        ));?>
    </div>
<?php endif;?>