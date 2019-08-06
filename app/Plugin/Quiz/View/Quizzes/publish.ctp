<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php if (!empty($quiz['Quiz']['id'])): ?>
<?php $this->setNotEmpty('west'); ?>
<?php $this->start('west'); ?>
<?php echo $this->element('sidebar/browse', array('active' => 'publish')); ?>
<?php $this->end(); ?>
<?php endif; ?>

<div class="bar-content">  
    <div class="content_center">
        <div class="box3">
            <form id="createForm">
                <div class="mo_breadcrumb">
                    <h1><?php echo __d('quiz', 'Quiz Publish'); ?></h1>
                    <?php if ($quiz['Quiz']['published'] && $quiz['Quiz']['approved']): ?>
                        <a href="<?php echo $quiz['Quiz']['moo_href']; ?>" class="button button-action topButton button-mobi-top"><?php echo __d('quiz', 'View'); ?></a>
                    <?php else: ?>
                        <a href="<?php echo $this->request->base . '/quizzes/review/' . $quiz['Quiz']['id']; ?>" class="button button-action topButton button-mobi-top"><?php echo __d('quiz', 'Review'); ?></a>
                    <?php endif; ?>
                </div>
                <div class="full_content p_m_10">
                    <div class="form_content">
                        <?php if($bVerifyQuestion): ?>
                            <?php if(!empty($quiz['Quiz']['published'])): ?>
                                <p><?php echo __d('quiz', 'Click on `Un-Publish` button to un-publish this quiz.'); ?></p>
                                <p><a class="btn btn-action"  id="unPublishBtn" data-id="<?php echo $quiz['Quiz']['id']; ?>"><?php echo __d('quiz', 'Un-Publish'); ?></a></p>
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
                                <p><a class="btn btn-action" id="publishBtn" href="<?php echo $this->request->base . '/quizzes/publish/' . $quiz['Quiz']['id'] . '/1'; ?>"><?php echo __d('quiz', 'Publish'); ?></a></p>
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
