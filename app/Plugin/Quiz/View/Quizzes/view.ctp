<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $this->setNotEmpty('west'); ?>
<?php $this->start('west'); ?>
<?php echo $this->element('sidebar/view'); ?>
<?php echo $this->element('sidebar/duration'); ?>
<?php echo $this->element('sidebar/tag'); ?>
<?php $this->end(); ?>

<div id="quiz-content">
    <?php echo $this->element('detail/view_detail'); ?>
</div>
