<div class="title-modal">
    <?php echo __d('store', 'Categories');?>    
    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="box_content">
        <div class="box2 search-friend">
            <?php echo $this->Element('Store.misc/product_categories', array(
                'prefix' => 'mobile_'
            ));?>
        </div>
    </div>
</div>