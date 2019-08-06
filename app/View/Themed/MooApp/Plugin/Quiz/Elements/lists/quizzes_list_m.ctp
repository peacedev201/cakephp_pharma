<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="bar-content">  
    <div class="content_center">
        <div class="pull-right" style="margin-right: 10px">
            <?php if (!empty($uid)): ?>
            <a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base . '/quizzes/create'; ?>"><?php echo __d('quiz', 'Create New Quiz'); ?></a>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
        <ul class="quiz-content-list" id="list-content">
            <?php echo $this->element('lists/quizzes_list'); ?>
        </ul>	
    </div>
</div>

<script type="text/javascript">
function doRefesh() {
    window.location.reload();
}
</script>