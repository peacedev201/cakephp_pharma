<?php if ($item_object): ?>
    <?php $text = '<a href="'.$item_object['Contest']['moo_href'].'">'.$item_object['Contest']['moo_title'].'</a>'; ?>
<?php else:?>
    <?php $text = '<span class="notice_red">' . __d('contest', 'deleted') . '</span>'; ?>
<?php endif;?>

<?php echo __d('contest','Published contest %s',$text); ?>