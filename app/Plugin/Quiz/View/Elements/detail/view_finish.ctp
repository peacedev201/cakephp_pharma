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

<div class="simple-modal-finish text-center">
    <div class="quiz-finish"><?php echo __d('quiz', 'Your score'); ?>: <?php echo $iPercentResult; ?>/100</div>
    <?php if($iPercentResult >= $aResult['iPassScore']): ?>
    <div class="quiz-finish quiz-finish-pass"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Pass'); ?></div>
    <?php else: ?>
    <div class="quiz-finish quiz-finish-fail"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Fail'); ?></div>
    <?php endif; ?>
</div>