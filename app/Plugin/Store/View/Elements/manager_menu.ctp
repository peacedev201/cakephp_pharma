<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager', 'store_metismenu'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initManagerMenu();
<?php $this->Html->scriptEnd(); ?>
<?php
    echo $this->Html->css(array( 
        'Store.admin',
    ), array('block' => 'css', 'minify'=>false));
?>
 
<div class="bar-content">
    <div class="menu block-body">
        <ul id="<?php if(empty($dialog)):?>side-menu<?php else:?>side-menu-dialog<?php endif;?>" class="nav in">
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>" <?php if(!empty($active_menu) && $active_menu == 'seller'):?>class="active"<?php endif;?>>
                    <i class="material-icons">dashboard</i> <?php echo __d('store', "Seller");?>
                </a>
            </li>
            <?php if($allowSeller):?>
            <li <?php if(!empty($active_menu) && in_array($active_menu, array('manage_attributes', 'create_attribute'))):?>class="active"<?php endif;?>>
                <a href="#">
                    <i class="material-icons">reorder</i> <?php echo __d('store', "Attributes");?>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>attributes/" <?php if(!empty($active_menu) && $active_menu == 'manage_attributes'):?>class="active"<?php endif;?>><?php echo __d('store', "Manage Attributes");?></a>
                    </li>
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>attributes/create" <?php if(!empty($active_menu) && $active_menu == 'create_attribute'):?>class="active"<?php endif;?>><?php echo __d('store', "Create Attribute");?></a>
                    </li>
                </ul>
            </li>
            <li <?php if(!empty($active_menu) && in_array($active_menu, array('manage_producers', 'create_producer'))):?>class="active"<?php endif;?>>
                <a href="#">
                    <i class="material-icons">insert_chart</i> <?php echo __d('store', "Producer");?>
           
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>producers/" <?php if(!empty($active_menu) && $active_menu == 'manage_producers'):?>class="active"<?php endif;?>><?php echo __d('store', "Manage Producers");?></a>
                    </li>
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>producers/create" <?php if(!empty($active_menu) && $active_menu == 'create_producer'):?>class="active"<?php endif;?>><?php echo __d('store', "Create Producer");?></a>
                    </li>
                </ul>
            </li>
            <li <?php if(!empty($active_menu) && in_array($active_menu, array('manage_products', 'create_product'))):?>class="active"<?php endif;?>>
                <a href="#">
                    <i class="material-icons">palette</i> <?php echo __d('store', "Products");?>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>products/" <?php if(!empty($active_menu) && $active_menu == 'manage_products'):?>class="active"<?php endif;?>><?php echo __d('store', "Manage Products");?></a>
                    </li>
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>products/create" <?php if(!empty($active_menu) && $active_menu == 'create_product'):?>class="active"<?php endif;?>><?php echo __d('store', "Create Product");?></a>
                    </li>
                </ul>
            </li>
            <?php if(Configure::read('Store.store_enable_shipping')):?>
            <li <?php if(!empty($active_menu) && in_array($active_menu, array('manage_shippings', 'create_shipping', 'manage_shipping_zones', 'create_shipping_zone'))):?>class="active"<?php endif;?>>
                <a href="#">
                    <i class="material-icons">flight_takeoff</i> <?php echo __d('store', "Shippings");?>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>shipping_zones/" <?php if(!empty($active_menu) && $active_menu == 'manage_shipping_zones'):?>class="active"<?php endif;?>><?php echo __d('store', "Manage Shipping Zones");?></a>
                    </li>
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>shipping_zones/create" <?php if(!empty($active_menu) && $active_menu == 'create_shipping_zone'):?>class="active"<?php endif;?>><?php echo __d('store', "Create Shipping Zone");?></a>
                    </li>
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>shippings/" <?php if(!empty($active_menu) && $active_menu == 'manage_shippings'):?>class="active"<?php endif;?>><?php echo __d('store', "Manage Shippings");?></a>
                    </li>
                </ul>
            </li>
            <?php endif;?>
			<li <?php if(!empty($active_menu) && in_array($active_menu, array('manage_orders', 'create_order'))):?>class="active"<?php endif;?>>
                <a href="#">
                    <i class="material-icons">local_shipping</i> <?php echo __d('store', "Orders");?>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>orders/" <?php if(!empty($active_menu) && $active_menu == 'manage_orders'):?>class="active"<?php endif;?>><?php echo __d('store', "Manage Orders");?></a>
                    </li>
                    <li>
                        <a href="<?php echo STORE_MANAGER_URL;?>orders/create" <?php if(!empty($active_menu) && $active_menu == 'create_order'):?>class="active"<?php endif;?>><?php echo __d('store', "Create Order");?></a>
                    </li>
                </ul>
            </li>
            <?php if(Configure::read('Store.store_buy_featured_product') || Configure::read('Store.store_buy_featured_store')):?>
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>transactions/" <?php if(!empty($active_menu) && $active_menu == 'transactions'):?>class="active"<?php endif;?>>
                    <i class="material-icons">monetization_on</i> <?php echo __d('store', "Transactions");?>
                </a>
            </li>
            <?php endif;?>
            <?php endif;?>
            <li>
                <a href="javascript:void(0);" id="delete_seller">
                    <i class="material-icons">delete</i> <?php echo __d('store', "Delete Seller");?>
                </a>
            </li>
        </ul>
    </div>
</div>