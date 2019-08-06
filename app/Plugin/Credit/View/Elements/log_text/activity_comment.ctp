<?php
    $helper = MooCore::getInstance()->getHelper("Credit_Credit");
    $text = $helper->getTextActivityCommentItem($item_object);
?>

<?php echo __d('credit','Comment status - "%s"',$text) ?>