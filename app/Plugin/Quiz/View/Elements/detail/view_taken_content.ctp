<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>
<?php $iPercentResult = round(($aResult['iCountCorrect']/$aResult['iCountQuestion'])*100); ?>

<h3 class="title">
    <?php echo __d('quiz', 'Quiz Results'); ?>: 
    <?php if($iPercentResult >= $aResult['iPassScore']): ?>
    <span class="quiz-finish-pass"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Pass'); ?></span>
    <?php else: ?>
    <span class="quiz-finish-fail"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Fail'); ?></span>
    <?php endif; ?>
</h3>

<ul class="question_list">
<?php foreach ($aResultQuestion as $iKey => $aQuestion): ?>
    <li>
        <div class="col-md-12">
            <h3 class="title"><?php echo ($iKey + 1); ?>. <?php echo h($aQuestion['QuizQuestion']['title']); ?></h3>
        </div>
        <?php foreach($aQuestion['QuizAnswer'] as $aAnswers): ?>
        <div class="answer_list">
            <label <?php if ($aAnswers['correct'] == 1): echo ' class="result_pass"'; elseif (isset($aAnswers['user_answer'])): echo ' class="result_fail"'; endif;?>>
                <?php echo h($aAnswers['title']); ?>
            </label>
        </div>
        <?php endforeach; ?>
    </li>
<?php endforeach; ?>
    <li>
        <div class="col-md-12">
            <div class="margin_top_content">
                <a href="javascript:void(0);" class="shareFeedBtn btn btn-action" share-url="<?php echo $this->Html->url(array("plugin" => false, "controller" => "share", "action" => "ajax_share", "id" => $aActivity['Activity']['id'])); ?>"><?php echo __d('quiz', 'Share Your Result'); ?></a>
            </div>
        </div>
    </li>
</ul>