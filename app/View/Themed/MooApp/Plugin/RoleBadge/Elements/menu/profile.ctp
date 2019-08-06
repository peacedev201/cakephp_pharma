<?php
/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<li class="mdl-menu__item mdl-js-ripple-effect">
    <a href="<?php echo $this->request->here; ?>" id="awardProfile" data-url="<?php echo $this->request->base . '/awards/profile/' . $user['User']['id']; ?>" rel="profile-content">
        <?php echo __d('role_badge', 'Award Badges'); ?>
    </a>
</li>