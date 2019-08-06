<?php if(Configure::read('Store.store_enabled')):?>
<div class="box2 search-friend">	
    <h3>
        <?php echo __d('store', 'Search');?>  
    </h3>
    <div class="box_content">
        <?php echo $this->Element('Store.misc/search_store_form_content');?>
    </div>
</div>
<?php endif;?>