<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $quizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz'); ?>

<?php $this->setNotEmpty('west'); ?>
<?php $this->start('west'); ?>
<div class="bar-content">
    <div class="box2 filter_block quiz-content-browse-view">
        <div class="box_content">
            <ul class="list2 quiz-menu-list">
                <li><img width="100%" src="<?php echo $quizHelper->getImage($quiz, array('prefix' => '300_square')); ?>"></li>
                <li class="separate"></li>
            </ul>                 
            <ul class="list2 quiz-menu-list">
                <li class="current">
                    <a href="javascript:void(0)"><?php echo __d('quiz', 'Detail'); ?></a>
                </li>
            </ul>		
        </div>
    </div>
</div>
<?php $this->end(); ?>

<div id="quiz-content">
    <div class="bar-content full_content p_m_10">
        <div class="content_center">
            <div class="post_body">

                <div class="mo_breadcrumb">
                    <h1><?php echo h($quiz['Quiz']['title']); ?></h1>
                    <div class="list_option">
                        <div class="dropdown">
                            <button data-toggle="dropdown" data-target="#" id="dropdown-edit">
                                <i class="material-icons">more_vert</i>
                            </button>
                            <ul aria-labelledby="dropdown-edit" class="dropdown-menu" role="menu">
                                <?php if ($quiz['Quiz']['user_id'] == $uid || (!empty($cuser) && $cuser['Role']['is_admin'])): ?>
                                    <li><a href="<?php echo $this->request->base . '/quizzes/create/' . $quiz['Quiz']['id']; ?>"><?php echo __d('quiz', 'Edit'); ?></a></li>
                                    <li><a href="javascript:void(0);" data-id="<?php echo $quiz['Quiz']['id']; ?>" class="deleteQuiz"><?php echo __d('quiz', 'Delete'); ?></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="extra_info">
                    <?php echo __d('quiz', 'Posted by %s', $this->Moo->getName($quiz['User'], false)); ?>
                    <?php echo $this->Moo->getTime($quiz['Quiz']['created'], Configure::read('core.date_format'), $utz); ?>
                </div>
                
                <div class="post_content">
                    <?php if(empty($quiz['Quiz']['published'])): ?>
                    <span class="status_error"><?php echo __d('quiz', 'Not Published'); ?></span>
                    <?php endif; ?>
                    <?php if(empty($quiz['Quiz']['approved'])): ?>
                    <span class="status_error"><?php echo __d('quiz', 'Not Approved'); ?></span>
                    <?php endif; ?>
                </div>

                <div class="post_content">
                    <div><?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($quiz['Quiz']['description'], Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0)); ?></div>            
                </div>
                
                <div class="post_content">
                    <?php if(!empty($questions)): ?>
                    <ul class="results_list">
                        <?php foreach ($questions as $iKey => $question): ?>
                        <li>
                            <div class="question_number">
                                <?php echo ($iKey + 1); ?>
                            </div>
                            <div class="question_answer_list">
                                <div class="question_title">
                                    <?php echo h($question['QuizQuestion']['title']); ?>
                                </div>
                                <?php foreach($question['QuizAnswer'] as $aAnswers): ?>
                                <div class="answer_list">
                                    <label>
                                        <?php echo $this->Form->radio('correct.' . $question['QuizQuestion']['id'], array($aAnswers['id'] => ''), array('id' => false, 'label' => false, 'hiddenField' => false)); ?>&nbsp;&nbsp;<?php echo h($aAnswers['title']); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <?php endforeach; ?>
                        
                        <li class="last_results_list">
                            <div class="col-md-12">
                                <div class="margin_top_content">
                                    <a href="<?php echo $this->request->base . '/quizzes/publish/' . $quiz['Quiz']['id']; ?>" class="btn btn-action"><?php echo __d('quiz', 'Publish'); ?></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <?php else: ?>
                        <?php echo __d('quiz', 'No questions found'); ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnView();<?php $this->Html->scriptEnd(); ?>

