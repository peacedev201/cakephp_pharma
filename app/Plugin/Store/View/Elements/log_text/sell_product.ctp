<?php 
if(!empty($item_object['Store']['name']))
{
    echo sprintf(__d('store', 'sell product from store %s'), '<a href="'.$this->request->base.'/stores/manager/orders/">'.$item_object['Store']['name'].'</a>');
}
else
{
    echo __d('store', 'sell product');
}
?>
