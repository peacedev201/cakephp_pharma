<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store', 'store_jquery_ui'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initSearch();
<?php $this->Html->scriptEnd(); ?> 
    
<?php echo $this->Element('Store.search_form');?>