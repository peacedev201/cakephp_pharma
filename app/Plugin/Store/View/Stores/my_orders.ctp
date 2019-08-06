<?php
    echo $this->Html->css(array(
        'Store.store',
        'Store.star-rating'
        ),array('block' => 'css', 'minify'=>false));
?>
<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "store_store"], function($, store_store) {
            store_store.initGlobal();
            store_store.initProfileOrders();
            store_store.printOrder();
        });
    </script>
<?php else:?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'store_store', 'store_star_rating'), 
        'object' => array('$', 'store_store')
    ));?>
        store_store.initGlobal();
        store_store.initProfileOrders();
        store_store.printOrder();
    <?php $this->Html->scriptEnd(); ?>
<?php endif?>

<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_menu');?>
    <div class="content_center">
        <?php echo $this->Element('Store.mobile/mobile_group', array(
            'disable_search' => true
        ));?>
        <div class="main-container page-shop">
            <nav class="store_plugin-breadcrumb">
                <a href="<?php echo STORE_URL.'?type=my_wishlist';?>">
                    <?php echo  __d('store', 'My Orders');?>
                </a>
            </nav>
            <div class="col-xs-12 ">
                <div class="page-content">
                    <?php /*if($noPermission):?>
                        <div id="flashMessage" class="Metronic-alerts alert alert-danger fade in">
                            <?php echo __d('store', 'You don\'t have permission to view this page');?>
                        </div>
                    <?php //else:*/?>
                    <article class="post-6 page type-page status-publish hentry jsn-master">
                        <div class="entry-content">
                            <div class=" div-detail-app" id="table_order">
                                <div class="div-full-breabcrum">
                                    <div class="col-md-2 ">
                                        <div class="group-group text-left">
                                            <i class="text-app"><?php echo __d('store', 'Seller');?></i>
                                        </div>
                                    </div>
                                    <div class="col-md-2 ">
                                        <div class="group-group text-left">
                                            <i class="text-app"><?php echo __d('store', 'Code');?></i>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="group-group">
                                            <i class="text-app"><?php echo __d('store', 'Date');?></i>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="group-group">
                                            <i class="text-app"><?php echo __d('store', 'Payment');?></i>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="group-group">
                                            <i class="text-app"><?php echo __d('store', 'Status');?></i>
                                        </div>
                                    </div>
                                    <div class=" col-md-1 ">
                                        <div class="group-group text-right">
                                            <i class="text-app"><?php echo __d('store', 'Total');?></i>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="group-group">
                                            <i class="text-app"><?php echo __d('store', 'Detail');?></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="data-exist"></div>
                            </div>
                        </div>
                    </article>	
                    <?php //endif;?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>