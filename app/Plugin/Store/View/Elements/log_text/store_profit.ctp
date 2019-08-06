<?php 
if(!empty($item_object['Store']['name']))
{
    echo sprintf(__d('store', 'receive profit from store %s'), '<a href="'.$item_object['Store']['moo_href'].'">'.$item_object['Store']['name'].'</a>');
}
else
{
    echo __d('store', 'buy product');
}
?>
