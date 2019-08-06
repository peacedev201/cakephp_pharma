<?php if ($item_object): ?>
    <?php $text = '<a href="'.$item_object['Group']['moo_href'].'">'.$item_object['Group']['moo_title'].'</a>'; ?>
<?php else:?>
    <?php $text = '<span class="notice_red">' . __d('credit', 'deleted') . '</span>'; ?>
<?php endif;?>

<?php echo __d('credit','joined group %s',$text) ?>