<?php
    $helper = MooCore::getInstance()->getHelper("Credit_Credit");
    $text = $helper->getTextLikeItem($item_object);
?>

<?php echo __d('credit','Like %s',$text) ?>