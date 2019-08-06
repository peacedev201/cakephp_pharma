<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery','store_star_rating'), 
   // 'object' => array('$', 'store_store')
));?>
<?php $this->Html->scriptEnd(); ?> 
<?php if(Configure::read('Store.store_enabled')):?>
    <?php if($featuredStores != null):?> 
        <div class="box2 search-friend">
            <h3><?php echo __d('store', 'Featured Stores');?></h3>
            <div class="box_content">
                <?php echo $this->Element('Store.list/store_widget_list', array(
                    'stores' => $featuredStores
                ));?>
            </div>
        </div>
    <?php endif;?>
<?php endif;?>