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
            
            <?php echo $this->element('detail/view_content'); ?>
            
            <?php if ($uid): ?>
                <div class="post_content loadQuizView">
                    <?php if($bTaken): ?>
                    <a href="<?php echo $quiz['Quiz']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/quizzes/view_taken/' . $quiz['Quiz']['id']; ?>" rel="quiz-content" class="btn btn-action"><?php echo __d('quiz', 'View Results'); ?></a>
                    <?php else: ?>
                    <?php
                    $this->MooPopup->tag(array(
                        'href' => $this->request->base . '/quizzes/ajax_take_privacy/' . $quiz['Quiz']['id'],
                        'innerHtml'=> __d('quiz', 'Start Now'),
                        'title' => __d('quiz', 'Start Now'),
                        'class' => 'btn btn-action no-ajax'
                    ));
                    ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php echo __d('quiz', 'Login or register to take quiz or view result'); ?>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php echo $this->element('detail/view_like_comment'); ?>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooQuiz"], function(mooQuiz) {
        mooQuiz.initOnView();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnView();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>