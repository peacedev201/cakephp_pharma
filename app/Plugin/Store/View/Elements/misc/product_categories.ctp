<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "store_store", 'store_accordion'], function($, store_store, store_accordion) {
            store_store.initCategoryEffect();
        });
    </script>
<?php else: ?>
    <?php
        $this->Html->scriptStart(array(
            'inline' => false, 
            'domReady' => true, 
            'requires' => array('jquery', 'store_store', 'store_accordion'), 
            'object' => array('$', 'store_store')
        )); 
    ?>
        store_store.initCategoryEffect();
    <?php $this->Html->scriptEnd();?>
<?php endif; ?>
<div  class="thumbs glossymenu">
    <?php echo $this->Category->renderMainStoreCategory($storeCats, isset($store_category_id) ? $store_category_id : '', '', isset($prefix) ? $prefix : '');?>
</div>