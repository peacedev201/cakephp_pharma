<?php
echo $this->Html->css(array(
    'Store.store'), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initGlobal();
    store_store.initCart();
<?php $this->Html->scriptEnd(); ?> 
<div class="bar-content">  
    <?php echo $this->Element('Store.mobile/mobile_menu');?>
    <div class="content_center">
        <div class="main-container default-page">
            <div class="container channel-container ">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="page-content">
                            <form id="cartForm">
                                <?php echo $this->Form->hidden('allow_credit', array(
                                    'value' => $allow_credit
                                ));?>
                                <?php echo $this->Form->hidden('setting_show_money_type', array(
                                    'value' => $setting_show_money_type
                                ));?>
                                <?php echo $this->Form->hidden('warning_store', array(
                                    'value' => $warning_store
                                ));?>
                                <article class="post-6 page type-page status-publish hentry jsn-master cart-form">
                                    <div class="checkout-wrap">
                                        <ul class="checkout-bar">
                                            <li class="active">
                                                <a href="javascript:void(0)"><?php echo  __d('store', 'Carts');?></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)"><?php echo  __d('store', 'Billing & Payment');?></a>
                                            </li>
                                            <li><?php echo  __d('store', 'Complete');?></li>
                                        </ul>
                                    </div>
                                    <?php if($store_list != null && !Configure::read('Store.store_hide_cart_store_option')):?>
                                    <header class="entry-header" id="wrapper_cart_stores">
                                        <div class="store_plugin-ordering pull-right">
                                            <div class="orderby-wrapper">
                                                <?php echo $this->Form->select('store_id', $store_list, array(
                                                    'empty' => array('' => __d('store', 'All stores')),
                                                    'id' => 'store_id',
                                                    'class' => 'orderby pull-right'
                                                ));?>
                                            </div>
                                        </div>
                                    </header>
                                    <?php endif;?>
                                    <div class="entry-content">
                                        <div class="div-detail-app" id="table-cart">
                                            <div class="div-full-breabcrum">
                                                <div class="col-md-5 text-left ">
                                                    <div class="group-group ">
                                                        <i class="text-app"><?php echo __d('store', 'Product');?></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 text-left ">
                                                    <div class="group-group ">
                                                        <i class="text-app"><?php echo __d('store', 'Type');?></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-left">
                                                    <div class="group-group">
                                                        <i class="text-app"><?php echo __d('store', 'Price');?></i>
                                                    </div>
                                                </div>
                                                <div class=" col-md-1 ">
                                                    <div class="group-group ">
                                                        <i class="text-app"><?php echo __d('store', 'Quantity');?></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="group-group">
                                                        <i class="text-app"><?php echo __d('store', 'Total');?></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="group-group">
                                                        <i class="text-app"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="cart_content">
                                                <?php echo $this->element('Store.load_cart_by_store');?>
                                            </div>
                                            <div class="data-empty" <?php if($cart != null):?>style="display: none"<?php endif;?>>
                                                <?php echo __d('store', 'No products', 'Stores');?>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<?php if($is_app):?>
    <script>
    function doRefesh()
    {
        location.reload();
    }
    </script>
<?php endif;?>