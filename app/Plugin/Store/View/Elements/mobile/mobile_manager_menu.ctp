<?php
    echo $this->Html->css(array( 
        'Store.material.min',
        'Store.storeapp',
    ), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initManagerMenu();
<?php $this->Html->scriptEnd(); ?> 
<div class="profile_plg_menu">
    <div class="menu">
        <ul class="list2 menu_top_list">
            <li>
                <a href="<?php echo STORE_URL;?>">
                    <i class="material-icons">store</i>            
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" id="load_manager_menu">
                    <i class="material-icons">menu</i>            
                </a>
            </li>
            <?php if($this->Store->allowBuyProduct()):?>
            <li>
                <a href="<?php echo STORE_URL;?>carts">
                    <i class="material-icons">shopping_basket</i>            
                </a>
            </li>
            <?php endif;?>
            <li class="dropdown">
                <span id="profile_menu" data-toggle="dropdown"><i class="material-icons">arrow_drop_down_circle</i></span>
                <ul aria-labelledby="dropdown-edit" class="dropdown-menu mobileDropdown" for="profile_menu">
                    <?php if(Configure::read('store.uid') > 0):?>
                        <li>
                            <a href="<?php echo STORE_URL.'?type=my_wishlist';?>">
                                <?php echo __d('store', 'Wishlist');?>                                    
                            </a>
                        </li>		
                        <li>
                            <a href="<?php echo STORE_URL.'?type=my_orders';?>">
                                <?php echo __d('store', 'Orders');?>                                
                            </a>
                        </li>
						<li>
                            <a href="<?php echo STORE_URL.'?type=my_files';?>">
                                <?php echo __d('store', 'My Files');?>                                
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            </li>
        </ul>
    </div>
</div>