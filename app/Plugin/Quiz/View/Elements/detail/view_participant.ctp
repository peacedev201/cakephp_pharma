<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php if(!empty($bLoadHeader)): ?>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="post_body">
            <div class="mo_breadcrumb">
                <h1><?php echo __d('quiz', 'Participant'); ?></h1>
                <div class="list_option">
                    <div class="dropdown">
                        <?php echo __d('quiz', 'Sort by'); ?>
                        <button id="dropdown-sort" data-target="#" data-toggle="dropdown">
                            <i class="material-icons">expand_more</i>
                        </button>
                        <ul role="menu" class="dropdown-menu loadQuizView" aria-labelledby="dropdown-sort">
                            <li class="no-current">
                                <a href="<?php echo $quiz['Quiz']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/quizzes/view_participant/' . $quiz['Quiz']['id'] . '/latest'; ?>" rel="quiz-content"><?php echo __d('quiz', 'Latest'); ?></a>
                            </li>
                            <li class="no-current">
                                <a href="<?php echo $quiz['Quiz']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/quizzes/view_participant/' . $quiz['Quiz']['id'] . '/score'; ?>" rel="quiz-content"><?php echo __d('quiz', 'Score'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php if ($uid && $bViewTaken): ?>
                <ul id="list-content" class="participant-list">
                    <?php echo $this->element('lists/participants_list'); ?>
                </ul>
            <?php elseif($uid): ?>
                <?php echo __d('quiz', 'Please take quiz before can view list of participants.'); ?>
            <?php else: ?>
                <?php echo __d('quiz', 'Login or register to view participant'); ?>
            <?php endif; ?>    
            <div class="clear"></div>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooQuiz"], function (mooQuiz) {
        mooQuiz.initOnView();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnView();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php else: ?>
    <?php echo $this->element('lists/participants_list'); ?>
<?php endif; ?>

<!-- bind load more -->
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooBehavior"], function (mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooBehavior'), 'object' => array('mooBehavior'))); ?>
mooBehavior.initMoreResults();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
