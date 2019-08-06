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
    <a id="my-taken" href="<?php echo $this->request->base . '/home/index/tab:my-taken'; ?>" data-url="<?php echo $this->request->base . '/quizzes/home'; ?>" rel="home-content">
        <i class="material-icons">help</i>&nbsp;<?php echo __d('quiz', 'My Quizzes'); ?>
        <span class="badge_counter"><?php echo $count; ?></span>
    </a>
</li>