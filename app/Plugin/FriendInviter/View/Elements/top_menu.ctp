<div class="job_breadcrumb">
    <h1><?php echo __d('friend_inviter', 'Invite your friends') ?></h1>
    <ul class="">
        <li<?php if($active == 'all'): ?> class="current" <?php endif;?> id="browse_all">
            <a href="<?php echo $this->request->base ?>/friend_inviters"><?php echo __('Home') ?></a>
        </li>
        <li<?php if($active == 'pending_invite'): ?> class="current" <?php endif;?>>
            <a href="<?php echo $this->request->base ?>/friend_inviter/friend_inviters/pending"><?php echo __d('friend_inviter', 'Pending Invites') ?></a>
        </li>
    </ul>
</div>
