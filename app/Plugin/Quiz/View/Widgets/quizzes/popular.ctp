<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

?>

<?php if(Configure::check('Quiz.quiz_enabled') && Configure::read('Quiz.quiz_enabled') && !empty($aQuizzes)): ?>
<div class="box2">
    <?php if($title_enable): ?>
    <h3><?php echo (!empty($title)) ? $title : __d('quiz', 'Popular Quizzes'); ?></h3>
    <?php endif; ?>
    
    <div class="box_content">
        <?php echo $this->element('Quiz.detail/view_list', array('aQuizzes' => $aQuizzes)); ?>
    </div>
</div>
<?php endif; ?>