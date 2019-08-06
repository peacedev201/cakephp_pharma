<?php
    $helper = MooCore::getInstance()->getHelper("Credit_Credit");
    $text = $helper->getTextCommentItem($item_object);
?>

<?php echo __d('credit','Commenting %s',$text) ?>
