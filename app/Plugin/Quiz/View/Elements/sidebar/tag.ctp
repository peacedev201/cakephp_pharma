<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php if(!empty($tags)): ?>
<div class="box2">
    <h3><?php echo __d('quiz', 'Tags'); ?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/tags_item_block'); ?>
    </div>
</div>
<?php endif; ?>