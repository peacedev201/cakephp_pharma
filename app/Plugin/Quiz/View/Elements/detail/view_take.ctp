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
            
            <div class="post_content">
                <form id="takeForm">
                    <?php echo $this->Form->hidden('direct_point', array('value' => 1)); ?>
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
                                    <button type="button" class="btn btn-action" data-id="<?php echo $quiz['Quiz']['id']; ?>" id="submitAnswerBtn"><?php echo __d('quiz', 'Submit Your Answers'); ?></button>
                                    <a href="javascript:void(0)" class="button button-action" id="resetBtn"><?php echo __d('quiz', 'Reset'); ?></a>
                                </div>
                            </div>
                        </li>
                        <li class="last_results_list">
                            <div class="col-md-12">
                                <div class="margin_top_content">
                                    <div class="error-message" id="errorMessage" style="display:none"></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </form>
            </div>
            
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooQuiz"], function(mooQuiz) {
        mooQuiz.initOnView(); mooQuiz.initTakeQuiz(<?php echo $quiz['Quiz']['timer'] * 60; ?>);
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnView(); mooQuiz.initTakeQuiz(<?php echo $quiz['Quiz']['timer'] * 60; ?>);
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>