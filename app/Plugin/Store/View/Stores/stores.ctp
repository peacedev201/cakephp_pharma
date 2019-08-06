<?php
    echo $this->Html->css(array(
        'Store.store',
        'Store.star-rating'
        ),array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store', 'store_jcarousel', 'store_star_rating'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initGlobal();
    store_store.initStoreList();
<?php $this->Html->scriptEnd(); ?> 

<div class="bar-content">  
    <?php echo $this->Element('Store.mobile/mobile_menu');?>
    <div class="content_center">
        <div class="main-container page-shop" id="archive-product">
            <div class="search_form">
                <?php echo $this->Element('Store.misc/search_store_form_content');?>
            </div>
            <nav class="store_plugin-breadcrumb">
                <a href="<?php echo STORE_URL;?>"><?php echo __d('store', 'Stores');?></a>		
                <?php if(!empty($storeCatBreadCrumb)):?>
                    <span class="separator">/</span>				
                    <?php //echo $this->Category->loadProductCategoryBreadCrumb($storeCatBreadCrumb, 'StoreCategory');?>	
                <?php endif;?>
            </nav>
            <div class="toolbar">
                <p class="store_plugin-result-count" id="top-paginator-counter"></p>
                <div id="top-paginator"></div>
                <div class="clearfix"></div>
            </div>
            <ul class="topic-content-list" id="store-list"></ul>	
        </div>
    </div>
</div>