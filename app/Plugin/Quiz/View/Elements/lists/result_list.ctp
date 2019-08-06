<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>
<?php foreach ($aResultQuestion as $iKey => $aQuestion): ?>
    <li>
        <div class="question_number">
            <?php echo ($iKey + 1); ?>
        </div>
        <div class="question_answer_list">
            <div class="question_title">
                <?php echo h($aQuestion['QuizQuestion']['title']); ?>
            </div>
            <div>
                <?php echo __d('quiz', "%s's Answer", $sUserName); ?>: <?php if(!empty($aQuestion['QuizQuestion']['user_answer_title'])): ?><span class="<?php echo (!empty($aQuestion['QuizQuestion']['user_answer_correct']) ? 'participant-pass' : 'participant-fail') ?>"><?php echo h($aQuestion['QuizQuestion']['user_answer_title']); ?></span><?php endif; ?>
            </div>
            <div>
                <?php echo __d('quiz', 'Correct Answer'); ?>: <span class="participant-pass"><?php echo h($aQuestion['QuizQuestion']['correct_answer_title']); ?></span>
            </div>
        </div>
        <div class="clear"></div>
    </li>
<?php endforeach; ?>

