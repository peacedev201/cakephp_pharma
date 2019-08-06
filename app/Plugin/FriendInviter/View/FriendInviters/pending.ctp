<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1>
                <?php echo __d('friend_inviter', 'Pending Invites'); ?>
            </h1>
        </div>
        <div>
            <a class="button button-action pull-right" href="<?php echo $this->request->base ?>/friend_inviters"><?php echo __d('friend_inviter', 'Back to Friend Inviter'); ?></a>
        </div>
        <div class="clear"></div>
        <?php if ($this->Paginator->counter('{:count}') == 0): ?>

            <div class="tip">
                <span><?php echo __d('friend_inviter', "You do not have any pending invitations at this time."); ?></span>
            </div>

        <?php else: ?>

            <div>
                <?php echo __d('friend_inviter', 'Total of invitations sent: %s', $this->Paginator->counter('{:count}')) ?><br>
                <?php echo __d('friend_inviter', 'Number of joined members: %s', $invite_signup_count) ?>
            </div>
            <div class="list_invited">
                <?php foreach ($invites as $invite) { ?>
                    <div id="invite_<?php echo $invite['Invite']['id'] ?>" class="clearfix">

                        <div class='profile_action_date'>

                            <div class="invite-name">
                                <div style="" title="<?php echo $invite['Invite']['recipient'] ?>">
                                    <i class="icon-envelope"></i>
                                    <?php
                                    $invite_email = $invite['Invite']['recipient'];
                                    $cut_email = (strlen($invite_email) < 25 ? $invite_email : (substr($invite_email, 0, 25) . '...') );
                                    echo $cut_email;
                                    ?>
                                </div>
                            </div>
                            <div class="invite-date">
                                &nbsp;&nbsp;  <?php echo $this->Moo->getTime($invite['Invite']['timestamp'], Configure::read('core.date_format'), $utz) ?> &nbsp; &nbsp;&nbsp;
                            </div>
                            <div class="invite-action">
                                <a href="javascript:void(0)" class='delete_invite' rel="<?php echo $invite['Invite']['id'] ?>"><?php echo __d('friend_inviter', 'Delete') ?></a>
                                <span style="padding-left: 2px;padding-right: 2px;"> | </span>
                                <span id="<?php echo $invite['Invite']['id'] ?>_link">
                                    <a href="javascript:void(0)" class='resend_invite' rel="<?php echo $invite['Invite']['id'] ?>"><?php echo __d('friend_inviter', 'Resend invitation') ?></a>
                                </span>
                            </div>

                        </div>
                    </div>
                <?php } ?>


                <div style="padding-bottom:0px; padding-top:16px; padding-right: 0px; padding-left:25px;">
                    <div class="pagination pull-right">
                                                <?php  
                            $this->Paginator->options['url']['?'] = array('app_no_tab' => 1);
                        ?>
                        <?php echo $this->Paginator->prev('« ' . __d('friend_inviter', 'Previous'), null, null, array('class' => 'disabled')); ?>
                        <?php echo $this->Paginator->numbers(); ?>
                        <?php echo $this->Paginator->next(__d('friend_inviter', 'Next') . ' »', null, null, array('class' => 'disabled')); ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
if ($is232):
    $this->Html->css(array('FriendInviter.fi'), array('block' => 'css'));
    $this->MooRequirejs->addPath(array(
        "mooPendinginvite" => $this->MooRequirejs->assetUrlJS("FriendInviter.js/pendinginvite.js", array('plugin' => true)),
    ));

    $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooPendinginvite'), 'object' => array('$', 'mooPendinginvite')));
    ?>
    mooPendinginvite.initOnPending();
    <?php $this->Html->scriptEnd(); ?>
<?php else: ?>
    <?php echo $this->Html->css(array('FriendInviter.fi.css'), null, array('inline' => false)); ?>
    <?php echo $this->Html->script(array('jquery.mp.min', 'FriendInviter.prev/pendinginvite'), array('inline' => false)); ?>                    
<?php endif; ?>
