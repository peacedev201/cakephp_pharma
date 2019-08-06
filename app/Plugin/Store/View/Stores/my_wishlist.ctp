<?php
    echo $this->Html->css(array(
        'Store.store',
        'Store.star-rating',
        'Store.jquery-ui'
        ),array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store', 'store_jcarousel', 'store_star_rating'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initGlobal();
    store_store.initProfileWishlist();
<?php $this->Html->scriptEnd(); ?>

<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_menu');?>
    <div class="content_center">
        <?php echo $this->Element('Store.mobile/mobile_group', array(
            'disable_search' => true
        ));?>
        <div class="main-container page-shop">
            <nav class="store_plugin-breadcrumb">
                <a href="<?php echo STORE_URL.'?type=my_wishlist';?>">
                    <?php echo  __d('store', 'Wishlist');?>
                </a>
            </nav>
            <div class="col-xs-12 ">
                <div class="page-content">
                    <article class="post-6 page type-page status-publish hentry jsn-master">
                        <div class="entry-content">
                                <div class="div-detail-app" id="table-wishlist">
                                    <div class="div-full-breabcrum">
                                        <div class="col-md-1 ">
                                            <div class="group-group ">
                                                <i class="text-app"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="text-align: left">
                                            <div class="group-group">
                                                <i class="text-app"><?php echo __d('store', 'Product Name');?></i>
                                            </div>
                                        </div>
                                        <div class=" col-md-3">
                                            <div class="group-group ">
                                                <i class="text-app"><?php echo __d('store', 'Price');?></i>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="group-group">
                                                <i class="text-app"><?php echo __d('store', 'Stock Status');?></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="data-exist"></div>
                                </div>
                        </div><!-- .entry-content -->
                    </article><!-- #post -->						
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>