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

<?php if (!empty($aQuizzes)): ?>
<ul class="quiz-block">
    <?php foreach ($aQuizzes as $aQuiz): ?>
    <li class="list-item-inline list-item-inline-text">
        <a href="<?php echo $aQuiz['Quiz']['moo_href']; ?>">
            <img width="70" src="<?php echo $quizHelper->getImage($aQuiz, array('prefix' => '75_square')); ?>" class="img_wrapper2 user_list" />
        </a>
        <div class="quiz_detail">
            <div class="title-list">
                <a class="title" href="<?php echo $aQuiz['Quiz']['moo_href']; ?>">
                    <?php echo h($aQuiz['Quiz']['title']); ?>
                </a>
            </div>
            <div class="like_count">
                <?php echo __dn('quiz', '%s take', '%s takes', $aQuiz['Quiz']['take_count'], $aQuiz['Quiz']['take_count']); ?> .
                <?php echo __dn('quiz', '%s comment', '%s comments', $aQuiz['Quiz']['comment_count'], $aQuiz['Quiz']['comment_count']); ?> .
                <?php echo __dn('quiz', '%s like', '%s likes', $aQuiz['Quiz']['like_count'], $aQuiz['Quiz']['like_count']); ?>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>