<?php
    $business = $business['Business'];
    $store = $store['Store'];
?>

<?php
    echo $this->Html->css(array(
        'Business.business',
        'Store.store',
        'Store.star-rating',
        'Store.jquery-ui'
        ),array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store', 'store_star_rating', 'store_jquery_ui'), 
    'object' => array('$', 'store_store', 'store_jquery_ui')
));?>
    store_store.initGlobal();
    store_store.initProductList(<?php echo $business['id'];?>);
<?php $this->Html->scriptEnd(); ?> 

<div class="business_store">
    <div class="main-container page-shop" id="archive-product">
        <nav class="store_plugin-breadcrumb">
            <div class="mo_breadcrumb">
                <h1><?php echo __d('business', 'Product');?></h1>
                <?php if(($business['parent_id'] == 0 && $permission_can_manage_products && $uid == $store['user_id'])):?>
                    <a href="<?php echo $this->request->base;?>/stores/manager/products/create" class="button button-action topButton button-mobi-top">
                        <?php echo __d('business', 'Add Product');?>
                    </a>
                <?php endif;?>
                
            </div>
        </nav>
        <div class="toolbar">
            <div class="view-mode">
                <a title="Grid" class="grid active" href="#">
                    <i class="material-icons">view_module</i>
                    <strong><?php echo __d('business', 'Grid');?></strong>
                </a>
                <a title="List" class="list" href="#">
                    <i class="material-icons">view_list</i>
                    <strong><?php echo __d('business', 'List');?></strong>
                </a>
            </div>
            <p class="store_plugin-result-count" id="top-paginator-counter"></p>
            <div id="top-paginator"></div>
            <div class="clearfix"></div>
        </div>
        <ul class="topic-content-list" id="product-list"></ul>	
    </div>
</div>