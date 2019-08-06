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
        <div class="mo_breadcrumb">
            <h1><?php echo __d('quiz', 'Quizzes'); ?></h1>
            <?php if (!empty($uid)): ?>
            <a class="button button-action topButton button-mobi-top" href="<?php echo $this->request->base . '/quizzes/create'; ?>"><?php echo __d('quiz', 'Create New Quiz'); ?></a>
            <?php endif; ?>
        </div>
        <ul class="quiz-content-list" id="list-content">
            <?php echo $this->element('lists/quizzes_list'); ?>
        </ul>	
    </div>
</div>
