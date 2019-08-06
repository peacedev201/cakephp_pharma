<div class="shop-products row grid-view">
    <?php if($stores != null):?>
        <?php foreach($stores as $store):
            $store = $store['Store'];
        ?> 
    <div class="first  item-col col-xs-12 col-sm-4 post-86 product type-product status-publish has-post-thumbnail product_cat-accessories product_cat-laptop product_cat-notebooks product_cat-watches jsn-master sale featured shipping-taxable purchasable product-type-simple product-cat-accessories product-cat-laptop product-cat-notebooks product-cat-watches instock jsn-bootstrap3">
        <div class="product-wrapper">
            <div class="list-col4">
                <div class="product-image">
                    <?php if($store['featured']):?>
                    <a class="featured_product" href="<?php echo $store['moo_href'];?>">
                        <span><?php echo __d('store', 'Featured');?></span>
                    </a>
                    <?php endif;?>
                    <a title="<?php echo $store['name'];?>" href="<?php echo $store['moo_href'];?>">
                        <img width="300" height="300" alt="<?php echo $store['name'];?>" class="primary_image wp-post-image" src="<?php echo $this->Store->getStoreImage($store);?>">					
                        <span class="shadow"></span>
                    </a>
                </div>
            </div>
            <div class="list-col8">
                <div class="gridview">
                    <h2 class="product-name">
                        <a href="<?php echo $store['moo_href'];?>">
                            <?php echo $store['name'];?>
                        </a>
                    </h2>
                    <p class="info"><?php echo __d('store', 'Email');?>: <?php echo $store['email'];?></p>
                    <p class="info"><?php echo __d('store', 'Phone');?>: <?php echo $store['phone'];?></p>
                    <p class="info product_address"><?php echo __d('store', 'Address');?>: <?php echo $store['address'];?></p>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
        <?php endforeach;?>
    <?php else:?>
        <center style="margin-top:10px"><?php echo __d('store', 'No stores');?></center>
    <?php endif;?>
</div>
<?php if($stores != null):?>
<div class="toolbar tb-bottom">
    <p class="store_plugin-result-count" id="bottom-paginator-counter">
        <?php echo $this->Paginator->counter(sprintf(__d('store', 'Showing %s–%s of %s results'), '{:start}', '{:end}', '{:count}'));?>
    </p>
    <nav class="store_plugin-pagination" id="bottom-paginator">
        <ul class="page-numbers">
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array(
                'class' => 'page-numbers previous', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array(
                'class' => 'page-numbers previous', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->numbers(array(
                'class' => 'page-numbers', 
                'tag' => 'li', 
                'separator' => '')); ?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array(
                'class' => 'page-numbers next', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array(
                'class' => 'page-numbers next', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
        </ul>
    </nav>
    <div class="clearfix"></div>
</div>
<?php endif;?>