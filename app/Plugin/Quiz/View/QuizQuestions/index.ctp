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
<?php echo $this->element('sidebar/browse', array('active' => 'questions')); ?>
<?php $this->end(); ?>
<?php endif; ?>
<div class="bar-content">  
    <div class="content_center">
        <?php if(!empty($quiz['Quiz']['take_count'])): ?>
        <div class="alert alert-warning">
            <strong><?php echo __d('quiz', 'Important!'); ?></strong>
            <?php if (Configure::check('Quiz.quiz_auto_approval') && !Configure::read('Quiz.quiz_auto_approval')): ?>
            <?php echo __d('quiz', "All participants will be auto removed if you made any changes related to questions of this quiz and is pending for admin's approval again."); ?>
            <?php else: ?>
            <?php echo __d('quiz', 'All participants will be auto removed if you made any changes related to questions of this quiz.'); ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="mo_breadcrumb">
            <h1><?php echo __d('quiz', 'Quiz Questions'); ?></h1>
            <?php if(!empty($questions)): ?>
            <a class="button button-action topButton button-mobi-top" id="orderQuestion" data-quiz_id="<?php echo $quiz['Quiz']['id']; ?>"><?php echo __d('quiz', 'Save order'); ?></a>
            <?php endif; ?>
            <a class="button button-action topButton button-mobi-top createQuestion" id="createQuestion" data-quiz_id="<?php echo $quiz['Quiz']['id']; ?>" data-quiz_question_id="0"><?php echo __d('quiz', 'Create New Question'); ?></a>
        </div>
        
        <ul class="quiz-content-list" id="questions-list-content">
            <?php $iCountQuestionConfig = Configure::check('Quiz.quiz_questions_count') ? Configure::read('Quiz.quiz_questions_count') : 2; ?>
            <?php if(empty($questions) || count($questions) < $iCountQuestionConfig): ?>
            <li class="full_content p_m_10 text-center"><?php echo __d('quiz', 'You need to create at least %s questions to be able to publish this quiz', $iCountQuestionConfig); ?></li>
            <?php endif; ?>
            
            <?php if(!empty($questions)): ?>
            <li class="not_style">
                <?php echo $this->element('lists/questions_list'); ?>
            </li>
            <?php endif; ?>
        </ul>
        <ul class="quiz-content-list" id="questions-create"></ul>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnListingQuestion();<?php $this->Html->scriptEnd(); ?>