<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<li>
    <a href="<?php echo $this->request->here; ?>" data-url="<?php echo $this->request->base . '/quizzes/profile/' . $user['User']['id'] . '/user'; ?>" rel="profile-content">
        <i class="material-icons">help</i>&nbsp;<?php echo __d('quiz', 'Quizzes'); ?>
        <span class="badge_counter"><?php echo $count; ?></span>
    </a>
</li>