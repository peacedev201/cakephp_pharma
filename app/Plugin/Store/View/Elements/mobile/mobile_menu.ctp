<?php 
    echo $this->Html->css(array( 
        'Store.storeapp',
        'Store.material',
    ), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooPhrase'), 
));?>
<?php $this->Html->scriptEnd(); ?> 

<div class="profile_plg_menu">
    <div class="menu">
        <ul class="list2 menu_top_list">
            <li>
                <a href="<?php echo STORE_URL."?app_no_tab=1";?>">
                    <i class="material-icons">store</i>           
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" id="btn_app_categories">
                    <i class="material-icons">menu</i>            
                </a>
            </li>
            <?php if($this->Store->allowBuyProduct()):?>
            <li>
                <a href="<?php echo STORE_URL;?>carts<?php echo $this->request->action == "checkout" ? "?app_no_tab=1" : ""?>">
                    <i class="material-icons">shopping_basket</i>            
                </a>
            </li>
            <?php endif;?>
            <li class="dropdown">
                <span id="profile_menu" data-toggle="dropdown"><i class="material-icons">arrow_drop_down_circle</i></span>
                <ul aria-labelledby="dropdown-edit" class="dropdown-menu mobileDropdown" for="profile_menu">
                    <li <?php if(empty($this->request->query["type"])):?>class="current"<?php endif;?>>
                        <a class="json-view" href="<?php echo STORE_URL."?app_no_tab=1";?>">
                            <?php echo __d('store', 'Products');?>
                        </a>                
                    </li>
                    <?php if(Configure::read('Store.show_store_list')):?>
                    <li <?php if(!empty($this->request->query["type"]) && $this->request->query["type"] == "stores"):?>class="current"<?php endif;?>>
                        <a class="json-view" href="<?php echo STORE_URL.'sellers';?><?php echo $this->request->action == "checkout" ? "&app_no_tab=1" : ""?>">
                            <?php echo __d('store', 'Stores');?>
                        </a>                
                    </li>
                    <?php endif;?>
                    <?php if(Configure::read('store.uid') > 0):?>
                        <li>
                            <a href="<?php echo STORE_URL.'?type=my_wishlist';?><?php echo $this->request->action == "checkout" ? "&app_no_tab=1" : ""?>">
                                <?php echo __d('store', 'Wishlist');?>                                    
                            </a>
                        </li>		
                        <li>
                            <a href="<?php echo STORE_URL.'?type=my_orders';?><?php echo $this->request->action == "checkout" ? "&app_no_tab=1" : ""?>">
                                <?php echo __d('store', 'Orders');?>                                
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo STORE_URL.'?type=my_files';?><?php echo $this->request->action == "checkout" ? "&app_no_tab=1" : ""?>">
                                <?php echo __d('store', 'My Files');?>                                
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if($hasStore):?>
                        <li>
                            <a href="<?php echo STORE_URL;?>manager<?php echo $this->request->action == "checkout" ? "&app_no_tab=1" : ""?>">
                                <?php echo __d('store', 'Seller Manager');?>                    
                            </a>
                        </li>  
                    <?php endif;?>
                </ul>
            </li>
        </ul>
    </div>
</div>