<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php if (!empty($aQuizTakes) && count($aQuizTakes) > 0) : ?>
    <?php foreach($aQuizTakes as $aQuizTake): ?>
    <li class="participant-index">
        <div class="participant-content">
            <?php echo $this->Moo->getItemPhoto(array('User' => $aQuizTake['User']), array('prefix' => '100_square'), array('class' => 'participant-img-user thumb_mobile', 'width' => '100')); ?>
            
            <div class="participant-info">
                <?php echo $this->Moo->getName($aQuizTake['User']); ?>
                <div class="date">
                    <?php echo __d('quiz', 'Taken date'); ?>: <?php echo $this->Time->event_format($aQuizTake['QuizTake']['created'], '%B %d, %Y'); ?>
                </div>
                <div class="extra_info">
                    <?php if($aQuizTake['QuizTake']['privacy'] == PRIVACY_PRIVATE && $uid != $aQuizTake['QuizTake']['user_id']): ?>
                        <span class="participant-fail"><?php echo __d('quiz', 'Results is private'); ?></span>
                    <?php else: ?>
                        <?php $iPercentResult = round(($aQuizTake['QuizTake']['correct_answer']/$iCountQuestion)*100); ?>
                        <?php if($iPercentResult >= $aQuizTake['Quiz']['pass_score']): ?>
                        <span class="participant-pass"><?php echo __d('quiz', 'Pass'); ?> - <?php echo $iPercentResult; ?>% (<?php echo $iPercentResult; ?>/100) - </span>
                        <?php else: ?>
                        <span class="participant-fail"><?php echo __d('quiz', 'Fail'); ?> - <?php echo $iPercentResult; ?>% (<?php echo $iPercentResult; ?>/100) - </span>
                        <?php endif; ?>
                        <?php
                        $this->MooPopup->tag(array(
                            'href' => $this->request->base . '/quizzes/view_result/' . $aQuizTake['QuizTake']['id'],
                            'innerHtml'=> __d('quiz', 'View Results'),
                            'title' => __d('quiz', 'View Results'),
                            'data-backdrop' => 'static'
                        ));
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
<?php else: ?>
    <li><div class="clear text-center"><?php echo __d('quiz', 'No results found'); ?></div></li>
<?php endif; ?>
    
<?php if (isset($more_url)&& !empty($more_result)): ?>
    <?php $this->Html->viewMore($more_url) ?>
<?php endif; ?>

