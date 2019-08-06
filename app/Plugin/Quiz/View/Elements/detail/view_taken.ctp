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

<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="post_body">
            
            <?php echo $this->element('detail/view_content'); ?>
            
            <div class="post_content">
                <h3 class="title">
                    <?php echo __d('quiz', 'Quiz Results'); ?>: 
                    <?php if($iPercentResult >= $aResult['iPassScore']): ?>
                    <span class="quiz-finish-pass"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Pass'); ?></span>
                    <?php else: ?>
                    <span class="quiz-finish-fail"><?php echo $iPercentResult; ?>% - <?php echo __d('quiz', 'Fail'); ?></span>
                    <?php endif; ?>
                </h3>

                <ul class="results_list">
                    <?php echo $this->element('lists/result_list'); ?>
                    <li class="last_results_list">
                        <div class="col-md-12">
                            <div class="margin_top_content">
                                <a href="javascript:void(0);" class="shareFeedBtn btn btn-action" share-url="<?php echo $this->Html->url(array("plugin" => false, "controller" => "share", "action" => "ajax_share", "id" => $aActivity['Activity']['id'])); ?>"><?php echo __d('quiz', 'Share Your Result'); ?></a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
</div>

<?php echo $this->element('detail/view_like_comment'); ?>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooQuiz"], function(mooQuiz) {
        mooQuiz.initOnView();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnView();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>