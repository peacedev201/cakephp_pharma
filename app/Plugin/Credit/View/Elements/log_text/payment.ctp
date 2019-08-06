<?php if ($item_object): ?>
    <?php if (strtolower($item['CreditLogs']['object_type']) == 'subscription_subscribe'):
        echo __d('credit','Upgrade membership using credit');
        return;
    else:
        $text = '<a href="'.(isset($item_object[key($item_object)]['moo_href']) && $item_object[key($item_object)]['moo_href'] ? $item_object[key($item_object)]['moo_href'] : 'javascript:void(0);').'">'.$item_object[key($item_object)]['moo_title'].'</a>';
    endif;
    ?>
<?php else:?>
    <?php $text = '<span class="notice_red">' . __d('credit', 'deleted') . '</span>'; ?>
<?php endif;?>

<?php echo __d('credit','Pay %s with credit',$text) ?>