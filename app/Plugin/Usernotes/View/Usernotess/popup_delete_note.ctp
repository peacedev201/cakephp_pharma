<div class="title-modal">
    <?php echo __d('usernotes','Delete note') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <?php if ($warning_msg): ?>
        <div><?php echo $warning_msg?></div>
    <?php else: ?>
    <div style="margin:0 0 5px 0"><?php printf( __d('usernotes','You can send <b>%s</b> an optional message below'), h($user['User']['name']) ); ?></div>
    <form id="addFriendForm">
    <input type="hidden" name="user_id" value="<?php echo $user['User']['id']?>">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="65" valign="top"><?php echo $this->Moo->getImage(array('User' => $user['User']), array("class" => "img_wrapper", 'prefix' => '50_square'))?></td>
            <td><textarea name="message"></textarea></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><br /><a href="javascript:void(0);" data-uid="<?php echo $user['User']['id']?>" id="sendReqAddFriendBtn" class="button button-action"><?php echo __d('usernotes','Send Request')?></a></td>
        </tr>
    </table>
    </form>
    <?php endif; ?>
</div>