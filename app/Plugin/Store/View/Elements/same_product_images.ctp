 <?php if($usedProducts != null):?> 
    <h3><?php echo __d('store', 'Product Used');?></h3>
    <div class="box_content">
        <?php echo $this->Element('Store.list/product_widget_list', array(
            'products' => $usedProducts
        ));?>
    </div>
<?php endif;?>