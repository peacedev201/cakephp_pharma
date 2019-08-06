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

<div class="modal-content">
    <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
            <?php echo __d('quiz', 'Quiz Results'); ?>: 
            <?php if($iPercentResult >= $aResult['iPassScore']): ?>
            <span class="quiz-finish-pass"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Pass'); ?></span>
            <?php else: ?>
            <span class="quiz-finish-fail"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Fail'); ?></span>
            <?php endif; ?>
        </h4>
    </div>
    <div class="modal-body">
        <ul class="results_list">
            <?php echo $this->element('lists/result_list'); ?>
        </ul>
    </div>
    <div class="modal-footer" style="padding: 15px 8px">
        <button data-dismiss="modal" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" type="button"><?php echo __d('quiz', 'Close'); ?></button>
    </div>
</div>