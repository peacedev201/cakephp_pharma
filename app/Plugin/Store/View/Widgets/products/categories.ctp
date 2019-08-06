<?php if(Configure::read('Store.store_enabled')):?>
    <div class="box2 filter_block">
        <h3><?php echo __d('store', 'Categories');?>
            <a href="<?php echo STORE_URL;?>" class="pull-right">
                <?php echo __d('store', 'View all');?>
            </a>
        </h3>
        <div id="thumbs" class="box_content">
            <?php echo $this->Element('Store.misc/product_categories', array(
                'storeCats' => $storeCats
            ));?>
        </div>
    </div>
<?php endif;?>