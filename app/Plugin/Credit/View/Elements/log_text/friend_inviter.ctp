<?php if ($item_object):
    $userBlock =  MooCore::getInstance()->getModel('UserBlock');
    $is_block = $userBlock->areUserBlocks( MooCore::getInstance()->getViewer(true), $item_object['User']['id']);
    if($is_block) {
        $text = '<a href="'.$item_object['User']['moo_href'].'">'.$item_object['User']['moo_title'].'</a>';
    }
    else {
        $text = $this->Moo->getName($item_object['User'], false);
    }
    ?>
<?php else:?>
    <?php $text = '<span class="notice_red">' . __d('credit', 'deleted') . '</span>'; ?>
<?php endif;?>

<?php echo __d('friend_inviter','Invite %s', $text) ?>