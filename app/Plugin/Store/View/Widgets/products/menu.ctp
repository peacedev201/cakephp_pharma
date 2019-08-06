<?php if(Configure::read('Store.store_enabled')):?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'store_store'), 
        'object' => array('$', 'store_store')
    ));?>
        store_store.initCreateSeller();
    <?php $this->Html->scriptEnd(); ?> 
    <div class="box2 filter_block">
        <h3>
            <?php echo __d('store', 'Menu');?>
        </h3>
        <div id="thumbs" class="box_content">
            <ul class="list2 menu-list">
                <li <?php if(empty($this->request->query["type"]) && $this->request->params["action"] == "index"):?>class="current"<?php endif;?>>
                    <a class="json-view" href="<?php echo STORE_URL;?>">
                        <?php echo __d('store', 'Products');?>
                    </a>                
                </li>
                <?php if(Configure::read('Store.show_store_list')):?>
                <li <?php if(!empty($this->request->params["action"]) && ($this->request->params["action"] == "sellers" || $this->request->params["action"] == "seller_products")):?>class="current"<?php endif;?>>
                    <a class="json-view" href="<?php echo STORE_URL.'sellers';?>">
                        <?php echo __d('store', 'Stores');?>
                    </a>                
                </li>
                <?php endif;?>
                <?php if(!empty($cuser)):?>
                <li <?php if(!empty($this->request->query["type"]) && $this->request->query["type"] == "my_orders"):?>class="current"<?php endif;?>>
                    <a class="json-view" href="<?php echo STORE_URL.'?type=my_orders';?>">
                        <?php echo __d('store', 'My Orders');?>
                    </a>                
                </li>
                <li <?php if(!empty($this->request->query["type"]) && $this->request->query["type"] == "my_wishlist"):?>class="current"<?php endif;?>>
                    <a class="json-view" href="<?php echo STORE_URL.'?type=my_wishlist';?>">
                        <?php echo __d('store', 'My Wishlist');?>
                    </a>                
                </li>
                <li <?php if(!empty($this->request->query["type"]) && $this->request->query["type"] == "my_files"):?>class="current"<?php endif;?>>
                    <a class="json-view" href="<?php echo STORE_URL.'?type=my_files';?>">
                        <?php echo __d('store', 'My Files');?>
                    </a>                
                </li>
                <?php endif;?>
            </ul>
            <?php if($allow_create_store && !$hasStore):?> 
            <div class="create_new_store">
                <?php if ($is_integrate_to_business): ?>
                    <a href="javascript:void(0)" class="addon_createbutton btn btn-action" id="btn_create_seller">
                        <?php echo __d('store', 'Create seller');?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo STORE_URL;?>create" class="addon_createbutton btn btn-action">
                        <?php echo __d('store', 'Create seller');?>
                    </a>
                <?php endif; ?>
            </div>
            <?php elseif($hasStore):?> 
            <div class="create_new_store">
                <a href="<?php echo STORE_MANAGER_URL;?>products/create" class="addon_createbutton btn btn-action">
                    <?php echo __d('store', 'Create product');?>
                </a>
            </div>
            <?php endif;?>
        </div>
    </div>
<?php endif;?>