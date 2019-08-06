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
        <div class="box3">
            <form id="createForm">
                <div class="mo_breadcrumb">
                    <?php if (!$quiz['Quiz']['published'] || !$quiz['Quiz']['approved']): ?>
                        <a href="<?php echo $this->request->base . '/quizzes/areview/' . $quiz['Quiz']['id']; ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored"><?php echo __d('quiz', 'Review'); ?></a>
                    <?php endif; ?>
                </div>
                <div class="full_content p_m_10">
                    <div class="form_content">
                        <?php if($bVerifyQuestion): ?>
                            <?php if(!empty($quiz['Quiz']['published'])): ?>
                                <p><?php echo __d('quiz', 'Click on `Un-Publish` button to un-publish this quiz.'); ?></p>
                                <p><a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base . '/quizzes/publish/' . $quiz['Quiz']['id'] . '/0?app_no_tab=1'; ?>"><?php echo __d('quiz', 'Un-Publish'); ?></a></p>
                            <?php else: ?>
                                <p><?php echo __d('quiz', 'Please click on `Publish` button to publish your quiz to all members.'); ?></p>
                                <p>
                                    <strong><?php echo __d('quiz', 'Important!'); ?></strong>
                                    <?php if (Configure::check('Quiz.quiz_auto_approval') && !Configure::read('Quiz.quiz_auto_approval')): ?>
                                    <?php echo __d('quiz', "All participants will be auto removed if you made any changes related to questions of this quiz and is pending for admin's approval again."); ?>
                                    <?php else: ?>
                                    <?php echo __d('quiz', 'All participants will be auto removed if you made any changes related to questions of this quiz.'); ?>
                                    <?php endif; ?>
                                </p>
                                <p><a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base . '/quizzes/publish/' . $quiz['Quiz']['id'] . '/1?app_no_tab=1'; ?>"><?php echo __d('quiz', 'Publish'); ?></a></p>
                            <?php endif; ?>
                        <?php else: ?>
                                <p><?php echo __d('quiz', 'Please add questions to quiz before being able to publish it.'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnPublish();<?php $this->Html->scriptEnd(); ?>
