<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store'), 
    'object' => array('$', 'store_store')
));?>
    <?php if($is_app):?>
        store_store.initCreateSeller();
    <?php endif;?>
<?php $this->Html->scriptEnd(); ?> 
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
        <?php if(!isset($disable_search)):?>
            <br/>
            <?php echo $this->Element('Store.misc/search_form_content');?>
        <?php endif;?>
    </div>
</div>