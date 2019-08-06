<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="post_body">
            <?php echo $this->renderLike(); ?>
        </div>
    </div>
    <div class="post_body">
        <?php echo $this->renderComment(); ?>
    </div>
</div>