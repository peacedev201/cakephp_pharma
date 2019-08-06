<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>
<?php $iNumberAnswer = Configure::check('Quiz.quiz_answers_count') ? Configure::read('Quiz.quiz_answers_count') : 2; ?>
<?php $quizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz'); ?>

<script type="text/javascript">
    require(["mooQuiz"], function(mooQuiz) {
        mooQuiz.initOnCreateQuestion();
    });
</script>

<li class="full_content p_m_10">
    <div class="create_form">
        <div class="box3">
            <form id="createForm">
                <?php
                echo $this->Form->hidden('id', array('value' => $question['QuizQuestion']['id']));
                echo $this->Form->hidden('quiz_id', array('value' => $quiz['Quiz']['id']));
                ?>
                <div class="mo_breadcrumb">
                    <h1><?php echo (empty($question['QuizQuestion']['id'])) ? __d('quiz', 'Create New Question') : __d('quiz', 'Edit Question'); ?></h1>	
                </div>
                <div class="full_content p_m_10">
                    <div>
                        <ul>
                            <li>
                                <div class="col-md-2">
                                    <label><?php echo __d('quiz', 'Question'); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <?php echo $this->Form->text('title', array('value' => $question['QuizQuestion']['title'])); ?>
                                </div>
                                <div class="clear"></div>
                            </li>

                            <li>
                                <div class="col-md-2">
                                    <label><?php echo __d('quiz', 'Answer'); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <ul id="sortable_answers" class="answers-content-list ui-sortable">
                                    <?php if(isset($question['QuizAnswer'])): ?>
                                        <?php foreach($question['QuizAnswer'] as $aAnswers): ?>
                                            <li class="placeholder">
                                                <div class="col-drap reorder hidden-xs"><i class="material-icons">import_export</i></div>
                                                <div class="col-content">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <?php echo $this->Form->text('answers.title.' . $aAnswers['id'], array('placeholder' => __d('quiz', 'Title'), 'value' => $aAnswers['title'])); ?>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <div class="row correct_remove">
                                                        <div class="col-md-12">
                                                            <label>
                                                                <?php echo $this->Form->hidden('answers.correct.' . $aAnswers['id'], array('value' => $aAnswers['correct'] ? $aAnswers['correct'] : 0, 'id' => 'answersCorrect')); ?>
                                                                <input type="checkbox" class="resulttmp"<?php echo $aAnswers['correct'] ? 'checked="checked"' : ''; ?>> <?php echo __d('quiz', 'Correct Answer'); ?>
                                                            </label>
                                                            <a href="javascript:void(0);" class="pull-right remove_answer"><?php echo __d('quiz', 'Remove Answer'); ?></a>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php for($i = 1; $i <= $iNumberAnswer; $i++): ?>
                                        <li class="placeholder">
                                            <div class="col-drap reorder hidden-xs"><i class="material-icons">import_export</i></i></div>
                                            <div class="col-content">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?php echo $this->Form->text('answers.title.', array('placeholder' => __d('quiz', 'Title'), 'value' => '')); ?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                                <div class="row correct_remove">
                                                    <div class="col-md-12">
                                                        <label>
                                                            <?php echo $this->Form->hidden('answers.correct.', array('value' => 0, 'id' => 'answersCorrect')) ?>
                                                            <input type="checkbox" class="resulttmp"> <?php echo __d('quiz', 'Correct Answer'); ?>
                                                        </label>
                                                        <a href="javascript:void(0);" class="pull-right remove_answer"><?php echo __d('quiz', 'Remove Answer'); ?></a>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                    </ul>

                                    <ul>
                                        <li id="addNew" class="placeholder hide">
                                            <div class="col-drap reorder hidden-xs"><i class="material-icons">import_export</i></div>
                                            <div class="col-content">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?php echo $this->Form->text('answers.title.', array('placeholder' => __d('quiz', 'Title'), 'value' => '')); ?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                                <div class="row correct_remove">
                                                    <div class="col-md-12">
                                                        <label>
                                                            <?php echo $this->Form->hidden('answers.correct.', array('value' => 0, 'id' => 'answersCorrect')) ?>
                                                            <input type="checkbox" class="resulttmp"> <?php echo __d('quiz', 'Correct Answer'); ?>
                                                        </label>
                                                        <a href="javascript:void(0);" class="pull-right remove_answer"><?php echo __d('quiz', 'Remove Answer'); ?></a>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>

                                    <div>
                                        <a href="javascript:void(0)" id="addNewAnswer"><?php echo __d('quiz', 'Add New Answer'); ?></a>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </li>

                            <li>
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-10">
                                    <div>           
                                        <button type="button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="saveBtn"><?php echo __d('quiz', 'Save'); ?></button>
                                        <a class='button button-action' id="cancelBtn" href="javascript:void(0);"><?php echo __d('quiz', 'Cancel'); ?></a>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </li>
                            
                            <li>
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-10">
                                    <div class="error-message" id="errorMessage" style="display:none"></div>
                                </div>
                                <div class="clear"></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>   
</li>