<?php if(Configure::read('Store.store_enabled')):?>
<div class="box2 search-friend">	
    <h3>
        <?php echo __d('store', 'Search');?>  
    </h3>
    <div class="box_content">
        <?php if(!empty($this->request->params['action']) && $this->request->params['action'] == "sellers"):?>
            <?php echo $this->Element('Store.misc/search_store_form_content');?>
        <?php else:?>
            <?php echo $this->Element('Store.misc/search_form_content');?>
        <?php endif;?>
    </div>
</div>
<?php endif;?>