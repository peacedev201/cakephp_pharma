<?php if ($item_object): ?>
    <?php $text = '<a href="'.$item_object['User']['moo_href'].'">'.$item_object['User']['moo_title'].'</a>'; ?>
<?php else:?>
    <?php $text = '<span class="notice_red">' . __d('credit', 'deleted') . '</span>'; ?>
<?php endif;?>

<?php echo __d('credit','Sending credits to %s for closing contest',$text) ?>