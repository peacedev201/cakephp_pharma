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
        <?php echo $this->element('likes', array('shareUrl' => $this->Html->url(array('plugin' => false, 'controller' => 'share', 'action' => 'ajax_share', 'Quiz_Quiz', 'id' => $quiz['Quiz']['id'], 'type' => 'quiz_item_detail'), true), 'item' => $quiz['Quiz'], 'type' => $quiz['Quiz']['moo_type'])); ?>
    </div>
</div>

<div class="bar-content full_content p_m_10">
    <?php echo $this->renderComment(); ?>
</div>