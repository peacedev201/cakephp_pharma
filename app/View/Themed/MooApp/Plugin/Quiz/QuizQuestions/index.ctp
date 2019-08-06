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
            <a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1 createQuestion" id="createQuestion" data-quiz_id="<?php echo $quiz['Quiz']['id']; ?>" data-quiz_question_id="0"><?php echo __d('quiz', 'Create New Question'); ?></a>
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