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
    'requires' => array('jquery', 'store_store', 'store_star_rating', 'store_jquery_ui'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initGlobal();
    store_store.initProductList();
    <?php if($is_app):?>
        store_store.initCreateSeller();
        store_store.initSearch();
    <?php endif;?>
<?php $this->Html->scriptEnd(); ?> 

<div class="bar-content">  
    <?php echo $this->Element('Store.mobile/mobile_menu');?>
    <div class="content_center">
        <div class="main-container page-shop" id="archive-product">
            <div class="search_form">
                <?php if($allow_create_store && !$hasStore):?> 
                    <div class="create_new_store">
                        <?php if ($is_integrate_to_business): ?>
                            <a href="javascript:void(0)" class="addon_createbutton btn btn-action padding-button" id="btn_create_seller">
                                <?php echo __d('store', 'Create seller');?>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo STORE_URL;?>create" class="addon_createbutton btn btn-action padding-button">
                                <?php echo __d('store', 'Create seller');?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php elseif($hasStore):?> 
                <div class="create_new_store">
                    <a href="<?php echo STORE_MANAGER_URL;?>products/create" class="addon_createbutton btn btn-action padding-button">
                        <?php echo __d('store', 'Create product');?>
                    </a>
                </div>
                <?php endif;?>
                <br/>
                <?php echo $this->Element('Store.misc/search_form_content');?>
            </div>
            <nav class="store_plugin-breadcrumb">
                <?php if(isset($store_id) && $store_id > 0):?>
                    <a href="<?php echo STORE_URL.'sellers';?>"><?php echo __d('store', 'Stores');?></a>
                    <span class="separator">/</span>
                    <a href="<?php echo $store['Store']['moo_href'];?>"><?php echo $store['Store']['name'];?></a>
                <?php else:?>
                    <a href="<?php echo STORE_URL;?>"><?php echo __d('store', 'Products');?></a>		
                    <?php if(!empty($cat_paths)):?>
                        <?php foreach($cat_paths as $cat_path):
                            $cat_path = $cat_path['StoreCategory'];
                        ?>
                            <span class="separator">/</span>				
                            <a href="<?php echo $cat_path['moo_href'];?>"><?php echo $cat_path['name'];?></a>	
                        <?php endforeach;?>
                    <?php endif;?>
                <?php endif;?>
            </nav>
            <div class="toolbar">
                <div class="view-mode">
                    <a title="Grid" class="grid active" href="#">
                        <i class="material-icons">view_module</i>
                        <strong><?php echo __d('store', 'Grid');?></strong>
                    </a>
                    <a title="List" class="list" href="#">
                        <i class="material-icons">view_list</i>
                        <strong><?php echo __d('store', 'List');?></strong>
                    </a>
                </div>
                <p class="store_plugin-result-count" id="top-paginator-counter"></p>
                <div id="top-paginator"></div>
                <div class="clearfix"></div>
            </div>
            <?php echo $this->Form->hidden('store_id', array(
                'value' => !empty($store_id) ? $store_id : ''
            ));?>
            <?php echo $this->Form->hidden('store_category_id', array(
                'value' => !empty($store_category_id) ? $store_category_id : ''
            ));?>
            <ul class="topic-content-list" id="product-list"></ul>	
        </div>
    </div>
</div>
        
<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>