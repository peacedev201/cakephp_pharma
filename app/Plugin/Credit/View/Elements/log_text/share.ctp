<?php /*if ($item_object):
    if( key($item_object) == 'Activity' ) {
        $text = 'post';
    }
    else {
        $text = '<a href="'.$item_object[key($item_object)]['moo_href'].'">'.$item_object[key($item_object)]['moo_title'].'</a>';
    }
    ?>
<?php else:?>
    <?php $text = '<span class="notice_red">' . __d('credit', 'deleted') . '</span>'; ?>
<?php endif;*/?>

<?php echo __d('credit','Share an item') ?>